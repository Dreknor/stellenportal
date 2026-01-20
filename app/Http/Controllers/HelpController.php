<?php

namespace App\Http\Controllers;

use App\Mail\HelpContactMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class HelpController extends Controller
{
    /**
     * Display the help page.
     */
    public function index(): View
    {
        return view('help.index');
    }

    /**
     * Send a contact message from the help page.
     */
    public function sendContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $supportEmail = config('mail.support_email');

        if (!$supportEmail) {
            return back()->with('error', __('Die Support-E-Mail-Adresse ist nicht konfiguriert. Bitte kontaktieren Sie den Administrator.'));
        }

        try {
            Mail::to($supportEmail)->send(new HelpContactMail(
                senderName: $validated['name'],
                senderEmail: $validated['email'],
                mailSubject: $validated['subject'],
                messageContent: $validated['message'],
                userId: auth()->check() ? auth()->id() : null
            ));

            return back()->with('success', __('Ihre Nachricht wurde erfolgreich versendet. Wir werden uns so schnell wie möglich bei Ihnen melden.'));
        } catch (\Exception $e) {
            Log::error('Failed to send help contact email', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return back()->with('error', __('Beim Versenden Ihrer Nachricht ist ein Fehler aufgetreten. Bitte versuchen Sie es später erneut.'));
        }
    }
}

