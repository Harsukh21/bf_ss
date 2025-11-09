<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class MarketController extends Controller
{
    public function index(Request $request)
    {
        // Cache filter options for 5 minutes using raw DB queries
        $sports = Cache::remember('markets.sports', 300, function () {
            return DB::table('market_lists')
                ->select('sportName')
                ->distinct()
                ->orderBy('sportName')
                ->pluck('sportName');
        });

        $tournaments = Cache::remember('markets.tournaments', 300, function () {
            return DB::table('market_lists')
                ->select('tournamentsName', 'sportName')
                ->distinct()
                ->orderBy('tournamentsName')
                ->get();
        });

        $marketTypeRecords = Cache::remember('markets.market_names', 300, function () {
            return DB::table('market_lists')
                ->select('marketName', 'tournamentsName', 'eventName')
                ->distinct()
                ->orderBy('marketName')
                ->get();
        });

        $marketTypes = $marketTypeRecords;

        // Get tournaments grouped by sport for JavaScript filtering
        $tournamentsBySport = Cache::remember('markets.tournaments_by_sport', 300, function () {
            return DB::table('market_lists')
                ->select('tournamentsName', 'sportName')
                ->distinct()
                ->orderBy('tournamentsName')
                ->get()
                ->groupBy('sportName');
        });

        // Get market types grouped by tournament for JavaScript filtering
        $marketTypesByTournament = Cache::remember('markets.types_by_tournament', 300, function () use ($marketTypeRecords) {
            return $marketTypeRecords->groupBy('tournamentsName');
        });

        $eventsByTournament = Cache::remember('markets.events_by_tournament', 300, function () {
            return DB::table('market_lists')
                ->select('eventName', 'tournamentsName')
                ->distinct()
                ->orderBy('eventName')
                ->get()
                ->groupBy('tournamentsName');
        });

        $marketTypesByEvent = $marketTypeRecords->groupBy('eventName');

        // Build optimized raw query with specific column selection
        $query = DB::table('market_lists')
            ->select([
                'id',
                '_id',
                'eventName',
                'exEventId',
                'exMarketId',
                'isPreBet',
                'marketName',
                'marketTime',
                'sportName',
                'tournamentsName',
                'type',
                'isLive',
                'isRecentlyAdded',
                'status',
                'created_at'
            ]);

        // Apply optimized filters with raw DB queries
        $this->applyFilters($query, $request);

        // Get total count for pagination
        $totalCount = $query->count();

        // Apply pagination manually
        $page = $request->get('page', 1);
        $perPage = 15;
        $offset = ($page - 1) * $perPage;

        $markets = $query
            ->orderBy('marketTime', 'desc')
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($perPage)
            ->get();
        // Create paginator manually
        $paginatedMarkets = new LengthAwarePaginator(
            $markets,
            $totalCount,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );

        $paginatedMarkets->appends($request->query());

        // Get active filters for display
        $activeFilters = $this->getActiveFilters($request);

        return view('markets.index', [
            'paginatedMarkets' => $paginatedMarkets,
            'sports' => $sports,
            'tournaments' => $tournaments,
            'marketTypes' => $marketTypes,
            'activeFilters' => $activeFilters,
            'tournamentsBySport' => $tournamentsBySport,
            'marketTypesByTournament' => $marketTypesByTournament,
            'eventsByTournament' => $eventsByTournament,
            'marketTypesByEvent' => $marketTypesByEvent,
            'pageTitle' => 'Market List',
            'pageHeading' => 'Market List',
            'pageSubheading' => 'Browse markets today and tomorrow',
        ]);
    }

    public function all(Request $request)
    {
        $sports = Cache::remember('markets.sports', 300, function () {
            return DB::table('market_lists')
                ->select('sportName')
                ->distinct()
                ->orderBy('sportName')
                ->pluck('sportName');
        });

        $tournaments = Cache::remember('markets.tournaments', 300, function () {
            return DB::table('market_lists')
                ->select('tournamentsName', 'sportName')
                ->distinct()
                ->orderBy('tournamentsName')
                ->get();
        });

        $marketTypeRecords = Cache::remember('markets.market_names', 300, function () {
            return DB::table('market_lists')
                ->select('marketName', 'tournamentsName', 'eventName')
                ->distinct()
                ->orderBy('marketName')
                ->get();
        });

        $marketTypes = $marketTypeRecords;

        $tournamentsBySport = Cache::remember('markets.tournaments_by_sport', 300, function () {
            return DB::table('market_lists')
                ->select('tournamentsName', 'sportName')
                ->distinct()
                ->orderBy('tournamentsName')
                ->get()
                ->groupBy('sportName');
        });

        $marketTypesByTournament = Cache::remember('markets.types_by_tournament', 300, function () use ($marketTypeRecords) {
            return $marketTypeRecords->groupBy('tournamentsName');
        });

        $eventsByTournament = Cache::remember('markets.events_by_tournament', 300, function () {
            return DB::table('market_lists')
                ->select('eventName', 'tournamentsName')
                ->distinct()
                ->orderBy('eventName')
                ->get()
                ->groupBy('tournamentsName');
        });

        $marketTypesByEvent = $marketTypeRecords->groupBy('eventName');

        $query = DB::table('market_lists')
            ->select([
                'id',
                '_id',
                'eventName',
                'exEventId',
                'exMarketId',
                'isPreBet',
                'marketName',
                'marketTime',
                'sportName',
                'tournamentsName',
                'type',
                'isLive',
                'isRecentlyAdded',
                'status',
                'created_at'
            ]);

        $this->applyFilters($query, $request);

        $totalCount = $query->count();

        $page = $request->get('page', 1);
        $perPage = 15;
        $offset = ($page - 1) * $perPage;

        $markets = $query
            ->orderBy('marketTime', 'desc')
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($perPage)
            ->get();

        $paginatedMarkets = new LengthAwarePaginator(
            $markets,
            $totalCount,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );

        $paginatedMarkets->appends($request->query());

        $activeFilters = $this->getActiveFilters($request);

        return view('markets.all', [
            'paginatedMarkets' => $paginatedMarkets,
            'sports' => $sports,
            'tournaments' => $tournaments,
            'marketTypes' => $marketTypes,
            'activeFilters' => $activeFilters,
            'tournamentsBySport' => $tournamentsBySport,
            'marketTypesByTournament' => $marketTypesByTournament,
            'eventsByTournament' => $eventsByTournament,
            'marketTypesByEvent' => $marketTypesByEvent,
            'pageTitle' => 'All Markets List',
            'pageHeading' => 'All Markets List',
            'pageSubheading' => 'Browse every market without date limits',
        ]);
    }

    public function show($id)
    {
        $market = DB::table('market_lists')
            ->where('id', $id)
            ->first();

        if (!$market) {
            abort(404, 'Market not found');
        }

        return view('markets.show', compact('market'));
    }

    private function applyFilters($query, Request $request)
    {
        // Sport filter
        if ($request->filled('sport')) {
            $query->where('sportName', $request->sport);
        }

        // Tournament filter
        if ($request->filled('tournament')) {
            $query->where('tournamentsName', $request->tournament);
        }

        if ($request->filled('event_name')) {
            $query->where('eventName', $request->event_name);
        }

        // Market name filter
        if ($request->filled('market_name')) {
            $query->where('marketName', $request->market_name);
        } elseif ($request->filled('type')) {
            // Backward compatibility if legacy parameter is present
            $query->where(function ($q) use ($request) {
                $q->where('marketName', $request->type)
                  ->orWhere('type', $request->type);
            });
        }

        // Live filter
        if ($request->has('is_live')) {
            $query->where('isLive', true);
        }

        // Pre-bet filter
        if ($request->has('is_prebet')) {
            $query->where('isPreBet', true);
        }

        if ($request->boolean('recently_added')) {
            $query->where('isRecentlyAdded', true);
        }

        // Date & time filter - using marketTime from market_lists table
        $timezone = config('app.timezone', 'UTC');
        $dateFromEnabled = $request->boolean('date_from_enabled');
        $dateToEnabled = $request->boolean('date_to_enabled');
        $timeFromEnabled = $request->boolean('time_from_enabled');
        $timeToEnabled = $request->boolean('time_to_enabled');

        $timeFormats = ['h:i:s A', 'h:i A', 'H:i:s', 'H:i'];

        $startDateTime = null;
        $endDateTime = null;

        if ($dateFromEnabled && $request->filled('date_from')) {
            $timeComponent = '00:00:00';

            if ($timeFromEnabled && $request->filled('time_from')) {
                foreach ($timeFormats as $format) {
                    try {
                        $timeComponent = Carbon::createFromFormat($format, $request->time_from)->format('H:i:s');
                        break;
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }

            try {
                $startDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $request->date_from . ' ' . $timeComponent, $timezone);
            } catch (\Exception $e) {
                $startDateTime = Carbon::now($timezone)->startOfDay();
            }
        }

        if ($dateToEnabled && $request->filled('date_to')) {
            $timeComponent = '23:59:59';

            if ($timeToEnabled && $request->filled('time_to')) {
                foreach ($timeFormats as $format) {
                    try {
                        $timeComponent = Carbon::createFromFormat($format, $request->time_to)->format('H:i:s');
                        break;
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }

            try {
                $endDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $request->date_to . ' ' . $timeComponent, $timezone);
            } catch (\Exception $e) {
                $endDateTime = Carbon::now($timezone)->endOfDay();
            }
        }

        if ($startDateTime && $endDateTime) {
            if ($endDateTime->lt($startDateTime)) {
                $endDateTime = $startDateTime->copy()->endOfDay();
            }

            $query->whereBetween('marketTime', [
                $startDateTime->format('Y-m-d H:i:s'),
                $endDateTime->format('Y-m-d H:i:s'),
            ]);
        } elseif ($startDateTime) {
            $query->where('marketTime', '>=', $startDateTime->format('Y-m-d H:i:s'));
        } elseif ($endDateTime) {
            $query->where('marketTime', '<=', $endDateTime->format('Y-m-d H:i:s'));
        }

        // Search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('marketName', 'ILIKE', "%{$searchTerm}%")
                  ->orWhere('eventName', 'ILIKE', "%{$searchTerm}%")
                  ->orWhere('sportName', 'ILIKE', "%{$searchTerm}%")
                  ->orWhere('tournamentsName', 'ILIKE', "%{$searchTerm}%");
            });
        }
    }

    private function getActiveFilters(Request $request)
    {
        $activeFilters = [];

        if ($request->filled('sport')) {
            $activeFilters['Sport'] = $request->sport;
        }

        if ($request->filled('tournament')) {
            $activeFilters['Tournament'] = $request->tournament;
        }

        if ($request->filled('event_name')) {
            $activeFilters['Event'] = $request->event_name;
        }

        if ($request->filled('market_name')) {
            $activeFilters['Market'] = $request->market_name;
        } elseif ($request->filled('type')) {
            $activeFilters['Market'] = $request->type;
        }

        if ($request->has('is_live')) {
            $activeFilters['Live'] = 'Yes';
        }

        if ($request->has('is_prebet')) {
            $activeFilters['Pre-bet'] = 'Yes';
        }

        if ($request->boolean('recently_added')) {
            $activeFilters['Recently Added'] = 'Yes';
        }

        if ($request->boolean('date_from_enabled') && $request->filled('date_from')) {
            $activeFilters['From Date'] = $request->date_from;
        }

        if ($request->boolean('date_to_enabled') && $request->filled('date_to')) {
            $activeFilters['To Date'] = $request->date_to;
        }

        if ($request->boolean('time_from_enabled') && $request->boolean('date_from_enabled') && $request->filled('time_from')) {
            $activeFilters['From Time'] = $request->time_from;
        }

        if ($request->boolean('time_to_enabled') && $request->boolean('date_to_enabled') && $request->filled('time_to')) {
            $activeFilters['To Time'] = $request->time_to;
        }

        if ($request->filled('search')) {
            $activeFilters['Search'] = $request->search;
        }

        return $activeFilters;
    }

    public function export(Request $request)
    {
        // Build the same query as index but without pagination
        $query = DB::table('market_lists')
            ->select([
                'id',
                '_id',
                'eventName',
                'exEventId',
                'exMarketId',
                'isPreBet',
                'marketName',
                'marketTime',
                'sportName',
                'tournamentsName',
                'type',
                'isLive',
                'isRecentlyAdded',
                'status',
                'created_at'
            ]);

        // Apply the same filters
        $this->applyFilters($query, $request);

        // Get all results (no pagination)
        $markets = $query->orderBy('marketTime', 'desc')
                         ->orderBy('id', 'desc')
                         ->get();

        // Prepare CSV data
        $filename = 'markets_export_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($markets) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID',
                'Market ID',
                'Event Name',
                'Event ID',
                'Market Name',
                'Sport',
                'Tournament',
                'Type',
                'Status',
                'Market Time',
                'Created At'
            ]);

            // Add data rows
            foreach ($markets as $market) {
                $status = $market->status;

                if (!$status) {
                    if ($market->isLive) {
                        $status = 'Live';
                    } elseif ($market->isPreBet) {
                        $status = 'Pre-bet';
                    } else {
                        $status = 'Scheduled';
                    }
                }

                $status = strtoupper($status);

                fputcsv($file, [
                    $market->id,
                    $market->_id,
                    $market->eventName,
                    $market->exEventId,
                    $market->marketName,
                    $market->sportName,
                    $market->tournamentsName,
                    $market->type,
                    $status,
                    $market->marketTime,
                    $market->created_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
