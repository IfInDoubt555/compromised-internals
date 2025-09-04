<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

class Reply extends Model
{
    protected $fillable = ['thread_id','user_id','body'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime', 'updated_at' => 'datetime'];
    }

    public function thread() { return $this->belongsTo(Thread::class); }
    public function user()   { return $this->belongsTo(User::class); }

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