<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Exception;
use App\Models\Event;

class EventController extends Controller
{
    public function index(Request $request)
    {
        // Cache filter options for 5 minutes using raw DB queries
        $sports = Cache::remember('events.sports', 300, function () {
            return DB::table('events')
                ->select('sportId')
                ->distinct()
                ->orderBy('sportId')
                ->pluck('sportId');
        });

        // Get tournaments grouped by sport
        $tournaments = Cache::remember('events.tournaments', 300, function () {
            return DB::table('events')
                ->select('tournamentsId', 'tournamentsName', 'sportId')
                ->distinct()
                ->orderBy('tournamentsName')
                ->get();
        });

        // Get all tournaments grouped by sport for JavaScript filtering
        $tournamentsBySport = Cache::remember('events.tournaments_by_sport', 300, function () {
            return DB::table('events')
                ->select('tournamentsId', 'tournamentsName', 'sportId')
                ->distinct()
                ->orderBy('tournamentsName')
                ->get()
                ->groupBy('sportId');
        });

        $selectColumns = [
            'id',
            'eventId',
            'exEventId',
            'sportId',
            'tournamentsId',
            'tournamentsName',
            'eventName',
            'highlight',
            'quicklink',
            'popular',
            'IsSettle',
            'IsVoid',
            'IsUnsettle',
            'dataSwitch',
            'isRecentlyAdded',
            'marketTime',
            'createdAt'
        ];
        $selectList = implode(', ', array_map([$this, 'quoteColumn'], $selectColumns));
        $selectList .= ', ' . $this->getMatchOddsStatusSelect();

        $isRecentlyAdded = $request->boolean('recently_added');

        $defaultDateFilters = $this->getDefaultEventDateConditions($request);

        $filterSql = $this->buildEventFilterSql($request, $defaultDateFilters);
        $whereSql = !empty($filterSql['conditions'])
            ? ' WHERE ' . implode(' AND ', $filterSql['conditions'])
            : '';

        // Get total count for pagination
        $countSql = "SELECT COUNT(*) AS total FROM events{$whereSql}";
        $totalCountResult = DB::selectOne($countSql, $filterSql['bindings']);
        $totalCount = $totalCountResult ? (int) $totalCountResult->total : 0;

        $statusSummary = $this->fetchEventStatusSummary($whereSql, $filterSql['bindings']);
        
        // Apply pagination manually for better performance
        $page = $request->get('page', 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        // Get paginated results using raw query
        $orderDirection = $isRecentlyAdded ? 'desc' : 'asc';

        $dataSql = sprintf(
            'SELECT %s FROM events%s ORDER BY %s %s, %s %s LIMIT ? OFFSET ?',
            $selectList,
            $whereSql,
            $this->quoteColumn('marketTime'),
            strtoupper($orderDirection),
            $this->quoteColumn('id'),
            strtoupper($orderDirection)
        );

        $dataBindings = array_merge($filterSql['bindings'], [$perPage, $offset]);
        $events = collect(DB::select($dataSql, $dataBindings));

        // Create pagination object manually
        $paginatedEvents = new \Illuminate\Pagination\LengthAwarePaginator(
            $events,
            $totalCount,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );

        $paginatedEvents->appends($request->query());

        // Get sport configuration
        $sportConfig = config('sports.sports');
        
        return view('events.index', [
            'paginatedEvents' => $paginatedEvents,
            'sports' => $sports,
            'tournaments' => $tournaments,
            'sportConfig' => $sportConfig,
            'statusOptions' => $this->getEventStatusMap(),
            'tournamentsBySport' => $tournamentsBySport,
            'pageTitle' => 'Event List',
            'pageHeading' => 'Event List',
            'pageSubheading' => 'Events for today and tomorrow are shown here.',
            'statusSummary' => $statusSummary,
        ]);
    }

    public function all(Request $request)
    {
        $sports = Cache::remember('events.sports', 300, function () {
            return DB::table('events')
                ->select('sportId')
                ->distinct()
                ->orderBy('sportId')
                ->pluck('sportId');
        });

        $tournaments = Cache::remember('events.tournaments', 300, function () {
            return DB::table('events')
                ->select('tournamentsId', 'tournamentsName', 'sportId')
                ->distinct()
                ->orderBy('tournamentsName')
                ->get();
        });

        $tournamentsBySport = Cache::remember('events.tournaments_by_sport', 300, function () {
            return DB::table('events')
                ->select('tournamentsId', 'tournamentsName', 'sportId')
                ->distinct()
                ->orderBy('tournamentsName')
                ->get()
                ->groupBy('sportId');
        });

        $selectColumns = [
            'id',
            'eventId',
            'exEventId',
            'sportId',
            'tournamentsId',
            'tournamentsName',
            'eventName',
            'highlight',
            'quicklink',
            'popular',
            'IsSettle',
            'IsVoid',
            'IsUnsettle',
            'dataSwitch',
            'isRecentlyAdded',
            'marketTime',
            'createdAt'
        ];
        $selectList = implode(', ', array_map([$this, 'quoteColumn'], $selectColumns));
        $selectList .= ', ' . $this->getMatchOddsStatusSelect();

        $defaultDateFilters = ['conditions' => [], 'bindings' => []];
        $filterSql = $this->buildEventFilterSql($request, $defaultDateFilters);
        $whereSql = !empty($filterSql['conditions'])
            ? ' WHERE ' . implode(' AND ', $filterSql['conditions'])
            : '';

        $countSql = "SELECT COUNT(*) AS total FROM events{$whereSql}";
        $totalCountResult = DB::selectOne($countSql, $filterSql['bindings']);
        $totalCount = $totalCountResult ? (int) $totalCountResult->total : 0;

        $page = $request->get('page', 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $dataSql = sprintf(
            'SELECT %s FROM events%s ORDER BY %s DESC, %s DESC LIMIT ? OFFSET ?',
            $selectList,
            $whereSql,
            $this->quoteColumn('marketTime'),
            $this->quoteColumn('id')
        );

        $dataBindings = array_merge($filterSql['bindings'], [$perPage, $offset]);
        $events = collect(DB::select($dataSql, $dataBindings));

        $paginatedEvents = new \Illuminate\Pagination\LengthAwarePaginator(
            $events,
            $totalCount,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );

        $paginatedEvents->appends($request->query());

        $sportConfig = config('sports.sports');
        $statusSummary = $this->fetchEventStatusSummary($whereSql, $filterSql['bindings']);

        return view('events.all', [
            'paginatedEvents' => $paginatedEvents,
            'sports' => $sports,
            'tournaments' => $tournaments,
            'sportConfig' => $sportConfig,
            'statusOptions' => $this->getEventStatusMap(),
            'tournamentsBySport' => $tournamentsBySport,
            'pageTitle' => 'All Events List',
            'pageHeading' => 'All Events List',
            'pageSubheading' => 'Browse every scheduled event without date limits',
            'statusSummary' => $statusSummary,
        ]);
    }

    /**
     * Show individual event details
     */
    public function show($id)
    {
        $event = DB::table('events')
            ->select([
                'id',
                '_id',
                'eventId',
                'exEventId',
                'sportId',
                'tournamentsId',
                'tournamentsName',
                'eventName',
                'highlight',
                'quicklink',
                'popular',
                'IsSettle',
                'IsVoid',
                'IsUnsettle',
                'dataSwitch',
                'createdAt',
                'updated_at',
                'created_at'
            ])
            ->where('id', $id)
            ->first();

        if (!$event) {
            abort(404, 'Event not found');
        }

        // Get sport configuration
        $sportConfig = config('sports.sports');
        
        return view('events.show', compact('event', 'sportConfig'));
    }

    /**
     * Apply filters with optimized raw DB queries
     */
    private function buildEventFilterSql(Request $request, array $additionalConditions = []): array
    {
        $conditions = [];
        $bindings = [];

        if ($request->filled('search')) {
            $conditions[] = '(' . $this->quoteColumn('eventName') . ' ILIKE ? OR ' . $this->quoteColumn('tournamentsName') . ' ILIKE ?)';
            $bindings[] = $request->search . '%';
            $bindings[] = $request->search . '%';
        }

        if ($request->filled('sport')) {
            $conditions[] = $this->quoteColumn('sportId') . ' = ?';
            $bindings[] = $request->sport;
        }

        if ($request->filled('tournament')) {
            $conditions[] = $this->quoteColumn('tournamentsId') . ' = ?';
            $bindings[] = $request->tournament;
        }

        if ($request->filled('status')) {
            $statusCondition = $this->mapEventStatusCondition($request->status);
            if ($statusCondition) {
                $conditions[] = $statusCondition['sql'];
                $bindings = array_merge($bindings, $statusCondition['bindings']);
            }
        }

        if ($request->filled('highlight')) {
            $conditions[] = $this->quoteColumn('highlight') . ' = ?';
            $bindings[] = $request->boolean('highlight');
        }

        if ($request->filled('popular')) {
            $conditions[] = $this->quoteColumn('popular') . ' = ?';
            $bindings[] = $request->boolean('popular');
        }

        $isRecentlyAdded = $request->boolean('recently_added');
        if ($isRecentlyAdded) {
            $conditions[] = $this->quoteColumn('isRecentlyAdded') . ' = ?';
            $bindings[] = true;
        }

        $dateFilters = $this->resolveEventDateFilters($request, $isRecentlyAdded);
        if ($dateFilters['start'] && $dateFilters['end']) {
            $conditions[] = "{$dateFilters['column']} BETWEEN ? AND ?";
            $bindings[] = $dateFilters['start'];
            $bindings[] = $dateFilters['end'];
        } elseif ($dateFilters['start']) {
            $conditions[] = "{$dateFilters['column']} >= ?";
            $bindings[] = $dateFilters['start'];
        } elseif ($dateFilters['end']) {
            $conditions[] = "{$dateFilters['column']} <= ?";
            $bindings[] = $dateFilters['end'];
        }

        if (!empty($additionalConditions['conditions'])) {
            $conditions = array_merge($conditions, $additionalConditions['conditions']);
            $bindings = array_merge($bindings, $additionalConditions['bindings'] ?? []);
        }

        return [
            'conditions' => $conditions,
            'bindings' => $bindings,
        ];
    }

    private function getDefaultEventDateConditions(Request $request): array
    {
        $hasCustomDateFilter =
            $request->boolean('event_date_from_enabled') ||
            $request->boolean('event_date_to_enabled') ||
            $request->boolean('time_from_enabled') ||
            $request->boolean('time_to_enabled');

        if ($hasCustomDateFilter) {
            return ['conditions' => [], 'bindings' => []];
        }

        $timezone = config('app.timezone', 'UTC');
        $startDate = Carbon::now($timezone)->startOfDay();
        $endDate = Carbon::now($timezone)->addDay()->endOfDay();

        $column = $this->quoteColumn('marketTime');

        return [
            'conditions' => ["{$column} BETWEEN ? AND ?"],
            'bindings' => [
                $startDate->format('Y-m-d H:i:s'),
                $endDate->format('Y-m-d H:i:s'),
            ],
        ];
    }

    private function mapEventStatusCondition(string $status): ?array
    {
        $statusValue = (int) $status;
        $statusMap = $this->getEventStatusMap();

        if (!array_key_exists($statusValue, $statusMap)) {
            return null;
        }

        $eventExEventColumn = $this->quoteColumn('exEventId');

        $sql = 'EXISTS (
            SELECT 1
            FROM market_lists ml_status
            WHERE ml_status."exEventId" = ' . $eventExEventColumn . '
              AND ml_status."type" = ?
              AND ml_status."status" = ?
        )';

        return [
            'sql' => $sql,
            'bindings' => ['match_odds', $statusValue],
        ];
    }

    private function getEventStatusMap(): array
    {
        return [
            1 => 'Unsettled',
            2 => 'Upcoming',
            3 => 'In Play',
            4 => 'Settled',
            5 => 'Voided',
            6 => 'Removed',
        ];
    }

    private function getMatchOddsStatusSelect(): string
    {
        return $this->getEffectiveEventStatusExpression() . ' AS "matchOddsStatus"';
    }

    private function getMatchOddsStatusExpression(?string $tableAlias = null): string
    {
        if ($tableAlias) {
            $column = '"' . str_replace('"', '""', $tableAlias) . '"."exEventId"';
        } else {
            $column = $this->quoteColumn('exEventId');
        }

        return '(SELECT ml."status"
            FROM market_lists ml
            WHERE ml."type" = \'match_odds\'
              AND ml."exEventId" = ' . $column . '
            ORDER BY ml."id" DESC
            LIMIT 1)';
    }

    private function getEffectiveEventStatusExpression(?string $tableAlias = null): string
    {
        $matchStatusExpr = $this->getMatchOddsStatusExpression($tableAlias);

        $column = function (string $columnName) use ($tableAlias): string {
            if ($tableAlias) {
                return '"' . str_replace('"', '""', $tableAlias) . '"."' . str_replace('"', '""', $columnName) . '"';
            }
            return $this->quoteColumn($columnName);
        };

        return sprintf(
            'COALESCE(
                %s,
                CASE
                    WHEN %s = 1 THEN 4
                    WHEN %s = 1 THEN 5
                    WHEN %s = 1 THEN 1
                    ELSE NULL
                END
            )',
            $matchStatusExpr,
            $column('IsSettle'),
            $column('IsVoid'),
            $column('IsUnsettle')
        );
    }

    private function fetchEventStatusSummary(string $whereSql, array $bindings): array
    {
        $statusExpr = $this->getEffectiveEventStatusExpression();

        $statusSql = sprintf(
            'SELECT match_status, COUNT(*) AS total FROM (
                SELECT %s AS match_status FROM events%s
            ) AS status_source
            GROUP BY match_status',
            $statusExpr,
            $whereSql
        );

        $rows = DB::select($statusSql, $bindings);

        $counts = [];
        foreach ($rows as $row) {
            if ($row->match_status !== null) {
                $counts[(int) $row->match_status] = (int) $row->total;
            }
        }

        $summary = [];
        foreach ($this->getEventStatusMap() as $statusId => $label) {
            $summary[$statusId] = $counts[$statusId] ?? 0;
        }

        return $summary;
    }

    private function resolveEventDateFilters(Request $request, bool $isRecentlyAdded): array
    {
        $timezone = config('app.timezone', 'UTC');
        $dateFromEnabled = $request->boolean('event_date_from_enabled');
        $dateToEnabled = $request->boolean('event_date_to_enabled');
        $timeFromEnabled = $request->boolean('time_from_enabled');
        $timeToEnabled = $request->boolean('time_to_enabled');

        $timeFormats = ['h:i:s A', 'h:i A', 'H:i:s', 'H:i'];
        $startDateTime = null;
        $endDateTime = null;

        if ($dateFromEnabled && $request->filled('event_date_from')) {
            $timeComponent = '00:00:00';
            if ($timeFromEnabled && $request->filled('time_from')) {
                foreach ($timeFormats as $format) {
                    try {
                        $timeComponent = Carbon::createFromFormat($format, $request->time_from)->format('H:i:s');
                        break;
                    } catch (Exception $e) {
                        continue;
                    }
                }
            }

            try {
                $startDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $request->event_date_from . ' ' . $timeComponent, $timezone);
            } catch (Exception $e) {
                $startDateTime = Carbon::now($timezone)->startOfDay();
            }
        }

        if ($dateToEnabled && $request->filled('event_date_to')) {
            $timeComponent = '23:59:59';
            if ($timeToEnabled && $request->filled('time_to')) {
                foreach ($timeFormats as $format) {
                    try {
                        $timeComponent = Carbon::createFromFormat($format, $request->time_to)->format('H:i:s');
                        break;
                    } catch (Exception $e) {
                        continue;
                    }
                }
            }

            try {
                $endDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $request->event_date_to . ' ' . $timeComponent, $timezone);
            } catch (Exception $e) {
                $endDateTime = Carbon::now($timezone)->endOfDay();
            }
        }

        if ($startDateTime && $endDateTime && $endDateTime->lt($startDateTime)) {
            $endDateTime = $startDateTime->copy()->endOfDay();
        }

        return [
            'start' => $startDateTime ? $startDateTime->format('Y-m-d H:i:s') : null,
            'end' => $endDateTime ? $endDateTime->format('Y-m-d H:i:s') : null,
            'column' => $this->quoteColumn($isRecentlyAdded ? 'createdAt' : 'marketTime'),
        ];
    }

    private function quoteColumn(string $column): string
    {
        return '"' . str_replace('"', '""', $column) . '"';
    }

    /**
     * Fast bulk operations for large datasets
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'event_ids' => 'required|array',
            'event_ids.*' => 'integer|exists:events,id',
            'action' => 'required|in:settle,void,highlight,unhighlight'
        ]);

        $eventIds = $request->event_ids;
        $action = $request->action;

        // Use bulk updates for better performance
        switch ($action) {
            case 'settle':
                DB::table('events')
                    ->whereIn('id', $eventIds)
                    ->update([
                        'IsSettle' => 1,
                        'IsUnsettle' => 0,
                        'IsVoid' => 0,
                        'updated_at' => now()
                    ]);
                break;

            case 'void':
                DB::table('events')
                    ->whereIn('id', $eventIds)
                    ->update([
                        'IsVoid' => 1,
                        'IsSettle' => 0,
                        'IsUnsettle' => 0,
                        'updated_at' => now()
                    ]);
                break;

            case 'highlight':
                DB::table('events')
                    ->whereIn('id', $eventIds)
                    ->update(['highlight' => 1, 'updated_at' => now()]);
                break;

            case 'unhighlight':
                DB::table('events')
                    ->whereIn('id', $eventIds)
                    ->update(['highlight' => 0, 'updated_at' => now()]);
                break;
        }

        // Clear cache
        Cache::forget('events.sports');
        Cache::forget('events.tournaments');
        Cache::forget('events.tournaments_by_sport');

        return response()->json([
            'success' => true,
            'message' => 'Events updated successfully',
            'updated_count' => count($eventIds)
        ]);
    }

    /**
     * Fast statistics query
     */
    public function getStats()
    {
        return Cache::remember('events.stats', 60, function () {
            return DB::table('events')
                ->selectRaw('
                    COUNT(*) as total_events,
                    SUM("IsSettle") as settled_count,
                    SUM("IsUnsettle") as unsettled_count,
                    SUM("IsVoid") as void_count,
                    SUM(CASE WHEN "highlight" = true THEN 1 ELSE 0 END) as highlighted_count,
                    SUM(CASE WHEN "popular" = true THEN 1 ELSE 0 END) as popular_count
                ')
                ->first();
        });
    }

    /**
     * Optimized search with suggestions using raw DB queries
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:50'
        ]);

        $query = $request->q;

        // Use raw DB query for fast response
        $events = DB::table('events')
            ->select('id', 'eventId', 'eventName', 'tournamentsName')
            ->where(function ($q) use ($query) {
                $q->where('eventName', 'like', $query . '%')
                  ->orWhere('tournamentsName', 'like', $query . '%');
            })
            ->limit(10)
            ->get();

        return response()->json($events);
    }

    /**
     * Export events to CSV with filters applied
     */
    public function export(Request $request)
    {
        $selectColumns = [
            'id',
            'eventId',
            'sportId',
            'tournamentsId',
            'tournamentsName',
            'eventName',
            'highlight',
            'quicklink',
            'popular',
            'IsSettle',
            'IsVoid',
            'IsUnsettle',
            'dataSwitch',
            'isRecentlyAdded',
            'marketTime',
            'createdAt'
        ];
        $selectList = implode(', ', array_map([$this, 'quoteColumn'], $selectColumns));
        $selectList .= ', ' . $this->getMatchOddsStatusSelect();

        $filterSql = $this->buildEventFilterSql($request, ['conditions' => [], 'bindings' => []]);
        $whereSql = !empty($filterSql['conditions'])
            ? ' WHERE ' . implode(' AND ', $filterSql['conditions'])
            : '';

        $dataSql = sprintf(
            'SELECT %s FROM events%s ORDER BY %s DESC, %s DESC',
            $selectList,
            $whereSql,
            $this->quoteColumn('marketTime'),
            $this->quoteColumn('id')
        );

        $events = collect(DB::select($dataSql, $filterSql['bindings']));

        // Get sport configuration for display
        $sportConfig = config('sports.sports');
        $statusMap = $this->getEventStatusMap();

        // Prepare CSV data
        $filename = 'events_export_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($events, $sportConfig, $statusMap) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID',
                'Event ID',
                'Sport',
                'Tournament ID',
                'Tournament Name',
                'Event Name',
                'Highlight',
                'Quicklink',
                'Popular',
                'Status',
                'Data Switch',
                'Event Time',
                'Created At'
            ]);

            // Add data rows
            foreach ($events as $event) {
                $statusValue = isset($event->matchOddsStatus) ? (int) $event->matchOddsStatus : null;
                $status = $statusValue && isset($statusMap[$statusValue])
                    ? $statusMap[$statusValue]
                    : 'Unknown';

                // Get sport name from config
                $sportName = $sportConfig[$event->sportId] ?? $event->sportId;

                fputcsv($file, [
                    $event->id,
                    $event->eventId,
                    $sportName,
                    $event->tournamentsId,
                    $event->tournamentsName,
                    $event->eventName,
                    $event->highlight ? 'Yes' : 'No',
                    $event->quicklink ? 'Yes' : 'No',
                    $event->popular ? 'Yes' : 'No',
                    $status,
                    $event->dataSwitch ? 'On' : 'Off',
                    $event->marketTime,
                    $event->createdAt
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Update missing market time from market_lists
     */
    public function updateMarketTime(Request $request, Event $event)
    {
        if (!$event->exEventId) {
            return response()->json([
                'success' => false,
                'message' => 'Event does not have an external ID to match.'
            ], 422);
        }

        $marketTime = DB::table('market_lists')
            ->where('exEventId', $event->exEventId)
            ->orderBy('marketTime', 'asc')
            ->value('marketTime');

        if (!$marketTime) {
            return response()->json([
                'success' => false,
                'message' => 'No matching market time found for this event.'
            ], 404);
        }

        DB::table('events')
            ->where('id', $event->id)
            ->update([
                'marketTime' => $marketTime,
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'marketTime' => Carbon::parse($marketTime)->timezone(config('app.timezone', 'UTC'))->format('M d, Y h:i A'),
        ]);
    }
}
