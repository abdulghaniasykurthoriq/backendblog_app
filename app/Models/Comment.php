<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;


class Comment extends Model
{
    use HasFactory;
    protected $fillable = [
        'comment',
        'user_id',
        'post_id'
    ];

    // 1 coment dimiliki oleh 1 user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
