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
        $query = AffiliateClick::query()->latest();

        if ($brand = $request->string('brand')->trim())   $query->where('brand', $brand);
        if ($subid = $request->string('subid')->trim())   $query->where('subid', 'like', "%{$subid}%");
        if ($host  = $request->string('host')->trim())    $query->where('host', 'like', "%{$host}%");
        if ($from  = $request->date('from'))              $query->whereDate('created_at', '>=', $from);
        if ($to    = $request->date('to'))                $query->whereDate('created_at', '<=', $to);

        $clicks = $query->paginate(50)->withQueryString();

        // quick aggregates (today, 7d, 30d)
        $stats = [
            'today' => (clone $query)->cloneWithout(['columns','orders','limit','offset'])
                        ->whereDate('created_at', now()->toDateString())->count(),
            '7d'    => (clone $query)->cloneWithout(['columns','orders','limit','offset'])
                        ->where('created_at','>=', now()->subDays(7))->count(),
            '30d'   => (clone $query)->cloneWithout(['columns','orders','limit','offset'])
                        ->where('created_at','>=', now()->subDays(30))->count(),
        ];

        $brands = array_keys(config('affiliates.brands', []));

        return view('admin.affiliates.clicks.index', compact('clicks','stats','brands'));
    }

    public function export(Request $request): StreamedResponse
    {
        $file = 'affiliate_clicks_'.now()->format('Ymd_His').'.csv';

        $query = AffiliateClick::query()->latest();
        if ($brand = $request->string('brand')->trim()) $query->where('brand', $brand);
        if ($subid = $request->string('subid')->trim()) $query->where('subid', 'like', "%{$subid}%");
        if ($host  = $request->string('host')->trim())  $query->where('host',  'like', "%{$host}%");
        if ($from  = $request->date('from'))            $query->whereDate('created_at', '>=', $from);
        if ($to    = $request->date('to'))              $query->whereDate('created_at', '<=', $to);

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
    public function chartData()
    {
        $byDay = AffiliateClick::selectRaw('DATE(created_at) as date, count(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        return response()->json($byDay);
    }

}