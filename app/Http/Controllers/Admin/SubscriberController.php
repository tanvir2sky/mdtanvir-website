<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    private const STATUSES = ['confirmed', 'pending', 'unsubscribed'];

    public function index(Request $request)
    {
        $status = in_array($request->query('status'), self::STATUSES, true) ? $request->query('status') : null;

        $subscribers = $this->filtered($status)
            ->when($request->query('q'), fn ($query, $q) => $query->where('email', 'like', "%{$q}%"))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $counts = [
            'all' => Subscriber::count(),
            'confirmed' => Subscriber::active()->count(),
            'pending' => Subscriber::pending()->count(),
            'unsubscribed' => Subscriber::unsubscribed()->count(),
        ];

        return view('admin.subscribers.index', compact('subscribers', 'counts', 'status'));
    }

    public function export(Request $request)
    {
        $status = in_array($request->query('status'), self::STATUSES, true) ? $request->query('status') : 'confirmed';
        $query = $this->filtered($status)->orderBy('id');

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['email', 'locale', 'status', 'subscribed_at', 'confirmed_at', 'unsubscribed_at', 'source']);

            $query->chunkById(500, function ($subscribers) use ($out) {
                foreach ($subscribers as $subscriber) {
                    fputcsv($out, [
                        $subscriber->email,
                        $subscriber->locale,
                        $subscriber->status(),
                        $subscriber->created_at?->toIso8601String(),
                        $subscriber->confirmed_at?->toIso8601String(),
                        $subscriber->unsubscribed_at?->toIso8601String(),
                        $subscriber->source,
                    ]);
                }
            });

            fclose($out);
        }, "subscribers-{$status}-".now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function destroy(Subscriber $subscriber)
    {
        $subscriber->delete();

        return back()->with('status', 'Subscriber deleted.');
    }

    private function filtered(?string $status)
    {
        return match ($status) {
            'confirmed' => Subscriber::query()->active(),
            'pending' => Subscriber::query()->pending(),
            'unsubscribed' => Subscriber::query()->unsubscribed(),
            default => Subscriber::query(),
        };
    }
}
