<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaitingList extends Model
{
    use HasFactory;

    protected $table = 'waiting_list'; // Specify the table name if it's not the plural form of the model name

    protected $fillable = [
        'name',
        'email',
        'signup_source',
    ];
}