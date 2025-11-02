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
        $availableMarketNames = collect([]);
        $ratesTableNotFound = false;
        
        if ($selectedEventId) {
            // Check if table exists for this event
            if (MarketRate::tableExistsForEvent($selectedEventId)) {
                $query = MarketRate::forEvent($selectedEventId);
                
                // Get available market names for the dropdown
                $availableMarketNames = MarketRate::forEvent($selectedEventId)
                    ->select('marketName')
                    ->distinct()
                    ->whereNotNull('marketName')
                    ->orderBy('marketName')
                    ->pluck('marketName');

                // Apply market filter
                if ($request->filled('market_name')) {
                    $query->where('marketName', $request->get('market_name'));
                }

                // Apply status filter
                if ($request->filled('status')) {
                    if ($request->get('status') === 'inplay') {
                        $query->where('inplay', true);
                    } elseif ($request->get('status') === 'not_inplay') {
                        $query->where('inplay', false);
                    }
                }

                // Apply date range filter
                if ($request->filled('date_from')) {
                    $dateFrom = \Carbon\Carbon::parse($request->get('date_from'))->setTimezone('Asia/Kolkata');
                    $query->where('created_at', '>=', $dateFrom);
                }
                if ($request->filled('date_to')) {
                    $dateTo = \Carbon\Carbon::parse($request->get('date_to'))->setTimezone('Asia/Kolkata');
                    $query->where('created_at', '<=', $dateTo);
                }

                $marketRates = $query->latest('created_at')->paginate(10);
            } else {
                // Table does not exist for this event
                $ratesTableNotFound = true;
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

        return view('market-rates.index', compact('marketRates', 'events', 'selectedEventId', 'eventInfo', 'availableMarketNames', 'ratesTableNotFound'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $selectedEventId = $request->get('exEventId');
        $gridCount = $request->get('grid');
        $gridEnabled = !empty($gridCount) && in_array((int)$gridCount, [10, 20, 40, 60]);
        $gridCountValue = $gridEnabled ? (int)$gridCount : null;
        
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

        // Get next and previous market rates for navigation (filtered by marketName)
        // Ensure we only get records with the exact same marketName
        $allMarketRates = MarketRate::forEvent($selectedEventId)
            ->where('marketName', $marketRate->marketName)
            ->whereNotNull('marketName')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $currentIndex = $allMarketRates->search(function($item) use ($id) {
            return $item->id == $id;
        });
        
        $previousMarketRate = null;
        $nextMarketRate = null;
        $gridMarketRates = collect();
        
        if ($currentIndex !== false) {
            if ($currentIndex > 0) {
                $previousMarketRate = $allMarketRates[$currentIndex - 1];
            }
            if ($currentIndex < $allMarketRates->count() - 1) {
                $nextMarketRate = $allMarketRates[$currentIndex + 1];
            }

            // When grid mode is enabled, get current record + (count-1) newer records
            // All records are already filtered by marketName above
            if ($gridEnabled) {
                $currentCreatedAt = $marketRate->created_at;
                $additionalRecords = $gridCountValue - 1; // Subtract 1 for current record
                
                // Get up to (count-1) records with same marketName that are newer (created_at > current)
                $newerRecords = MarketRate::forEvent($selectedEventId)
                    ->where('marketName', $marketRate->marketName)
                    ->whereNotNull('marketName')
                    ->where('created_at', '>', $currentCreatedAt)
                    ->orderBy('created_at', 'asc') // Order ascending to get them in chronological order
                    ->limit($additionalRecords)
                    ->get();
                
                // Double-check marketName matches and create collection with current record first
                $gridMarketRates = collect([$marketRate])
                    ->merge($newerRecords->filter(function($item) use ($marketRate) {
                        return $item->marketName === $marketRate->marketName;
                    }))
                    ->values();
            }
        }

        return view('market-rates.show', compact(
            'marketRate',
            'eventInfo',
            'selectedEventId',
            'previousMarketRate',
            'nextMarketRate',
            'gridEnabled',
            'gridCountValue',
            'gridMarketRates'
        ));
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

    /**
     * Export market rates to CSV
     */
    public function export(Request $request)
    {
        $selectedEventId = $request->get('exEventId');
        
        if (!$selectedEventId) {
            return redirect()->route('market-rates.index')
                ->with('error', 'Please select an event to export data.');
        }

        if (!MarketRate::tableExistsForEvent($selectedEventId)) {
            return redirect()->route('market-rates.index')
                ->with('error', 'No data available for this event.');
        }

        $query = MarketRate::forEvent($selectedEventId);

        // Apply market filter
        if ($request->filled('market_name')) {
            $query->where('marketName', $request->get('market_name'));
        }

        // Apply status filter
        if ($request->filled('status')) {
            if ($request->get('status') === 'inplay') {
                $query->where('inplay', true);
            } elseif ($request->get('status') === 'not_inplay') {
                $query->where('inplay', false);
            }
        }

        // Apply date range filter
        if ($request->filled('date_from')) {
            $dateFrom = \Carbon\Carbon::parse($request->get('date_from'))->setTimezone('Asia/Kolkata');
            $query->where('created_at', '>=', $dateFrom);
        }
        if ($request->filled('date_to')) {
            $dateTo = \Carbon\Carbon::parse($request->get('date_to'))->setTimezone('Asia/Kolkata');
            $query->where('created_at', '<=', $dateTo);
        }

        // Get all data (no pagination for export)
        $marketRates = $query->latest('created_at')->get();

        // Get event information
        $eventInfo = Event::where('exEventId', $selectedEventId)->first();
        if (!$eventInfo) {
            $marketEventInfo = DB::table('market_lists')
                ->where('exEventId', $selectedEventId)
                ->select('eventName', 'exEventId')
                ->first();
            if ($marketEventInfo) {
                $eventInfo = (object) [
                    'eventName' => $marketEventInfo->eventName,
                    'exEventId' => $marketEventInfo->exEventId,
                ];
            }
        }

        // Generate CSV
        $filename = 'market_rates_' . $selectedEventId . '_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($marketRates) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Market Name',
                'Market ID',
                'Runner Name',
                'Back Price',
                'Back Size',
                'Lay Price',
                'Lay Size',
                'Status',
                'In Play',
                'Is Completed',
                'Created At'
            ]);

            // CSV Data
            foreach ($marketRates as $rate) {
                $runners = is_string($rate->runners) ? json_decode($rate->runners, true) : $rate->runners;
                $runners = is_array($runners) ? $runners : [];
                
                $status = '';
                if ($rate->isCompleted) {
                    $status = 'Completed';
                } elseif ($rate->inplay) {
                    $status = 'In Play';
                } else {
                    $status = 'Upcoming';
                }

                // Export each runner
                foreach ($runners as $runner) {
                    $runner = is_array($runner) ? $runner : (array) $runner;
                    $runnerName = $runner['runnerName'] ?? 'Unknown';
                    
                    // Get best back and lay prices
                    $exchange = is_array($runner['exchange'] ?? null) ? $runner['exchange'] : (array) ($runner['exchange'] ?? []);
                    $availableToBack = $exchange['availableToBack'] ?? [];
                    $availableToLay = $exchange['availableToLay'] ?? [];
                    
                    $availableToBack = is_array($availableToBack) ? $availableToBack : (array) $availableToBack;
                    $availableToLay = is_array($availableToLay) ? $availableToLay : (array) $availableToLay;
                    
                    $bestBack = !empty($availableToBack) && is_array($availableToBack[0] ?? null) ? $availableToBack[0] : [];
                    $bestLay = !empty($availableToLay) && is_array($availableToLay[0] ?? null) ? $availableToLay[0] : [];
                    
                    fputcsv($file, [
                        $rate->marketName,
                        $rate->exMarketId,
                        $runnerName,
                        $bestBack['price'] ?? '',
                        $bestBack['size'] ?? '',
                        $bestLay['price'] ?? '',
                        $bestLay['size'] ?? '',
                        $status,
                        $rate->inplay ? 'Yes' : 'No',
                        $rate->isCompleted ? 'Yes' : 'No',
                        $rate->created_at
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
