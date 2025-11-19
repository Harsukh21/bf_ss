<?php

namespace App\Http\Controllers;

use App\Models\MarketRate;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class MarketRateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get all available events for dropdown (from events table and market_lists)
        $eventsFromEvents = Event::select('eventId', 'eventName', 'exEventId', 'marketTime', 'createdAt')
            ->get()
            ->map(function ($event) {
                $dateSource = $event->marketTime ?? $event->createdAt;
                $formattedDate = $dateSource ? Carbon::parse($dateSource)->timezone(config('app.timezone', 'UTC'))->format('M d, Y h:i A') : null;

                return (object) [
                    'eventId' => $event->eventId,
                    'eventName' => $event->eventName,
                    'exEventId' => $event->exEventId,
                    'marketTime' => $dateSource ? Carbon::parse($dateSource)->format('Y-m-d H:i:s') : null,
                    'formattedDate' => $formattedDate,
                ];
            });
        
        // Get events from market_lists and normalize the data structure
        $marketEvents = DB::table('market_lists')
            ->select('exEventId', 'eventName', 'marketTime')
            ->whereNotIn('exEventId', $eventsFromEvents->pluck('exEventId'))
            ->distinct()
            ->get();
        
        // Convert to proper objects with consistent property names
        $eventsFromMarkets = $marketEvents->map(function ($event) {
            $formattedDate = $event->marketTime ? Carbon::parse($event->marketTime)->timezone(config('app.timezone', 'UTC'))->format('M d, Y h:i A') : null;
            return (object) [
                'eventId' => 'market_' . time() . '_' . rand(1000, 9999),
                'eventName' => $event->eventName,
                'exEventId' => $event->exEventId,
                'marketTime' => $event->marketTime ? Carbon::parse($event->marketTime)->format('Y-m-d H:i:s') : null,
                'formattedDate' => $formattedDate,
            ];
        });
        
        $events = $eventsFromEvents->concat($eventsFromMarkets);
        
        // Get selected event ID
        $selectedEventId = $request->get('exEventId');
        
        $marketRates = collect([]);
        $eventInfo = null;
        $availableMarketNames = collect([]);
        $ratesTableNotFound = false;
        $timezone = config('app.timezone', 'UTC');
        
        if ($selectedEventId) {
            // Check if table exists for this event
            if (MarketRate::tableExistsForEvent($selectedEventId)) {
                $query = MarketRate::forEvent($selectedEventId);
                $latestMarketList = DB::table('market_lists')
                    ->where('exEventId', $selectedEventId)
                    ->select('exMarketId', 'winnerType', 'status', 'selectionName')
                    ->get()
                    ->keyBy('exMarketId');
                
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

                if ($request->filled('volume_max')) {
                    $query->where('totalMatched', '<=', (float) $request->get('volume_max'));
                }

                // Apply date and time filters
                $timeFormats = ['h:i:s A', 'h:i A', 'H:i:s', 'H:i'];
                $startDateTime = null;
                $endDateTime = null;

                if ($request->filled('filter_date')) {
                    $parsedDate = $this->parseFilterDate($request->get('filter_date'), $timezone);
                    if ($parsedDate) {
                        $baseDate = $parsedDate->copy();
                        $startDateTime = $baseDate->copy()->startOfDay();
                        $endDateTime = $baseDate->copy()->endOfDay();

                        if ($request->filled('time_from')) {
                            $timeComponent = null;
                            foreach ($timeFormats as $format) {
                                try {
                                    $timeComponent = Carbon::createFromFormat($format, $request->get('time_from'), $timezone)->format('H:i:s');
                                    break;
                                } catch (Exception $e) {
                                    continue;
                                }
                            }

                            if ($timeComponent) {
                                $startDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $baseDate->format('Y-m-d') . ' ' . $timeComponent, $timezone);
                            }
                        }

                        if ($request->filled('time_to')) {
                            $timeComponent = null;
                            foreach ($timeFormats as $format) {
                                try {
                                    $timeComponent = Carbon::createFromFormat($format, $request->get('time_to'), $timezone)->format('H:i:s');
                                    break;
                                } catch (Exception $e) {
                                    continue;
                                }
                            }

                            if ($timeComponent) {
                                $endDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $baseDate->format('Y-m-d') . ' ' . $timeComponent, $timezone);
                            }
                        }

                        if ($startDateTime && $endDateTime && $endDateTime->lt($startDateTime)) {
                            $endDateTime = $startDateTime->copy()->endOfDay();
                        }
                    }
                }

                if ($startDateTime && $endDateTime) {
                    $query->whereBetween('created_at', [
                        $startDateTime->format('Y-m-d H:i:s'),
                        $endDateTime->format('Y-m-d H:i:s'),
                    ]);
                } elseif ($startDateTime) {
                    $query->where('created_at', '>=', $startDateTime->format('Y-m-d H:i:s'));
                } elseif ($endDateTime) {
                    $query->where('created_at', '<=', $endDateTime->format('Y-m-d H:i:s'));
                }

                $marketRates = $query->latest('created_at')->paginate(10);
                $marketRates->getCollection()->transform(function ($rate) use ($latestMarketList) {
                    $meta = $latestMarketList->get($rate->exMarketId);
                    $rate->marketListStatus = $meta->status ?? null;
                    $rate->marketListWinnerType = $meta->winnerType ?? null;
                    $rate->marketListSelectionName = $meta->selectionName ?? null;
                    return $rate;
                });
            } else {
                // Table does not exist for this event
                $ratesTableNotFound = true;
            }
            
            // Get event information (from events table or market_lists)
            $eventInfo = Event::where('exEventId', $selectedEventId)->first();
            if (!$eventInfo) {
                $marketEventInfo = DB::table('market_lists')
                    ->where('exEventId', $selectedEventId)
                    ->select('eventName', 'exEventId', 'marketTime')
                    ->selectRaw("'market_list' as source")
                    ->first();
                if ($marketEventInfo) {
                    // Convert stdClass to object with proper attributes
                    $eventInfo = (object) [
                        'eventName' => $marketEventInfo->eventName,
                        'exEventId' => $marketEventInfo->exEventId,
                        'source' => $marketEventInfo->source,
                        'marketTime' => $marketEventInfo->marketTime ?? null,
                    ];
                }
            }

            if ($eventInfo) {
                $dateSource = $eventInfo->marketTime ?? ($eventInfo->createdAt ?? null);
                if ($dateSource) {
                    $parsedSource = Carbon::parse($dateSource)->timezone($timezone);
                    $eventInfo->formattedDate = $parsedSource->format('M d, Y h:i A');
                } else {
                    $eventInfo->formattedDate = null;
                }
            }
        }

        $defaultFilterDate = null;

        if ($eventInfo) {
            $rawDate = $eventInfo->marketTime ?? ($eventInfo->createdAt ?? null);
            if ($rawDate) {
                try {
                    $defaultFilterDate = Carbon::parse($rawDate)->timezone($timezone)->format('d/m/Y');
                } catch (Exception $e) {
                    $defaultFilterDate = null;
                }
            }
        }

        $filterCount = 0;
        if ($request->filled('market_name')) $filterCount++;
        if ($request->filled('filter_date')) $filterCount++;
        if ($request->filled('time_from')) $filterCount++;
        if ($request->filled('time_to')) $filterCount++;
        if ($request->filled('volume_max')) $filterCount++;

        return view('market-rates.index', compact(
            'marketRates',
            'events',
            'selectedEventId',
            'eventInfo',
            'availableMarketNames',
            'ratesTableNotFound',
            'filterCount',
            'defaultFilterDate'
        ));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        \Log::info('MarketRateController@show - Starting', [
            'id' => $id,
            'exEventId' => $request->get('exEventId'),
            'grid' => $request->get('grid'),
            'all_params' => $request->all()
        ]);

        try {
            $selectedEventId = $request->get('exEventId');
            \Log::info('MarketRateController@show - Event ID extracted', ['exEventId' => $selectedEventId]);

            $gridCount = $request->get('grid');
            $gridEnabled = !empty($gridCount) && in_array((int)$gridCount, [10, 20, 40, 60]);
            $gridCountValue = $gridEnabled ? (int)$gridCount : null;
            
            if (!$selectedEventId) {
                \Log::error('MarketRateController@show - No exEventId provided', ['id' => $id]);
                return redirect()->route('market-rates.index')
                    ->with('error', 'Market rates not found for this event.');
            }

            \Log::info('MarketRateController@show - Checking if table exists', ['exEventId' => $selectedEventId]);
            $tableExists = MarketRate::tableExistsForEvent($selectedEventId);
            \Log::info('MarketRateController@show - Table exists check result', [
                'exEventId' => $selectedEventId,
                'tableExists' => $tableExists
            ]);

            if (!$tableExists) {
                \Log::error('MarketRateController@show - Table does not exist', ['exEventId' => $selectedEventId]);
                return redirect()->route('market-rates.index')
                    ->with('error', 'Market rates not found for this event.');
            }

            \Log::info('MarketRateController@show - Fetching market rate', [
                'exEventId' => $selectedEventId,
                'id' => $id
            ]);
            $query = MarketRate::forEvent($selectedEventId);
            $marketRate = $query->find($id);
            \Log::info('MarketRateController@show - Market rate fetched', [
                'id' => $id,
                'found' => !is_null($marketRate),
                'marketRateData' => $marketRate ? [
                    'id' => $marketRate->id ?? null,
                    'exMarketId' => $marketRate->exMarketId ?? null,
                    'marketName' => $marketRate->marketName ?? null,
                    'hasRunners' => !empty($marketRate->runners ?? null),
                    'created_at' => $marketRate->created_at ?? null,
                ] : null
            ]);

            if (!$marketRate) {
                \Log::error('MarketRateController@show - Market rate not found', [
                    'exEventId' => $selectedEventId,
                    'id' => $id
                ]);
                return redirect()->route('market-rates.index')
                    ->with('error', 'Market rate not found.');
            }
        } catch (\Exception $e) {
            \Log::error('MarketRateController@show - Error in initial setup', [
                'id' => $id,
                'exEventId' => $request->get('exEventId') ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }

        try {
            \Log::info('MarketRateController@show - Fetching event info', ['exEventId' => $selectedEventId]);
            $eventInfo = Event::where('exEventId', $selectedEventId)->first();
            \Log::info('MarketRateController@show - Event info fetched', [
                'exEventId' => $selectedEventId,
                'found' => !is_null($eventInfo),
                'eventName' => $eventInfo->eventName ?? null
            ]);
        } catch (\Exception $e) {
            \Log::error('MarketRateController@show - Error fetching event info', [
                'exEventId' => $selectedEventId,
                'error' => $e->getMessage()
            ]);
            $eventInfo = null;
        }

        try {
            \Log::info('MarketRateController@show - Fetching market list meta', [
                'exMarketId' => $marketRate->exMarketId ?? null
            ]);
            $marketListMeta = DB::table('market_lists')
                ->where('exMarketId', $marketRate->exMarketId)
                ->select('status', 'winnerType', 'selectionName')
                ->first();
            \Log::info('MarketRateController@show - Market list meta fetched', [
                'exMarketId' => $marketRate->exMarketId ?? null,
                'found' => !is_null($marketListMeta),
                'status' => $marketListMeta->status ?? null
            ]);
        } catch (\Exception $e) {
            \Log::error('MarketRateController@show - Error fetching market list meta', [
                'exMarketId' => $marketRate->exMarketId ?? null,
                'error' => $e->getMessage()
            ]);
            $marketListMeta = null;
        }

        $marketListStatus = $marketListMeta->status ?? null;
        $marketListWinnerType = $marketListMeta->winnerType ?? null;
        $marketListSelectionName = $marketListMeta->selectionName ?? null;

        // Extract all unique runners from all market rates for this market
        try {
            \Log::info('MarketRateController@show - Fetching runners list', [
                'exEventId' => $selectedEventId,
                'marketName' => $marketRate->marketName ?? null,
                'marketNameEmpty' => empty($marketRate->marketName ?? null)
            ]);
            
            if (empty($marketRate->marketName)) {
                // If marketName is null/empty, get all rates without marketName filter
                \Log::info('MarketRateController@show - Using whereNull for marketName');
                $allMarketRatesForRunnerList = MarketRate::forEvent($selectedEventId)
                    ->whereNull('marketName')
                    ->whereNotNull('runners')
                    ->get();
            } else {
                \Log::info('MarketRateController@show - Using where clause for marketName', [
                    'marketName' => $marketRate->marketName
                ]);
                $allMarketRatesForRunnerList = MarketRate::forEvent($selectedEventId)
                    ->where('marketName', $marketRate->marketName)
                    ->whereNotNull('marketName')
                    ->whereNotNull('runners')
                    ->get();
            }
            \Log::info('MarketRateController@show - Runners list fetched', [
                'count' => $allMarketRatesForRunnerList->count()
            ]);
        } catch (\Exception $e) {
            \Log::error('MarketRateController@show - Error fetching market rates for runner list', [
                'exEventId' => $selectedEventId,
                'marketRateId' => $id,
                'marketName' => $marketRate->marketName ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $allMarketRatesForRunnerList = collect([]);
        }
        
        try {
            \Log::info('MarketRateController@show - Processing runners', [
                'ratesCount' => $allMarketRatesForRunnerList->count()
            ]);
            $allRunners = collect();
            foreach ($allMarketRatesForRunnerList as $rateIndex => $rate) {
                try {
                    $runners = is_string($rate->runners) ? json_decode($rate->runners, true) : $rate->runners;
                    if (is_array($runners)) {
                        foreach ($runners as $runnerIndex => $runner) {
                            try {
                                $runner = is_array($runner) ? $runner : (array) $runner;
                                $runnerName = $runner['runnerName'] ?? null;
                                if ($runnerName && !$allRunners->contains($runnerName)) {
                                    $allRunners->push($runnerName);
                                }
                            } catch (\Exception $e) {
                                \Log::error('MarketRateController@show - Error processing runner', [
                                    'rateIndex' => $rateIndex,
                                    'runnerIndex' => $runnerIndex,
                                    'error' => $e->getMessage()
                                ]);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('MarketRateController@show - Error processing rate runners', [
                        'rateIndex' => $rateIndex,
                        'rateId' => $rate->id ?? null,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            $allRunners = $allRunners->sort()->values();
            \Log::info('MarketRateController@show - Runners processed', [
                'uniqueRunnersCount' => $allRunners->count()
            ]);
        } catch (\Exception $e) {
            \Log::error('MarketRateController@show - Error processing all runners', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $allRunners = collect();
        }
        
        // Get selected runner from request
        $selectedRunner = $request->get('runner');
        // Get next and previous market rates for navigation (filtered by marketName)
        // Ensure we only get records with the exact same marketName
        // Note: For performance, we'll only fetch records around the current one (100 before, 100 after)
        try {
            \Log::info('MarketRateController@show - Fetching all market rates for navigation', [
                'exEventId' => $selectedEventId,
                'marketName' => $marketRate->marketName ?? null,
                'marketNameEmpty' => empty($marketRate->marketName ?? null)
            ]);
            
            // First, get the current market rate's created_at to find nearby records
            $currentCreatedAt = $marketRate->created_at;
            \Log::info('MarketRateController@show - Current created_at', ['created_at' => $currentCreatedAt]);
            
            // Build base query
            $baseQuery = MarketRate::forEvent($selectedEventId);
            
            if (empty($marketRate->marketName)) {
                \Log::info('MarketRateController@show - Using whereNull for all market rates');
                $baseQuery->whereNull('marketName');
            } else {
                \Log::info('MarketRateController@show - Using where clause for all market rates', [
                    'marketName' => $marketRate->marketName
                ]);
                $baseQuery->where('marketName', $marketRate->marketName)
                    ->whereNotNull('marketName');
            }
            
            // For performance, limit to 200 records around the current one (100 before + 100 after)
            // This is enough for navigation and prevents timeout on large datasets
            $allMarketRates = $baseQuery
                ->where(function($q) use ($currentCreatedAt) {
                    // Get records within a reasonable time window (e.g., 24 hours before and after)
                    $q->whereBetween('created_at', [
                        \Carbon\Carbon::parse($currentCreatedAt)->subHours(24),
                        \Carbon\Carbon::parse($currentCreatedAt)->addHours(24)
                    ]);
                })
                ->orderBy('created_at', 'desc')
                ->limit(200)
                ->get();
                
            \Log::info('MarketRateController@show - All market rates fetched (limited)', [
                'count' => $allMarketRates->count(),
                'limit_applied' => true
            ]);
            
            // If we don't have enough records around the current one, try to find it in the limited set
            // If current record is not in this set, add it manually
            $foundCurrent = $allMarketRates->contains(function($item) use ($id) {
                return $item->id == $id;
            });
            
            if (!$foundCurrent) {
                \Log::info('MarketRateController@show - Current record not in limited set, adding it');
                // Current record is outside the time window, add it to the collection
                $allMarketRates->prepend($marketRate);
                // Re-sort to maintain order
                $allMarketRates = $allMarketRates->sortByDesc('created_at')->values();
            }
            
        } catch (\Exception $e) {
            // Fallback: if query fails, just get the current market rate
            \Log::error('MarketRateController@show - Error fetching all market rates', [
                'exEventId' => $selectedEventId,
                'marketRateId' => $id,
                'marketName' => $marketRate->marketName ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $allMarketRates = collect([$marketRate]);
        }
        
        try {
            \Log::info('MarketRateController@show - Finding current index', [
                'id' => $id,
                'allMarketRatesCount' => $allMarketRates->count()
            ]);
            $currentIndex = $allMarketRates->search(function($item) use ($id) {
                return $item->id == $id;
            });
            \Log::info('MarketRateController@show - Current index found', [
                'currentIndex' => $currentIndex !== false ? $currentIndex : 'not_found'
            ]);
        } catch (\Exception $e) {
            \Log::error('MarketRateController@show - Error finding current index', [
                'error' => $e->getMessage()
            ]);
            $currentIndex = false;
        }
        
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
                try {
                    \Log::info('MarketRateController@show - Grid mode enabled', [
                        'gridCount' => $gridCountValue,
                        'currentCreatedAt' => $marketRate->created_at ?? null
                    ]);
                    $currentCreatedAt = $marketRate->created_at;
                    $additionalRecords = $gridCountValue - 1; // Subtract 1 for current record
                    
                    // Get up to (count-1) records with same marketName that are newer (created_at > current)
                    if (empty($marketRate->marketName)) {
                        \Log::info('MarketRateController@show - Fetching newer records with whereNull');
                        $newerRecords = MarketRate::forEvent($selectedEventId)
                            ->whereNull('marketName')
                            ->where('created_at', '>', $currentCreatedAt)
                            ->orderBy('created_at', 'asc') // Order ascending to get them in chronological order
                            ->limit($additionalRecords)
                            ->get();
                    } else {
                        \Log::info('MarketRateController@show - Fetching newer records with where clause', [
                            'marketName' => $marketRate->marketName
                        ]);
                        $newerRecords = MarketRate::forEvent($selectedEventId)
                            ->where('marketName', $marketRate->marketName)
                            ->whereNotNull('marketName')
                            ->where('created_at', '>', $currentCreatedAt)
                            ->orderBy('created_at', 'asc') // Order ascending to get them in chronological order
                            ->limit($additionalRecords)
                            ->get();
                    }
                    \Log::info('MarketRateController@show - Newer records fetched', [
                        'count' => $newerRecords->count()
                    ]);
                } catch (\Exception $e) {
                    \Log::error('MarketRateController@show - Error fetching newer records for grid', [
                        'exEventId' => $selectedEventId,
                        'marketRateId' => $id,
                        'marketName' => $marketRate->marketName ?? null,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    $newerRecords = collect([]);
                }
                
                // Double-check marketName matches and create collection with current record first
                try {
                    \Log::info('MarketRateController@show - Creating grid market rates collection');
                    $gridMarketRates = collect([$marketRate])
                        ->merge($newerRecords->filter(function($item) use ($marketRate) {
                            return $item->marketName === $marketRate->marketName;
                        }))
                        ->values();

                    \Log::info('MarketRateController@show - Fetching grid meta', [
                        'exMarketIdsCount' => $gridMarketRates->pluck('exMarketId')->filter()->count()
                    ]);
                    $gridMeta = DB::table('market_lists')
                        ->whereIn('exMarketId', $gridMarketRates->pluck('exMarketId')->filter()->all())
                        ->select('exMarketId', 'status', 'winnerType', 'selectionName')
                        ->get()
                        ->keyBy('exMarketId');

                    \Log::info('MarketRateController@show - Mapping grid market rates with meta', [
                        'gridMetaCount' => $gridMeta->count()
                    ]);
                    $gridMarketRates = $gridMarketRates->map(function ($rate) use ($gridMeta) {
                        $meta = $gridMeta->get($rate->exMarketId);
                        $rate->marketListStatus = $meta->status ?? null;
                        $rate->marketListWinnerType = $meta->winnerType ?? null;
                        $rate->marketListSelectionName = $meta->selectionName ?? null;
                        return $rate;
                    });
                    \Log::info('MarketRateController@show - Grid market rates created', [
                        'count' => $gridMarketRates->count()
                    ]);
                } catch (\Exception $e) {
                    \Log::error('MarketRateController@show - Error creating grid market rates', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    $gridMarketRates = collect([$marketRate]);
                }
            }
        }

        try {
            \Log::info('MarketRateController@show - Preparing view data', [
                'hasMarketRate' => !is_null($marketRate),
                'hasEventInfo' => !is_null($eventInfo),
                'hasPreviousMarketRate' => !is_null($previousMarketRate),
                'hasNextMarketRate' => !is_null($nextMarketRate),
                'gridEnabled' => $gridEnabled,
                'allRunnersCount' => $allRunners->count()
            ]);

            return view('market-rates.show', compact(
                'marketRate',
                'eventInfo',
                'selectedEventId',
                'previousMarketRate',
                'nextMarketRate',
                'gridEnabled',
                'gridCountValue',
                'gridMarketRates',
                'marketListStatus',
                'marketListWinnerType',
                'marketListSelectionName',
                'allRunners',
                'selectedRunner'
            ));
        } catch (\Exception $e) {
            \Log::error('MarketRateController@show - Error rendering view', [
                'id' => $id,
                'exEventId' => $selectedEventId ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
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

        // Apply date and time filters
        $timezone = config('app.timezone', 'UTC');
        $timeFormats = ['h:i:s A', 'h:i A', 'H:i:s', 'H:i'];
        $startDateTime = null;
        $endDateTime = null;

        if ($request->filled('filter_date')) {
            $parsedDate = $this->parseFilterDate($request->get('filter_date'), $timezone);
            if ($parsedDate) {
                $baseDate = $parsedDate->copy();
                $startDateTime = $baseDate->copy()->startOfDay();
                $endDateTime = $baseDate->copy()->endOfDay();

                if ($request->filled('time_from')) {
                    $timeComponent = null;
                    foreach ($timeFormats as $format) {
                        try {
                            $timeComponent = Carbon::createFromFormat($format, $request->get('time_from'), $timezone)->format('H:i:s');
                            break;
                        } catch (Exception $e) {
                            continue;
                        }
                    }

                    if ($timeComponent) {
                        $startDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $baseDate->format('Y-m-d') . ' ' . $timeComponent, $timezone);
                    }
                }

                if ($request->filled('time_to')) {
                    $timeComponent = null;
                    foreach ($timeFormats as $format) {
                        try {
                            $timeComponent = Carbon::createFromFormat($format, $request->get('time_to'), $timezone)->format('H:i:s');
                            break;
                        } catch (Exception $e) {
                            continue;
                        }
                    }

                    if ($timeComponent) {
                        $endDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $baseDate->format('Y-m-d') . ' ' . $timeComponent, $timezone);
                    }
                }

                if ($startDateTime && $endDateTime && $endDateTime->lt($startDateTime)) {
                    $endDateTime = $startDateTime->copy()->endOfDay();
                }
            }
        }

        if ($startDateTime && $endDateTime) {
            $query->whereBetween('created_at', [
                $startDateTime->format('Y-m-d H:i:s'),
                $endDateTime->format('Y-m-d H:i:s'),
            ]);
        } elseif ($startDateTime) {
            $query->where('created_at', '>=', $startDateTime->format('Y-m-d H:i:s'));
        } elseif ($endDateTime) {
            $query->where('created_at', '<=', $endDateTime->format('Y-m-d H:i:s'));
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

    /**
     * Normalize date input into a Carbon instance.
     */
    private function parseFilterDate(?string $value, string $timezone): ?Carbon
    {
        if (!$value) {
            return null;
        }

        $normalized = trim($value);
        if ($normalized === '') {
            return null;
        }

        $formats = ['d/m/Y', 'd-m-Y', 'Y-m-d'];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $normalized, $timezone);
                if ($date !== false) {
                    return $date;
                }
            } catch (Exception $e) {
                continue;
            }
        }

        return null;
    }
}
