<?php

namespace App\Http\Controllers;

use App\Mail\ConfirmSubscription;
use App\Models\Subscriber;
use App\Support\Locale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    public function store(Request $request)
    {
        $locale = Locale::isSupported($request->input('locale')) ? $request->input('locale') : Locale::default();
        App::setLocale($locale);

        $back = $this->backUrl($request);

        // Honeypot: real people never fill this hidden field. Pretend it worked.
        if (filled($request->input('website'))) {
            return redirect()->to($back)->with('newsletter_status', __('Almost done! Check your inbox to confirm your subscription.'));
        }

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:190'],
        ]);

        if ($validator->fails()) {
            return redirect()->to($back)->withErrors($validator, 'newsletter')->withInput();
        }

        $email = mb_strtolower(trim($validator->validated()['email']));
        $subscriber = Subscriber::query()->firstOrNew(['email' => $email]);

        // Only (re)send a confirmation to people who aren't already active subscribers.
        if (! $subscriber->isActive()) {
            $subscriber->fill([
                'locale' => $locale,
                'consent_ip' => $request->ip(),
                'source' => mb_substr((string) $request->input('source', 'website'), 0, 60),
                'unsubscribed_at' => null,
                'confirmed_at' => null,
            ])->save();

            Mail::to($subscriber->email)->queue(new ConfirmSubscription($subscriber));
        }

        // Same message whether the address is new or known, so nobody can probe the list.
        return redirect()->to($back)->with('newsletter_status', __('Almost done! Check your inbox to confirm your subscription.'));
    }

    public function confirm(Subscriber $subscriber)
    {
        if ($subscriber->unsubscribed_at === null && $subscriber->confirmed_at === null) {
            $subscriber->forceFill(['confirmed_at' => now()])->save();
        }

        return redirect()->to(lroute('newsletter.confirmed', [], $subscriber->locale));
    }

    public function unsubscribe(Request $request, string $token)
    {
        $subscriber = Subscriber::query()->where('token', $token)->firstOrFail();

        if ($subscriber->unsubscribed_at === null) {
            $subscriber->forceFill(['unsubscribed_at' => now()])->save();
        }

        // One-click unsubscribe from mail clients (RFC 8058) posts here and needs no page.
        if ($request->isMethod('post')) {
            return response()->noContent();
        }

        return redirect()->to(lroute('newsletter.unsubscribed', [], $subscriber->locale));
    }

    public function confirmed()
    {
        return view('newsletter.status', [
            'icon' => 'fas fa-envelope-circle-check',
            'title' => __('You are subscribed!'),
            'message' => __('Thanks for confirming. You will get an email whenever I publish a new article.'),
        ]);
    }

    public function unsubscribed()
    {
        return view('newsletter.status', [
            'icon' => 'fas fa-door-open',
            'title' => __('You have been unsubscribed'),
            'message' => __('You will not receive any more emails from me. Sorry to see you go!'),
        ]);
    }

    /** Where to send the visitor back to: the page they came from, at the form. */
    private function backUrl(Request $request): string
    {
        $previous = url()->previous();
        $base = str_starts_with($previous, url('/')) ? strtok($previous, '#') : lroute('blog.index');

        return $base.'#newsletter';
    }
}
