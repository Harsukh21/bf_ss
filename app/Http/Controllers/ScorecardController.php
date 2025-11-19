<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScorecardController extends Controller
{
    public function index(Request $request)
    {
        // Get events that have at least one market with status = 3 (INPLAY)
        // Use raw SQL with proper PostgreSQL quoting for case-sensitive column names
        $events = DB::table('events')
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
            ->where('market_lists.status', 3) // INPLAY status
            ->groupBy(
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

        // Format the events with sport names
        $events->getCollection()->transform(function ($event) use ($sports) {
            $event->sportName = $sports[$event->sportId] ?? 'Unknown Sport';
            $event->formatted_market_time = $event->marketTime 
                ? Carbon::parse($event->marketTime)->format('M d, Y h:i A') 
                : null;
            $event->formatted_first_market_time = $event->first_market_time 
                ? Carbon::parse($event->first_market_time)->format('M d, Y h:i A') 
                : null;
            return $event;
        });

        return view('scorecard.index', [
            'events' => $events,
            'sports' => $sports,
        ]);
    }
}
