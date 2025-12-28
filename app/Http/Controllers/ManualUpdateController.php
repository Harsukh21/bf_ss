<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class ManualUpdateController extends Controller
{
    private const STATUS_LABELS = [
        1 => 'UNSETTLED',
        2 => 'UPCOMING',
        3 => 'INPLAY',
        4 => 'SETTLED',
        5 => 'VOIDED',
        6 => 'REMOVED',
    ];

    private const REDIS_MARKET_IDS = 'cache:market:exMarketIds';
    private const REDIS_INPLAY_PAIRS = 'cache:marketprice:inplayPairs';
    private const REDIS_PREBET_PAIRS = 'cache:marketprice:prebetPairs';
    private const REDIS_INPLAY_FREEZE_WATCH = 'cache:marketprice:inplayFreezeWatch';
    private const REDIS_MARKET_MISSING = 'cache:marketprice:missing';
    private const REDIS_EVENT_SPORT_MAP = 'cache:event:sportMap';
    private const REDIS_MARKET_META_PREFIX = 'market:meta:';

    public function index(Request $request)
    {
        $filters = [
            'exMarketId' => trim((string) $request->query('exMarketId', '')),
            'exEventId' => trim((string) $request->query('exEventId', '')),
            'eventName' => trim((string) $request->query('eventName', '')),
        ];

        $hasFilters = !empty(array_filter($filters, static fn ($value) => $value !== ''));
        $markets = null;

        if ($hasFilters) {
            $query = DB::table('market_lists')
                ->select([
                    'id',
                    'exMarketId',
                    'exEventId',
                    'eventName',
                    'marketName',
                    'status',
                    'winnerType',
                    'selectionName',
                    'marketTime',
                    'isCompleted',
                    'completeTime',
                ]);

            if ($filters['exMarketId'] !== '') {
                $query->where('exMarketId', $filters['exMarketId']);
            }

            if ($filters['exEventId'] !== '') {
                $query->where('exEventId', $filters['exEventId']);
            }

            if ($filters['eventName'] !== '') {
                $query->where('eventName', 'ILIKE', '%' . $filters['eventName'] . '%');
            }

            $markets = $query
                ->orderBy('marketTime', 'desc')
                ->orderBy('id', 'desc')
                ->paginate(20)
                ->appends($request->query());
        }

        return view('manual-update.index', [
            'filters' => $filters,
            'markets' => $markets,
            'statusLabels' => self::STATUS_LABELS,
            'hasFilters' => $hasFilters,
        ]);
    }

    public function view(Request $request, ?string $exMarketId = null)
    {
        $exMarketId = trim((string) ($exMarketId ?? $request->query('exMarketId', '')));
        $id = $request->query('id');

        $query = DB::table('market_lists');

        if ($exMarketId !== '') {
            $query->where('exMarketId', $exMarketId);
        } elseif (!empty($id)) {
            $query->where('id', $id);
        } else {
            return redirect()
                ->route('manual-update.index')
                ->withErrors(['exMarketId' => 'Provide an exMarketId or market id to view details.']);
        }

        $market = $query->first();

        if (!$market) {
            return redirect()
                ->route('manual-update.index')
                ->withErrors(['exMarketId' => 'Market not found.']);
        }

        return view('manual-update.view', [
            'market' => $market,
            'statusLabels' => self::STATUS_LABELS,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'exMarketId' => ['required', 'string'],
            'status' => ['required', 'integer', 'between:1,6'],
            'selectionName' => ['nullable', 'string', 'max:255', 'required_if:status,4'],
        ]);

        $exMarketId = trim($validated['exMarketId']);
        $newStatus = (int) $validated['status'];

        $market = DB::table('market_lists')
            ->select('exMarketId', 'exEventId', 'isPreBet')
            ->where('exMarketId', $exMarketId)
            ->first();

        if (!$market) {
            return redirect()
                ->route('manual-update.index')
                ->withErrors(['exMarketId' => 'Market not found.']);
        }

        $updatePayload = [
            'status' => $newStatus,
            'updated_at' => now(),
        ];

        if ($newStatus === 4) {
            $updatePayload['winnerType'] = 'settle';
            $updatePayload['selectionName'] = trim($validated['selectionName']);
        } elseif ($newStatus === 5) {
            $updatePayload['winnerType'] = 'void';
            $updatePayload['selectionName'] = null;
        }

        if (in_array($newStatus, [4, 5], true)) {
            $updatePayload['isCompleted'] = true;
            $updatePayload['isPreBet'] = false;
            $updatePayload['completeTime'] = now();
        } else {
            $updatePayload['isCompleted'] = false;
            $updatePayload['completeTime'] = null;
        }

        DB::table('market_lists')
            ->where('exMarketId', $exMarketId)
            ->update($updatePayload);

        $this->clearRedisMarket(
            $exMarketId,
            $market->exEventId ?? null,
            $market->isPreBet ?? null
        );

        if (in_array($newStatus, [4, 5], true) && !empty($market->exEventId)) {
            $pendingCount = DB::table('market_lists')
                ->where('exEventId', $market->exEventId)
                ->where(function ($query) {
                    $query->whereNull('status')
                        ->orWhereNotIn('status', [4, 5]);
                })
                ->count();

            if ($pendingCount === 0) {
                $eventUpdated = DB::table('events')
                    ->where('exEventId', $market->exEventId)
                    ->update([
                        'isCompleted' => true,
                        'status' => 4,
                        'completeTime' => DB::raw('COALESCE("completeTime", NOW())'),
                        'updated_at' => now(),
                    ]);

                if ($eventUpdated) {
                    $this->clearRedisEvent($market->exEventId);
                }
            }
        }

        return redirect()
            ->route('manual-update.view', ['exMarketId' => $exMarketId])
            ->with('success', 'Market updated and redis cache cleared.');
    }

    private function clearRedisMarket(string $exMarketId, ?string $exEventId, $isPreBet): void
    {
        $removalSet = [];
        $removalSet[] = $exMarketId;

        if ($exEventId) {
            $removalSet[] = "{$exEventId}|{$exMarketId}";
            $removalSet[] = "{$exMarketId}|{$exEventId}";
        }

        $flag = $isPreBet === true ? '1' : '0';
        $flagVariants = array_unique([$flag, '1', '0']);
        if ($exEventId) {
            foreach ($flagVariants as $flagValue) {
                $removalSet[] = "{$exEventId}|{$exMarketId}|{$flagValue}";
                $removalSet[] = "{$exMarketId}|{$exEventId}|{$flagValue}";
            }
        }

        $removalList = array_values(array_unique(array_filter($removalSet)));
        $metaKey = self::REDIS_MARKET_META_PREFIX . $exMarketId;

        try {
            $connection = Redis::connection();
            $client = $connection->client();
            $originalPrefix = null;

            if ($client instanceof \Redis && defined('Redis::OPT_PREFIX')) {
                $originalPrefix = $client->getOption(\Redis::OPT_PREFIX);
                $client->setOption(\Redis::OPT_PREFIX, '');
            }

            try {
                if ($removalList) {
                    if ($client instanceof \Redis) {
                        $client->srem(self::REDIS_MARKET_IDS, ...$removalList);
                    } else {
                        $connection->command('SREM', array_merge([self::REDIS_MARKET_IDS], $removalList));
                    }
                }

                if ($client instanceof \Redis) {
                    $client->del($metaKey);
                } else {
                    $connection->command('DEL', [$metaKey]);
                }
            } finally {
                if ($client instanceof \Redis && $originalPrefix !== null) {
                    $client->setOption(\Redis::OPT_PREFIX, $originalPrefix);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Manual market update redis cleanup failed.', [
                'exMarketId' => $exMarketId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function clearRedisEvent(string $exEventId): void
    {
        $eventId = trim($exEventId);
        if ($eventId === '') {
            return;
        }

        try {
            $connection = Redis::connection();
            $client = $connection->client();
            $originalPrefix = null;

            if ($client instanceof \Redis && defined('Redis::OPT_PREFIX')) {
                $originalPrefix = $client->getOption(\Redis::OPT_PREFIX);
                $client->setOption(\Redis::OPT_PREFIX, '');
            }

            try {
                $removeFromSet = function (string $key, callable $predicate) use ($connection, $client) {
                    $members = [];
                    if ($client instanceof \Redis) {
                        $members = $client->smembers($key) ?: [];
                    } else {
                        $members = $connection->command('SMEMBERS', [$key]) ?: [];
                    }
                    if (!is_array($members) || !$members) {
                        return;
                    }
                    $toRemove = [];
                    foreach ($members as $member) {
                        if ($predicate($member)) {
                            $toRemove[] = $member;
                        }
                    }
                    if (!$toRemove) {
                        return;
                    }
                    if ($client instanceof \Redis) {
                        $client->srem($key, ...$toRemove);
                    } else {
                        $connection->command('SREM', array_merge([$key], $toRemove));
                    }
                };

                $removeFromSet(self::REDIS_MARKET_IDS, function ($value) use ($eventId) {
                    $parts = array_map('trim', explode('|', (string) $value));
                    foreach ($parts as $part) {
                        if ($part !== '' && $part === $eventId) {
                            return true;
                        }
                    }
                    return false;
                });

                $removeFromSet(self::REDIS_INPLAY_PAIRS, function ($value) use ($eventId) {
                    $parts = array_map('trim', explode('|', (string) $value));
                    return isset($parts[1]) && $parts[1] === $eventId;
                });

                $removeFromSet(self::REDIS_PREBET_PAIRS, function ($value) use ($eventId) {
                    $parts = array_map('trim', explode('|', (string) $value));
                    return isset($parts[1]) && $parts[1] === $eventId;
                });

                $removeFromSet(self::REDIS_MARKET_MISSING, function ($value) use ($eventId) {
                    $parts = array_map('trim', explode('|', (string) $value));
                    foreach ($parts as $part) {
                        if ($part !== '' && $part === $eventId) {
                            return true;
                        }
                    }
                    return false;
                });

                $keys = [];
                if ($client instanceof \Redis) {
                    $keys = $client->hkeys(self::REDIS_INPLAY_FREEZE_WATCH) ?: [];
                } else {
                    $keys = $connection->command('HKEYS', [self::REDIS_INPLAY_FREEZE_WATCH]) ?: [];
                }

                if (is_array($keys) && $keys) {
                    $toRemove = [];
                    foreach ($keys as $key) {
                        $parts = array_map('trim', explode('|', (string) $key));
                        if (isset($parts[1]) && $parts[1] === $eventId) {
                            $toRemove[] = $key;
                        }
                    }
                    if ($toRemove) {
                        if ($client instanceof \Redis) {
                            $client->hdel(self::REDIS_INPLAY_FREEZE_WATCH, ...$toRemove);
                        } else {
                            $connection->command('HDEL', array_merge([self::REDIS_INPLAY_FREEZE_WATCH], $toRemove));
                        }
                    }
                }

                if ($client instanceof \Redis) {
                    $client->hdel(self::REDIS_EVENT_SPORT_MAP, $eventId);
                } else {
                    $connection->command('HDEL', [self::REDIS_EVENT_SPORT_MAP, $eventId]);
                }
            } finally {
                if ($client instanceof \Redis && $originalPrefix !== null) {
                    $client->setOption(\Redis::OPT_PREFIX, $originalPrefix);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Manual event update redis cleanup failed.', [
                'exEventId' => $eventId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
