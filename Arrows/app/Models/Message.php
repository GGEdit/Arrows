<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id', 'user_id', 'content', 'attachment_url',
    ];

    protected $appends = ['post_date'];

    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function getPostDateAttribute(){
        return (new Carbon($this->created_at))->format('Y-m-d');
    }
}
