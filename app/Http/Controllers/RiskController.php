<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiskController extends Controller
{
    public function pending(Request $request)
    {
        $filters = $this->buildFilters($request);
        $baseQuery = $this->buildMarketQuery([4, 5], $filters, false);
        $summary = $this->buildSummary($baseQuery);
        $markets = (clone $baseQuery)->paginate(20)->withQueryString();

        return view('risk.pending', [
            'markets' => $markets,
            'statusFilter' => [4, 5],
            'summary' => $summary,
            'filters' => $filters,
            'sports' => $this->getSportsList(),
            'tournamentsBySport' => $this->getTournamentsBySport(),
        ]);
    }

    public function done(Request $request)
    {
        $filters = $this->buildFilters($request);
        $baseQuery = $this->buildMarketQuery([4, 5], $filters, true);
        $summary = $this->buildSummary($baseQuery);
        $markets = (clone $baseQuery)->paginate(20)->withQueryString();

        return view('risk.done', [
            'markets' => $markets,
            'statusFilter' => [4, 5],
            'filters' => $filters,
            'summary' => $summary,
            'sports' => $this->getSportsList(),
            'tournamentsBySport' => $this->getTournamentsBySport(),
        ]);
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
                'market_lists.remark',
                'events.completeTime',
                'market_lists.created_at',
            ])
            ->leftJoin('events', 'events.exEventId', '=', 'market_lists.exEventId')
            ->whereIn('status', $statuses)
            ->when($onlyDone, function ($q) {
                $q->where('is_done', true);
            }, function ($q) {
                $q->where(function ($inner) {
                    $inner->whereNull('is_done')->orWhere('is_done', false);
                });
            });

        if ($filters['sport']) {
            $query->where('sportName', $filters['sport']);
        }

        if ($filters['tournament']) {
            $query->where('tournamentsName', $filters['tournament']);
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
                $query->whereRaw("(labels ->> ?)::boolean = true", [$labelKey]);
            }
        }

        if ($filters['status']) {
            $query->where('market_lists.status', $filters['status']);
        }

        if ($filters['date_from']) {
            $query->whereDate('events.completeTime', '>=', $filters['date_from']);
        }

        if ($filters['date_to']) {
            $query->whereDate('events.completeTime', '<=', $filters['date_to']);
        }

        return $query->orderByDesc('marketTime');
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
            'tournament' => $request->input('tournament'),
            'labels' => $labelFilter,
            'status' => $request->filled('status') && in_array((int) $request->input('status'), [4, 5], true)
                ? (int) $request->input('status')
                : null,
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
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
            'remark' => ['required', 'string', 'max:2000'],
        ]);

        $market = DB::table('market_lists')->where('id', $marketId)->first();

        if (!$market) {
            abort(404);
        }

        $labels = $this->normalizeLabels(json_decode($market->labels ?? '{}', true));
        $allChecked = collect($labels)->every(fn ($value) => (bool) $value === true);

        if (!$allChecked) {
            return response()->json([
                'success' => false,
                'message' => 'Please select all labels before marking as done.',
            ], 422);
        }

        DB::table('market_lists')
            ->where('id', $marketId)
            ->update([
                'is_done' => true,
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
        return ['4x', 'b2c', 'b2b', 'usdt'];
    }

    private function buildSummary($query): array
    {
        return [
            'total' => (clone $query)->count(),
            'settled' => (clone $query)->where('status', 4)->count(),
            'voided' => (clone $query)->where('status', 5)->count(),
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
}

