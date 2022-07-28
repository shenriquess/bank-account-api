<?php

namespace App\Http\Controllers;

use App\Models\Conta as Conta;
use App\Http\Resources\Conta as ContaResource;
use Illuminate\Http\Request;

class ContaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $contas = Conta::paginate(15);
        return ContaResource::collection($contas);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $conta = new Conta;
        $conta->dolar_australiano = $request->input('dolar_australiano');
        $conta->dolar_canadense = $request->input('dolar_canadense');
        $conta->franco_suico = $request->input('franco_suico');
        $conta->coroa_dinamarquesa = $request->input('coroa_dinamarquesa');
        $conta->euro = $request->input('euro');
        $conta->libra_esterlina = $request->input('libra_esterlina');
        $conta->iene = $request->input('iene');
        $conta->coroa_norueguesa= $request->input('coroa_norueguesa');
        $conta->coroa_sueca = $request->input('coroa_sueca');
        $conta->dolar_eua = $request->input('dolar_eua');
        $conta->real = $request->input('real');

    if( $conta->save() ){
      return new ContaResource( $conta );
    }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $conta = Conta::findOrFail( $id );
        return new ContaResource( $conta );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $conta = conta::findOrFail( $request->id );
        $conta->dolar_australiano = $request->input('dolar_australiano');
        $conta->dolar_canadense = $request->input('dolar_canadense');
        $conta->franco_suico = $request->input('franco_suico');
        $conta->coroa_dinamarquesa = $request->input('coroa_dinamarquesa');
        $conta->euro = $request->input('euro');
        $conta->libra_esterlina = $request->input('libra_esterlina');
        $conta->iene = $request->input('iene');
        $conta->coroa_norueguesa= $request->input('coroa_norueguesa');
        $conta->coroa_sueca = $request->input('coroa_sueca');
        $conta->dolar_eua = $request->input('dolar_eua');
        $conta->real = $request->input('real');
    
        if( $conta->save() ){
          return new ContaResource( $conta );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $conta = Conta::findOrFail( $id );
        if( $conta->delete() ){
            return new ContaResource( $conta );
        }
    }
}
