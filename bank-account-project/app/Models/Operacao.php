<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operacao extends Model
{
    protected $table = 'operacoes';

    protected $fillable = ['id_conta', 'tipo', 'moeda', 'valor', 'created_at', 'updated_at'];

    function contas() {
        return $this->belongsTo(Conta::class);
    }
}
