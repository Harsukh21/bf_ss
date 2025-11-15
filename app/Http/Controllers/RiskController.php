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
        ]);
    }

    private function buildMarketQuery(array $statuses, array $filters, bool $onlyDone)
    {
        $query = DB::table('market_lists')
            ->select([
                'id',
                'marketName',
                'eventName',
                'tournamentsName',
                'sportName',
                'status',
                'winnerType',
                'selectionName',
                'marketTime',
                'labels',
                'is_done',
                'remark',
                'created_at',
            ])
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
                $q->where('marketName', 'ILIKE', $term)
                    ->orWhere('eventName', 'ILIKE', $term);
            });
        }

        if (!empty($filters['labels'])) {
            foreach ($filters['labels'] as $labelKey) {
                $query->whereRaw("(labels ->> ?)::boolean = true", [$labelKey]);
            }
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
}

