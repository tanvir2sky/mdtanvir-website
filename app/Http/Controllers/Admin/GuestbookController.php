<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuestbookEntry;
use Illuminate\Http\Request;

class GuestbookController extends Controller
{
    public function index(Request $request)
    {
        $status = in_array($request->query('status'), ['pending', 'approved'], true) ? $request->query('status') : null;

        $entries = GuestbookEntry::query()
            ->when($status === 'pending', fn ($query) => $query->pending())
            ->when($status === 'approved', fn ($query) => $query->approved())
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $counts = [
            'all' => GuestbookEntry::count(),
            'pending' => GuestbookEntry::pending()->count(),
            'approved' => GuestbookEntry::approved()->count(),
        ];

        return view('admin.guestbook.index', compact('entries', 'counts', 'status'));
    }

    public function approve(GuestbookEntry $entry)
    {
        $entry->update(['approved_at' => now()]);

        return back()->with('status', 'Note approved.');
    }

    public function unapprove(GuestbookEntry $entry)
    {
        $entry->update(['approved_at' => null]);

        return back()->with('status', 'Note hidden.');
    }

    public function destroy(GuestbookEntry $entry)
    {
        $entry->delete();

        return back()->with('status', 'Note deleted.');
    }
}
