<?php
namespace App\Http\Controllers;

use App\Models\Label;
use App\Models\LabelProof;
use App\Models\LabelReport;
use App\Models\LabelProofType;
use App\Models\LabelSport;
use Illuminate\Http\Request;

class LabelReportsController extends Controller
{
    public function index(Label $label, Request $request)
    {
        $query = LabelReport::with('proofType')->where('label_id', $label->id);

        // Text search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('user_name', 'ilike', "%{$s}%")
                  ->orWhere('agent',    'ilike', "%{$s}%")
                  ->orWhere('origin',   'ilike', "%{$s}%")
                  ->orWhere('remark',   'ilike', "%{$s}%");
            });
        }

        // Date range
        if ($request->filled('date_from')) $query->whereDate('report_date', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('report_date', '<=', $request->date_to);

        // Field filters
        if ($request->filled('f_user'))   $query->where('user_name', 'ilike', "%{$request->f_user}%");
        if ($request->filled('f_agent'))  $query->where('agent',     'ilike', "%{$request->f_agent}%");
        if ($request->filled('f_origin')) $query->where('origin',    'ilike', "%{$request->f_origin}%");

        // Numeric range filters (on JSON originals — approximate via cast)
        // These are applied in PHP after fetch for simplicity with JSON columns
        $perPage = 20;
        $reports = $query->orderByDesc('report_date')->orderByDesc('id')
                         ->paginate($perPage)->withQueryString();

        $proofTypes = LabelProofType::where('label_id', $label->id)->orderBy('name')->get();
        $sports     = LabelSport::where('label_id', $label->id)->orderBy('name')->get();

        return view('labels.modules.reports', compact('label', 'reports', 'proofTypes', 'sports'));
    }

    public function create(Label $label)
    {
        $proofTypes = LabelProofType::where('label_id', $label->id)->orderBy('name')->get();
        $sports     = LabelSport::where('label_id', $label->id)->orderBy('name')->get();
        return view('labels.modules.reports_create', compact('label', 'proofTypes', 'sports'));
    }

    public function store(Request $request, Label $label)
    {
        $request->validate([
            'report_date'         => 'nullable|date',
            'user_name'           => 'nullable|string|max:255',
            'agent'               => 'nullable|string|max:255',
            'origin'              => 'nullable|string|max:255',
            'before_void_balance' => 'nullable|numeric',
            'after_void_balance'  => 'nullable|numeric',
            'catch_by'            => 'nullable|string|max:255',
            'proof_type_id'       => 'nullable|exists:label_proof_types,id',
            'proof_status'        => 'nullable|string|max:50',
            'void_status'         => 'nullable|string|max:50',
            'remark'              => 'nullable|string',
            'originals'           => 'nullable|array',
        ]);

        $originals = $this->buildOriginals($request);

        LabelReport::create([
            'label_id'            => $label->id,
            'report_date'         => $request->report_date,
            'user_name'           => $request->user_name,
            'agent'               => $request->agent,
            'origin'              => $request->origin,
            'before_void_balance' => $request->before_void_balance,
            'after_void_balance'  => $request->after_void_balance,
            'catch_by'            => $request->catch_by,
            'proof_type_id'       => $request->proof_type_id ?: null,
            'proof_status'        => $request->proof_status ?: 'submitted',
            'void_status'         => $request->void_status,
            'remark'              => $request->remark,
            'originals'           => $originals,
            'created_by'          => auth()->id(),
        ]);

        return redirect()->route('labels.reports', $label)
            ->with('success', 'Report record added successfully.');
    }

    public function edit(Label $label, LabelReport $report)
    {
        abort_if($report->label_id !== $label->id, 404);
        $proofTypes = LabelProofType::where('label_id', $label->id)->orderBy('name')->get();
        $sports     = LabelSport::where('label_id', $label->id)->orderBy('name')->get();
        return view('labels.modules.reports_edit', compact('label', 'report', 'proofTypes', 'sports'));
    }

    public function update(Request $request, Label $label, LabelReport $report)
    {
        abort_if($report->label_id !== $label->id, 404);

        $originals = $this->buildOriginals($request);

        $report->update([
            'report_date'         => $request->report_date,
            'user_name'           => $request->user_name,
            'agent'               => $request->agent,
            'origin'              => $request->origin,
            'before_void_balance' => $request->before_void_balance,
            'after_void_balance'  => $request->after_void_balance,
            'catch_by'            => $request->catch_by,
            'proof_type_id'       => $request->proof_type_id ?: null,
            'proof_status'        => $request->proof_status ?: 'submitted',
            'void_status'         => $request->void_status,
            'remark'              => $request->remark,
            'originals'           => $originals,
        ]);

        return redirect()->route('labels.reports', $label)
            ->with('success', 'Report record updated successfully.');
    }

    public function storeFromProof(Request $request, Label $label, LabelProof $proof)
    {
        abort_if($proof->label_id !== $label->id, 404);

        $request->validate([
            'report_date'         => 'nullable|date',
            'user_name'           => 'nullable|string|max:255',
            'agent'               => 'nullable|string|max:255',
            'origin'              => 'nullable|string|max:255',
            'before_void_balance' => 'nullable|numeric',
            'after_void_balance'  => 'nullable|numeric',
            'catch_by'            => 'nullable|string|max:255',
            'proof_type_id'       => 'nullable|exists:label_proof_types,id',
            'proof_status'        => 'nullable|string|max:50',
            'void_status'         => 'nullable|string|max:50',
            'remark'              => 'nullable|string',
            'originals'           => 'nullable|array',
        ]);

        $originals = $this->buildOriginals($request);

        LabelReport::create([
            'label_id'            => $label->id,
            'report_date'         => $request->report_date ?: $proof->proof_date,
            'user_name'           => $request->user_name ?? $proof->user_name,
            'agent'               => $request->agent ?? $proof->agent_name,
            'origin'              => $request->origin ?? $proof->whitelabel?->name,
            'before_void_balance' => $request->before_void_balance,
            'after_void_balance'  => $request->after_void_balance,
            'catch_by'            => $request->catch_by,
            'proof_type_id'       => $request->proof_type_id ?: $proof->proof_type_id,
            'proof_status'        => $request->proof_status ?: ($proof->status ?? 'submitted'),
            'void_status'         => $request->void_status,
            'remark'              => $request->remark,
            'originals'           => $originals,
            'created_by'          => auth()->id(),
        ]);

        return redirect()->route('labels.reports', $label)
            ->with('success', 'Report created from proof #' . $proof->id . ' successfully.');
    }

    public function destroy(Label $label, LabelReport $report)
    {
        abort_if($report->label_id !== $label->id, 404);
        $report->delete();
        return redirect()->route('labels.reports', $label)
            ->with('success', 'Report record deleted successfully.');
    }

    public function export(Label $label, Request $request)
    {
        $query = LabelReport::with('proofType')->where('label_id', $label->id);
        if ($request->filled('date_from')) $query->whereDate('report_date', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('report_date', '<=', $request->date_to);
        if ($request->filled('f_user'))    $query->where('user_name', 'ilike', "%{$request->f_user}%");
        if ($request->filled('f_agent'))   $query->where('agent', 'ilike', "%{$request->f_agent}%");
        if ($request->filled('f_origin'))  $query->where('origin', 'ilike', "%{$request->f_origin}%");

        $reports = $query->orderByDesc('report_date')->get();

        $csv = "Date,User Name,Agent,Origin,Sport Name,Event Name,Market Name,P&L,Odds,Stack,Time\n";
        foreach ($reports as $r) {
            foreach (($r->originals ?? []) as $orig) {
                foreach (($orig['bet_details'] ?? [[]]) as $bet) {
                    $csv .= implode(',', [
                        $r->report_date?->format('d/m/Y') ?? '',
                        $r->user_name ?? '',
                        $r->agent ?? '',
                        $r->origin ?? '',
                        $orig['sport_name'] ?? '',
                        $orig['event_name'] ?? '',
                        $orig['market_name'] ?? '',
                        $orig['pl'] ?? '',
                        $bet['odds'] ?? '',
                        $bet['stack'] ?? '',
                        $bet['time'] ?? '',
                    ]) . "\n";
                }
            }
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="reports-' . $label->slug . '-' . now()->format('Ymd') . '.csv"',
        ]);
    }

    private function buildOriginals(Request $request): array
    {
        $originals = [];
        $rawOriginals = $request->input('originals', []);
        if (!is_array($rawOriginals)) return [];

        foreach ($rawOriginals as $orig) {
            $betDetails = [];
            foreach (($orig['bet_details'] ?? []) as $bet) {
                if (!empty($bet['odds']) || !empty($bet['stack']) || !empty($bet['time'])) {
                    $betDetails[] = [
                        'odds'  => is_numeric($bet['odds']  ?? '') ? (float)$bet['odds']  : null,
                        'stack' => is_numeric($bet['stack'] ?? '') ? (float)$bet['stack'] : null,
                        'time'  => trim($bet['time'] ?? ''),
                    ];
                }
            }
            $originals[] = [
                'sport_name'  => trim($orig['sport_name']  ?? ''),
                'event_name'  => trim($orig['event_name']  ?? ''),
                'market_name' => trim($orig['market_name'] ?? ''),
                'pl'          => is_numeric($orig['pl'] ?? '') ? (float)$orig['pl'] : null,
                'bet_details' => $betDetails,
            ];
        }
        return $originals;
    }
}
