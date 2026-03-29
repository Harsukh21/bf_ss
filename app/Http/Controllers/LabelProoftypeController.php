<?php
namespace App\Http\Controllers;

use App\Models\Label;
use App\Models\LabelProofType;
use Illuminate\Http\Request;

class LabelProoftypeController extends Controller
{
    public function index(Label $label, Request $request)
    {
        $query = LabelProofType::where('label_id', $label->id);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('name', 'ilike', "%{$s}%");
        }

        $proofTypes = $query->latest()->paginate(20)->withQueryString();

        return view('labels.modules.prooftype', compact('label', 'proofTypes'));
    }

    public function store(Request $request, Label $label)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['label_id']   = $label->id;
        $validated['created_by'] = auth()->id();

        LabelProofType::create($validated);

        return redirect()->route('labels.prooftype', $label)
            ->with('success', 'Proof type added successfully.');
    }

    public function update(Request $request, Label $label, LabelProofType $prooftype)
    {
        abort_if($prooftype->label_id !== $label->id, 404);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $prooftype->update($validated);

        return redirect()->route('labels.prooftype', $label)
            ->with('success', 'Proof type updated successfully.');
    }

    public function destroy(Label $label, LabelProofType $prooftype)
    {
        abort_if($prooftype->label_id !== $label->id, 404);

        $prooftype->delete();

        return redirect()->route('labels.prooftype', $label)
            ->with('success', 'Proof type deleted successfully.');
    }
}
