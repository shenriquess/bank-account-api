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
                'numero_conta' => $this->id,
                'AUD' => $this->AUD,
                'CAD' => $this->CAD,
                'CHF' => $this->CHF,
                'DDK' => $this->DDK,
                'EUR' => $this->EUR,
                'JPY' => $this->JPY,
                'NOK' => $this->NOK,
                'SEK' => $this->SEK,
                'USD' => $this->USD,
                'BRL' => $this->BRL,
                //'data_criacao' => $this->created_at,
                //'data_atualizacao' => $this->updated_at
          ];
    }
}
