<?php

namespace App\Http\Controllers;

use App\Models\MarketRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MarketRateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MarketRate::query();

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('marketName', 'like', "%{$search}%")
                  ->orWhere('exMarketId', 'like', "%{$search}%");
            });
        }

        // Apply market filter
        if ($request->filled('market_name')) {
            $query->where('marketName', $request->get('market_name'));
        }

        // Apply status filter
        if ($request->filled('status')) {
            if ($request->get('status') === 'inplay') {
                $query->where('inplay', true);
            } elseif ($request->get('status') === 'completed') {
                $query->where('isCompleted', true);
            } elseif ($request->get('status') === 'upcoming') {
                $query->where('inplay', false)->where('isCompleted', false);
            }
        }

        // Apply date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        $marketRates = $query->latest()->paginate(10);
        
        return view('market-rates.index', compact('marketRates'));
    }


    /**
     * Display the specified resource.
     */
    public function show(MarketRate $marketRate)
    {
        return view('market-rates.show', compact('marketRate'));
    }


    /**
     * Search market rates (redirects to index with search parameter)
     */
    public function search(Request $request)
    {
        return redirect()->route('market-rates.index', $request->all());
    }
}
