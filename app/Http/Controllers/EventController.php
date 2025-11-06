<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

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

        // Build optimized raw query with specific column selection
        $query = DB::table('events')
            ->select([
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
                'marketTime',
                'createdAt'
            ]);

        // Apply optimized filters with raw DB queries
        $this->applyFilters($query, $request);

        // Get total count for pagination
        $totalCount = (clone $query)->count();
        
        // Apply pagination manually for better performance
        $page = $request->get('page', 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        // Get paginated results using raw query
        $events = $query->orderBy('createdAt', 'desc')
                       ->orderBy('id', 'desc')
                       ->offset($offset)
                       ->limit($perPage)
                       ->get();

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

        // Get sport configuration
        $sportConfig = config('sports.sports');
        
        return view('events.index', compact('paginatedEvents', 'sports', 'tournaments', 'sportConfig', 'tournamentsBySport'));
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
    private function applyFilters($query, Request $request)
    {
        // Optimized search with raw DB queries
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('eventName', 'like', $searchTerm . '%') // Prefix search for better performance
                  ->orWhere('tournamentsName', 'like', $searchTerm . '%');
            });
        }

        // Use exact matches for better performance
        if ($request->filled('sport')) {
            $query->where('sportId', $request->sport);
        }

        if ($request->filled('tournament')) {
            $query->where('tournamentsId', $request->tournament);
        }

        // Optimized status filtering with raw queries
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'settled':
                    $query->where('IsSettle', 1)->where('IsVoid', 0);
                    break;
                case 'void':
                    $query->where('IsVoid', 1);
                    break;
                case 'unsettled':
                    $query->where('IsUnsettle', 1)->where('IsSettle', 0)->where('IsVoid', 0);
                    break;
            }
        }

        // Boolean filters
        if ($request->filled('highlight')) {
            $query->where('highlight', $request->boolean('highlight'));
        }

        if ($request->filled('popular')) {
            $query->where('popular', $request->boolean('popular'));
        }

        // Date range filtering
        if ($request->filled('date_from')) {
            $query->where('createdAt', '>=', $request->date_from . ' 00:00:00');
        }

        if ($request->filled('date_to')) {
            $query->where('createdAt', '<=', $request->date_to . ' 23:59:59');
        }
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
        // Build the same query as index but without pagination
        $query = DB::table('events')
            ->select([
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
                'createdAt'
            ]);

        // Apply the same filters
        $this->applyFilters($query, $request);

        // Get all results (no pagination)
        $events = $query->orderBy('createdAt', 'desc')
                       ->orderBy('id', 'desc')
                       ->get();

        // Get sport configuration for display
        $sportConfig = config('sports.sports');

        // Prepare CSV data
        $filename = 'events_export_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($events, $sportConfig) {
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
                'Created At'
            ]);

            // Add data rows
            foreach ($events as $event) {
                // Determine status
                $status = 'Unsettled';
                if ($event->IsVoid) {
                    $status = 'Void';
                } elseif ($event->IsSettle) {
                    $status = 'Settled';
                } elseif ($event->IsUnsettle) {
                    $status = 'Unsettled';
                }

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
                    $event->createdAt
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
