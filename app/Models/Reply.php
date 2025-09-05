<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @use HasFactory<\Database\Factories\ReplyFactory>
 */
class Reply extends Model
{
    use HasFactory;
    /** @var list<string> */
    protected $fillable = ['thread_id', 'user_id', 'body'];

    /** @return array<string,string> */
    protected function casts(): array
    {
        return ['created_at' => 'datetime', 'updated_at' => 'datetime'];
    }

    /** @return BelongsTo<Thread, Reply> */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    /** @return BelongsTo<User, Reply> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getBodyHtmlAttribute(): string
    {
        static $converter = null;

        if ($converter === null) {
            $config = [
                'html_input'         => 'strip', // strip raw HTML in replies for safety
                'allow_unsafe_links' => false,
            ];

            $env = new Environment($config);
            $env->addExtension(new CommonMarkCoreExtension());
            $converter = new MarkdownConverter($env);
        }

        return (string) $converter->convert((string) $this->body);
    }
}