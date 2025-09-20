<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class FixUnicodeEscapes extends Command
{
    protected $signature = 'fix:unicode-escapes {table=threads} {--column=body} {--dry}';
    protected $description = 'Decode stray uXXXX / surrogate pair sequences saved as plain text in a column';

    public function handle(): int
    {
        $table  = $this->argument('table');
        $column = $this->option('column');
        $dry    = (bool) $this->option('dry');

        $total = DB::table($table)->count();
        $this->info("Scanning {$total} rows in {$table}.{$column}…");

        $updated = 0;

        DB::table($table)->orderBy('id')->chunkById(500, function ($rows) use (&$updated, $table, $column, $dry) {
            foreach ($rows as $row) {
                $orig = (string) ($row->{$column} ?? '');
                $fixed = $this->decodeUnicodeLike($orig);

                if ($fixed !== $orig) {
                    $updated++;
                    if (!$dry) {
                        DB::table($table)->where('id', $row->id)->update([$column => $fixed]);
                    }
                }
            }
        });

        $msg = $dry ? 'would be updated' : 'updated';
        $this->info("{$updated} / {$total} rows {$msg}.");
        return Command::SUCCESS;
    }

    /**
     * Decode plain-text sequences like `u2019`, `u003E`, and surrogate pairs `ud83dudd17`.
     */
    private function decodeUnicodeLike(string $s): string
    {
        // 1) Surrogate pairs: ud83dudxxx -> actual emoji
        $s = preg_replace_callback('/u(d[89ab][0-9a-f]{2})u(d[c-f][0-9a-f]{2})/i', function ($m) {
            $hi = hexdec($m[1]);
            $lo = hexdec($m[2]);
            $codepoint = 0x10000 + (($hi - 0xD800) << 10) + ($lo - 0xDC00);
            return $this->codepointToUtf8($codepoint);
        }, $s);

        // 2) Single BMP code units: u2019, u003E, u00EB, etc.
        $s = preg_replace_callback('/u([0-9a-f]{4})\b/i', function ($m) {
            $code = hexdec($m[1]);
            return $this->codepointToUtf8($code);
        }, $s);

        return $s;
    }

    private function codepointToUtf8(int $cp): string
    {
        // Use HTML entity decode for portability
        return html_entity_decode('&#' . $cp . ';', ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
