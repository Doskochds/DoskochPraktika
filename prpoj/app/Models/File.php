<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class File extends Model
{
    protected $fillable = ['user_id', 'file_name', 'comment', 'delete_at'];
    use SoftDeletes;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function oneTimeLinks()
    {
        return $this->hasMany(OneTimeLink::class, 'file_id');
    }
}
