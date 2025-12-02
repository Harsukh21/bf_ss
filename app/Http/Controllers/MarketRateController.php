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

        $marketListMeta = DB::table('market_lists')
            ->where('exMarketId', $marketRate->exMarketId)
            ->select('status', 'winnerType', 'selectionName')
            ->first();

        // Map status from integer to readable string
        $statusMap = [
            1 => 'UNSETTLED',
            2 => 'UPCOMING',
            3 => 'INPLAY',
            4 => 'CLOSED',
            5 => 'VOIDED',
            6 => 'REMOVED',
        ];
        $marketListStatus = $marketListMeta && $marketListMeta->status ? ($statusMap[$marketListMeta->status] ?? null) : null;
        $marketListWinnerType = $marketListMeta->winnerType ?? null;
        $marketListSelectionName = $marketListMeta->selectionName ?? null;

        // Extract all unique runners from all market rates for this market
        try {
            if (empty($marketRate->marketName)) {
                $allMarketRatesForRunnerList = MarketRate::forEvent($selectedEventId)
                    ->whereNull('marketName')
                    ->whereNotNull('runners')
                    ->get();
            } else {
                $allMarketRatesForRunnerList = MarketRate::forEvent($selectedEventId)
                    ->where('marketName', $marketRate->marketName)
                    ->whereNotNull('marketName')
                    ->whereNotNull('runners')
                    ->get();
            }
        } catch (\Exception $e) {
            $allMarketRatesForRunnerList = collect([]);
        }
        
        $allRunners = collect();
        foreach ($allMarketRatesForRunnerList as $rate) {
            $runners = is_string($rate->runners) ? json_decode($rate->runners, true) : $rate->runners;
            if (is_array($runners)) {
                foreach ($runners as $runner) {
                    $runner = is_array($runner) ? $runner : (array) $runner;
                    $runnerName = $runner['runnerName'] ?? null;
                    if ($runnerName && !$allRunners->contains($runnerName)) {
                        $allRunners->push($runnerName);
                    }
                }
            }
        }
        $allRunners = $allRunners->sort()->values();
        
        // Get selected runner from request
        $selectedRunner = $request->get('runner');
        // Get next and previous market rates for navigation (filtered by marketName)
        // For performance, limit to 200 records within 24 hours around the current record
        try {
            $currentCreatedAt = $marketRate->created_at;
            $baseQuery = MarketRate::forEvent($selectedEventId);
            
            if (empty($marketRate->marketName)) {
                $baseQuery->whereNull('marketName');
            } else {
                $baseQuery->where('marketName', $marketRate->marketName)
                    ->whereNotNull('marketName');
            }
            
            // Limit to 200 records within 24 hours to prevent timeout on large datasets
            $allMarketRates = $baseQuery
                ->where(function($q) use ($currentCreatedAt) {
                    $q->whereBetween('created_at', [
                        Carbon::parse($currentCreatedAt)->subHours(24),
                        Carbon::parse($currentCreatedAt)->addHours(24)
                    ]);
                })
                ->orderBy('created_at', 'desc')
                ->limit(200)
                ->get();
            
            // Ensure current record is included for proper navigation
            $foundCurrent = $allMarketRates->contains(function($item) use ($id) {
                return $item->id == $id;
            });
            
            if (!$foundCurrent) {
                $allMarketRates->prepend($marketRate);
                $allMarketRates = $allMarketRates->sortByDesc('created_at')->values();
            }
        } catch (\Exception $e) {
            $allMarketRates = collect([$marketRate]);
        }
        
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
            if ($gridEnabled) {
                try {
                    $currentCreatedAt = $marketRate->created_at;
                    $additionalRecords = $gridCountValue - 1;
                    
                    if (empty($marketRate->marketName)) {
                        $newerRecords = MarketRate::forEvent($selectedEventId)
                            ->whereNull('marketName')
                            ->where('created_at', '>', $currentCreatedAt)
                            ->orderBy('created_at', 'asc')
                            ->limit($additionalRecords)
                            ->get();
                    } else {
                        $newerRecords = MarketRate::forEvent($selectedEventId)
                            ->where('marketName', $marketRate->marketName)
                            ->whereNotNull('marketName')
                            ->where('created_at', '>', $currentCreatedAt)
                            ->orderBy('created_at', 'asc')
                            ->limit($additionalRecords)
                            ->get();
                    }
                } catch (\Exception $e) {
                    $newerRecords = collect([]);
                }
                
                $gridMarketRates = collect([$marketRate])
                    ->merge($newerRecords->filter(function($item) use ($marketRate) {
                        return $item->marketName === $marketRate->marketName;
                    }))
                    ->values();

                $gridMeta = DB::table('market_lists')
                    ->whereIn('exMarketId', $gridMarketRates->pluck('exMarketId')->filter()->all())
                    ->select('exMarketId', 'status', 'winnerType', 'selectionName')
                    ->get()
                    ->keyBy('exMarketId');

                // Map status from integer to readable string
                $statusMap = [
                    1 => 'UNSETTLED',
                    2 => 'UPCOMING',
                    3 => 'INPLAY',
                    4 => 'CLOSED',
                    5 => 'VOIDED',
                    6 => 'REMOVED',
                ];

                $gridMarketRates = $gridMarketRates->map(function ($rate) use ($gridMeta, $statusMap) {
                    $meta = $gridMeta->get($rate->exMarketId);
                    $rate->marketListStatus = $meta && $meta->status ? ($statusMap[$meta->status] ?? null) : null;
                    $rate->marketListWinnerType = $meta->winnerType ?? null;
                    $rate->marketListSelectionName = $meta->selectionName ?? null;
                    return $rate;
                });
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
            'gridMarketRates',
            'marketListStatus',
            'marketListWinnerType',
            'marketListSelectionName',
            'allRunners',
            'selectedRunner'
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
