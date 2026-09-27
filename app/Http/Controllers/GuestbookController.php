<?php

namespace App\Http\Controllers;

use App\Models\GuestbookEntry;
use App\Notifications\NewGuestbookEntry;
use App\Support\Locale;
use App\Support\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class GuestbookController extends Controller
{
    public function index()
    {
        return view('guestbook.index', [
            'entries' => GuestbookEntry::query()->approved()->latest('approved_at')->paginate(20),
        ]);
    }

    public function store(Request $request)
    {
        $locale = Locale::isSupported($request->input('locale')) ? $request->input('locale') : Locale::default();
        App::setLocale($locale);
        $back = lroute('guestbook.index', [], $locale).'#sign';
        $thanks = __('Thanks for signing! Your note will appear after a quick review.');

        // Honeypot: pretend it worked.
        if (filled($request->input('company'))) {
            return redirect()->to($back)->with('guestbook_status', $thanks);
        }

        $turnstileEnabled = filled(config('services.turnstile.site_key')) && filled(config('services.turnstile.secret_key'));

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:60'],
            'message' => ['required', 'string', 'min:2', 'max:500'],
            'website' => ['nullable', 'url:https,http', 'max:255'],
            'cf-turnstile-response' => $turnstileEnabled ? ['required', 'string'] : ['nullable'],
        ]);

        if ($validator->fails()) {
            return redirect()->to($back)->withErrors($validator, 'guestbook')->withInput();
        }

        if ($turnstileEnabled && ! $this->turnstilePasses($request)) {
            return redirect()->to($back)
                ->withErrors(['turnstile' => __('Captcha verification failed. Please try again.')], 'guestbook')
                ->withInput();
        }

        $data = $validator->validated();
        $entry = GuestbookEntry::create([
            'name' => trim($data['name']),
            'message' => trim($data['message']),
            'website' => $data['website'] ?? null,
            'locale' => $locale,
            'visitor_hash' => Visitor::hash($request),
        ]);

        if (filled($adminEmail = config('app.admin_email'))) {
            try {
                Notification::route('mail', $adminEmail)->notify(new NewGuestbookEntry($entry));
            } catch (\Throwable $e) {
                Log::warning('Guestbook notification failed: '.$e->getMessage());
            }
        }

        return redirect()->to($back)->with('guestbook_status', $thanks);
    }

    private function turnstilePasses(Request $request): bool
    {
        return (bool) Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => config('services.turnstile.secret_key'),
            'response' => $request->input('cf-turnstile-response'),
            'remoteip' => $request->ip(),
        ])->json('success');
    }
}
