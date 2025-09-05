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
     * @var array<string, array<int, string>> $map
     */
    private const MAP = [
        'events' => [
            'overview'   => ['overview'],
            'route'      => ['route details', 'route'],
            'results'    => ['results'],
            'vehicles'   => ['vehicle highlights', 'vehicles'],
            'challenges' => ['navigation and challenges', 'navigation & challenges', 'navigation', 'challenges'],
        ],
        'drivers' => [
            'overview'     => ['overview'],
            'achievements' => ['major achievements', 'achievements'],
            'vehicles'     => ['vehicle highlights', 'vehicles', 'car highlights'],
            'style'        => ['driving style and legacy', 'driving style & legacy', 'driving style', 'legacy'],
            'navigation'   => ['navigation and teamwork', 'navigation & teamwork', 'teamwork', 'navigation'],
        ],
        'cars' => [
            'overview'        => ['overview'],
            'specs'           => ['technical specs & innovations', 'technical specs and innovations', 'technical specs', 'specs', 'innovations'],
            'highlights'      => ['competitive highlights', 'highlights', 'results'],
            'characteristics' => ['driving characteristics', 'characteristics', 'legacy & cultural impact', 'legacy and cultural impact', 'legacy'],
        ],
    ];

    /**
     * Parse the provided HTML and return sections as key-value pairs.
     *
     * @param string|null $html The HTML content to parse.
     * @param string $type The type of content being parsed ('events', 'drivers', or 'cars').
     * @return array<string, string> Parsed sections with keys and content.
     */
    public static function parse(?string $html, string $type): array
    {
        if ($html === null || $html === '') {
            return [];
        }

        $type = self::normalizeType($type);
        /** @var array<string, array<int, string>> $map */
        $map = self::MAP[$type] ?? [];

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
            $key = self::matchKey($title, $map);

            if ($key === null) {
                continue;
            }

            /** @var DOMNode|null $until */
            $until = $h2s[$i + 1] ?? null;
            $sections[$key] = self::collectUntilNextH2($h2, $until);
        }

        return $sections;
    }

    /**
     * Normalize the type to one of the available categories: 'events', 'drivers', or 'cars'.
     *
     * @param string $type The type to normalize.
     * @return string The normalized type.
     */
    private static function normalizeType(string $type): string
    {
        $type = strtolower($type);
        if (str_starts_with($type, 'event')) {
            return 'events';
        }
        if (str_starts_with($type, 'driver')) {
            return 'drivers';
        }
        if (str_starts_with($type, 'car')) {
            return 'cars';
        }
        return 'events';
    }

    /**
     * Clean up the heading text by removing emojis, extra spaces, and punctuation.
     *
     * @param string $s The text to clean.
     * @return string The cleaned text.
     */
    private static function clean(string $s): string
    {
        // Drop emoji and punctuation that often precede the title
        $s = (string) preg_replace('/[\x{1F300}-\x{1FAFF}\x{2600}-\x{27BF}]/u', '', $s);
        $s = strtolower(trim($s));

        // Normalize spaces & punctuation
        $s = (string) preg_replace('/\s+/', ' ', $s);
        $s = str_replace(['–', '—', '&'], ['-', '-', '&'], $s);
        return $s;
    }

    /**
     * Match the cleaned title to a key in the aliases map.
     *
     * @param string $title The title to match.
     * @param array<string, array<int, string>> $map The aliases map.
     * @return string|null The matched key or null if no match.
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

        // Fuzzy: starts-with match (helps when headings have extra words)
        foreach ($map as $key => $aliases) {
            foreach ($aliases as $alias) {
                if (str_starts_with($title, self::clean($alias))) {
                    return $key;
                }
            }
        }

        return null;
    }

    /**
     * Collect the content until the next H2 element is encountered.
     *
     * @param DOMNode $h2 The current H2 node.
     * @param DOMNode|null $nextH2 The next H2 node.
     * @return string The collected content as a string.
     */
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

    /**
     * Get the outer HTML of a DOM node.
     *
     * @param DOMNode $node The node to convert to HTML.
     * @return string The outer HTML of the node.
     */
    private static function outerHTML(DOMNode $node): string
    {
        $doc = new DOMDocument();
        $doc->appendChild($doc->importNode($node, true));
        $html = trim((string) $doc->saveHTML());

        // Remove XML header if any
        $html = (string) preg_replace('/^<\?xml.*?\?>/i', '', $html);
        return $html;
    }
}