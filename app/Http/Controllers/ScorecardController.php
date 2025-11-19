<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScorecardController extends Controller
{
    public function index(Request $request)
    {
        // Build the query with filters
        $query = DB::table('events')
            ->select([
                'events.id',
                'events.eventId',
                'events.exEventId',
                'events.eventName',
                'events.sportId',
                'events.tournamentsId',
                'events.tournamentsName',
                'events.marketTime',
                'events.createdAt',
                DB::raw('COUNT(DISTINCT "market_lists"."id") as inplay_markets_count'),
                DB::raw('MIN("market_lists"."marketTime") as first_market_time'),
            ])
            ->join('market_lists', function($join) {
                $join->on('market_lists.exEventId', '=', 'events.exEventId');
            })
            ->where('market_lists.status', 3); // INPLAY status

        // Apply filters
        if ($request->filled('search')) {
            $search = '%' . $request->input('search') . '%';
            $query->where(function($q) use ($search) {
                $q->where('events.eventName', 'ILIKE', $search)
                  ->orWhere('events.tournamentsName', 'ILIKE', $search);
            });
        }

        if ($request->filled('sport')) {
            $sportName = $request->input('sport');
            $sports = config('sports.sports', []);
            // Find sportId by sport name
            $sportId = array_search($sportName, $sports);
            if ($sportId !== false) {
                $query->where('events.sportId', $sportId);
            }
        }

        if ($request->filled('tournament')) {
            $query->where('events.tournamentsName', $request->input('tournament'));
        }

        // Date range filters
        if ($request->filled('date_from')) {
            $dateFrom = Carbon::parse($request->input('date_from'))->startOfDay();
            $query->where('events.marketTime', '>=', $dateFrom);
        }

        if ($request->filled('date_to')) {
            $dateTo = Carbon::parse($request->input('date_to'))->endOfDay();
            $query->where('events.marketTime', '<=', $dateTo);
        }

        $events = $query->groupBy(
                'events.id',
                'events.eventId',
                'events.exEventId',
                'events.eventName',
                'events.sportId',
                'events.tournamentsId',
                'events.tournamentsName',
                'events.marketTime',
                'events.createdAt'
            )
            ->orderBy('events.marketTime', 'desc')
            ->paginate(20);

        // Get sports list for display
        $sports = config('sports.sports', []);

        // Get tournaments list for filter
        $tournaments = DB::table('events')
            ->select('tournamentsName')
            ->distinct()
            ->orderBy('tournamentsName')
            ->pluck('tournamentsName');

        // Get label configuration
        $labelConfig = config('labels.labels', []);
        $labelKeys = array_keys($labelConfig);

        // Get all exEventIds from the paginated events
        $eventIds = $events->pluck('exEventId')->toArray();

        // Fetch all labels for in-play markets of these events in one query
        $allMarkets = DB::table('market_lists')
            ->select('exEventId', 'labels')
            ->whereIn('exEventId', $eventIds)
            ->where('status', 3)
            ->whereNotNull('labels')
            ->get()
            ->groupBy('exEventId');

        // Format the events with sport names and aggregate labels
        $events->getCollection()->transform(function ($event) use ($sports, $labelKeys, $allMarkets) {
            $event->sportName = $sports[$event->sportId] ?? 'Unknown Sport';
            $event->formatted_market_time = $event->marketTime 
                ? Carbon::parse($event->marketTime)->format('M d, Y h:i A') 
                : null;
            $event->formatted_first_market_time = $event->first_market_time 
                ? Carbon::parse($event->first_market_time)->format('M d, Y h:i A') 
                : null;
            
            // Aggregate labels from all in-play markets for this event
            $markets = $allMarkets->get($event->exEventId, collect());
            
            // Aggregate labels: show label as checked if at least one market has it checked
            $aggregatedLabels = [];
            foreach ($labelKeys as $labelKey) {
                $aggregatedLabels[$labelKey] = false;
                foreach ($markets as $market) {
                    $labels = is_string($market->labels) ? json_decode($market->labels, true) : $market->labels;
                    if (is_array($labels) && isset($labels[$labelKey]) && (bool)$labels[$labelKey] === true) {
                        $aggregatedLabels[$labelKey] = true;
                        break; // At least one market has this label checked
                    }
                }
            }
            
            $event->labels = $aggregatedLabels;
            
            return $event;
        });

        return view('scorecard.index', [
            'events' => $events,
            'sports' => $sports,
            'tournaments' => $tournaments,
            'labelConfig' => $labelConfig,
        ]);
    }
}
