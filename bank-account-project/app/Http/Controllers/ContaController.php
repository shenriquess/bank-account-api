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
        $conta->AUD = $request->input('AUD');
        $conta->CAD = $request->input('CAD');
        $conta->CHF = $request->input('CHF');
        $conta->DDK = $request->input('DDK');
        $conta->EUR = $request->input('EUR');
        $conta->GBP = $request->input('GBP');
        $conta->JPY = $request->input('JPY');
        $conta->NOK = $request->input('NOK');
        $conta->SEK = $request->input('SEK');
        $conta->USD = $request->input('USD');
        $conta->BRL = $request->input('BRL');

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
        $conta->AUD = $request->input('AUD');
        $conta->CAD = $request->input('CAD');
        $conta->CHF = $request->input('CHF');
        $conta->DDK = $request->input('DDK');
        $conta->EUR = $request->input('EUR');
        $conta->GBP = $request->input('GBP');
        $conta->JPY = $request->input('JPY');
        $conta->NOK = $request->input('NOK');
        $conta->SEK = $request->input('SEK');
        $conta->USD = $request->input('USD');
        $conta->BRL = $request->input('BRL');

    
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
