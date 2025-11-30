<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Services\TelegramService;
use App\Models\User;

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
                'events.label_timestamps',
                'events.sc_type',
                'events.remind_me_after',
                'events.new_limit',
                DB::raw('COUNT(DISTINCT "market_lists"."id") as inplay_markets_count'),
                DB::raw('MIN("market_lists"."marketTime") as first_market_time'),
            ])
            ->leftJoin('market_lists', function($join) {
                $join->on('market_lists.exEventId', '=', 'events.exEventId')
                     ->where('market_lists.status', 3); // INPLAY status
            })
            ->where(function($query) {
                $query->where(function($q) {
                    // Show interrupted events (regardless of market status or settle/void status)
                    $q->where('events.is_interrupted', true);
                })->orWhere(function($q) {
                    // Show non-interrupted events that have INPLAY markets and are not settled/void
                    $q->whereNotNull('market_lists.id') // Must have INPLAY markets
                      ->where('events.IsSettle', 0)
                      ->where('events.IsVoid', 0)
                      ->where(function($subQ) {
                          $subQ->where('events.is_interrupted', false)
                                ->orWhereNull('events.is_interrupted');
                      });
                });
            });

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
            $dateInput = $request->input('date_from');
            // Try to parse DD/MM/YYYY format first, fallback to standard parse
            try {
                $dateFrom = Carbon::createFromFormat('d/m/Y', $dateInput)->startOfDay();
            } catch (\Exception $e) {
                // Fallback to Carbon::parse for other formats (Y-m-d, etc.)
                $dateFrom = Carbon::parse($dateInput)->startOfDay();
            }
            $query->where('events.marketTime', '>=', $dateFrom);
        }

        if ($request->filled('date_to')) {
            $dateInput = $request->input('date_to');
            // Try to parse DD/MM/YYYY format first, fallback to standard parse
            try {
                $dateTo = Carbon::createFromFormat('d/m/Y', $dateInput)->endOfDay();
            } catch (\Exception $e) {
                // Fallback to Carbon::parse for other formats (Y-m-d, etc.)
                $dateTo = Carbon::parse($dateInput)->endOfDay();
            }
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

        // Get label keys for checking required labels (first 4: 4X, B2C, B2B, USDT)
        $labelConfig = config('labels.labels', []);
        $labelKeys = array_keys($labelConfig);
        $allLabelKeys = array_map('strtolower', $labelKeys); // Labels stored with lowercase keys
        
        // Only check the first 4 required labels: 4x, b2c, b2b, usdt
        $requiredLabelKeys = ['4x', 'b2c', 'b2b', 'usdt'];
        
        // Build custom ordering:
        // 1. is_interrupted = true first
        // 2. Then in-play (marketTime <= now, currently active)
        // 3. Then upcoming (marketTime > now, future in-play)
        // 4. Then events WITHOUT all required labels checked (events with all required labels checked will appear later)
        $now = Carbon::now();
        $nowStr = $now->format('Y-m-d H:i:s');
        
        // Build condition for required labels checked (only first 4)
        $allLabelsCondition = $this->buildAllLabelsCheckedCondition($requiredLabelKeys);
        
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
                'events.label_timestamps',
                'events.sc_type',
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
                    WHEN ' . $allLabelsCondition . ' THEN 2
                    ELSE 1
                END as sort_all_labels
            ', [$nowStr, $nowStr])
            ->orderBy('sort_interrupted', 'asc') // 1. is_interrupted = true first
            ->orderBy('sort_time_status', 'asc') // 2. In-play (current) first, then upcoming
            ->orderBy('sort_all_labels', 'asc') // 3. Events WITHOUT all required labels (4X, B2C, B2B, USDT) checked first (1 before 2)
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
            
            // Parse label timestamps from events table (JSONB)
            // Timestamps are stored as: {"4x":"2025-11-27 15:30:00","b2c":null,"b2b":null,"usdt":null} (lowercase keys)
            $eventLabelTimestamps = [];
            if ($event->label_timestamps) {
                $timestamps = is_string($event->label_timestamps) ? json_decode($event->label_timestamps, true) : $event->label_timestamps;
                if (is_array($timestamps)) {
                    $eventLabelTimestamps = $timestamps;
                }
            }
            
            // Normalize labels: ensure all label keys exist with default false value
            // Keys in DB are lowercase (e.g., "4x"), but we use original keys from config for display
            $parsedLabels = [];
            $parsedLabelTimestamps = [];
            foreach ($labelKeys as $labelKey) {
                // Check lowercase key (stored in DB) first, fallback to original key for backward compatibility
                $dbKey = strtolower($labelKey);
                $parsedLabels[$labelKey] = (isset($eventLabels[$dbKey]) && (bool)$eventLabels[$dbKey] === true)
                    || (isset($eventLabels[$labelKey]) && (bool)$eventLabels[$labelKey] === true);
                
                // Parse timestamp (use lowercase key from DB)
                $parsedLabelTimestamps[$labelKey] = $eventLabelTimestamps[$dbKey] ?? $eventLabelTimestamps[$labelKey] ?? null;
            }
            
            $event->labels = $parsedLabels;
            $event->label_timestamps = $parsedLabelTimestamps;
            
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
            
            // Get existing labels and timestamps
            $existingLabels = json_decode($event->labels ?? '{}', true);
            $existingTimestamps = json_decode($event->label_timestamps ?? '{}', true);
            
            // Prepare normalized labels and timestamps
            $normalizedTimestamps = [];
            $now = now()->format('Y-m-d H:i:s');
            
            $labelConfig = config('labels.labels', []);
            $user = Auth::user();
            
            foreach ($labelKeys as $key) {
                $dbKey = strtolower($key);
                // Preserve existing label value if not provided in request
                $existingValue = $existingLabels[$dbKey] ?? false;
                
                // Use provided value if exists, otherwise keep existing value
                $isChecked = isset($labels[$key]) ? (bool) $labels[$key] : $existingValue;
                $wasChecked = $existingValue;
                
                $normalizedLabels[$dbKey] = $isChecked;
                
                // Log label change if value changed and user is authenticated
                if ($isChecked !== $wasChecked && $user && isset($labels[$key])) {
                    $this->logLabelChange(
                        $exEventId,
                        $event->eventName ?? 'Unknown Event',
                        $key,
                        $labelConfig[$key] ?? strtoupper($key),
                        $wasChecked ? 'Checked' : 'Unchecked',
                        $isChecked ? 'Checked' : 'Unchecked',
                        $request
                    );
                }
                
                // Update timestamp: set when checked, clear when unchecked
                if ($isChecked && !$wasChecked) {
                    // Just checked - set timestamp
                    $normalizedTimestamps[$dbKey] = $now;
                } elseif ($isChecked && $wasChecked) {
                    // Already checked - preserve existing timestamp
                    $normalizedTimestamps[$dbKey] = $existingTimestamps[$dbKey] ?? $now;
                } else {
                    // Unchecked - set to null
                    $normalizedTimestamps[$dbKey] = null;
                }
            }
            
            $updateData['labels'] = json_encode($normalizedLabels);
            $updateData['label_timestamps'] = json_encode($normalizedTimestamps);
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

    /**
     * Log label change to system_logs table
     */
    private function logLabelChange($exEventId, $eventName, $labelKey, $labelName, $oldValue, $newValue, Request $request)
    {
        try {
            $user = Auth::user();
            $userName = $user ? ($user->name ?? 'Unknown') : 'Unknown';
            $userEmail = $user ? ($user->email ?? 'N/A') : 'N/A';
            
            DB::table('system_logs')->insert([
                'user_id' => $user ? $user->id : null,
                'action' => 'update_label',
                'description' => "User {$userName} ({$userEmail}) updated label '{$labelName}' from '{$oldValue}' to '{$newValue}' for event {$exEventId}",
                'exEventId' => $exEventId,
                'label_name' => $labelName,
                'old_value' => $oldValue,
                'new_value' => $newValue,
                'event_name' => $eventName,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::error('Failed to log label change: ' . $e->getMessage(), [
                'exEventId' => $exEventId,
                'labelKey' => $labelKey,
                'error' => $e->getMessage(),
            ]);
        }
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
        
        // Get existing labels and timestamps
        $existingLabels = json_decode($event->labels ?? '{}', true);
        $existingTimestamps = json_decode($event->label_timestamps ?? '{}', true);
        
        // Check if BOOKMAKER or UNMATCH are being checked - require web_pin verification
        $pinRequiredLabels = ['bookmaker', 'unmatch'];
        $needsPinVerification = false;
        $labelsToVerify = [];
        
        foreach ($pinRequiredLabels as $pinLabel) {
            $isChecked = isset($labels[$pinLabel]) ? (bool) $labels[$pinLabel] : false;
            $wasChecked = isset($existingLabels[$pinLabel]) ? (bool) $existingLabels[$pinLabel] : false;
            
            // If trying to check (not uncheck) BOOKMAKER or UNMATCH, require PIN
            if ($isChecked && !$wasChecked) {
                $needsPinVerification = true;
                $labelsToVerify[] = $pinLabel;
            }
        }
        
        // If PIN is required but not provided or invalid, reject the update
        if ($needsPinVerification) {
            if (!$request->has('web_pin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Web PIN is required to check BOOKMAKER or UNMATCH labels.',
                    'requires_pin' => true,
                    'labels' => $labelsToVerify,
                ], 422);
            }
            
            // Verify web_pin
            $webPin = $request->input('web_pin');
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated.',
                ], 401);
            }
            
            // Get user's web_pin from database
            $userData = DB::table('users')->where('id', $user->id)->first();
            
            if (empty($userData->web_pin)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Web PIN is not set for your account.',
                    'requires_pin' => true,
                ], 422);
            }
            
            // Verify web_pin
            $storedPin = $userData->web_pin;
            $isVerified = false;
            
            // Check if stored PIN is hashed
            if (preg_match('/^\$2[ayb]\$.{56}$/', $storedPin)) {
                // Hashed PIN - use Hash::check
                $isVerified = Hash::check($webPin, $storedPin);
            } else {
                // Plain text PIN - direct comparison
                $isVerified = $webPin === $storedPin;
            }
            
            if (!$isVerified) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Web PIN. Labels were not updated.',
                    'requires_pin' => true,
                    'labels' => $labelsToVerify,
                ], 422);
            }
        }
        
        // Prepare normalized labels and timestamps
        $normalizedTimestamps = [];
        $now = now()->format('Y-m-d H:i:s');
        
        $labelConfig = config('labels.labels', []);
        $user = Auth::user();
        
        foreach ($labelKeys as $key) {
            $dbKey = strtolower($key);
            $isChecked = isset($labels[$key]) ? (bool) $labels[$key] : false;
            $wasChecked = isset($existingLabels[$dbKey]) ? (bool) $existingLabels[$dbKey] : false;
            
            $normalizedLabels[$dbKey] = $isChecked;
            
            // Log label change if value changed
            if ($isChecked !== $wasChecked && $user) {
                $this->logLabelChange(
                    $exEventId,
                    $event->eventName ?? 'Unknown Event',
                    $key,
                    $labelConfig[$key] ?? strtoupper($key),
                    $wasChecked ? 'Checked' : 'Unchecked',
                    $isChecked ? 'Checked' : 'Unchecked',
                    $request
                );
            }
            
            // Update timestamp: set when checked, clear when unchecked
            if ($isChecked && !$wasChecked) {
                // Just checked - set timestamp
                $normalizedTimestamps[$dbKey] = $now;
            } elseif ($isChecked && $wasChecked) {
                // Already checked - preserve existing timestamp
                $normalizedTimestamps[$dbKey] = $existingTimestamps[$dbKey] ?? $now;
            } else {
                // Unchecked - set to null
                $normalizedTimestamps[$dbKey] = null;
            }
        }

        DB::table('events')
            ->where('exEventId', $exEventId)
            ->update([
                'labels' => json_encode($normalizedLabels),
                'label_timestamps' => json_encode($normalizedTimestamps),
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Labels updated successfully.',
        ]);
    }

    public function updateScType(Request $request, $exEventId)
    {
        $request->validate([
            'sc_type' => ['required', 'string', 'in:Sportradar,Old SC(Cric),SR Premium,SpreadeX'],
            'web_pin' => ['required', 'string', 'regex:/^[0-9]+$/', 'min:6'],
        ], [
            'sc_type.required' => 'SC Type is required.',
            'sc_type.in' => 'Invalid SC Type selected.',
            'web_pin.required' => 'Web PIN is required.',
            'web_pin.regex' => 'Web PIN must contain only numbers.',
            'web_pin.min' => 'Web PIN must be at least 6 digits.',
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

        // Verify web_pin and check if user is admin
        $webPin = $request->input('web_pin');
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.',
            ], 401);
        }

        // Get user's web_pin from database
        $userData = DB::table('users')->where('id', $user->id)->first();
        
        if (empty($userData->web_pin)) {
            return response()->json([
                'success' => false,
                'message' => 'Web PIN is not set for your account.',
            ], 422);
        }

        // Verify web_pin
        $storedPin = $userData->web_pin;
        $isVerified = false;

        // Check if stored PIN is hashed
        if (preg_match('/^\$2[ayb]\$.{56}$/', $storedPin)) {
            // Hashed PIN - use Hash::check
            $isVerified = Hash::check($webPin, $storedPin);
        } else {
            // Plain text PIN - direct comparison
            $isVerified = $webPin === $storedPin;
        }

        if (!$isVerified) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Web PIN.',
            ], 422);
        }

        // Update sc_type
        DB::table('events')
            ->where('exEventId', $exEventId)
            ->update([
                'sc_type' => $request->input('sc_type'),
                'updated_at' => now(),
            ]);

        // Log the action (wrap in try-catch to prevent errors if logging fails)
        try {
            DB::table('system_logs')->insert([
                'user_id' => $user->id,
                'action' => 'update_sc_type',
                'description' => "Admin {$user->name} ({$user->email}) updated SC Type to '{$request->input('sc_type')}' for event {$exEventId}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::error('Failed to log SC Type update: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'SC Type updated successfully.',
        ]);
    }

    public function updateNewLimit(Request $request, $exEventId)
    {
        $request->validate([
            'new_limit' => ['required', 'numeric', 'min:0'],
            'web_pin' => ['required', 'string', 'regex:/^[0-9]+$/', 'min:6'],
        ], [
            'new_limit.required' => 'New Limit is required.',
            'new_limit.numeric' => 'New Limit must be a number.',
            'new_limit.min' => 'New Limit must be greater than or equal to 0.',
            'web_pin.required' => 'Web PIN is required.',
            'web_pin.regex' => 'Web PIN must contain only numbers.',
            'web_pin.min' => 'Web PIN must be at least 6 digits.',
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

        // Verify web_pin and check if user is admin
        $webPin = $request->input('web_pin');
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.',
            ], 401);
        }

        // Get user's web_pin from database
        $userData = DB::table('users')->where('id', $user->id)->first();
        
        if (empty($userData->web_pin)) {
            return response()->json([
                'success' => false,
                'message' => 'Web PIN is not set for your account.',
            ], 422);
        }

        // Verify web_pin
        $storedPin = $userData->web_pin;
        $isVerified = false;

        // Check if stored PIN is hashed
        if (preg_match('/^\$2[ayb]\$.{56}$/', $storedPin)) {
            // Hashed PIN - use Hash::check
            $isVerified = Hash::check($webPin, $storedPin);
        } else {
            // Plain text PIN - direct comparison
            $isVerified = $webPin === $storedPin;
        }

        if (!$isVerified) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Web PIN.',
            ], 422);
        }

        // Verify that all 4 required labels are checked
        $requiredLabelKeys = ['4x', 'b2c', 'b2b', 'usdt'];
        $existingLabels = json_decode($event->labels ?? '{}', true);
        
        $allRequiredChecked = true;
        foreach ($requiredLabelKeys as $labelKey) {
            if (!isset($existingLabels[$labelKey]) || !$existingLabels[$labelKey]) {
                $allRequiredChecked = false;
                break;
            }
        }

        if (!$allRequiredChecked) {
            return response()->json([
                'success' => false,
                'message' => 'All required labels (4X, B2C, B2B, USDT) must be checked before setting New Limit.',
            ], 400);
        }

        // Get old value BEFORE updating
        $oldValue = $event->new_limit !== null ? (string) $event->new_limit : null;
        $newValue = (string) $request->input('new_limit');

        // Update new_limit
        DB::table('events')
            ->where('exEventId', $exEventId)
            ->update([
                'new_limit' => (int) $request->input('new_limit'),
                'updated_at' => now(),
            ]);

        // Log the action
        try {
            DB::table('system_logs')->insert([
                'user_id' => $user->id,
                'exEventId' => $exEventId,
                'action' => 'update_new_limit',
                'description' => $oldValue 
                    ? "Admin {$user->name} ({$user->email}) updated New Limit from '{$oldValue}' to '{$newValue}' for event {$exEventId}"
                    : "Admin {$user->name} ({$user->email}) set New Limit to '{$newValue}' for event {$exEventId}",
                'event_name' => $event->eventName ?? null,
                'old_value' => $oldValue,
                'new_value' => $newValue,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to log New Limit update: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'New Limit updated successfully.',
        ]);
    }
}
