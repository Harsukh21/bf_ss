<?php
namespace App\Http\Controllers;

use App\Models\Label;
use App\Models\LabelSport;
use Illuminate\Http\Request;

class LabelSportsController extends Controller
{
    public function index(Label $label, Request $request)
    {
        $query = LabelSport::where('label_id', $label->id);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('name', 'ilike', "%{$s}%");
        }

        $perPage = in_array($request->per_page, [10, 20, 50, 100]) ? (int)$request->per_page : 20;
        $sports  = $query->orderBy('name')->paginate($perPage)->withQueryString();

        return view('labels.modules.sports', compact('label', 'sports', 'perPage'));
    }

    public function store(Request $request, Label $label)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        LabelSport::create([
            'label_id'   => $label->id,
            'name'       => $request->name,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('labels.sports', $label)
            ->with('success', 'Sport added successfully.');
    }

    public function update(Request $request, Label $label, LabelSport $sport)
    {
        abort_if($sport->label_id !== $label->id, 404);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $sport->update(['name' => $request->name]);

        return redirect()->route('labels.sports', $label)
            ->with('success', 'Sport updated successfully.');
    }

    public function destroy(Label $label, LabelSport $sport)
    {
        abort_if($sport->label_id !== $label->id, 404);

        $sport->delete();

        return redirect()->route('labels.sports', $label)
            ->with('success', 'Sport deleted successfully.');
    }
}
