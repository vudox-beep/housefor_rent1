@extends('layouts.dealer')

@section('title', 'Upgrade to Gold')

@section('content')
    <div class="card" style="max-width: 900px; margin: 0 auto;">
        <div class="card-header">
            <h3 class="card-title">Choose Your Plan</h3>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; padding: 1rem;">
            <!-- Basic Plan -->
            <div style="border: 1px solid #E5E7EB; border-radius: var(--radius-lg); padding: 1.5rem; text-align: center; display: flex; flex-direction: column;">
                <h4 style="font-size: 1.25rem; color: var(--dark-text); margin-bottom: 0.5rem;">Basic</h4>
                <div style="font-size: 2rem; font-weight: 800; color: var(--dark-text); margin-bottom: 1.5rem;">Free</div>
                <ul style="text-align: left; margin-bottom: 2rem; space-y: 0.5rem; flex-grow: 1;">
                    <li style="margin-bottom: 0.4rem; font-size: 0.9rem;">✅ Post Listings</li>
                    <li style="margin-bottom: 0.4rem; font-size: 0.9rem;">❌ Max 1 Image per Listing</li>
                    <li style="margin-bottom: 0.4rem; font-size: 0.9rem;">❌ No Video Uploads</li>
                    <li style="margin-bottom: 0.4rem; font-size: 0.9rem;">❌ Basic Support</li>
                </ul>
                <button disabled style="width: 100%; padding: 0.7rem; background-color: #E5E7EB; color: #9CA3AF; border: none; border-radius: var(--radius-md); font-weight: 600; cursor: not-allowed; font-size: 0.9rem;">Current Plan</button>
            </div>

            <!-- Gold Plan -->
            <div style="border: 2px solid var(--primary-color); border-radius: var(--radius-lg); padding: 1.5rem; text-align: center; position: relative; display: flex; flex-direction: column;">
                <div style="position: absolute; top: -10px; left: 50%; transform: translateX(-50%); background-color: var(--primary-color); color: white; padding: 0.2rem 0.8rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">RECOMMENDED</div>
                <h4 style="font-size: 1.25rem; color: var(--primary-color); margin-bottom: 0.5rem; margin-top: 0.5rem;">Gold</h4>
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--dark-text); margin-bottom: 1.5rem;">ZMW 20<span style="font-size: 0.85rem; color: var(--muted-text); font-weight: normal;">/mo</span></div>
                <ul style="text-align: left; margin-bottom: 2rem; space-y: 0.5rem; flex-grow: 1;">
                    <li style="margin-bottom: 0.4rem; font-size: 0.9rem;">✅ Unlimited Listings</li>
                    <li style="margin-bottom: 0.4rem; font-size: 0.9rem;">✅ Unlimited Images</li>
                    <li style="margin-bottom: 0.4rem; font-size: 0.9rem;">✅ <strong>Video Uploads</strong></li>
                    <li style="margin-bottom: 0.4rem; font-size: 0.9rem;">✅ Priority Support</li>
                </ul>
                
                <form action="{{ route('dealer.subscription.process') }}" method="POST", style="display: flex; flex-direction: column;">
                    @csrf
                    <input type="hidden" name="plan" value="gold">
                    <div style="margin-bottom: 1rem; text-align: left;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.4rem;">Payment Method</label>
                        <select name="payment_method" required style="width: 100%; padding: 0.5rem; border: 1px solid #E5E7EB; border-radius: var(--radius-md); font-size: 0.85rem;">
                            <option value="airtel_money">Airtel Money (via Lenco)</option>
                            <option value="mtn_money">MTN Mobile Money (via Lenco)</option>
                            <option value="visa_mastercard">Visa / Mastercard (via Lenco)</option>
                        </select>
                    </div>
                    <button type="submit" style="width: 100%; padding: 0.7rem; background-color: var(--primary-color); color: white; border: none; border-radius: var(--radius-md); font-weight: 600; cursor: pointer; box-shadow: var(--hover-shadow); font-size: 0.9rem;">Upgrade for K20</button>
                </form>
            </div>
        </div>
    </div>
@endsection
