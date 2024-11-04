<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meet extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id', 'room_id', 'meet_name',
    ];

    public function owner(){
        return $this->belongsTo('App\Models\User', 'owner_id');
    }
}
