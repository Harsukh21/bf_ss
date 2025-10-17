<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MarketRate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MarketRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data first
        MarketRate::truncate();

        // Get all markets from market_lists table
        $markets = DB::table('market_lists')->get();

        foreach ($markets as $market) {
            $runners = $this->generateRunnersForMarket($market->marketName, $market->sportName);
            
            MarketRate::create([
                'exMarketId' => $market->exMarketId,
                'marketName' => $market->marketName,
                'runners' => json_encode($runners),
                'inplay' => $market->isLive,
                'isCompleted' => $market->isCompleted,
            ]);
        }
    }

    /**
     * Generate realistic runners based on market name and sport
     */
    private function generateRunnersForMarket($marketName, $sportName)
    {
        $runners = [];
        $selectionId = 10000; // Starting selection ID

        switch ($sportName) {
            case 'Soccer':
                $runners = $this->generateSoccerRunners($marketName, $selectionId);
                break;
            case 'Tennis':
                $runners = $this->generateTennisRunners($marketName, $selectionId);
                break;
            case 'Cricket':
                $runners = $this->generateCricketRunners($marketName, $selectionId);
                break;
            case 'Basketball':
                $runners = $this->generateBasketballRunners($marketName, $selectionId);
                break;
            case 'Boxing':
                $runners = $this->generateBoxingRunners($marketName, $selectionId);
                break;
            default:
                $runners = [
                    ['selectionId' => $selectionId++, 'runnerName' => 'Option 1', 'handicap' => null, 'sortPriority' => 1, 'metadata' => ['runnerId' => $selectionId - 1]],
                    ['selectionId' => $selectionId++, 'runnerName' => 'Option 2', 'handicap' => null, 'sortPriority' => 2, 'metadata' => ['runnerId' => $selectionId - 1]]
                ];
        }

        return $runners;
    }

    private function generateSoccerRunners($marketName, $selectionId)
    {
        switch ($marketName) {
            case 'Match Winner':
                return [
                    ['selectionId' => $selectionId++, 'runnerName' => 'Home Win', 'handicap' => null, 'sortPriority' => 1, 'metadata' => ['runnerId' => $selectionId - 1]],
                    ['selectionId' => $selectionId++, 'runnerName' => 'Draw', 'handicap' => null, 'sortPriority' => 2, 'metadata' => ['runnerId' => $selectionId - 1]],
                    ['selectionId' => $selectionId++, 'runnerName' => 'Away Win', 'handicap' => null, 'sortPriority' => 3, 'metadata' => ['runnerId' => $selectionId - 1]]
                ];
            case 'Over/Under 2.5 Goals':
                return [
                    ['selectionId' => $selectionId++, 'runnerName' => 'Over 2.5', 'handicap' => 2.5, 'sortPriority' => 1, 'metadata' => ['runnerId' => $selectionId - 1]],
                    ['selectionId' => $selectionId++, 'runnerName' => 'Under 2.5', 'handicap' => 2.5, 'sortPriority' => 2, 'metadata' => ['runnerId' => $selectionId - 1]]
                ];
            default:
                return [
                    ['selectionId' => $selectionId++, 'runnerName' => 'Yes', 'handicap' => null, 'sortPriority' => 1, 'metadata' => ['runnerId' => $selectionId - 1]],
                    ['selectionId' => $selectionId++, 'runnerName' => 'No', 'handicap' => null, 'sortPriority' => 2, 'metadata' => ['runnerId' => $selectionId - 1]]
                ];
        }
    }

    private function generateTennisRunners($marketName, $selectionId)
    {
        switch ($marketName) {
            case 'Match Winner':
                return [
                    ['selectionId' => $selectionId++, 'runnerName' => 'Player 1', 'handicap' => null, 'sortPriority' => 1, 'metadata' => ['runnerId' => $selectionId - 1]],
                    ['selectionId' => $selectionId++, 'runnerName' => 'Player 2', 'handicap' => null, 'sortPriority' => 2, 'metadata' => ['runnerId' => $selectionId - 1]]
                ];
            case 'Total Sets':
                return [
                    ['selectionId' => $selectionId++, 'runnerName' => 'Over 2.5', 'handicap' => 2.5, 'sortPriority' => 1, 'metadata' => ['runnerId' => $selectionId - 1]],
                    ['selectionId' => $selectionId++, 'runnerName' => 'Under 2.5', 'handicap' => 2.5, 'sortPriority' => 2, 'metadata' => ['runnerId' => $selectionId - 1]]
                ];
            default:
                return [
                    ['selectionId' => $selectionId++, 'runnerName' => 'Player 1', 'handicap' => null, 'sortPriority' => 1, 'metadata' => ['runnerId' => $selectionId - 1]],
                    ['selectionId' => $selectionId++, 'runnerName' => 'Player 2', 'handicap' => null, 'sortPriority' => 2, 'metadata' => ['runnerId' => $selectionId - 1]]
                ];
        }
    }

    private function generateCricketRunners($marketName, $selectionId)
    {
        switch ($marketName) {
            case 'Match Winner':
                return [
                    ['selectionId' => $selectionId++, 'runnerName' => 'Team 1', 'handicap' => null, 'sortPriority' => 1, 'metadata' => ['runnerId' => $selectionId - 1]],
                    ['selectionId' => $selectionId++, 'runnerName' => 'Team 2', 'handicap' => null, 'sortPriority' => 2, 'metadata' => ['runnerId' => $selectionId - 1]]
                ];
            case 'Total Runs':
                return [
                    ['selectionId' => $selectionId++, 'runnerName' => 'Over 300', 'handicap' => 300, 'sortPriority' => 1, 'metadata' => ['runnerId' => $selectionId - 1]],
                    ['selectionId' => $selectionId++, 'runnerName' => 'Under 300', 'handicap' => 300, 'sortPriority' => 2, 'metadata' => ['runnerId' => $selectionId - 1]]
                ];
            default:
                return [
                    ['selectionId' => $selectionId++, 'runnerName' => 'Team 1', 'handicap' => null, 'sortPriority' => 1, 'metadata' => ['runnerId' => $selectionId - 1]],
                    ['selectionId' => $selectionId++, 'runnerName' => 'Team 2', 'handicap' => null, 'sortPriority' => 2, 'metadata' => ['runnerId' => $selectionId - 1]]
                ];
        }
    }

    private function generateBasketballRunners($marketName, $selectionId)
    {
        switch ($marketName) {
            case 'Match Winner':
                return [
                    ['selectionId' => $selectionId++, 'runnerName' => 'Home Team', 'handicap' => null, 'sortPriority' => 1, 'metadata' => ['runnerId' => $selectionId - 1]],
                    ['selectionId' => $selectionId++, 'runnerName' => 'Away Team', 'handicap' => null, 'sortPriority' => 2, 'metadata' => ['runnerId' => $selectionId - 1]]
                ];
            case 'Total Points':
                return [
                    ['selectionId' => $selectionId++, 'runnerName' => 'Over 210', 'handicap' => 210, 'sortPriority' => 1, 'metadata' => ['runnerId' => $selectionId - 1]],
                    ['selectionId' => $selectionId++, 'runnerName' => 'Under 210', 'handicap' => 210, 'sortPriority' => 2, 'metadata' => ['runnerId' => $selectionId - 1]]
                ];
            default:
                return [
                    ['selectionId' => $selectionId++, 'runnerName' => 'Home Team', 'handicap' => null, 'sortPriority' => 1, 'metadata' => ['runnerId' => $selectionId - 1]],
                    ['selectionId' => $selectionId++, 'runnerName' => 'Away Team', 'handicap' => null, 'sortPriority' => 2, 'metadata' => ['runnerId' => $selectionId - 1]]
                ];
        }
    }

    private function generateBoxingRunners($marketName, $selectionId)
    {
        return [
            ['selectionId' => $selectionId++, 'runnerName' => 'Fighter 1', 'handicap' => null, 'sortPriority' => 1, 'metadata' => ['runnerId' => $selectionId - 1]],
            ['selectionId' => $selectionId++, 'runnerName' => 'Fighter 2', 'handicap' => null, 'sortPriority' => 2, 'metadata' => ['runnerId' => $selectionId - 1]]
        ];
    }
}
