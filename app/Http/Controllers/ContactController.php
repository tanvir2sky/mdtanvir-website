<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Notifications\NewContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $turnstileEnabled = filled(config('services.turnstile.site_key')) && filled(config('services.turnstile.secret_key'));
        $contactUrl = route('home').'#contact';

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:4000'],
            'cf-turnstile-response' => $turnstileEnabled ? ['required', 'string'] : ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return redirect()->to($contactUrl)->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

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
                return redirect()->to($contactUrl)
                    ->withErrors(['turnstile' => 'Captcha verification failed. Please try again.'])
                    ->withInput();
            }
        }

        unset($data['cf-turnstile-response']);

        $contact = ContactMessage::create($data);

        if (filled($adminEmail = config('app.admin_email'))) {
            try {
                Notification::route('mail', $adminEmail)->notify(new NewContactMessage($contact));
            } catch (\Throwable $e) {
                Log::warning('Contact notification failed: '.$e->getMessage());
            }
        }

        return redirect()->to($contactUrl)
            ->with('contact_status', 'Thanks, your message has been sent successfully. I will get back to you soon.');
    }
}
