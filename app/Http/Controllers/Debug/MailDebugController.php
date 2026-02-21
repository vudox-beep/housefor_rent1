<?php

namespace App\Http\Controllers\Debug;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailDebugController extends Controller
{
    public function send(Request $request)
    {
        $to = $request->input('to') ?: $request->user()->email;
        $subject = 'HouseForRent Email Test';
        $body = "This is a test email from HouseForRent.\n\nIf you received this, SMTP is working.";

        try {
            Mail::raw($body, function ($message) use ($to, $subject) {
                $message->to($to)->subject($subject);
            });
        } catch (\Throwable $e) {
            return back()->withErrors(['mail' => 'Failed to send test email: '.$e->getMessage()]);
        }

        return back()->with('status', 'Test email sent to '.$to);
    }
}
