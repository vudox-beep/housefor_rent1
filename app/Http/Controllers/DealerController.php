<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Listing;
use App\Models\Lead;

class DealerController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $hasGold = $user->subscription_plan === 'gold';
        $isGold = $user->isGold();
        $hasTrial = $user->hasActiveTrial();
        $expiresAt = $isGold ? $user->subscription_expires_at : ($hasTrial ? $user->trial_expires_at : $user->subscription_expires_at);

        $status = 'Basic';
        if ($isGold) {
            $status = 'Active';
        } elseif ($hasTrial) {
            $status = 'Trial';
        } elseif ($hasGold) {
            $status = 'Expired';
        }

        $daysRemaining = null;
        $expiresOn = null;
        if ($expiresAt) {
            $daysRemaining = max(0, (int) now()->startOfDay()->diffInDays($expiresAt->startOfDay(), false));
            $expiresOn = $expiresAt->format('M d, Y');
        }

        $subscription = (object) [
            'plan' => $isGold ? 'Gold Dealer' : ($hasTrial ? 'Free Trial' : 'Basic Dealer'),
            'status' => $status,
            'is_gold' => $isGold,
            'days_remaining' => $daysRemaining,
            'expires_on' => $expiresOn,
        ];

        // Real Stats
        $stats = (object)[
            'total_listings' => Listing::where('user_id', $user->id)->count(),
            'total_views' => Listing::where('user_id', $user->id)->sum('views'),
            'total_leads' => Lead::whereHas('listing', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count(),
            'active_listings' => Listing::where('user_id', $user->id)->where('status', 'active')->count()
        ];

        // Real Analytics Data
        $analytics = Listing::where('user_id', $user->id)
            ->withCount(['leads'])
            ->orderByDesc('views')
            ->take(5)
            ->get()
            ->map(function ($listing) {
                return (object)[
                    'listing' => $listing->title,
                    'views' => $listing->views,
                    'leads' => $listing->leads_count
                ];
            });

        return view('dealer.dashboard', compact('subscription', 'stats', 'analytics'));
    }

    public function subscription()
    {
        return view('dealer.subscription');
    }

    public function processSubscription(Request $request)
    {
        $validated = $request->validate([
            'plan' => 'required|in:gold',
            'payment_method' => 'required|string',
        ]);

        $user = Auth::user();
        $paymentMethod = $request->input('payment_method');
        
        // All payment methods (Airtel, MTN, Visa/Mastercard) route through Lenco payment form
        if (in_array($paymentMethod, ['airtel_money', 'mtn_money', 'visa_mastercard'])) {
            $amount = 20; // amount in ZMW for Gold plan
            return redirect()->route('payments.lenco.form', ['amount' => $amount, 'plan' => 'gold', 'method' => $paymentMethod]);
        }

        // Fallback / mock processing for unknown methods
        $user->update([
            'subscription_plan' => 'gold',
            'subscription_expires_at' => now()->addMonth(), // 1 Month Subscription
        ]);

        return redirect()->route('dealer.dashboard')->with('success', 'Upgrade successful! You are now a Gold Dealer.');
    }

    public function leads()
    {
        $user = Auth::user();
        
        // Get leads for listings owned by the logged-in user
        $leads = Lead::whereHas('listing', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with('listing')->latest()->get()->map(function($lead) {
            return (object)[
                'id' => $lead->id,
                'name' => $lead->name,
                'email' => $lead->email,
                'phone' => $lead->phone,
                'listing' => $lead->listing->title,
                'message' => $lead->message,
                'date' => $lead->created_at->diffForHumans(),
                'read' => $lead->read_at != null
            ];
        });

        return view('dealer.leads', compact('leads'));
    }

    public function myListings()
    {
        $listings = Listing::where('user_id', Auth::id())->latest()->paginate(10);
        return view('dealer.listings', compact('listings'));
    }

    public function updateListingStatus(Request $request, Listing $listing)
    {
        if ($listing->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:active,rented,sold',
        ]);

        $listing->update([
            'status' => $validated['status'],
        ]);

        return back()->with('success', 'Listing status updated.');
    }

    public function agents()
    {
        $agents = \App\Models\Agent::where('user_id', Auth::id())->latest()->paginate(10);
        return view('dealer.agents', compact('agents'));
    }

    public function storeAgent(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'photo' => 'nullable|image|max:6144',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('agents', 'public');
        }

        \App\Models\Agent::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'photo_path' => $photoPath ? '/storage/' . $photoPath : null,
        ]);

        return back()->with('success', 'Agent added successfully.');
    }

    public function destroyAgent(\App\Models\Agent $agent)
    {
        if ($agent->user_id !== Auth::id()) {
            abort(403);
        }

        $agent->delete();
        return back()->with('success', 'Agent removed successfully.');
    }

    public function profile()
    {
        $user = Auth::user();
        $isGold = $user->isGold();
        $hasTrial = $user->hasActiveTrial();
        $expiresAt = $isGold ? $user->subscription_expires_at : ($hasTrial ? $user->trial_expires_at : null);
        
        $subscription = (object) [
            'plan' => $isGold ? 'Gold Dealer' : ($hasTrial ? 'Free Trial' : 'Basic Dealer'),
            'status' => $isGold ? 'Active' : ($hasTrial ? 'Trial Active' : 'Basic'),
            'expires_on' => $expiresAt ? $expiresAt->format('F d, Y') : 'N/A',
        ];

        return view('dealer.profile', compact('user', 'subscription'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'avatar' => 'nullable|image|max:6144',
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = '/storage/' . $path;
        }

        $user->name = $validated['name'];
        $user->phone = $validated['phone'];
        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|confirmed|min:8',
        ]);

        Auth::user()->update([
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password changed successfully.');
    }
}
