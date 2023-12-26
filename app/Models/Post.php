<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Multicaret\Acquaintances\Traits\CanBeLiked;


class Post extends Model
{
    use HasFactory,CanBeLiked;

    protected $fillable = [
        "title",
        "body",
        "user_id"
    ];

    protected $default = [
        'interactions'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function likes_count()
    {
        return $this->likersCount();
    }
    public function share()
    {
        return $this->hasMany(Share::class);
    }

}
