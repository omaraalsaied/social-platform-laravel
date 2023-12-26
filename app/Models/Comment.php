<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Multicaret\Acquaintances\Traits\CanBeLiked;
use Multicaret\Acquaintances\Interaction;


class Comment extends Model
{
    use HasFactory,CanBeLiked;

    protected $fillable = [
        'body'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
    public function likes()
    {
        return $this->morphMany(Interaction::class, 'subject')
            ->where('relation', 'like')
            ->where('subject_type', 'App\Models\Comment'); 
    }
}
