<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContaBanco extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'agencia',
        'conta',
        'convenio',
    ];
}
