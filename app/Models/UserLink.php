<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'url',
        'source',
        'click',
        'location',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function clicks()
    {
        return $this->hasMany(ClickLink::class);
    }
}
