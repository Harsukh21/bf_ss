<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

        // Get list of all users who have checked checkboxes
        $betlistCheckers = DB::table('market_lists')
            ->select('labels')
            ->whereNotNull('labels')
            ->get()
            ->flatMap(function ($market) {
                $labels = json_decode($market->labels ?? '{}', true);
                $checkers = [];
                if (is_array($labels)) {
                    foreach ($labels as $labelKey => $labelValue) {
                        if (is_array($labelValue) && isset($labelValue['checked']) && $labelValue['checked'] === true && isset($labelValue['checked_by'])) {
                            $checkers[] = $labelValue['checked_by'];
                        }
                    }
                }
                return $checkers;
            })
            ->unique()
            ->filter()
            ->values();

        // Get user details for checkers
        $betlistCheckersList = collect();
        if ($betlistCheckers->isNotEmpty()) {
            $users = DB::table('users')
                ->select('id', 'name', 'email')
                ->whereIn('id', $betlistCheckers->toArray())
                ->orderBy('name')
                ->get()
                ->map(function ($user) {
                    return (object) [
                        'id' => $user->id,
                        'name' => $user->name ?? 'Unknown',
                        'email' => $user->email ?? '',
                    ];
                });
            $betlistCheckersList = $users;
        }

        return view('risk.index', [
            'markets' => $markets,
            'statusFilter' => [4, 5],
            'summary' => $summary,
            'filters' => $filters,
            'sports' => $this->getSportsList(),
            'tournamentsBySport' => $this->getTournamentsBySport(),
            'riskStatusFilter' => $statusFilter,
            'betlistCheckers' => $betlistCheckersList,
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
                'market_lists.chor_id',
                'market_lists.remark',
                'market_lists.completeTime',
                'market_lists.completed_by',
                'market_lists.completed_by_name',
                'market_lists.completed_by_email',
                'market_lists.completed_at',
                'market_lists.created_at',
            ])
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
                // Handle both old format (boolean) and new format (object with checked property)
                // Old format: labels->>'4x' = 'true' (boolean true stored as text 'true')
                // New format: labels->'4x'->>'checked' = 'true' (object with checked property)
                $query->where(function ($q) use ($labelKey) {
                    // Check if value exists and is not null, then check formats
                    $q->whereRaw(
                        "market_lists.labels -> ? IS NOT NULL AND (
                            (jsonb_typeof(market_lists.labels -> ?) = 'boolean' AND (market_lists.labels ->> ?)::boolean = true) OR
                            (jsonb_typeof(market_lists.labels -> ?) = 'object' AND jsonb_typeof(market_lists.labels -> ? -> 'checked') = 'boolean' AND (market_lists.labels -> ? ->> 'checked')::boolean = true) OR
                            (jsonb_typeof(market_lists.labels -> ?) = 'object' AND market_lists.labels -> ? ->> 'checked' = 'true')
                        )",
                        [$labelKey, $labelKey, $labelKey, $labelKey, $labelKey, $labelKey, $labelKey, $labelKey]
                    );
                });
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
            $query->whereBetween('market_lists.completeTime', [
                $startDateTime->format('Y-m-d H:i:s'),
                $endDateTime->format('Y-m-d H:i:s'),
            ]);
        } elseif ($startDateTime) {
            $query->where('market_lists.completeTime', '>=', $startDateTime->format('Y-m-d H:i:s'));
        } elseif ($endDateTime) {
            $query->where('market_lists.completeTime', '<=', $endDateTime->format('Y-m-d H:i:s'));
        } elseif ($filters['date_from'] && !$startDateTime) {
            // Fallback to date-only filtering if date parsing failed
            $query->whereDate('market_lists.completeTime', '>=', $filters['date_from']);
        } elseif ($filters['date_to'] && !$endDateTime) {
            // Fallback to date-only filtering if date parsing failed
            $query->whereDate('market_lists.completeTime', '<=', $filters['date_to']);
        }

        // Filter for recently added (markets closed in the past 30 minutes)
        if (!empty($filters['recently_added'])) {
            $now = \Carbon\Carbon::now();
            $thirtyMinutesAgo = $now->copy()->subMinutes(30);
            
            $query->where(function ($q) use ($now, $thirtyMinutesAgo) {
                // Check completeTime first, fallback to marketTime
                $q->where(function ($subQ) use ($now, $thirtyMinutesAgo) {
                    $subQ->whereNotNull('market_lists.completeTime')
                        ->where('market_lists.completeTime', '>=', $thirtyMinutesAgo->format('Y-m-d H:i:s'))
                        ->where('market_lists.completeTime', '<=', $now->format('Y-m-d H:i:s'));
                })->orWhere(function ($subQ) use ($now, $thirtyMinutesAgo) {
                    $subQ->whereNull('market_lists.completeTime')
                        ->whereNotNull('market_lists.marketTime')
                        ->where('market_lists.marketTime', '>=', $thirtyMinutesAgo->format('Y-m-d H:i:s'))
                        ->where('market_lists.marketTime', '<=', $now->format('Y-m-d H:i:s'));
                });
            });
        }

        // Filter by checked_by (betlist check by)
        if (!empty($filters['checked_by'])) {
            $checkedByUserId = (int) $filters['checked_by'];
            $query->where(function ($q) use ($checkedByUserId) {
                // Check if any label has this user as checked_by
                $labelKeys = $this->getLabelKeys();
                foreach ($labelKeys as $labelKey) {
                    $q->orWhereRaw(
                        "(market_lists.labels -> ? -> 'checked_by')::text = ?",
                        [$labelKey, (string) $checkedByUserId]
                    );
                }
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
            'checked_by' => $request->input('checked_by'), // Betlist check by filter
        ];
    }

    public function updateLabels(Request $request, int $marketId)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.',
            ], 401);
        }

        $market = DB::table('market_lists')->where('id', $marketId)->first();
        if (!$market) {
            return response()->json([
                'success' => false,
                'message' => 'Market not found.',
            ], 404);
        }

        $existingLabels = json_decode($market->labels ?? '{}', true);
        $requestLabels = $request->input('labels', []);
        $labelMetadata = $request->input('label_metadata');
        $isUnchecking = $request->input('is_unchecking', false);
        $checkPermission = $request->input('check_permission', false);
        $labelKey = $request->input('label_key') ?? ($labelMetadata['label_key'] ?? null);

        // If just checking permission (before opening modal)
        if ($checkPermission && $labelKey) {
            if (isset($existingLabels[$labelKey])) {
                $existingValue = $existingLabels[$labelKey];
                $isAlreadyChecked = false;
                $checkedBy = null;
                
                if (is_bool($existingValue) && $existingValue === true) {
                    // Old format - treat as checked but no owner, allow checking
                    return response()->json([
                        'success' => true,
                        'can_check' => true,
                    ]);
                } elseif (is_array($existingValue) && isset($existingValue['checked']) && $existingValue['checked'] === true) {
                    $isAlreadyChecked = true;
                    $checkedBy = $existingValue['checked_by'] ?? null;
                }
                
                if ($isAlreadyChecked && $checkedBy !== null && $checkedBy != $user->id) {
                    $checkerName = $existingValue['checker_name'] ?? 'Another admin';
                    return response()->json([
                        'success' => true,
                        'can_check' => false,
                        'message' => "This checkbox is already checked by {$checkerName}. Only they can uncheck it.",
                    ]);
                }
            }
            
            return response()->json([
                'success' => true,
                'can_check' => true,
            ]);
        }

        // If checking a checkbox with metadata
        if ($labelMetadata && isset($labelMetadata['label_key'])) {
            $labelKey = $labelMetadata['label_key'];
            $webPin = $labelMetadata['web_pin'] ?? '';
            
            // Check if checkbox is already checked by another admin
            if (isset($existingLabels[$labelKey])) {
                $existingValue = $existingLabels[$labelKey];
                $isAlreadyChecked = false;
                $checkedBy = null;
                
                if (is_bool($existingValue) && $existingValue === true) {
                    // Old format - treat as checked but no owner
                    $isAlreadyChecked = true;
                } elseif (is_array($existingValue) && isset($existingValue['checked']) && $existingValue['checked'] === true) {
                    $isAlreadyChecked = true;
                    $checkedBy = $existingValue['checked_by'] ?? null;
                }
                
                if ($isAlreadyChecked && $checkedBy !== null && $checkedBy != $user->id) {
                    $checkerName = $existingValue['checker_name'] ?? 'Another admin';
                    return response()->json([
                        'success' => false,
                        'message' => "This checkbox is already checked by {$checkerName}. Only they can uncheck it.",
                    ], 422);
                }
            }
            
            // Verify web_pin
            $userData = DB::table('users')->where('id', $user->id)->first();
            
            if (empty($userData->web_pin)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Web PIN is not set for your account.',
                ], 422);
            }
            
            $storedPin = $userData->web_pin;
            $isVerified = false;
            
            if (preg_match('/^\$2[ayb]\$.{56}$/', $storedPin)) {
                $isVerified = Hash::check($webPin, $storedPin);
            } else {
                $isVerified = ($webPin === $storedPin);
            }
            
            if (!$isVerified) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Web PIN. Please try again.',
                ], 422);
            }
            
            // Store metadata for this checkbox
            $checkerName = $labelMetadata['checker_name'] ?? auth()->user()->name;
            $chorId = $labelMetadata['chor_id'] ?? null;
            $remark = $labelMetadata['remark'] ?? null;
            
            $labels = $this->normalizeLabels($existingLabels);
            $labels[$labelKey] = [
                'checked' => true,
                'checker_name' => $checkerName,
                'chor_id' => $chorId,
                'remark' => $remark,
                'checked_by' => auth()->id(),
                'checked_at' => now()->toDateTimeString(),
            ];
            
            // Log checkbox submission to system_logs table
            try {
                DB::table('system_logs')->insert([
                    'user_id' => auth()->id(),
                    'action' => 'update_sc_label',
                    'description' => "User {$checkerName} checked label '{$labelKey}' for market '{$market->marketName}' (Event: {$market->eventName}). Chor ID: {$chorId}, Remark: {$remark}",
                    'exEventId' => $market->exEventId ?? null,
                    'label_name' => strtoupper($labelKey),
                    'old_value' => 'unchecked',
                    'new_value' => 'checked',
                    'event_name' => $market->eventName ?? 'N/A',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Log error but don't fail the request
                \Log::error('Failed to log checkbox check to system_logs: ' . $e->getMessage());
            }
        } 
        // If unchecking a checkbox
        elseif ($isUnchecking && $labelKey) {
            // Check if checkbox exists and is checked
            if (!isset($existingLabels[$labelKey])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Checkbox is not checked.',
                ], 422);
            }
            
            $existingValue = $existingLabels[$labelKey];
            $isChecked = false;
            $checkedBy = null;
            
            if (is_bool($existingValue) && $existingValue === true) {
                // Old format - allow unchecking
                $isChecked = true;
            } elseif (is_array($existingValue) && isset($existingValue['checked']) && $existingValue['checked'] === true) {
                $isChecked = true;
                $checkedBy = $existingValue['checked_by'] ?? null;
            }
            
            if (!$isChecked) {
                return response()->json([
                    'success' => false,
                    'message' => 'Checkbox is not checked.',
                ], 422);
            }
            
            // Verify that current user is the one who checked it
            if ($checkedBy !== null && $checkedBy != $user->id) {
                $checkerName = $existingValue['checker_name'] ?? 'Another admin';
                return response()->json([
                    'success' => false,
                    'message' => "Only {$checkerName} (who checked this) can uncheck it.",
                ], 422);
            }
            
            // Uncheck the checkbox
            $labels = $this->normalizeLabels($existingLabels);
            $labels[$labelKey] = false;
        } 
        // Regular label update (for backward compatibility)
        else {
            $labels = $this->normalizeLabels($requestLabels);
        }

        // Helper function to check if label is checked (handles both boolean and object formats)
        $isLabelChecked = function($value) {
            if (is_bool($value)) {
                return $value === true;
            }
            if (is_array($value) && isset($value['checked'])) {
                return (bool) $value['checked'];
            }
            return false;
        };

        // Check if all 4 required labels are checked: 4x, b2c, b2b, usdt
        $requiredLabelKeys = ['4x', 'b2c', 'b2b', 'usdt'];
        $allRequiredChecked = true;
        foreach ($requiredLabelKeys as $key) {
            // Check if label exists and is checked
            if (!isset($labels[$key])) {
                $allRequiredChecked = false;
                break;
            }
            $labelValue = $labels[$key];
            if (!$isLabelChecked($labelValue)) {
                $allRequiredChecked = false;
                break;
            }
        }

        // Prepare update data
        $updateData = [
            'labels' => json_encode($labels),
            'updated_at' => now(),
        ];

        // Don't automatically set is_done - user must click Complete button to mark as done
        // This allows users to uncheck checkboxes even after all 4 are checked
        // Only update labels, not is_done status

        DB::table('market_lists')
            ->where('id', $marketId)
            ->update($updateData);

        return response()->json([
            'success' => true,
            'labels' => $labels,
            'is_done' => $market->is_done ?? false, // Return current status, don't change it
            'all_required_checked' => $allRequiredChecked,
        ]);
    }

    public function markDone(Request $request, int $marketId)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'chor_id' => ['required', 'string'],
            'remark' => ['required', 'string', 'max:2000'],
            'web_pin' => ['required', 'string'],
        ]);

        // Verify web_pin
        $webPin = $request->input('web_pin');
        $user = auth()->user();
        
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
            // PIN is hashed, use Hash::check
            $isVerified = \Illuminate\Support\Facades\Hash::check($webPin, $storedPin);
        } else {
            // PIN is plain text (backward compatibility)
            $isVerified = ($webPin === $storedPin);
        }
        
        if (!$isVerified) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Web PIN. Please try again.',
            ], 422);
        }

        $market = DB::table('market_lists')->where('id', $marketId)->first();

        if (!$market) {
            abort(404);
        }

        $labels = $this->normalizeLabels(json_decode($market->labels ?? '{}', true));
        // Only first 4 labels are required: 4x, b2c, b2b, usdt
        $requiredLabelKeys = ['4x', 'b2c', 'b2b', 'usdt'];
        $allRequiredChecked = collect($requiredLabelKeys)->every(function($key) use ($labels) {
            return $this->isLabelChecked($labels[$key] ?? false);
        });

        if (!$allRequiredChecked) {
            return response()->json([
                'success' => false,
                'message' => 'Please select all required labels (4X, B2C, B2B, USDT) before marking as done.',
            ], 422);
        }

        // Store admin information who completed the market
        DB::table('market_lists')
            ->where('id', $marketId)
            ->update([
                'is_done' => true,
                'name' => $request->input('name'),
                'chor_id' => $request->input('chor_id'),
                'remark' => $request->input('remark'),
                'completed_by' => $user->id,
                'completed_by_name' => $user->name,
                'completed_by_email' => $user->email,
                'completed_at' => now(),
                'updated_at' => now(),
            ]);

        // Log to system_logs
        try {
            DB::table('system_logs')->insert([
                'user_id' => $user->id,
                'action' => 'market_marked_as_done',
                'description' => "Market '{$market->marketName}' (Event: {$market->eventName}) marked as completed by {$user->name} ({$user->email}).",
                'exEventId' => $market->exEventId ?? null,
                'label_name' => null,
                'old_value' => 'pending',
                'new_value' => 'completed',
                'event_name' => $market->eventName ?? 'N/A',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::error('Failed to log market completion to system_logs: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Market marked as done.',
        ]);
    }

    public function sendTelegram(Request $request, int $marketId)
    {
        $market = DB::table('market_lists')->where('id', $marketId)->first();

        if (!$market) {
            return response()->json([
                'success' => false,
                'message' => 'Market not found.',
            ], 404);
        }

        $labels = $this->normalizeLabels(json_decode($market->labels ?? '{}', true));
        $requiredLabelKeys = ['4x', 'b2c', 'b2b', 'usdt'];
        
        // Check if all required labels are checked
        $allRequiredChecked = collect($requiredLabelKeys)->every(function($key) use ($labels) {
            return $this->isLabelChecked($labels[$key] ?? false);
        });

        if (!$allRequiredChecked) {
            return response()->json([
                'success' => false,
                'message' => 'Please select all required labels (4X, B2C, B2B, USDT) before sending to Telegram.',
            ], 422);
        }

        // Build message with label information
        $labelLines = [];
        foreach ($requiredLabelKeys as $key) {
            $label = $labels[$key] ?? false;
            if ($this->isLabelChecked($label)) {
                $labelName = strtoupper($key);
                $checkerName = is_array($label) && isset($label['checker_name']) ? $label['checker_name'] : 'N/A';
                $chorId = is_array($label) && isset($label['chor_id']) && !empty($label['chor_id']) && strtoupper($label['chor_id']) !== 'NULL' ? $label['chor_id'] : 'N/A';
                $remark = is_array($label) && isset($label['remark']) && !empty($label['remark']) ? $label['remark'] : 'N/A';
                
                $labelLines[] = "🏷 Label: {$labelName}";
                $labelLines[] = "👤Checker: {$checkerName}";
                $labelLines[] = "🆔 Chor ID: {$chorId}";
                $labelLines[] = "🗒 Remark: {$remark}";
                $labelLines[] = ""; // Empty line between labels
            }
        }
        
        // Remove last empty line
        if (end($labelLines) === "") {
            array_pop($labelLines);
        }

        $message = "🚨🚨🚨 Fraudulent Activity Notification 🚨🚨🚨\n\n";
        $message .= "📊 Event Details\n\n";
        $message .= "📍 Event Name: {$market->eventName}\n";
        $message .= "🏢 Market Name: {$market->marketName}\n\n";
        $message .= implode("\n", $labelLines);

        // Get Telegram bot token and chat ID from config
        $botToken = config('services.telegram.bot_token');
        $chatId = config('services.telegram.froud_chat_id');

        if (empty($botToken) || empty($chatId)) {
            return response()->json([
                'success' => false,
                'message' => 'Telegram bot configuration is missing. Please check your .env file.',
            ], 500);
        }

        // Send message to Telegram
        $telegramUrl = "https://api.telegram.org/bot{$botToken}/sendMessage";
        
        $response = \Illuminate\Support\Facades\Http::post($telegramUrl, [
            'chat_id' => $chatId,
            'text' => $message,
        ]);

        if ($response->successful()) {
            // Log to system_logs
            try {
                $user = auth()->user();
                DB::table('system_logs')->insert([
                    'user_id' => $user->id ?? null,
                    'action' => 'telegram_fraud_notification_sent',
                    'description' => "Fraudulent activity notification sent to Telegram for market '{$market->marketName}' (Event: {$market->eventName}) by " . ($user->name ?? 'System'),
                    'exEventId' => $market->exEventId ?? null,
                    'label_name' => null,
                    'old_value' => null,
                    'new_value' => 'telegram_sent',
                    'event_name' => $market->eventName ?? 'N/A',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to log Telegram notification to system_logs: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Notification sent to Telegram successfully.',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification to Telegram. Please try again.',
            ], 500);
        }
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
            if (isset($labels[$key])) {
                // Handle both formats: boolean (old) and object (new)
                if (is_bool($labels[$key])) {
                    // Old format: simple boolean - keep as boolean for backward compatibility
                    $default[$key] = $labels[$key];
                } elseif (is_array($labels[$key]) && isset($labels[$key]['checked'])) {
                    // New format: object with metadata - keep full object
                    $default[$key] = $labels[$key];
                } else {
                    // Invalid format - default to false
                    $default[$key] = false;
                }
            } else {
                $default[$key] = false;
            }
        }

        return $default;
    }
    
    /**
     * Get checkbox checked state (handles both boolean and object formats)
     */
    private function isLabelChecked($labelValue): bool
    {
        if (is_bool($labelValue)) {
            return $labelValue;
        }
        if (is_array($labelValue) && isset($labelValue['checked'])) {
            return (bool) $labelValue['checked'];
        }
        return false;
    }
    
    /**
     * Get label metadata if available
     */
    private function getLabelMetadata($labelValue): ?array
    {
        if (is_array($labelValue) && isset($labelValue['checked']) && $labelValue['checked']) {
            return $labelValue;
        }
        return null;
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

    public function export(Request $request)
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
            $sportOrder = $sportPriority[$sportName] ?? 999;
            $timeField = !empty($market->completeTime) ? $market->completeTime : $market->marketTime;
            $timeValue = $timeField ? strtotime($timeField) : 0;
            return [$sportOrder, -$timeValue];
        });
        
        $doneMarkets = $doneMarkets->sortBy(function ($market) use ($sportPriority) {
            $sportName = $market->sportName ?? '';
            $sportOrder = $sportPriority[$sportName] ?? 999;
            $timeField = !empty($market->completeTime) ? $market->completeTime : $market->marketTime;
            $timeValue = $timeField ? strtotime($timeField) : 0;
            return [$sportOrder, -$timeValue];
        });
        
        // Combine: pending first, then done
        $sortedMarkets = $pendingMarkets->concat($doneMarkets);
        
        // Prepare CSV data
        $filename = 'betlist_check_export_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($sortedMarkets) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'Event & Market',
                'Sport & Tournament & Status & Winner',
                'Checker',
                'Froude IDs',
                'Remarks'
            ]);
            
            $labelOptions = config('labels.labels', [
                '4x' => '4X',
                'b2c' => 'B2C',
                'b2b' => 'B2B',
                'usdt' => 'USDT',
            ]);
            
            // Helper function to check if label is checked
            $isLabelChecked = function($value) {
                if (is_bool($value)) {
                    return $value === true;
                }
                if (is_array($value) && isset($value['checked'])) {
                    return (bool) $value['checked'];
                }
                return false;
            };
            
            foreach ($sortedMarkets as $market) {
                // Decode labels
                $decodedLabels = json_decode($market->labels ?? '{}', true);
                $labelKeys = array_keys($labelOptions);
                $defaultLabels = array_fill_keys($labelKeys, false);
                $labelStates = array_merge($defaultLabels, is_array($decodedLabels) ? array_intersect_key($decodedLabels, $defaultLabels) : []);
                
                // Event & Market
                $eventMarket = trim($market->eventName ?? '') . ' / ' . trim($market->marketName ?? '');
                
                // Sport & Tournament & Status & Winner
                $sport = $market->sportName ?? 'N/A';
                $tournament = $market->tournamentsName ?? 'N/A';
                $statusMap = [
                    4 => 'Settled',
                    5 => 'Voided',
                ];
                $status = $statusMap[$market->status] ?? 'Unknown';
                $winner = !empty($market->selectionName) ? 'Winner: ' . $market->selectionName : '';
                $sportTournamentStatusWinner = $sport . ' / ' . $tournament . ' / ' . $status . ($winner ? ' / ' . $winner : '');
                
                // Checker - collect all checker names from checked labels
                $checkerData = [];
                foreach ($labelStates as $key => $value) {
                    if ($isLabelChecked($value)) {
                        if (is_array($value) && isset($value['checker_name']) && !empty($value['checker_name'])) {
                            $checkerData[] = strtoupper($key) . ' : ' . $value['checker_name'];
                        } elseif (is_bool($value) && $value === true) {
                            $checkerData[] = strtoupper($key) . ' : —';
                        }
                    }
                }
                $checker = !empty($checkerData) ? implode(' | ', $checkerData) : '—';
                
                // Froude IDs (Chor IDs) - collect all chor_ids from checked labels
                $chorIdData = [];
                foreach ($labelStates as $key => $value) {
                    if ($isLabelChecked($value)) {
                        if (is_array($value) && isset($value['chor_id']) && !empty($value['chor_id'])) {
                            $chorIdData[] = strtoupper($key) . ' : ' . $value['chor_id'];
                        } elseif (is_bool($value) && $value === true) {
                            $chorIdData[] = strtoupper($key) . ' : —';
                        }
                    }
                }
                $froudeIds = !empty($chorIdData) ? implode(' | ', $chorIdData) : '—';
                
                // Remarks - collect all remarks from checked labels
                $remarkData = [];
                foreach ($labelStates as $key => $value) {
                    if ($isLabelChecked($value)) {
                        if (is_array($value) && isset($value['remark']) && !empty($value['remark'])) {
                            $remarkData[] = strtoupper($key) . ' : ' . $value['remark'];
                        } elseif (is_bool($value) && $value === true) {
                            $remarkData[] = strtoupper($key) . ' : —';
                        }
                    }
                }
                $remarks = !empty($remarkData) ? implode(' | ', $remarkData) : '—';
                
                // Write CSV row
                fputcsv($file, [
                    $eventMarket,
                    $sportTournamentStatusWinner,
                    $checker,
                    $froudeIds,
                    $remarks
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}

