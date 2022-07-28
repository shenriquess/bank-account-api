<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Conta extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,
            'dolar_australiano' => $this->dolar_australiano,
            'dolar_canadense' => $this->dolar_canadense,
            'franco_suico' => $this->franco_suico,
            'coroa_dinamarquesa' => $this->coroa_dinamarquesa,
            'euro' => $this->euro,
            'libra_esterlina' => $this->libra_esterlina,
            'iene' => $this->iene,
            'coroa_norueguesa'=> $this->coroa_norueguesa,
            'coroa_sueca' => $this->coroa_sueca,
            'dolar_eua' => $this->dolar_eua,
            'real' => $this->real,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
          ];
    }
}
