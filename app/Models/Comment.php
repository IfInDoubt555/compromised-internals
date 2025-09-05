<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

class Comment extends Model
{
    /** @var list<string> */
    protected $fillable = ['post_id', 'user_id', 'body'];

    /** 
     * @return BelongsTo<\App\Models\Post, \App\Models\Comment> 
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /** 
     * @return BelongsTo<\App\Models\User, \App\Models\Comment> 
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Convert the comment's body to HTML.
     *
     * @return string
     */
    public function getBodyHtmlAttribute(): string
    {
        static $converter = null;

        if ($converter === null) {
            $config = [
                'html_input'         => 'strip',   // strip raw HTML in replies for safety
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