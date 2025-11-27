<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiskController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->buildFilters($request);
        
        // Get pending markets query (is_done = false or null)
        $pendingQuery = $this->buildMarketQuery([4, 5], $filters, false);
        
        // Get done markets query (is_done = true)
        $doneQuery = $this->buildMarketQuery([4, 5], $filters, true);
        
        // Apply status filter if selected (pending/done)
        $statusFilter = $request->input('risk_status'); // 'pending' or 'done'
        
        $allMarkets = collect();
        
        if (!$statusFilter || $statusFilter === 'pending') {
            $pendingMarkets = (clone $pendingQuery)->get();
            foreach ($pendingMarkets as $market) {
                $market->risk_status = 'pending';
                $allMarkets->push($market);
            }
        }
        
        if (!$statusFilter || $statusFilter === 'done') {
            $doneMarkets = (clone $doneQuery)->get();
            foreach ($doneMarkets as $market) {
                $market->risk_status = 'done';
                $allMarkets->push($market);
            }
        }
        
        // Group by risk_status first (pending on top, done at bottom)
        $grouped = $allMarkets->groupBy('risk_status');
        $pendingMarkets = $grouped->get('pending', collect());
        $doneMarkets = $grouped->get('done', collect());
        
        // Define sport priority order
        $sportPriority = [
            'Basketball' => 1,
            'Boxing' => 2,
            'Cricket' => 3,
            'Soccer' => 4,
            'Tennis' => 5,
        ];
        
        // Sort each group: first by sport priority, then by close time (newest first)
        $pendingMarkets = $pendingMarkets->sortBy(function ($market) use ($sportPriority) {
            $sportName = $market->sportName ?? '';
            $sportOrder = $sportPriority[$sportName] ?? 999; // Higher number for non-priority sports
            $timeField = !empty($market->completeTime) ? $market->completeTime : $market->marketTime;
            $timeValue = $timeField ? strtotime($timeField) : 0;
            // Return array: first sort by sport order (ascending), then by time (descending = negative)
            return [$sportOrder, -$timeValue];
        });
        
        $doneMarkets = $doneMarkets->sortBy(function ($market) use ($sportPriority) {
            $sportName = $market->sportName ?? '';
            $sportOrder = $sportPriority[$sportName] ?? 999; // Higher number for non-priority sports
            $timeField = !empty($market->completeTime) ? $market->completeTime : $market->marketTime;
            $timeValue = $timeField ? strtotime($timeField) : 0;
            // Return array: first sort by sport order (ascending), then by time (descending = negative)
            return [$sportOrder, -$timeValue];
        });
        
        // Combine: pending first, then done
        $sortedMarkets = $pendingMarkets->concat($doneMarkets);
        
        // Paginate manually
        $page = $request->get('page', 1);
        $perPage = 20;
        $total = $sortedMarkets->count();
        $offset = ($page - 1) * $perPage;
        $items = $sortedMarkets->slice($offset, $perPage)->values();
        
        // Build summary from both queries
        $pendingSummary = $this->buildSummary($pendingQuery);
        $doneSummary = $this->buildSummary($doneQuery);
        $summary = [
            'pending' => $pendingSummary,
            'done' => $doneSummary,
            'total' => $pendingSummary['total'] + $doneSummary['total'],
        ];
        
        // Create paginator manually
        $markets = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );
        $markets->appends($request->query());

        return view('risk.index', [
            'markets' => $markets,
            'statusFilter' => [4, 5],
            'summary' => $summary,
            'filters' => $filters,
            'sports' => $this->getSportsList(),
            'tournamentsBySport' => $this->getTournamentsBySport(),
            'riskStatusFilter' => $statusFilter,
        ]);
    }

    public function pending(Request $request)
    {
        return redirect()->route('risk.index', array_merge($request->query(), ['risk_status' => 'pending']));
    }

    public function done(Request $request)
    {
        return redirect()->route('risk.index', array_merge($request->query(), ['risk_status' => 'done']));
    }

    private function buildMarketQuery(array $statuses, array $filters, bool $onlyDone)
    {
        $query = DB::table('market_lists')
            ->select([
                'market_lists.id',
                'market_lists.marketName',
                'market_lists.eventName',
                'market_lists.tournamentsName',
                'market_lists.sportName',
                'market_lists.status',
                'market_lists.winnerType',
                'market_lists.selectionName',
                'market_lists.marketTime',
                'market_lists.labels',
                'market_lists.is_done',
                'market_lists.name',
                'market_lists.remark',
                'events.completeTime',
                'market_lists.created_at',
            ])
            ->leftJoin('events', 'events.exEventId', '=', 'market_lists.exEventId')
            ->whereIn('market_lists.status', $statuses)
            ->when($onlyDone, function ($q) {
                $q->where('market_lists.is_done', true);
            }, function ($q) {
                $q->where(function ($inner) {
                    $inner->whereNull('market_lists.is_done')->orWhere('market_lists.is_done', false);
                });
            });

        if ($filters['sport']) {
            $query->where('market_lists.sportName', $filters['sport']);
        }

        if ($filters['search']) {
            $term = '%' . $filters['search'] . '%';
            $query->where(function ($q) use ($term) {
                $q->where('market_lists.marketName', 'ILIKE', $term)
                    ->orWhere('market_lists.eventName', 'ILIKE', $term);
            });
        }

        if (!empty($filters['labels'])) {
            foreach ($filters['labels'] as $labelKey) {
                $query->whereRaw("(market_lists.labels ->> ?)::boolean = true", [$labelKey]);
            }
        }

        if ($filters['status']) {
            $query->where('market_lists.status', $filters['status']);
        }

        // Handle date and time filtering for completeTime
        $timezone = config('app.timezone', 'UTC');
        $timeFormats = ['h:i:s A', 'h:i A', 'H:i:s', 'H:i'];
        $startDateTime = null;
        $endDateTime = null;

        if ($filters['date_from']) {
            $parsedDate = $this->parseFilterDate($filters['date_from'], $timezone);
            if ($parsedDate) {
                $baseDate = $parsedDate->copy();
                $startDateTime = $baseDate->copy()->startOfDay();

                if ($filters['time_from']) {
                    $timeComponent = null;
                    foreach ($timeFormats as $format) {
                        try {
                            $timeComponent = \Carbon\Carbon::createFromFormat($format, $filters['time_from'], $timezone)->format('H:i:s');
                            break;
                        } catch (\Exception $e) {
                            continue;
                        }
                    }

                    if ($timeComponent) {
                        $startDateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $baseDate->format('Y-m-d') . ' ' . $timeComponent, $timezone);
                    }
                }
            }
        }

        if ($filters['date_to']) {
            $parsedDate = $this->parseFilterDate($filters['date_to'], $timezone);
            if ($parsedDate) {
                $baseDate = $parsedDate->copy();
                $endDateTime = $baseDate->copy()->endOfDay();

                if ($filters['time_to']) {
                    $timeComponent = null;
                    foreach ($timeFormats as $format) {
                        try {
                            $timeComponent = \Carbon\Carbon::createFromFormat($format, $filters['time_to'], $timezone)->format('H:i:s');
                            break;
                        } catch (\Exception $e) {
                            continue;
                        }
                    }

                    if ($timeComponent) {
                        $endDateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $baseDate->format('Y-m-d') . ' ' . $timeComponent, $timezone);
                    }
                }
            }
        }

        if ($startDateTime && $endDateTime && $endDateTime->lt($startDateTime)) {
            $endDateTime = $startDateTime->copy()->endOfDay();
        }

        if ($startDateTime && $endDateTime) {
            $query->whereBetween('events.completeTime', [
                $startDateTime->format('Y-m-d H:i:s'),
                $endDateTime->format('Y-m-d H:i:s'),
            ]);
        } elseif ($startDateTime) {
            $query->where('events.completeTime', '>=', $startDateTime->format('Y-m-d H:i:s'));
        } elseif ($endDateTime) {
            $query->where('events.completeTime', '<=', $endDateTime->format('Y-m-d H:i:s'));
        } elseif ($filters['date_from'] && !$startDateTime) {
            // Fallback to date-only filtering if date parsing failed
            $query->whereDate('events.completeTime', '>=', $filters['date_from']);
        } elseif ($filters['date_to'] && !$endDateTime) {
            // Fallback to date-only filtering if date parsing failed
            $query->whereDate('events.completeTime', '<=', $filters['date_to']);
        }

        // Filter for recently added (markets closed in the past 30 minutes)
        if (!empty($filters['recently_added'])) {
            $now = \Carbon\Carbon::now();
            $thirtyMinutesAgo = $now->copy()->subMinutes(30);
            
            $query->where(function ($q) use ($now, $thirtyMinutesAgo) {
                // Check completeTime first, fallback to marketTime
                $q->where(function ($subQ) use ($now, $thirtyMinutesAgo) {
                    $subQ->whereNotNull('events.completeTime')
                        ->where('events.completeTime', '>=', $thirtyMinutesAgo->format('Y-m-d H:i:s'))
                        ->where('events.completeTime', '<=', $now->format('Y-m-d H:i:s'));
                })->orWhere(function ($subQ) use ($now, $thirtyMinutesAgo) {
                    $subQ->whereNull('events.completeTime')
                        ->whereNotNull('market_lists.marketTime')
                        ->where('market_lists.marketTime', '>=', $thirtyMinutesAgo->format('Y-m-d H:i:s'))
                        ->where('market_lists.marketTime', '<=', $now->format('Y-m-d H:i:s'));
                });
            });
        }

        return $query->orderByDesc('market_lists.marketTime');
    }

    private function parseFilterDate($dateStr, $timezone)
    {
        try {
            // Try DD/MM/YYYY format first
            if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $dateStr, $matches)) {
                return \Carbon\Carbon::createFromDate((int) $matches[3], (int) $matches[2], (int) $matches[1], $timezone);
            }
            // Try YYYY-MM-DD format
            return \Carbon\Carbon::parse($dateStr, $timezone);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function buildFilters(Request $request): array
    {
        $labelKeys = $this->getLabelKeys();
        $labelFilter = collect($request->input('labels', []))
            ->map(fn ($value) => strtolower((string) $value))
            ->filter(fn ($value) => in_array($value, $labelKeys, true))
            ->unique()
            ->values()
            ->all();

        return [
            'search' => $request->input('search'),
            'sport' => $request->input('sport'),
            'labels' => $labelFilter,
            'status' => $request->filled('status') && in_array((int) $request->input('status'), [4, 5], true)
                ? (int) $request->input('status')
                : null,
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'time_from' => $request->input('time_from'),
            'time_to' => $request->input('time_to'),
            'risk_status' => $request->input('risk_status'), // 'pending' or 'done'
            'recently_added' => $request->input('recently_added') == '1', // Recently added filter
        ];
    }

    public function updateLabels(Request $request, int $marketId)
    {
        $labels = $this->normalizeLabels($request->input('labels', []));

        DB::table('market_lists')
            ->where('id', $marketId)
            ->update([
                'labels' => json_encode($labels),
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'labels' => $labels,
        ]);
    }

    public function markDone(Request $request, int $marketId)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'chor_id' => ['required', 'string'],
            'remark' => ['required', 'string', 'max:2000'],
        ]);

        $market = DB::table('market_lists')->where('id', $marketId)->first();

        if (!$market) {
            abort(404);
        }

        $labels = $this->normalizeLabels(json_decode($market->labels ?? '{}', true));
        // Only first 4 labels are required: 4x, b2c, b2b, usdt
        $requiredLabelKeys = ['4x', 'b2c', 'b2b', 'usdt'];
        $allRequiredChecked = collect($requiredLabelKeys)->every(function($key) use ($labels) {
            return isset($labels[$key]) && (bool) $labels[$key] === true;
        });

        if (!$allRequiredChecked) {
            return response()->json([
                'success' => false,
                'message' => 'Please select all required labels (4X, B2C, B2B, USDT) before marking as done.',
            ], 422);
        }

        DB::table('market_lists')
            ->where('id', $marketId)
            ->update([
                'is_done' => true,
                'name' => $request->input('name'),
                'chor_id' => $request->input('chor_id'),
                'remark' => $request->input('remark'),
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Market marked as done.',
        ]);
    }

    private function normalizeLabels($labels): array
    {
        $default = collect($this->getLabelKeys())
            ->mapWithKeys(fn ($key) => [$key => false])
            ->toArray();

        if (!is_array($labels)) {
            $labels = [];
        }

        foreach ($default as $key => $value) {
            $default[$key] = (bool) ($labels[$key] ?? $value);
        }

        return $default;
    }

    private function getLabelKeys(): array
    {
        return array_keys(config('labels.labels', []));
    }

    private function buildSummary($query): array
    {
        return [
            'total' => (clone $query)->count(),
            'settled' => (clone $query)->where('market_lists.status', 4)->count(),
            'voided' => (clone $query)->where('market_lists.status', 5)->count(),
        ];
    }

    private function getSportsList(): array
    {
        return config('sports.sports', []);
    }

    private function getTournamentsBySport(): array
    {
        $rows = DB::table('market_lists')
            ->select('sportName', 'tournamentsName')
            ->whereNotNull('sportName')
            ->whereNotNull('tournamentsName')
            ->groupBy('sportName', 'tournamentsName')
            ->get();

        $map = [];
        $all = [];

        foreach ($rows as $row) {
            $sport = trim($row->sportName);
            $tournament = trim($row->tournamentsName);

            if ($sport === '' || $tournament === '') {
                continue;
            }

            $map[$sport][] = $tournament;
            $all[] = $tournament;
        }

        foreach ($map as $sport => $list) {
            $map[$sport] = array_values(array_unique($list));
            sort($map[$sport]);
        }

        $map['__all'] = array_values(array_unique($all));
        sort($map['__all']);

        return $map;
    }

    /**
     * Vol. Base Markets - Show markets with max totalMatched values (Optimized - Only Max Record Per Table)
     */
    public function volBaseMarkets(Request $request)
    {
        $filters = $this->buildVolBaseFilters($request);
        
        // Cache event table list for 5 minutes
        $eventIds = \Illuminate\Support\Facades\Cache::remember('vol_base_markets.event_tables', 300, function () {
            return \App\Models\MarketRate::getAvailableEventTables();
        });
        
        if (empty($eventIds)) {
            return view('risk.vol-base-markets', [
                'markets' => collect([]),
                'filters' => $filters,
                'sports' => $this->getSportsList(),
                'tournamentsBySport' => $this->getTournamentsBySport(),
            ]);
        }
        
        // Optimized query: Only show markets that have dynamic tables
        // Process tables in batches to avoid memory issues
        $tablesWithData = \Illuminate\Support\Facades\Cache::remember('vol_base_markets.existing_tables', 600, function () {
            return DB::select("
                SELECT table_name 
                FROM information_schema.tables 
                WHERE table_schema = 'public' 
                AND table_name LIKE 'market_rates_%'
                ORDER BY table_name
            ");
        });
        
        if (empty($tablesWithData)) {
            // No tables exist, return empty result
            return view('risk.vol-base-markets', [
                'markets' => new \Illuminate\Pagination\LengthAwarePaginator(collect([]), 0, 20, 1),
                'filters' => $filters,
                'sports' => $this->getSportsList(),
                'tournamentsBySport' => $this->getTournamentsBySport(),
            ]);
        }
        
        // Extract event IDs from table names
        $eventIdsWithTables = collect($tablesWithData)->map(function($table) {
            return str_replace('market_rates_', '', $table->table_name);
        })->toArray();
        
        // Process tables in batches to avoid memory issues (50 tables per batch)
        $batchSize = 50;
        $batches = array_chunk($tablesWithData, $batchSize);
        $allMarketVolumes = collect();
        
        foreach ($batches as $batch) {
            $unionParts = [];
            $pdo = DB::getPdo();
            
            foreach ($batch as $table) {
                $tableName = $table->table_name;
                $eventId = str_replace('market_rates_', '', $tableName);
                
                // Get max totalMatched per exMarketId from each table
                $unionParts[] = "SELECT 
                    " . $pdo->quote($eventId) . "::text as ex_event_id,
                    \"exMarketId\",
                    MAX(\"totalMatched\") as max_total_matched
                FROM \"{$tableName}\"
                WHERE \"exMarketId\" IS NOT NULL
                GROUP BY \"exMarketId\"";
            }
            
            // Process this batch
            if (!empty($unionParts)) {
                $unionSql = implode(' UNION ALL ', $unionParts);
                
                $batchResults = DB::select("
                    SELECT 
                        ex_event_id,
                        \"exMarketId\",
                        MAX(max_total_matched) as max_total_matched
                    FROM ({$unionSql}) as market_volumes
                    GROUP BY ex_event_id, \"exMarketId\"
                ");
                
                foreach ($batchResults as $result) {
                    $allMarketVolumes->push((object) [
                        'ex_event_id' => $result->ex_event_id,
                        'exMarketId' => $result->exMarketId,
                        'max_total_matched' => (float) $result->max_total_matched,
                    ]);
                }
            }
        }
        
        // If no volumes found, return empty
        if ($allMarketVolumes->isEmpty()) {
            return view('risk.vol-base-markets', [
                'markets' => new \Illuminate\Pagination\LengthAwarePaginator(collect([]), 0, 20, 1),
                'filters' => $filters,
                'sports' => $this->getSportsList(),
                'tournamentsBySport' => $this->getTournamentsBySport(),
            ]);
        }
        
        // Get unique market IDs with their max volumes
        // Filter out markets with 0 or null volume
        $marketVolumesMap = $allMarketVolumes
            ->filter(function($item) {
                return $item->max_total_matched > 0;
            })
            ->groupBy(function($item) {
                return $item->ex_event_id . '|' . $item->exMarketId;
            })
            ->map(function($group) {
                return $group->max('max_total_matched');
            });
        
        // Build query with market_lists - filter to only markets that have volumes
        $marketKeys = $marketVolumesMap->keys()->toArray();
        $eventMarketPairs = collect($marketKeys)->map(function($key) {
            [$eventId, $marketId] = explode('|', $key);
            return ['eventId' => $eventId, 'marketId' => $marketId];
        });
        
        // Extract unique event IDs and market IDs
        $eventIds = $eventMarketPairs->pluck('eventId')->unique()->toArray();
        $marketIds = $eventMarketPairs->pluck('marketId')->unique()->toArray();
        
        // Build query - only get markets that exist in our volumes map
        $query = DB::table('market_lists')
            ->whereIn('market_lists.exEventId', $eventIds)
            ->whereIn('market_lists.exMarketId', $marketIds)
            ->whereNotNull('market_lists.exMarketId')
            ->whereNotNull('market_lists.marketName')
            ->select([
                'market_lists.id',
                'market_lists.exMarketId',
                'market_lists.marketName',
                'market_lists.eventName',
                'market_lists.exEventId',
                'market_lists.tournamentsName',
                'market_lists.sportName',
                'market_lists.status',
                'market_lists.marketTime',
                'market_lists.winnerType',
                'market_lists.selectionName',
            ])
            ->distinct();
        
        // Apply filters
        if ($filters['sport']) {
            $query->where('market_lists.sportName', $filters['sport']);
        }
        
        if ($filters['search']) {
            $searchTerm = '%' . $filters['search'] . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('market_lists.marketName', 'ILIKE', $searchTerm)
                  ->orWhere('market_lists.eventName', 'ILIKE', $searchTerm);
            });
        }
        
        // Date range filter
        if ($filters['date_from'] || $filters['date_to']) {
            $timezone = config('app.timezone', 'UTC');
            
            if ($filters['date_from']) {
                try {
                    $parsedDate = $this->parseFilterDate($filters['date_from'], $timezone);
                    if ($parsedDate) {
                        $query->where('market_lists.marketTime', '>=', $parsedDate->copy()->startOfDay()->format('Y-m-d H:i:s'));
                    }
                } catch (\Exception $e) {
                    // Ignore invalid date
                }
            }
            
            if ($filters['date_to']) {
                try {
                    $parsedDate = $this->parseFilterDate($filters['date_to'], $timezone);
                    if ($parsedDate) {
                        $query->where('market_lists.marketTime', '<=', $parsedDate->copy()->endOfDay()->format('Y-m-d H:i:s'));
                    }
                } catch (\Exception $e) {
                    // Ignore invalid date
                }
            }
        }
        
        // Get results and add max_total_matched, filter to only include markets with volumes > 0
        $markets = $query->get()->map(function($market) use ($marketVolumesMap) {
            $key = $market->exEventId . '|' . $market->exMarketId;
            $volume = $marketVolumesMap->get($key);
            if ($volume !== null && $volume > 0) {
                $market->max_total_matched = $volume;
                return $market;
            }
            return null;
        })->filter(); // Remove null values (markets without volumes or with 0 volume)
        
        // Filter out markets with 0 volume
        $markets = $markets->filter(function($market) {
            return isset($market->max_total_matched) && $market->max_total_matched > 0;
        });
        
        // Apply volume filter after getting data
        if ($filters['volume_value'] && $filters['volume_operator']) {
            $volumeValue = (float) $filters['volume_value'];
            $markets = $markets->filter(function($market) use ($volumeValue, $filters) {
                if ($filters['volume_operator'] === 'greater_than') {
                    return $market->max_total_matched > $volumeValue;
                } elseif ($filters['volume_operator'] === 'less_than') {
                    return $market->max_total_matched < $volumeValue;
                }
                return true;
            });
        }
        
        // Order by max_total_matched descending, then by marketTime
        $markets = $markets->sortByDesc('max_total_matched')
            ->sortByDesc('marketTime')
            ->values();
        
        // Paginate manually
        $page = $request->get('page', 1);
        $perPage = 20;
        $total = $markets->count();
        $offset = ($page - 1) * $perPage;
        $items = $markets->slice($offset, $perPage)->values();
        
        $paginatedMarkets = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );
        $paginatedMarkets->appends($request->query());
        
        return view('risk.vol-base-markets', [
            'markets' => $paginatedMarkets,
            'filters' => $filters,
            'sports' => $this->getSportsList(),
            'tournamentsBySport' => $this->getTournamentsBySport(),
        ]);
        
        // Apply filters at database level
        if ($filters['sport']) {
            $query->where('market_lists.sportName', $filters['sport']);
        }
        
        if ($filters['search']) {
            $searchTerm = '%' . $filters['search'] . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('market_lists.marketName', 'ILIKE', $searchTerm)
                  ->orWhere('market_lists.eventName', 'ILIKE', $searchTerm);
            });
        }
        
        // Volume filter
        if ($filters['volume_value'] && $filters['volume_operator']) {
            $volumeValue = (float) $filters['volume_value'];
            if ($filters['volume_operator'] === 'greater_than') {
                $query->where('market_max.max_total_matched', '>', $volumeValue);
            } elseif ($filters['volume_operator'] === 'less_than') {
                $query->where('market_max.max_total_matched', '<', $volumeValue);
            }
        }
        
        // Date range filter
        if ($filters['date_from'] || $filters['date_to']) {
            $timezone = config('app.timezone', 'UTC');
            
            if ($filters['date_from']) {
                try {
                    $parsedDate = $this->parseFilterDate($filters['date_from'], $timezone);
                    if ($parsedDate) {
                        $query->where('market_lists.marketTime', '>=', $parsedDate->copy()->startOfDay()->format('Y-m-d H:i:s'));
                    }
                } catch (\Exception $e) {
                    // Ignore invalid date
                }
            }
            
            if ($filters['date_to']) {
                try {
                    $parsedDate = $this->parseFilterDate($filters['date_to'], $timezone);
                    if ($parsedDate) {
                        $query->where('market_lists.marketTime', '<=', $parsedDate->copy()->endOfDay()->format('Y-m-d H:i:s'));
                    }
                } catch (\Exception $e) {
                    // Ignore invalid date
                }
            }
        }
        
        // Order by max_total_matched descending, then by marketTime
        $query->orderByDesc('market_max.max_total_matched')
              ->orderByDesc('market_lists.marketTime');
        
        // Paginate
        $perPage = 20;
        $markets = $query->paginate($perPage);
        $markets->appends($request->query());
        
        return view('risk.vol-base-markets', [
            'markets' => $markets,
            'filters' => $filters,
            'sports' => $this->getSportsList(),
            'tournamentsBySport' => $this->getTournamentsBySport(),
        ]);
    }
    
    private function buildVolBaseFilters(Request $request): array
    {
        return [
            'search' => $request->input('search'),
            'sport' => $request->input('sport'),
            'volume_operator' => $request->input('volume_operator'), // 'greater_than' or 'less_than'
            'volume_value' => $request->input('volume_value'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
        ];
    }
}

