<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reportable_id' => 'required|integer',
            'reportable_type' => 'required|string',
            'reason' => 'required|string|max:1000',
        ]);

        Report::create([
            'reporter_id' => Auth::id(),
            'reportable_id' => $validated['reportable_id'],
            'reportable_type' => $validated['reportable_type'], // e.g., 'App\Models\Listing'
            'type' => 'Fake Listing', // Default type for this button
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        return back()->with('success', 'Thank you for your report. We will investigate this listing.');
    }
}
