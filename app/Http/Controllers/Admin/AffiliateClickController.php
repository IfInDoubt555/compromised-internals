<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateClick;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AffiliateClickController extends Controller
{
    public function index(Request $request)
    {
        // Base query
        $base = AffiliateClick::query();

        // Apply filters only when the inputs are actually present
        $filtered = (clone $base);
        if ($request->filled('brand')) {
            $filtered->where('brand', $request->input('brand'));
        }
        if ($request->filled('subid')) {
            $filtered->where('subid', 'like', '%'.$request->input('subid').'%');
        }
        if ($request->filled('host')) {
            $filtered->where('host', 'like', '%'.$request->input('host').'%');
        }
        if ($from = $request->date('from')) {
            $filtered->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->date('to')) {
            $filtered->whereDate('created_at', '<=', $to);
        }

        // Table
        $clicks = (clone $filtered)->latest()->paginate(50)->withQueryString();

        // Stats from the SAME filtered set
        $stats = [
            'today' => (clone $filtered)->whereDate('created_at', today())->count(),
            '7d'    => (clone $filtered)->where('created_at', '>=', now()->subDays(7))->count(),
            '30d'   => (clone $filtered)->where('created_at', '>=', now()->subDays(30))->count(),
        ];

        $brands = array_keys(config('affiliates.brands', []));

        return view('admin.affiliates.clicks.index', compact('clicks','stats','brands'));
    }

    public function export(Request $request): StreamedResponse
    {
        $file = 'affiliate_clicks_'.now()->format('Ymd_His').'.csv';

        $query = AffiliateClick::query()->latest();
        if ($request->filled('brand')) $query->where('brand', $request->input('brand'));
        if ($request->filled('subid')) $query->where('subid', 'like', '%'.$request->input('subid').'%');
        if ($request->filled('host'))  $query->where('host',  'like', '%'.$request->input('host').'%');
        if ($from = $request->date('from')) $query->whereDate('created_at', '>=', $from);
        if ($to   = $request->date('to'))   $query->whereDate('created_at', '<=', $to);

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id','brand','subid','host','url','ip','user_id','referer','ua','created_at']);
            $query->chunkById(100, function ($rows) use ($out) {
                foreach ($rows as $r) {
                    fputcsv($out, [
                        $r->id, $r->brand, $r->subid, $r->host, $r->url, $r->ip, $r->user_id,
                        $r->referer, mb_substr($r->ua ?? '', 0, 200), $r->created_at,
                    ]);
                }
            });
            fclose($out);
        }, $file, ['Content-Type' => 'text/csv']);
    }

    /**
     * Return JSON data for clicks per day for the last 30 days.
     */
    public function chartData(Request $request)
    {
        // Optional: respect date filters used on the page
        $q = AffiliateClick::query();
        if ($from = $request->date('from')) $q->whereDate('created_at', '>=', $from);
        if ($to   = $request->date('to'))   $q->whereDate('created_at', '<=', $to);

        // Grouping mode
        if ($request->query('group') === 'brand') {
            $data = $q->selectRaw('COALESCE(NULLIF(brand, \'\'), \'(none)\') as k, COUNT(*) as c')
                      ->groupBy('k')->orderByDesc('c')->pluck('c', 'k');
            return response()->json($data);
        }

        // Default: by day (last 30 days)
        $data = $q->where('created_at', '>=', now()->subDays(30))
                  ->selectRaw('DATE(created_at) as d, COUNT(*) as c')
                  ->groupBy('d')->orderBy('d')->pluck('c', 'd');
        return response()->json($data);
    }
}