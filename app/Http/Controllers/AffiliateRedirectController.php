<?php

namespace App\Http\Controllers;

use App\Models\AffiliateClick;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;


class AffiliateRedirectController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        // Required: destination URL
        /** @var string|null $to */
        $to = $request->query('u');
        /** @var string|null $brand */
        $brand = $request->query('brand'); // booking|trip|agoda|expedia|viator (optional)
        /** @var string|null $subid */
        $subid = $request->query('subid'); // optional tracking

        if (!$to) {
            abort(400, 'Missing destination.');
        }

        // Parse & validate URL
        $parts = parse_url($to);
        if (!isset($parts['scheme'], $parts['host'])) {
            abort(400, 'Invalid URL.');
        }

        $host = strtolower($parts['host']);

        // Allowlist check (prevents open redirect abuse)
        $brands = config('affiliates.brands', []);
        /** @var array<int,string> $allowedHosts */
        $allowedHosts = $brand && isset($brands[$brand])
            ? (array) $brands[$brand]
            : Arr::flatten($brands); // if brand not provided, allow any known host


        $isAllowed = false;
        foreach ($allowedHosts as $allowed) {
            // allow subdomains, e.g., www.booking.com, secure.booking.com
            if ($host === $allowed || str_ends_with($host, '.'.$allowed)) {
                $isAllowed = true; break;
            }
        }
        abort_unless($isAllowed, 403, 'Destination not allowed.');

        // Optionally append a unified subid param for reporting (if not already present)
        if ($subid) {
            $subParam = config('affiliates.subid_param.default', 'subid');
            // rebuild query string with subid if absent
            $query = [];
            if (!empty($parts['query'])) parse_str($parts['query'], $query);
            $query[$subParam] = $subid;
            $parts['query'] = http_build_query($query);
            // rebuild $to
            $to = ($parts['scheme'] ?? 'https').'://'.$parts['host']
               .(!empty($parts['path']) ? $parts['path'] : '')
               .($parts['query'] ? '?'.$parts['query'] : '')
               .(!empty($parts['fragment']) ? '#'.$parts['fragment'] : '');
        }

        // Log click (best-effort; do not block redirect)
        try {
            AffiliateClick::create([
                'brand'    => $brand,
                'subid'    => $subid,
                'url'      => $to,
                'host'     => $host,
                'user_id'  => Auth::id(),
                'ip'       => $request->ip(),
                'ua'       => (string) $request->userAgent(),
                'referer'  => (string) $request->headers->get('referer'),
            ]);
        } catch (\Throwable $e) {
            // swallow; logging must not break redirect
        }

        return redirect()->away($to, 302);
    }
}