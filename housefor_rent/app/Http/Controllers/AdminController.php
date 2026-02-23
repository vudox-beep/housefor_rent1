<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Listing;
use App\Models\Report;
use App\Models\Lead;

use App\Models\Payment;
use App\Models\Setting;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'total_users' => User::where('role', 'user')->count(),
            'total_dealers' => User::where('role', 'dealer')->count(),
            'total_listings' => Listing::count(),
            'pending_reports' => Report::where('status', 'pending')->count(),
            'total_collected_zwm' => Payment::where('status', 'completed')->where('currency', 'ZMW')->sum('amount'),
            'pending_collected_zwm' => Payment::where('status', 'pending')->where('currency', 'ZMW')->sum('amount'),
            'free_trial_enabled' => Setting::getBool('free_trial_enabled', true),
            'recent_users' => User::latest()->take(5)->get(),
            'recent_listings' => Listing::with('user')->latest()->take(10)->get(),
        ]);
    }

    public function updateFreeTrial(Request $request)
    {
        $enabled = $request->boolean('free_trial_enabled');
        Setting::setValue('free_trial_enabled', $enabled ? '1' : '0');
        return back()->with('success', 'Free trial offer updated.');
    }

    public function users()
    {
        $users = User::where('role', 'user')->withCount('listings')->latest()->paginate(10);
        return view('admin.users', compact('users'));
    }

    public function dealers()
    {
        // Fetching real dealers from DB with pagination
        $dealers = User::where('role', 'dealer')
            ->withCount('listings')
            ->latest()
            ->paginate(15);
            
        // Add calculated fields or transform via map is tricky with paginate
        // So we'll pass the paginator to view and handle display logic there
        // or just rely on model attributes since we added them to model/DB.

        return view('admin.dealers', compact('dealers'));
    }

    public function reports()
    {
        $reports = Report::with('reporter', 'reportable')->latest()->paginate(10);
        return view('admin.reports', compact('reports'));
    }

    public function suspendUser($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'suspended']);
        return back()->with('success', 'User has been suspended.');
    }

    public function activateUser($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'active']);
        return back()->with('success', 'User has been activated.');
    }

    public function transactions()
    {
        $transactions = Payment::with('user')->latest()->paginate(15);
        return view('admin.transactions', compact('transactions'));
    }

    public function updateListingStatus(Request $request, Listing $listing)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,rented,sold',
        ]);

        $listing->update([
            'status' => $validated['status'],
        ]);

        return back()->with('success', 'Listing status updated.');
    }
}
