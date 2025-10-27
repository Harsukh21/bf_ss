<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;

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

        $marketTypes = Cache::remember('markets.types', 300, function () {
            return DB::table('market_lists')
                ->select('type', 'tournamentsName')
                ->distinct()
                ->orderBy('type')
                ->get();
        });

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
        $marketTypesByTournament = Cache::remember('markets.types_by_tournament', 300, function () {
            return DB::table('market_lists')
                ->select('type', 'tournamentsName')
                ->distinct()
                ->orderBy('type')
                ->get()
                ->groupBy('tournamentsName');
        });

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

        return view('markets.index', compact(
            'paginatedMarkets',
            'sports',
            'tournaments',
            'marketTypes',
            'activeFilters',
            'tournamentsBySport',
            'marketTypesByTournament'
        ));
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

        // Market type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Live filter
        if ($request->has('is_live')) {
            $query->where('isLive', true);
        }

        // Pre-bet filter
        if ($request->has('is_prebet')) {
            $query->where('isPreBet', true);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('marketTime', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('marketTime', '<=', $request->date_to);
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

        if ($request->filled('type')) {
            $activeFilters['Type'] = $request->type;
        }

        if ($request->has('is_live')) {
            $activeFilters['Live'] = 'Yes';
        }

        if ($request->has('is_prebet')) {
            $activeFilters['Pre-bet'] = 'Yes';
        }

        if ($request->filled('date_from')) {
            $activeFilters['From Date'] = $request->date_from;
        }

        if ($request->filled('date_to')) {
            $activeFilters['To Date'] = $request->date_to;
        }

        if ($request->filled('search')) {
            $activeFilters['Search'] = $request->search;
        }

        return $activeFilters;
    }
}
