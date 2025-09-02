<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

class Reply extends Model
{
    protected $fillable = ['thread_id','user_id','body'];

    public function thread() { return $this->belongsTo(Thread::class); }
    public function user()   { return $this->belongsTo(User::class); }

    public function getBodyHtmlAttribute(): string
    {
        static $converter = null;
        if ($converter === null) {
            $env = new Environment([
                'html_input'         => 'strip', // STRIP raw HTML in replies for safety
                'allow_unsafe_links' => false,
            ]);
            $env->addExtension(new CommonMarkCoreExtension());
            $converter = new MarkdownConverter($env);
        }
        return $converter->convert((string) $this->body)->getContent();
    }
}