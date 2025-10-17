<?php

namespace App\Http\Controllers;

use App\Models\MarketRate;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketRateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get all available events for dropdown (from events table and market_lists)
        $eventsFromEvents = Event::select('eventId', 'eventName', 'exEventId')->get();
        
        // Get events from market_lists and normalize the data structure
        $marketEvents = DB::table('market_lists')
            ->select('exEventId', 'eventName')
            ->whereNotIn('exEventId', $eventsFromEvents->pluck('exEventId'))
            ->distinct()
            ->get();
        
        // Convert to proper objects with consistent property names
        $eventsFromMarkets = $marketEvents->map(function ($event) {
            return (object) [
                'eventId' => 'market_' . time() . '_' . rand(1000, 9999),
                'eventName' => $event->eventName,
                'exEventId' => $event->exEventId
            ];
        });
        
        $events = $eventsFromEvents->concat($eventsFromMarkets);
        
        // Get selected event ID
        $selectedEventId = $request->get('exEventId');
        
        $marketRates = collect([]);
        $eventInfo = null;
        
        if ($selectedEventId) {
            // Check if table exists for this event
            if (MarketRate::tableExistsForEvent($selectedEventId)) {
                $query = MarketRate::forEvent($selectedEventId);

                // Apply search filter
                if ($request->filled('search')) {
                    $search = $request->get('search');
                    $query->where(function ($q) use ($search) {
                        $q->where('marketName', 'like', "%{$search}%")
                          ->orWhere('exMarketId', 'like', "%{$search}%");
                    });
                }

                // Apply market filter
                if ($request->filled('market_name')) {
                    $query->where('marketName', $request->get('market_name'));
                }

                // Apply status filter
                if ($request->filled('status')) {
                    if ($request->get('status') === 'inplay') {
                        $query->where('inplay', true);
                    } elseif ($request->get('status') === 'completed') {
                        $query->where('isCompleted', true);
                    } elseif ($request->get('status') === 'upcoming') {
                        $query->where('inplay', false)->where('isCompleted', false);
                    }
                }

                $marketRates = $query->oldest('created_at')->paginate(10);
            }
            
            // Get event information (from events table or market_lists)
            $eventInfo = Event::where('exEventId', $selectedEventId)->first();
            if (!$eventInfo) {
                $marketEventInfo = DB::table('market_lists')
                    ->where('exEventId', $selectedEventId)
                    ->select('eventName', 'exEventId')
                    ->selectRaw("'market_list' as source")
                    ->first();
                if ($marketEventInfo) {
                    // Convert stdClass to object with proper attributes
                    $eventInfo = (object) [
                        'eventName' => $marketEventInfo->eventName,
                        'exEventId' => $marketEventInfo->exEventId,
                        'source' => $marketEventInfo->source
                    ];
                }
            }
        }

        return view('market-rates.index', compact('marketRates', 'events', 'selectedEventId', 'eventInfo'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $selectedEventId = $request->get('exEventId');
        
        if (!$selectedEventId || !MarketRate::tableExistsForEvent($selectedEventId)) {
            return redirect()->route('market-rates.index')
                ->with('error', 'Market rates not found for this event.');
        }

        $query = MarketRate::forEvent($selectedEventId);
        $marketRate = $query->find($id);

        if (!$marketRate) {
            return redirect()->route('market-rates.index')
                ->with('error', 'Market rate not found.');
        }

        $eventInfo = Event::where('exEventId', $selectedEventId)->first();

        return view('market-rates.show', compact('marketRate', 'eventInfo', 'selectedEventId'));
    }

    /**
     * Search market rates (redirects to index with search parameter)
     */
    public function search(Request $request)
    {
        return redirect()->route('market-rates.index', $request->all());
    }

    /**
     * Get market rates count for an event (AJAX endpoint)
     */
    public function getCount(Request $request)
    {
        $exEventId = $request->get('exEventId');
        
        if (!$exEventId) {
            return response()->json(['count' => 0]);
        }

        $count = MarketRate::getCountForEvent($exEventId);
        
        return response()->json(['count' => $count]);
    }
}
