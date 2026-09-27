<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreCheck;
use Illuminate\Support\Facades\DB;

class StoreCheckController extends Controller
{
    public function index()
    {
        $checks = StoreCheck::query()->latest('created_at')->paginate(30);

        $topHosts = StoreCheck::query()
            ->select('host', DB::raw('count(*) as total'), DB::raw('max(created_at) as last_checked'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('host')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return view('admin.store-checks.index', compact('checks', 'topHosts'));
    }
}
