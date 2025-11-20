<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\TelegramService;

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
                'events.is_interrupted',
                'events.labels',
                'events.remind_me_after',
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

        // Interrupted status filter
        if ($request->filled('interrupted_status')) {
            $interruptedStatus = $request->input('interrupted_status');
            if ($interruptedStatus === 'on') {
                $query->where('events.is_interrupted', true);
            } elseif ($interruptedStatus === 'off') {
                $query->where(function($q) {
                    $q->where('events.is_interrupted', false)
                      ->orWhereNull('events.is_interrupted');
                });
            }
            // If 'all', no filter is applied
        }

        // Labels filter - filter by events that have specific labels checked
        if ($request->filled('labels') && is_array($request->input('labels'))) {
            $selectedLabels = $request->input('labels');
            // Only process labels that are checked (true)
            $activeLabels = array_filter($selectedLabels, function($value) {
                return $value === '1' || $value === 'true' || $value === true;
            });
            
            if (!empty($activeLabels)) {
                $labelConfig = config('labels.labels', []);
                $allowedLabelKeys = array_keys($labelConfig);
                
                $query->where(function($q) use ($activeLabels, $allowedLabelKeys) {
                    foreach ($activeLabels as $labelKey => $value) {
                        // Only process if key is in allowed list (prevent SQL injection)
                        if (!in_array($labelKey, $allowedLabelKeys)) {
                            continue;
                        }
                        
                        $normalizedKey = strtolower($labelKey); // Labels are stored with lowercase keys
                        // Escape the key for use in raw SQL (prevent SQL injection)
                        $escapedKey = str_replace("'", "''", $normalizedKey); // Escape single quotes for PostgreSQL
                        // Check if label exists in JSONB and is true
                        // PostgreSQL JSONB path query: events.labels->>'labelKey' = 'true'
                        $q->orWhereRaw("COALESCE(events.labels->>'{$escapedKey}', 'false') = 'true'");
                    }
                });
            }
        }

        // Get label keys for checking all labels
        $labelConfig = config('labels.labels', []);
        $labelKeys = array_keys($labelConfig);
        $allLabelKeys = array_map('strtolower', $labelKeys); // Labels stored with lowercase keys
        
        // Build custom ordering:
        // 1. is_interrupted = true first
        // 2. Then in-play (marketTime <= now, currently active)
        // 3. Then upcoming (marketTime > now, future in-play)
        // 4. Then events with all 4 labels checked
        $now = Carbon::now();
        $nowStr = $now->format('Y-m-d H:i:s');
        
        // Build condition for all labels checked
        $allLabelsCondition = $this->buildAllLabelsCheckedCondition($allLabelKeys);
        
        $events = $query->groupBy(
                'events.id',
                'events.eventId',
                'events.exEventId',
                'events.eventName',
                'events.sportId',
                'events.tournamentsId',
                'events.tournamentsName',
                'events.marketTime',
                'events.createdAt',
                'events.is_interrupted',
                'events.labels',
                'events.remind_me_after'
            )
            ->selectRaw('
                events.*,
                CASE 
                    WHEN events."is_interrupted" = true THEN 1
                    ELSE 2
                END as sort_interrupted,
                CASE 
                    WHEN events."marketTime" IS NOT NULL AND events."marketTime" <= ? THEN 1
                    WHEN events."marketTime" IS NOT NULL AND events."marketTime" > ? THEN 2
                    ELSE 3
                END as sort_time_status,
                CASE 
                    WHEN ' . $allLabelsCondition . ' THEN 1
                    ELSE 2
                END as sort_all_labels
            ', [$nowStr, $nowStr])
            ->orderBy('sort_interrupted', 'asc') // 1. is_interrupted = true first
            ->orderBy('sort_time_status', 'asc') // 2. In-play (current) first, then upcoming
            ->orderBy('sort_all_labels', 'asc') // 3. All labels checked first
            ->orderBy('events.marketTime', 'desc') // 4. Then by newest marketTime
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

        // Fetch all markets (with old_limit) for all events in one query
        $marketOldLimits = DB::table('market_lists')
            ->select('exEventId', 'marketName', 'old_limit')
            ->whereIn('exEventId', $eventIds)
            ->where('status', 3) // INPLAY status
            ->orderBy('marketName')
            ->get()
            ->groupBy('exEventId');

        // Format the events with sport names and parse labels from events table
        $events->getCollection()->transform(function ($event) use ($sports, $labelKeys, $marketOldLimits) {
            $event->sportName = $sports[$event->sportId] ?? 'Unknown Sport';
            $event->formatted_market_time = $event->marketTime 
                ? Carbon::parse($event->marketTime)->format('M d, Y h:i A') 
                : null;
            $event->formatted_first_market_time = $event->first_market_time 
                ? Carbon::parse($event->first_market_time)->format('M d, Y h:i A') 
                : null;
            
            // Parse labels from events table (JSONB) - same structure as market_lists
            // Labels are stored as: {"4x":false,"b2c":false,"b2b":false,"usdt":false} (lowercase keys)
            $eventLabels = [];
            if ($event->labels) {
                $labels = is_string($event->labels) ? json_decode($event->labels, true) : $event->labels;
                if (is_array($labels)) {
                    $eventLabels = $labels;
                }
            }
            
            // Normalize labels: ensure all label keys exist with default false value
            // Keys in DB are lowercase (e.g., "4x"), but we use original keys from config for display
            $parsedLabels = [];
            foreach ($labelKeys as $labelKey) {
                // Check lowercase key (stored in DB) first, fallback to original key for backward compatibility
                $dbKey = strtolower($labelKey);
                $parsedLabels[$labelKey] = (isset($eventLabels[$dbKey]) && (bool)$eventLabels[$dbKey] === true)
                    || (isset($eventLabels[$labelKey]) && (bool)$eventLabels[$labelKey] === true);
            }
            
            $event->labels = $parsedLabels;
            
            // Get all markets with old_limit for this event (show all markets even if old_limit is 0 or null)
            $eventMarkets = $marketOldLimits->get($event->exEventId, collect());
            $event->market_old_limits = $eventMarkets->map(function ($market) {
                return (object) [
                    'marketName' => $market->marketName,
                    'old_limit' => $market->old_limit ?? 0,
                ];
            })->values()->toArray();
            
            return $event;
        });

        return view('scorecard.index', [
            'events' => $events,
            'sports' => $sports,
            'tournaments' => $tournaments,
            'labelConfig' => $labelConfig,
        ]);
    }

    public function getEventMarkets($exEventId)
    {
        $markets = DB::table('market_lists')
            ->select('id', 'exMarketId', 'marketName', 'old_limit', 'status')
            ->where('exEventId', $exEventId)
            ->where('status', 3) // INPLAY status
            ->orderBy('marketName')
            ->get();

        return response()->json([
            'success' => true,
            'markets' => $markets,
        ]);
    }

    public function updateEvent(Request $request, $exEventId)
    {
        $request->validate([
            'markets' => ['nullable', 'array'],
            'markets.*.id' => ['required_with:markets', 'integer'],
            'markets.*.old_limit' => ['nullable', 'numeric', 'min:0'],
            'is_interrupted' => ['nullable', 'boolean'],
            'labels' => ['nullable', 'array'],
            'remind_me_after' => ['nullable', 'integer', 'in:5,10,15,20,25,30'],
        ]);

        $event = DB::table('events')
            ->where('exEventId', $exEventId)
            ->first();

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found.',
            ], 404);
        }

        // Update old_limit for each market in market_lists (only if markets array is provided)
        if ($request->has('markets') && is_array($request->input('markets'))) {
            foreach ($request->input('markets', []) as $marketData) {
                $marketId = $marketData['id'];
                $oldLimit = isset($marketData['old_limit']) && $marketData['old_limit'] !== '' 
                    ? (int) $marketData['old_limit'] 
                    : null;

                DB::table('market_lists')
                    ->where('id', $marketId)
                    ->where('exEventId', $exEventId)
                    ->update([
                        'old_limit' => $oldLimit,
                        'updated_at' => now(),
                    ]);
            }
        }

        // Prepare update data for events table
        $updateData = [
            'updated_at' => now(),
        ];

        // Check if this is the first time marking as interrupted
        $wasInterrupted = (bool) ($event->is_interrupted ?? false);
        $isFirstTimeInterrupted = false;
        $isBeingTurnedOff = false;

        // Update is_interrupted (if provided in request)
        if ($request->has('is_interrupted')) {
            $updateData['is_interrupted'] = (bool) $request->input('is_interrupted', false);
            
            // Check if this is first time being marked as interrupted
            if ($updateData['is_interrupted'] && !$wasInterrupted) {
                $isFirstTimeInterrupted = true;
            }
            
            // Check if interruption is being turned OFF
            if (!$updateData['is_interrupted'] && $wasInterrupted) {
                $isBeingTurnedOff = true;
            }
            
            // If turning off interruption, delete pending reminders
            if (!$updateData['is_interrupted']) {
                DB::table('event_reminders')
                    ->where('exEventId', $exEventId)
                    ->where('sent', false)
                    ->delete();
                
                // Also clear remind_me_after if turning off
                $updateData['remind_me_after'] = null;
                
                // IMPORTANT: Preserve existing labels when turning off interruption
                // Do NOT update labels - they should remain as they were
                // Only update labels if explicitly provided in request
            }
        } elseif ($request->has('markets') || $request->has('remind_me_after') || $request->has('labels')) {
            // If updating other fields from modal form, is_interrupted should always be true (modal only opens when toggle is ON)
            // But check if it was provided in the request first
            if ($request->has('is_interrupted')) {
                $updateData['is_interrupted'] = (bool) $request->input('is_interrupted', true);
            } else {
                // If not provided, set to true (because modal form is only submitted when toggle is ON)
                $updateData['is_interrupted'] = true;
            }
            
            // Check if this is first time being marked as interrupted (when modal form is submitted)
            if ($updateData['is_interrupted'] && !$wasInterrupted) {
                $isFirstTimeInterrupted = true;
            }
        }

        // Update labels (JSONB) - normalize to match market_lists structure
        // Only update labels if explicitly provided in request (not when just turning off interruption)
        if ($request->has('labels')) {
            $labels = $request->input('labels', []);
            // Normalize labels: ensure all keys are lowercase and boolean values
            $normalizedLabels = [];
            $labelKeys = array_keys(config('labels.labels', []));
            foreach ($labelKeys as $key) {
                // Preserve existing label value if not provided in request
                $existingLabels = json_decode($event->labels ?? '{}', true);
                $existingValue = $existingLabels[strtolower($key)] ?? false;
                
                // Use provided value if exists, otherwise keep existing value
                $normalizedLabels[strtolower($key)] = isset($labels[$key]) ? (bool) $labels[$key] : $existingValue;
            }
            $updateData['labels'] = json_encode($normalizedLabels);
        }

        // Update remind_me_after
        if ($request->filled('remind_me_after')) {
            $remindMeAfter = (int) $request->input('remind_me_after');
            $updateData['remind_me_after'] = $remindMeAfter;

            // Calculate reminder_time (current time + remind_me_after minutes)
            // Only if is_interrupted is true
            if ($updateData['is_interrupted'] && $remindMeAfter > 0) {
                $reminderTime = Carbon::now()->addMinutes($remindMeAfter);
                
                // Store or update reminder record
                DB::table('event_reminders')->updateOrInsert(
                    [
                        'exEventId' => $exEventId,
                        'reminder_time' => $reminderTime->format('Y-m-d H:i:s'),
                    ],
                    [
                        'reminder_time' => $reminderTime->format('Y-m-d H:i:s'),
                        'sent' => false,
                        'sent_at' => null,
                        'error_message' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            } elseif (!$updateData['is_interrupted'] || $remindMeAfter <= 0) {
                // If not interrupted or remind_me_after is 0, delete pending reminders
                DB::table('event_reminders')
                    ->where('exEventId', $exEventId)
                    ->where('sent', false)
                    ->delete();
            }
        }

        DB::table('events')
            ->where('exEventId', $exEventId)
            ->update($updateData);

        // Send immediate Telegram notification if this is first time being interrupted
        if ($isFirstTimeInterrupted) {
            try {
                // Get sport name from config
                $sports = config('sports.sports', []);
                $sportId = $event->sportId ?? null;
                
                // Prepare event data for notification
                $eventData = (object) [
                    'eventId' => $event->eventId ?? null,
                    'exEventId' => $event->exEventId,
                    'eventName' => $event->eventName ?? 'N/A',
                    'sportName' => $sportId && isset($sports[$sportId]) ? $sports[$sportId] : 'Unknown Sport',
                    'tournamentsName' => $event->tournamentsName ?? 'N/A',
                    'remind_me_after' => $updateData['remind_me_after'] ?? $event->remind_me_after ?? null,
                ];

                // Get market old limits for this event
                $marketOldLimits = DB::table('market_lists')
                    ->select('marketName', 'old_limit')
                    ->where('exEventId', $exEventId)
                    ->where('status', 3) // INPLAY status
                    ->whereNotNull('old_limit')
                    ->orderBy('marketName')
                    ->get()
                    ->map(function ($market) {
                        return (object) [
                            'marketName' => $market->marketName,
                            'old_limit' => $market->old_limit ?? 0,
                        ];
                    })
                    ->toArray();

                $eventData->market_old_limits = $marketOldLimits;

                // Send immediate notification
                $telegramService = new TelegramService();
                $telegramService->sendInterruptionNotification($eventData);
            } catch (\Exception $e) {
                // Log error but don't fail the request
                \Log::error('Failed to send interruption notification', [
                    'exEventId' => $exEventId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Send Telegram notification if interruption is being turned OFF
        if ($isBeingTurnedOff) {
            try {
                // Get sport name from config
                $sports = config('sports.sports', []);
                $sportId = $event->sportId ?? null;
                
                // Get market old limits before clearing (for the message)
                $marketOldLimits = DB::table('market_lists')
                    ->select('marketName', 'old_limit')
                    ->where('exEventId', $exEventId)
                    ->where('status', 3) // INPLAY status
                    ->whereNotNull('old_limit')
                    ->orderBy('marketName')
                    ->get()
                    ->map(function ($market) {
                        return (object) [
                            'marketName' => $market->marketName,
                            'old_limit' => $market->old_limit ?? 0,
                        ];
                    })
                    ->toArray();
                
                // Prepare event data for notification
                $eventData = (object) [
                    'eventId' => $event->eventId ?? null,
                    'exEventId' => $event->exEventId,
                    'eventName' => $event->eventName ?? 'N/A',
                    'sportName' => $sportId && isset($sports[$sportId]) ? $sports[$sportId] : 'Unknown Sport',
                    'tournamentsName' => $event->tournamentsName ?? 'N/A',
                    'market_old_limits' => $marketOldLimits,
                ];

                // Send interruption resolved notification
                $telegramService = new TelegramService();
                $telegramService->sendInterruptionResolvedNotification($eventData);
            } catch (\Exception $e) {
                // Log error but don't fail the request
                \Log::error('Failed to send interruption resolved notification', [
                    'exEventId' => $exEventId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Event settings updated successfully.',
        ]);
    }

    /**
     * Build SQL condition to check if all labels are checked
     */
    private function buildAllLabelsCheckedCondition($labelKeys): string
    {
        $conditions = [];
        foreach ($labelKeys as $key) {
            $escapedKey = str_replace("'", "''", $key);
            $conditions[] = "COALESCE(events.labels->>'{$escapedKey}', 'false') = 'true'";
        }
        
        if (empty($conditions)) {
            return 'false';
        }
        
        return '(' . implode(' AND ', $conditions) . ')';
    }

    public function updateLabels(Request $request, $exEventId)
    {
        $request->validate([
            'labels' => ['required', 'array'],
        ]);

        $event = DB::table('events')
            ->where('exEventId', $exEventId)
            ->first();

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found.',
            ], 404);
        }

        // Normalize labels: ensure all keys are lowercase and boolean values (same as market_lists)
        $labels = $request->input('labels', []);
        $normalizedLabels = [];
        $labelKeys = array_keys(config('labels.labels', []));
        foreach ($labelKeys as $key) {
            $normalizedLabels[strtolower($key)] = isset($labels[$key]) ? (bool) $labels[$key] : false;
        }

        DB::table('events')
            ->where('exEventId', $exEventId)
            ->update([
                'labels' => json_encode($normalizedLabels),
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Labels updated successfully.',
        ]);
    }
}
