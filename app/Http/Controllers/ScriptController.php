<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class ScriptController extends Controller
{
    public function index()
    {
        return view('script.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $handle = fopen($validated['file']->getRealPath(), 'r');
        if (! $handle) {
            return back()->withErrors(['file' => 'Unable to read the uploaded file.']);
        }

        $header = fgetcsv($handle);
        if (! $header) {
            fclose($handle);
            return back()->withErrors(['file' => 'The CSV file is empty.']);
        }

        $header = array_map(fn ($h) => strtolower(trim($h)), $header);
        $requiredHeaders = ['exmarketid', 'markettime', 'selectionname', 'winnertype'];

        if (array_diff($requiredHeaders, $header)) {
            fclose($handle);
            return back()->withErrors(['file' => 'CSV headers must be: exMarketId, marketTime, selectionName, winnerType.']);
        }

        $updated = 0;
        $notUpdated = 0;
        $notUpdatedIds = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (! $row || count(array_filter($row, fn ($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $data = array_combine($header, array_map('trim', $row));
            if ($data === false) {
                continue;
            }

            $exMarketId = $data['exmarketid'] ?? null;
            if (! $exMarketId) {
                continue;
            }

            $market = DB::table('market_lists')->where('exMarketId', $exMarketId);

            if (! $market->exists()) {
                $notUpdated++;
                $notUpdatedIds[] = $exMarketId;
                continue;
            }

            $updatePayload = [];

            if (! empty($data['markettime'])) {
                try {
                    $updatePayload['marketTime'] = Carbon::parse($data['markettime']);
                } catch (\Throwable $e) {
                    // Ignore invalid date formats
                }
            }

            if (! empty($data['selectionname'])) {
                $updatePayload['selectionName'] = $data['selectionname'];
            }

            if (! empty($data['winnertype'])) {
                $winnerType = strtolower($data['winnertype']);
                $updatePayload['winnerType'] = $data['winnertype'];

                $statusMap = [
                    'settle' => 4,
                    'settled' => 4,
                    'void' => 5,
                ];

                if (isset($statusMap[$winnerType])) {
                    $updatePayload['status'] = $statusMap[$winnerType];
                }
            }

            if (! empty($updatePayload) && $market->update($updatePayload)) {
                $updated++;
            } else {
                $notUpdated++;
                $notUpdatedIds[] = $exMarketId;
            }
        }

        fclose($handle);

        if (! empty($notUpdatedIds)) {
            Log::info('Script upload - market_lists not updated', ['exMarketIds' => $notUpdatedIds]);
        }

        return back()->with('summary', [
            'updated' => $updated,
            'not_updated' => $notUpdated,
        ]);
    }
}
