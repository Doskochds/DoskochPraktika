<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OneTimeLink extends Model
{
use HasFactory;
use SoftDeletes;
protected $fillable = ['file_id', 'token'];
public function file()
{
        return $this->belongsTo(File::class);
}
}
