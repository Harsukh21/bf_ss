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

        $hasCustomDateFilter = $request->boolean('date_from_enabled') || $request->boolean('date_to_enabled');
        $isRecentlyAdded = $request->boolean('recently_added');

        if (!$hasCustomDateFilter && !$isRecentlyAdded) {
            $timezone = config('app.timezone', 'UTC');
            $startDate = Carbon::now($timezone)->startOfDay();
            $endDate = Carbon::now($timezone)->addDay()->endOfDay();

            $query->whereBetween('marketTime', [
                $startDate->format('Y-m-d H:i:s'),
                $endDate->format('Y-m-d H:i:s'),
            ]);
        } elseif (!$hasCustomDateFilter && $isRecentlyAdded) {
            $timezone = config('app.timezone', 'UTC');
            $startDate = Carbon::now($timezone)->startOfDay();
            $endDate = Carbon::now($timezone)->addDay()->endOfDay();

            $query->whereBetween('marketTime', [
                $startDate->format('Y-m-d H:i:s'),
                $endDate->format('Y-m-d H:i:s'),
            ]);
        }

        // Get total count for pagination
        $totalCount = $query->count();

        // Apply pagination manually
        $page = $request->get('page', 1);
        $perPage = 15;
        $offset = ($page - 1) * $perPage;

        $markets = $query
            ->orderBy('marketTime', 'asc')
            ->orderBy('id', 'asc')
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
            'pageSubheading' => 'Market for today and tomorrow are shown here.',
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

        $page = $request->get('page', 1);
        $perPage = 15;
        $offset = ($page - 1) * $perPage;

        $selectColumns = [
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
            'labels',
            'selectionName',
            'winnerType',
            'created_at'
        ];
        $selectList = implode(', ', array_map([$this, 'quoteColumn'], $selectColumns));

        $filters = $this->buildMarketFilterSql($request);
        $whereSql = '';
        if (!empty($filters['conditions'])) {
            $whereSql = ' WHERE ' . implode(' AND ', $filters['conditions']);
        }

        $countSql = "SELECT COUNT(*) as total FROM market_lists{$whereSql}";
        $totalCountResult = DB::selectOne($countSql, $filters['bindings']);
        $totalCount = $totalCountResult ? (int) $totalCountResult->total : 0;

        $dataSql = sprintf(
            'SELECT %s FROM market_lists%s ORDER BY %s DESC, %s DESC LIMIT ? OFFSET ?',
            $selectList,
            $whereSql,
            $this->quoteColumn('marketTime'),
            $this->quoteColumn('id')
        );

        $dataBindings = array_merge($filters['bindings'], [$perPage, $offset]);
        $markets = collect(DB::select($dataSql, $dataBindings));

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

        if ($request->filled('status')) {
            $query->where('status', (int) $request->status);
        }

        // Live filter
        if ($request->has('is_live')) {
            $query->where('isLive', true);
        }

        // Pre-bet filter
        if ($request->has('is_prebet')) {
            $query->where('isPreBet', true);
        }

        $isRecentlyAdded = $request->boolean('recently_added');

        if ($isRecentlyAdded) {
            $query->where('isRecentlyAdded', true);
        }

        $dateFilters = $this->resolveMarketDateFilters($request, $isRecentlyAdded);

        if ($dateFilters['start'] && $dateFilters['end']) {
            $query->whereBetween($dateFilters['column'], [$dateFilters['start'], $dateFilters['end']]);
        } elseif ($dateFilters['start']) {
            $query->where($dateFilters['column'], '>=', $dateFilters['start']);
        } elseif ($dateFilters['end']) {
            $query->where($dateFilters['column'], '<=', $dateFilters['end']);
        }

        // Search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('marketName', 'ILIKE', "%{$searchTerm}%")
                  ->orWhere('eventName', 'ILIKE', "%{$searchTerm}%")
                  ->orWhere('sportName', 'ILIKE', "%{$searchTerm}%")
                  ->orWhere('tournamentsName', 'ILIKE', "%{$searchTerm}%")
                  ->orWhere('exEventId', 'ILIKE', "%{$searchTerm}%");
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

        if ($request->filled('status')) {
            $statusMap = [
                '1' => 'Unsettled',
                '2' => 'Upcoming',
                '3' => 'In Play',
                '4' => 'Settled',
                '5' => 'Voided',
                '6' => 'Removed',
            ];
            $activeFilters['Status'] = $statusMap[$request->status] ?? $request->status;
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

    private function buildMarketFilterSql(Request $request): array
    {
        $conditions = [];
        $bindings = [];

        if ($request->filled('sport')) {
            $conditions[] = $this->quoteColumn('sportName') . ' = ?';
            $bindings[] = $request->sport;
        }

        if ($request->filled('tournament')) {
            $conditions[] = $this->quoteColumn('tournamentsName') . ' = ?';
            $bindings[] = $request->tournament;
        }

        if ($request->filled('event_name')) {
            $conditions[] = $this->quoteColumn('eventName') . ' = ?';
            $bindings[] = $request->event_name;
        }

        if ($request->filled('market_name')) {
            $conditions[] = $this->quoteColumn('marketName') . ' = ?';
            $bindings[] = $request->market_name;
        } elseif ($request->filled('type')) {
            $conditions[] = '(' . $this->quoteColumn('marketName') . ' = ? OR ' . $this->quoteColumn('type') . ' = ?)';
            $bindings[] = $request->type;
            $bindings[] = $request->type;
        }

        if ($request->filled('status')) {
            $conditions[] = $this->quoteColumn('status') . ' = ?';
            $bindings[] = (int) $request->status;
        }

        if ($request->has('is_live')) {
            $conditions[] = $this->quoteColumn('isLive') . ' = ?';
            $bindings[] = true;
        }

        if ($request->has('is_prebet')) {
            $conditions[] = $this->quoteColumn('isPreBet') . ' = ?';
            $bindings[] = true;
        }

        $isRecentlyAdded = $request->boolean('recently_added');
        if ($isRecentlyAdded) {
            $conditions[] = $this->quoteColumn('isRecentlyAdded') . ' = ?';
            $bindings[] = true;
        }

        $dateFilters = $this->resolveMarketDateFilters($request, $isRecentlyAdded);
        $quotedColumn = $this->quoteColumn($dateFilters['column']);
        if ($dateFilters['start'] && $dateFilters['end']) {
            $conditions[] = "{$quotedColumn} BETWEEN ? AND ?";
            $bindings[] = $dateFilters['start'];
            $bindings[] = $dateFilters['end'];
        } elseif ($dateFilters['start']) {
            $conditions[] = "{$quotedColumn} >= ?";
            $bindings[] = $dateFilters['start'];
        } elseif ($dateFilters['end']) {
            $conditions[] = "{$quotedColumn} <= ?";
            $bindings[] = $dateFilters['end'];
        }

        if ($request->filled('search')) {
            $conditions[] = '('
                . $this->quoteColumn('eventName') . " ILIKE ? OR "
                . $this->quoteColumn('marketName') . " ILIKE ? OR "
                . $this->quoteColumn('exEventId') . " ILIKE ?)";
            $searchBinding = '%' . $request->search . '%';
            $bindings[] = $searchBinding;
            $bindings[] = $searchBinding;
            $bindings[] = $searchBinding;
        }

        return [
            'conditions' => $conditions,
            'bindings' => $bindings,
        ];
    }

    private function resolveMarketDateFilters(Request $request, bool $isRecentlyAdded): array
    {
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

        if ($startDateTime && $endDateTime && $endDateTime->lt($startDateTime)) {
            $endDateTime = $startDateTime->copy()->endOfDay();
        }

        $columnName = $isRecentlyAdded ? 'created_at' : 'marketTime';
        
        return [
            'start' => $startDateTime ? $startDateTime->format('Y-m-d H:i:s') : null,
            'end' => $endDateTime ? $endDateTime->format('Y-m-d H:i:s') : null,
            'column' => $columnName,
        ];
    }

    private function quoteColumn(string $column): string
    {
        return '"' . str_replace('"', '""', $column) . '"';
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
            $statusLookup = [
                1 => 'UNSETTLED',
                2 => 'UPCOMING',
                3 => 'INPLAY',
                4 => 'SETTLED',
                5 => 'VOIDED',
                6 => 'REMOVED',
            ];

            foreach ($markets as $market) {
                $status = $market->status;
                if (!is_null($status) && isset($statusLookup[(int) $status])) {
                    $statusLabel = $statusLookup[(int) $status];
                } else {
                    if ($market->isLive) {
                        $statusLabel = 'INPLAY';
                    } elseif ($market->isPreBet) {
                        $statusLabel = 'UPCOMING';
                    } else {
                        $statusLabel = 'UNSETTLED';
                    }
                }

                fputcsv($file, [
                    $market->id,
                    $market->_id,
                    $market->eventName,
                    $market->exEventId,
                    $market->marketName,
                    $market->sportName,
                    $market->tournamentsName,
                    $market->type,
                    $statusLabel,
                    $market->marketTime,
                    $market->created_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
