<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

class Comment extends Model
{
    protected $fillable = ['post_id','user_id','body'];

    public function post(): BelongsTo { return $this->belongsTo(Post::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function getBodyHtmlAttribute(): string
    {
        static $converter = null;

        if ($converter === null) {
            $config = [
                'html_input'         => 'strip',   // block raw HTML for safety
                'allow_unsafe_links' => false,
                'max_nesting_level'  => 20,
            ];
            $env = new Environment($config);
            $env->addExtension(new CommonMarkCoreExtension());
            $converter = new MarkdownConverter($env);
        }

        return (string) $converter->convert((string) $this->body);
    }
}