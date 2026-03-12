<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $turnstileEnabled = filled(config('services.turnstile.site_key')) && filled(config('services.turnstile.secret_key'));

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:4000'],
            'cf-turnstile-response' => $turnstileEnabled ? ['required', 'string'] : ['nullable', 'string'],
        ]);

        if ($turnstileEnabled) {
            $verification = Http::asForm()->post(
                'https://challenges.cloudflare.com/turnstile/v0/siteverify',
                [
                    'secret' => config('services.turnstile.secret_key'),
                    'response' => $request->input('cf-turnstile-response'),
                    'remoteip' => $request->ip(),
                ]
            );

            if (! $verification->json('success')) {
                throw ValidationException::withMessages([
                    'turnstile' => 'Captcha verification failed. Please try again.',
                ]);
            }
        }

        unset($data['cf-turnstile-response']);

        ContactMessage::create($data);

        return redirect('/')
            ->with('contact_status', 'Thanks, your message has been sent successfully.');
    }
}
