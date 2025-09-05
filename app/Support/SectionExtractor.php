<?php

declare(strict_types=1);

namespace App\Support;

use DOMDocument;
use DOMNode;
use DOMXPath;

final class SectionExtractor
{
    /**
     * Canonical keys → heading aliases (lowercased, emoji/whitespace removed)
     * @var array<string, array<string, list<string>>>
     */
    private const MAP = [
        'events' => [
            'overview'   => ['overview'],
            'route'      => ['route details','route'],
            'results'    => ['results'],
            'vehicles'   => ['vehicle highlights','vehicles'],
            'challenges' => ['navigation and challenges','navigation & challenges','navigation','challenges'],
        ],
        'drivers' => [
            'overview'     => ['overview'],
            'achievements' => ['major achievements','achievements'],
            'vehicles'     => ['vehicle highlights','vehicles','car highlights'],
            'style'        => ['driving style and legacy','driving style & legacy','driving style','legacy'],
            'navigation'   => ['navigation and teamwork','navigation & teamwork','teamwork','navigation'],
        ],
        'cars' => [
            'overview'        => ['overview'],
            'specs'           => ['technical specs & innovations','technical specs and innovations','technical specs','specs','innovations'],
            'highlights'      => ['competitive highlights','highlights','results'],
            'characteristics' => ['driving characteristics','characteristics','legacy & cultural impact','legacy and cultural impact','legacy'],
        ],
    ];

    /**
     * @return array<string, string>
     */
    public static function parse(?string $html, string $type): array
    {
        if ($html === null || $html === '') {
            return [];
        }
        $type = self::normalizeType($type);
        $map  = self::MAP[$type] ?? [];

        $dom = new DOMDocument();
        // Guard against fragment warnings
        @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
        $xp = new DOMXPath($dom);

        /** @var list<DOMNode> $h2s */
        $h2s = iterator_to_array($xp->query('//h2') ?? []);

        $sections = [];
        $count = count($h2s);

        for ($i = 0; $i < $count; $i++) {
            /** @var DOMNode $h2 */
            $h2 = $h2s[$i];
            $title = self::clean($h2->textContent ?? '');
            $key   = self::matchKey($title, $map);
            if ($key === null) {
                continue;
            }

            /** @var DOMNode|null $until */
            $until = $h2s[$i + 1] ?? null;
            $sections[$key] = self::collectUntilNextH2($h2, $until);
        }

        /** @var array<string,string> $sections */
        return $sections;
    }

    private static function normalizeType(string $t): string
    {
        $t = strtolower($t);
        if (str_starts_with($t, 'event'))  {
            return 'events';
        }
        if (str_starts_with($t, 'driver')) {
            return 'drivers';
        }
        if (str_starts_with($t, 'car'))    {
            return 'cars';
        }
        return 'events';
    }

    private static function clean(string $s): string
    {
        // drop emoji and punctuation that often precedes the title
        $s = (string) preg_replace('/[\x{1F300}-\x{1FAFF}\x{2600}-\x{27BF}]/u', '', $s);
        $s = strtolower(trim($s));
        // normalize spaces & punctuation
        $s = (string) preg_replace('/\s+/', ' ', $s);
        $s = str_replace(['–','—','&'], ['-','-','&'], $s);
        return $s;
    }

    /**
     * @phpstan-type AliasList list<string>
     * @param array<string, AliasList> $map
     */
    private static function matchKey(string $title, array $map): ?string
    {
        foreach ($map as $key => $aliases) {
            foreach ($aliases as $alias) {
                if (self::clean($alias) === $title) {
                    return $key;
                }
            }
        }
        // fuzzy: starts-with match (helps when headings have extra words)
        foreach ($map as $key => $aliases) {
            foreach ($aliases as $alias) {
                if (str_starts_with($title, self::clean($alias))) {
                    return $key;
                }
            }
        }
        return null;
    }

    private static function collectUntilNextH2(DOMNode $h2, ?DOMNode $nextH2): string
    {
        $buf = '';
        for ($n = $h2->nextSibling; $n !== null && $n !== $nextH2; $n = $n->nextSibling) {
            // Skip whitespace-only text nodes
            if ($n->nodeType === XML_TEXT_NODE && trim($n->textContent ?? '') === '') {
                continue;
            }
            $buf .= self::outerHTML($n);
        }
        // Strip leading/trailing <hr> that often bracket sections
        $buf = (string) preg_replace('/^\s*<hr[^>]*>\s*/i', '', $buf);
        $buf = (string) preg_replace('/\s*<hr[^>]*>\s*$/i', '', $buf);
        return trim($buf);
    }

    private static function outerHTML(DOMNode $node): string
    {
        $doc = new DOMDocument();
        $doc->appendChild($doc->importNode($node, true));
        $html = trim((string) $doc->saveHTML());
        // remove XML header if any
        $html = (string) preg_replace('/^<\?xml.*?\?>/i', '', $html);
        return $html;
    }
}