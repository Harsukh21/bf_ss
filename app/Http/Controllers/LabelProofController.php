<?php
namespace App\Http\Controllers;

use App\Models\Label;
use App\Models\LabelProof;
use App\Models\LabelProofType;
use App\Models\LabelWhitelabel;
use App\Models\LabelSport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class LabelProofController extends Controller
{
    public function index(Label $label, Request $request)
    {
        $query = LabelProof::with(['whitelabel','proofType','sport'])
            ->where('label_id', $label->id);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('agent_name',     'ilike', "%{$s}%")
                  ->orWhere('user_name',    'ilike', "%{$s}%")
                  ->orWhere('event_name',   'ilike', "%{$s}%")
                  ->orWhere('market_name',  'ilike', "%{$s}%")
                  ->orWhere('whatsapp_group','ilike', "%{$s}%")
                  ->orWhere('navigation',   'ilike', "%{$s}%");
            });
        }

        // Filter panel filters
        if ($request->filled('f_whitelabel'))  $query->where('whitelabel_id', $request->f_whitelabel);
        if ($request->filled('f_proof_type'))  $query->where('proof_type_id', $request->f_proof_type);
        if ($request->filled('f_sport'))       $query->where('sport_id', $request->f_sport);
        if ($request->filled('f_agent'))       $query->where('agent_name', 'ilike', "%{$request->f_agent}%");
        if ($request->filled('f_user'))        $query->where('user_name',  'ilike', "%{$request->f_user}%");
        if ($request->filled('f_event'))       $query->where('event_name', 'ilike', "%{$request->f_event}%");
        if ($request->filled('f_market'))      $query->where('market_name','ilike', "%{$request->f_market}%");
        if ($request->filled('date_from'))     $query->whereDate('proof_date', '>=', $request->date_from);
        if ($request->filled('date_to'))       $query->whereDate('proof_date', '<=', $request->date_to);
        if ($request->filled('amount_min'))    $query->where('amount', '>=', $request->amount_min);
        if ($request->filled('amount_max'))    $query->where('amount', '<=', $request->amount_max);
        if ($request->filled('pl_min'))        $query->where('profit_loss', '>=', $request->pl_min);
        if ($request->filled('pl_max'))        $query->where('profit_loss', '<=', $request->pl_max);

        $perPage     = in_array($request->per_page, [10, 20, 50, 100]) ? (int)$request->per_page : 20;
        $proofs      = $query->latest()->paginate($perPage)->withQueryString();
        $whitelabels = LabelWhitelabel::where('label_id', $label->id)->orderBy('name')->get();
        $proofTypes  = LabelProofType::where('label_id', $label->id)->orderBy('name')->get();
        $sports      = LabelSport::where('label_id', $label->id)->orderBy('name')->get();

        return view('labels.modules.proof', compact('label', 'proofs', 'perPage', 'whitelabels', 'proofTypes', 'sports'));
    }

    public function create(Label $label)
    {
        $whitelabels = LabelWhitelabel::where('label_id', $label->id)->orderBy('name')->get();
        $proofTypes  = LabelProofType::where('label_id', $label->id)->orderBy('name')->get();
        $sports      = LabelSport::where('label_id', $label->id)->orderBy('name')->get();
        return view('labels.modules.proof_create', compact('label', 'whitelabels', 'proofTypes', 'sports'));
    }

    public function store(Request $request, Label $label)
    {
        $validated = $request->validate([
            'whitelabel_id'     => 'nullable|exists:label_whitelabels,id',
            'agent_name'        => 'nullable|string|max:255',
            'user_name'         => 'nullable|string|max:255',
            'proof_type_id'     => 'nullable|exists:label_proof_types,id',
            'amount'            => 'nullable|numeric',
            'sport_id'          => 'nullable|exists:label_sports,id',
            'event_name'        => 'nullable|string|max:255',
            'market_name'       => 'nullable|string|max:255',
            'profit_loss'       => 'nullable|numeric',
            'proof_date'        => 'nullable|date',
            'navigation'        => 'nullable|string',
            'images'            => 'nullable|array|max:6',
            'images.*'          => 'file|image|mimes:jpg,jpeg,png,webp|max:5120',
            'navigation2'       => 'nullable|string',
            'navigation2_images'=> 'nullable|array|max:6',
            'navigation2_images.*' => 'file|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        // Handle whatsapp_group from whitelabel
        if (!empty($validated['whitelabel_id'])) {
            $wl = LabelWhitelabel::find($validated['whitelabel_id']);
            $validated['whatsapp_group'] = $wl?->whatsapp_group;
        }

        $validated['images']             = $this->uploadImages($request, 'images', $label->id);
        $validated['navigation2_images'] = $this->uploadImages($request, 'navigation2_images', $label->id);
        $validated['label_id']   = $label->id;
        $validated['created_by'] = auth()->id();
        $validated['status']     = 'pending';

        LabelProof::create($validated);

        return redirect()->route('labels.proof', $label)
            ->with('success', 'Proof added successfully.');
    }

    public function show(Label $label, LabelProof $proof)
    {
        abort_if($proof->label_id !== $label->id, 404);
        $proof->load(['whitelabel', 'proofType', 'sport']);

        $templateHtml = $this->applyPlaceholders($proof->proofType?->description ?? '', $proof);

        return view('labels.modules.proof_show', compact('label', 'proof', 'templateHtml'));
    }

    public function edit(Label $label, LabelProof $proof)
    {
        abort_if($proof->label_id !== $label->id, 404);
        $whitelabels = LabelWhitelabel::where('label_id', $label->id)->orderBy('name')->get();
        $proofTypes  = LabelProofType::where('label_id', $label->id)->orderBy('name')->get();
        $sports      = LabelSport::where('label_id', $label->id)->orderBy('name')->get();
        return view('labels.modules.proof_edit', compact('label', 'proof', 'whitelabels', 'proofTypes', 'sports'));
    }

    public function update(Request $request, Label $label, LabelProof $proof)
    {
        abort_if($proof->label_id !== $label->id, 404);

        $validated = $request->validate([
            'whitelabel_id'     => 'nullable|exists:label_whitelabels,id',
            'agent_name'        => 'nullable|string|max:255',
            'user_name'         => 'nullable|string|max:255',
            'proof_type_id'     => 'nullable|exists:label_proof_types,id',
            'amount'            => 'nullable|numeric',
            'sport_id'          => 'nullable|exists:label_sports,id',
            'event_name'        => 'nullable|string|max:255',
            'market_name'       => 'nullable|string|max:255',
            'profit_loss'       => 'nullable|numeric',
            'proof_date'        => 'nullable|date',
            'navigation'        => 'nullable|string',
            'images'            => 'nullable|array|max:6',
            'images.*'          => 'file|image|mimes:jpg,jpeg,png,webp|max:5120',
            'navigation2'       => 'nullable|string',
            'navigation2_images'=> 'nullable|array|max:6',
            'navigation2_images.*' => 'file|image|mimes:jpg,jpeg,png,webp|max:5120',
            'remove_images'     => 'nullable|array',
            'remove_nav2_images'=> 'nullable|array',
        ]);

        if (!empty($validated['whitelabel_id'])) {
            $wl = LabelWhitelabel::find($validated['whitelabel_id']);
            $validated['whatsapp_group'] = $wl?->whatsapp_group;
        }

        // Handle existing images (keep ones not removed)
        $existing = $proof->images ?? [];
        $remove   = $request->input('remove_images', []);
        foreach ($remove as $path) {
            Storage::disk('public')->delete($path);
        }
        $kept = array_values(array_filter($existing, fn($p) => !in_array($p, $remove)));
        $new  = $this->uploadImages($request, 'images', $label->id);
        $validated['images'] = array_slice(array_merge($kept, $new), 0, 6);

        $existing2 = $proof->navigation2_images ?? [];
        $remove2   = $request->input('remove_nav2_images', []);
        foreach ($remove2 as $path) {
            Storage::disk('public')->delete($path);
        }
        $kept2 = array_values(array_filter($existing2, fn($p) => !in_array($p, $remove2)));
        $new2  = $this->uploadImages($request, 'navigation2_images', $label->id);
        $validated['navigation2_images'] = array_slice(array_merge($kept2, $new2), 0, 6);

        unset($validated['remove_images'], $validated['remove_nav2_images']);

        $proof->update($validated);

        return redirect()->route('labels.proof', $label)
            ->with('success', 'Proof updated successfully.');
    }

    public function destroy(Label $label, LabelProof $proof)
    {
        abort_if($proof->label_id !== $label->id, 404);

        foreach (($proof->images ?? []) as $path) {
            Storage::disk('public')->delete($path);
        }
        foreach (($proof->navigation2_images ?? []) as $path) {
            Storage::disk('public')->delete($path);
        }

        $proof->delete();

        return redirect()->route('labels.proof', $label)
            ->with('success', 'Proof deleted successfully.');
    }

    public function preview(Request $request, Label $label, LabelProof $proof)
    {
        abort_if($proof->label_id !== $label->id, 404);
        $proof->load(['whitelabel', 'proofType', 'sport']);

        $proofMaker    = (string) ($request->input('proof_maker') ?? '');
        $whatsappGroup = (string) ($request->input('whatsapp_group') ?? $proof->whatsapp_group ?? '');

        $templateHtml = $this->applyPlaceholders(
            $proof->proofType?->description ?? '',
            $proof,
            $proofMaker,
            $whatsappGroup
        );

        return view('labels.modules.proof_preview', compact('label', 'proof', 'templateHtml', 'proofMaker', 'whatsappGroup'));
    }

    public function download(Request $request, Label $label, LabelProof $proof)
    {
        abort_if($proof->label_id !== $label->id, 404);

        $proof->load(['whitelabel', 'proofType', 'sport']);

        $proofMaker    = (string) ($request->input('proof_maker') ?? '');
        $whatsappGroup = (string) ($request->input('whatsapp_group') ?? $proof->whatsapp_group ?? '');

        $templateHtml = $this->applyPlaceholders(
            $proof->proofType?->description ?? '',
            $proof,
            $proofMaker,
            $whatsappGroup
        );

        $pdf = Pdf::loadView('labels.modules.proof_pdf', [
            'proof'         => $proof,
            'label'         => $label,
            'proofMaker'    => $proofMaker,
            'whatsappGroup' => $whatsappGroup,
            'templateHtml'  => $templateHtml,
        ])->setPaper('a4', 'portrait');

        $filename = 'proof_' . $proof->id . '_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }

    private function applyPlaceholders(string $template, LabelProof $proof, ?string $proofMaker = null, ?string $whatsappGroup = null): string
    {
        $map = [
            'USER'         => $proof->user_name ?? '',
            'AGENT'        => $proof->agent_name ?? '',
            'AMOUNT'       => $proof->amount !== null ? number_format($proof->amount, 0) : '',
            'SPORT'        => $proof->sport?->name ?? '',
            'EVENT'        => $proof->event_name ?? '',
            'MARKET'       => $proof->market_name ?? '',
            'PL'           => $proof->profit_loss !== null ? number_format($proof->profit_loss, 0) : '',
            'PROFIT_LOSS'  => $proof->profit_loss !== null ? number_format($proof->profit_loss, 0) : '',
            'DATE'         => $proof->proof_date?->format('d/m/Y') ?? '',
            'WHITELABEL'   => $proof->whitelabel?->name ?? '',
            'WHATSAPP'     => $whatsappGroup ?? $proof->whatsapp_group ?? '',
            'PROOF_MAKER'  => $proofMaker ?? '',
            'NAVIGATION'   => $proof->navigation ?? '',
            'NAVIGATION2'  => $proof->navigation2 ?? '',
        ];

        $search  = [];
        $replace = [];
        foreach ($map as $key => $value) {
            $search[]  = '{' . $key . '}';
            $replace[] = $value;
            $search[]  = '[' . $key . ']';
            $replace[] = $value;
        }

        return str_replace($search, $replace, $template);
    }

    private function uploadImages(Request $request, string $field, int $labelId): array
    {
        $paths = [];
        if ($request->hasFile($field)) {
            foreach ($request->file($field) as $file) {
                $paths[] = $file->store("proofs/{$labelId}", 'public');
            }
        }
        return $paths;
    }
}
