<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conta extends Model
{
    protected $table = 'contas';

    protected $fillable = ['dolar_australiano', 'dolar_canadense', 'franco_suico', 'coroa_dinamarquesa',
                           'euro', 'libra_esterlina', 'iene', 'coroa_norueguesa', 'coroa_sueca',
                           'dolar_eua', 'real', 'created_at', 'updated_at'];
    
    public function operacoes()
    {
        return $this->hasMany(Operacao::class,'id_conta');
    }
}
