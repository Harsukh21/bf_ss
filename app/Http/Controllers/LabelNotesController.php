<?php
namespace App\Http\Controllers;

use App\Models\Label;
use App\Models\LabelNote;
use Illuminate\Http\Request;

class LabelNotesController extends Controller
{
    public function index(Label $label, Request $request)
    {
        $query = LabelNote::where('label_id', $label->id);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('origin',        'ilike', "%{$s}%")
                  ->orWhere('agent',       'ilike', "%{$s}%")
                  ->orWhere('user_name',   'ilike', "%{$s}%")
                  ->orWhere('whatsapp_group','ilike', "%{$s}%")
                  ->orWhere('note',        'ilike', "%{$s}%");
            });
        }

        $perPage = in_array($request->per_page, [10, 20, 50, 100]) ? (int)$request->per_page : 20;
        $notes   = $query->latest('note_date')->latest()->paginate($perPage)->withQueryString();

        return view('labels.modules.notes', compact('label', 'notes', 'perPage'));
    }

    public function store(Request $request, Label $label)
    {
        $validated = $request->validate([
            'note_date'     => 'nullable|date',
            'origin'        => 'nullable|string|max:500',
            'agent'         => 'nullable|string|max:255',
            'user_name'     => 'nullable|string|max:255',
            'whatsapp_group'=> 'nullable|string|max:255',
            'note'          => 'nullable|string|max:500',
        ]);

        $validated['label_id']   = $label->id;
        $validated['created_by'] = auth()->id();

        LabelNote::create($validated);

        return redirect()->route('labels.notes', $label)
            ->with('success', 'Note added successfully.');
    }

    public function update(Request $request, Label $label, LabelNote $note)
    {
        abort_if($note->label_id !== $label->id, 404);

        $validated = $request->validate([
            'note_date'     => 'nullable|date',
            'origin'        => 'nullable|string|max:500',
            'agent'         => 'nullable|string|max:255',
            'user_name'     => 'nullable|string|max:255',
            'whatsapp_group'=> 'nullable|string|max:255',
            'note'          => 'nullable|string|max:500',
        ]);

        $note->update($validated);

        return redirect()->route('labels.notes', $label)
            ->with('success', 'Note updated successfully.');
    }

    public function destroy(Label $label, LabelNote $note)
    {
        abort_if($note->label_id !== $label->id, 404);
        $note->delete();

        return redirect()->route('labels.notes', $label)
            ->with('success', 'Note deleted successfully.');
    }
}
