<?php

namespace App\Http\Controllers;

use App\Models\Label;
use App\Models\LabelWhitelabel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LabelWhitelabelController extends Controller
{
    public function index(Label $label, Request $request)
    {
        $query = LabelWhitelabel::where('label_id', $label->id)->withTrashed(false);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'ilike', "%{$s}%")
                  ->orWhere('whatsapp_group', 'ilike', "%{$s}%")
                  ->orWhere('domain', 'ilike', "%{$s}%");
            });
        }

        $whitelabels = $query->latest()->paginate(20)->withQueryString();

        return view('labels.modules.whitelabel', compact('label', 'whitelabels'));
    }

    public function store(Request $request, Label $label)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'whatsapp_group'  => 'nullable|string|max:255',
            'color'           => 'nullable|string|max:20',
            'domain'          => 'nullable|string|max:255',
            'logo'            => 'nullable|file|image|mimes:png,jpg,jpeg,webp|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store("whitelabels/{$label->id}", 'public');
            $validated['logo_link'] = Storage::url($path);
        }
        unset($validated['logo']);

        $validated['label_id']   = $label->id;
        $validated['created_by'] = auth()->id();

        LabelWhitelabel::create($validated);

        return redirect()->route('labels.whitelabel', $label)
            ->with('success', 'Whitelabel added successfully.');
    }

    public function update(Request $request, Label $label, LabelWhitelabel $whitelabel)
    {
        abort_if($whitelabel->label_id !== $label->id, 404);

        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'whatsapp_group'  => 'nullable|string|max:255',
            'color'           => 'nullable|string|max:20',
            'domain'          => 'nullable|string|max:255',
            'logo'            => 'nullable|file|image|mimes:png,jpg,jpeg,webp|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($whitelabel->logo_link && str_starts_with($whitelabel->logo_link, '/storage/')) {
                Storage::disk('public')->delete(
                    ltrim(str_replace('/storage', '', $whitelabel->logo_link), '/')
                );
            }
            $path = $request->file('logo')->store("whitelabels/{$label->id}", 'public');
            $validated['logo_link'] = Storage::url($path);
        }
        unset($validated['logo']);

        $whitelabel->update($validated);

        return redirect()->route('labels.whitelabel', $label)
            ->with('success', 'Whitelabel updated successfully.');
    }

    public function destroy(Label $label, LabelWhitelabel $whitelabel)
    {
        abort_if($whitelabel->label_id !== $label->id, 404);

        if ($whitelabel->logo_link && str_starts_with($whitelabel->logo_link, '/storage/')) {
            Storage::disk('public')->delete(
                ltrim(str_replace('/storage', '', $whitelabel->logo_link), '/')
            );
        }

        $whitelabel->delete();

        return redirect()->route('labels.whitelabel', $label)
            ->with('success', 'Whitelabel deleted successfully.');
    }
}
