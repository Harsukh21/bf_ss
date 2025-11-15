<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiskController extends Controller
{
    public function pending(Request $request)
    {
        $filters = $this->buildFilters($request);
        $markets = $this->fetchMarketsByStatus([4, 5], $filters, false);
        $summary = [
            'settled' => $markets->where('status', 4)->count(),
            'voided' => $markets->where('status', 5)->count(),
            'total' => $markets->count(),
        ];

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
        $markets = $this->fetchMarketsByStatus([4, 5], $filters, true);
        $summary = [
            'settled' => $markets->where('status', 4)->count(),
            'voided' => $markets->where('status', 5)->count(),
            'total' => $markets->count(),
        ];

        return view('risk.done', [
            'markets' => $markets,
            'statusFilter' => [4, 5],
            'filters' => $filters,
            'summary' => $summary,
        ]);
    }

    private function fetchMarketsByStatus(array $statuses, array $filters, bool $onlyDone)
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
            })
            ->orderByDesc('marketTime')
            ->limit($filters['limit']);

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

        return $query->get();
    }

    private function buildFilters(Request $request): array
    {
        return [
            'search' => $request->input('search'),
            'sport' => $request->input('sport'),
            'tournament' => $request->input('tournament'),
            'limit' => (int) $request->input('limit', 50),
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
        $default = [
            '4x' => false,
            'b2c' => false,
            'b2b' => false,
            'usdt' => false,
        ];

        if (!is_array($labels)) {
            $labels = [];
        }

        foreach ($default as $key => $value) {
            $default[$key] = (bool) ($labels[$key] ?? $value);
        }

        return $default;
    }
}

