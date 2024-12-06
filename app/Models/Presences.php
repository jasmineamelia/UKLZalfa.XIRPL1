<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presences extends Model
{
    protected $table = 'presences';
    protected $primarykey = 'id';
    public $timestamps = null;
    protected $fillable = ['id_user', 'date', 'time', 'status'];
}
