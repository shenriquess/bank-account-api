<?php

namespace App\Http\Controllers;

use App\Models\Operacao as Operacao;
use App\Http\Resources\Operacao as OperacaoResource;
use App\Http\Resources\Conta as ContaResource;
use App\Models\Conta as Conta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use DB;

class OperacaoController extends Controller
{
    
    /**
     * Salva uma nova operação no banco de dados.

     *
     * id_conta -> Número da conta
     * tipo -> (1) Depósito - (2) Saque
     * moeda -> (1) AUD - (2) CAD - (3) CHF - (4) DDK - (5) EUR - (6) GBP
     *          (7) JPY - (8) NOK - (9) SEK - (10) USD - (11) BRL
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $operacao = new Operacao;
        $operacao->id_conta = $request->input('id_conta');
        $operacao->tipo = $request->input('tipo');
        $operacao->moeda = $request->input('moeda');
        $operacao->valor = $request->input('valor');

        $conta = conta::findOrFail( $operacao->id_conta );

        if ($operacao->tipo == 1 && !is_null($conta)){

            $status = $this->deposito($operacao, $conta);

        } elseif ($operacao->tipo == 2 && !is_null($conta)) {

            $status = $this->saque($operacao, $conta);

        } else {
            return response()->json([
                "message" => "Operação inválida ou conta não encontrada."
              ], 402);
        }
        
        if($status){ 
            if ( $conta->save() && $operacao->save()){
                return $this->show($conta->id);
            }else{
                return response()->json([
                    "message" => "Operação falhou ou não há saldo disponível para saque."
                ], 402); 
            }
        }else{
            return response()->json([
                "message" => "Não há saldo disponível para saque."
            ], 402); 
        }
    }

    /**
     * Exibe todas as operações de uma determinada conta.
     *
     * @param  int  $id_conta
     * @return \Illuminate\Http\Response
     */
    public function show($id_conta)
    {
        //
        //$operacoes = Conta::find($id_conta)->operacoes;
        $operacoes = DB::select('SELECT op.id_conta,
                                   CASE
                                        WHEN tipo = 1 THEN "DEPÓSITO"
                                        WHEN tipo = 2 THEN "SAQUE"
                                   END AS tipo_operacao,
                                   CASE
                                        WHEN moeda = 1 THEN "AUD"
                                        WHEN moeda = 2 THEN "CAD"
                                        WHEN moeda = 3 THEN "CHF"
                                        WHEN moeda = 4 THEN "DKK"
                                        WHEN moeda = 5 THEN "EUR"
                                        WHEN moeda = 6 THEN "GBP"
                                        WHEN moeda = 7 THEN "JPY"
                                        WHEN moeda = 8 THEN "NOK"
                                        WHEN moeda = 9 THEN "SEK"
                                        WHEN moeda = 10 THEN "USD"
                                        ELSE "BRL"
                                   END AS tipo_moeda, FORMAT(op.valor, 2) as valor, op.created_at as data_hora
                                   FROM operacoes op
                                   WHERE op.id_conta >= "'.$id_conta.'"
                                   
                                   ');
        return new OperacaoResource( $operacoes );
    }

    /**
     * Exibe o saldo de todas as moedas de uma conta, ou da moeda especificada.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function saldo($id_conta, $moeda = 0)
    {
        //
        $conta = Conta::find( $id_conta );
        
        if(is_null($conta)){
            return response()->json([
                "message" => "Conta inválida."
              ], 402);
        }

        if (!is_null($conta) && $moeda == 0){
            $conta = Conta::findOrFail( $id_conta );
            return new ContaResource( $conta );
        } elseif (!is_null($conta) && $moeda == 1) {
            $conta = DB::select('SELECT id as id_conta, FORMAT(AUD, 2) as AUD
                                   FROM contas');
            return new OperacaoResource( $conta );
        } elseif (!is_null($conta) && $moeda == 2) {
            $conta = DB::select('SELECT id as id_conta, FORMAT(CAD, 2) as CAD
                                   FROM contas');
            return new OperacaoResource( $conta );
        } elseif (!is_null($conta) && $moeda == 3) {
            $conta = DB::select('SELECT id as id_conta, FORMAT(CHF, 2) as CHF
                                   FROM contas');
            return new OperacaoResource( $conta );
        } elseif (!is_null($conta) && $moeda == 4) {
            $conta = DB::select('SELECT id as id_conta, FORMAT(DDK, 2) as DDK
                                   FROM contas');
            return new OperacaoResource( $conta );
        } elseif (!is_null($conta) && $moeda == 5) {
            $conta = DB::select('SELECT id as id_conta, FORMAT(EUR, 2) as EUR
                                   FROM contas');
            return new OperacaoResource( $conta );
        } elseif (!is_null($conta) && $moeda == 6) {
            $conta = DB::select('SELECT id as id_conta, FORMAT(GBP, 2) as GBP
                                   FROM contas');
            return new OperacaoResource( $conta );
        } elseif (!is_null($conta) && $moeda == 7) {
            $conta = DB::select('SELECT id as id_conta, FORMAT(JPY, 2) as JPY
                                   FROM contas');
            return new OperacaoResource( $conta );
        } elseif (!is_null($conta) && $moeda == 8) {
            $conta = DB::select('SELECT id as id_conta, FORMAT(NOK, 2) as NOK
                                   FROM contas');
            return new OperacaoResource( $conta );
        } elseif (!is_null($conta) && $moeda == 9) {
            $conta = DB::select('SELECT id as id_conta, FORMAT(SEK, 2) as SEK
                                   FROM contas');
            return new OperacaoResource( $conta );
        } elseif (!is_null($conta) && $moeda == 10) {
            $conta = DB::select('SELECT id as id_conta, FORMAT(USD, 2) as USD
                                   FROM contas');
            return new OperacaoResource( $conta );
        } elseif (!is_null($conta) && $moeda == 11) {
            $conta = DB::select('SELECT id as id_conta, FORMAT(BRL, 2) as BRL
                                   FROM contas');
            return new OperacaoResource( $conta );
        } else {
            return response()->json([
                "message" => "Moeda inválida."
              ], 402);
        }
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
    }

    public function fetch($moeda, $data)
    {   
        $apiURL = "https://olinda.bcb.gov.br/olinda/servico/PTAX/versao/v1/odata/CotacaoMoedaDia(moeda=@moeda,dataCotacao=@dataCotacao)?%40moeda='{$moeda}'&%40dataCotacao='{$data}'";
        
        //dd($apiURL);
        $response = Http::get($apiURL);

        $cotacao = json_decode($response->getBody(),true);

        //dd($cotacao['value'][4]['cotacaoVenda']);
        return $cotacao;
    }

    public function exibirCotacao($moeda){
        if($moeda == '1'){
            return $this->fetch('AUD', date('m/d/Y'));
        } elseif ($moeda == '2') {
            return $this->fetch('CAD', date('m/d/Y'));
        } elseif ($moeda == '3') {
            return $this->fetch('CHF', date('m/d/Y'));
        } elseif ($moeda == '4') {
            return $this->fetch('DDK', date('m/d/Y'));
        } elseif ($moeda == '5') {
            return $this->fetch('EUR', date('m/d/Y'));
        } elseif ($moeda == '6') {
            return $this->fetch('GBP', date('m/d/Y'));
        } elseif ($moeda == '7') {
            return $this->fetch('JPY', date('m/d/Y'));
        } elseif ($moeda == '8') {
            return $this->fetch('NOK', date('m/d/Y'));
        } elseif ($moeda == '9') {
            return $this->fetch('SEK', date('m/d/Y'));
        } elseif ($moeda == '10') {
            return $this->fetch('USD', date('m/d/Y'));
        } else {
            return response()->json([
                "message" => "Moeda inválida."
              ], 402);
        }
        
    }

    public function deposito(Operacao $operacao, Conta $conta)
    {
        if($operacao->moeda == '1' && is_numeric($operacao->valor)){
            $conta->AUD += $operacao->valor;
            return TRUE;
        } elseif ($operacao->moeda == '2' && is_numeric($operacao->valor)) {
            $conta->CAD += $operacao->valor;
            return TRUE;
        } elseif ($operacao->moeda == '3' && is_numeric($operacao->valor)) {
            $conta->CHF += $operacao->valor;
            return TRUE;
        } elseif ($operacao->moeda == '4' && is_numeric($operacao->valor)) {
            $conta->DDK += $operacao->valor;
            return TRUE;
        } elseif ($operacao->moeda == '5' && is_numeric($operacao->valor)) {
            $conta->EUR += $operacao->valor;
            return TRUE;
        } elseif ($operacao->moeda == '6' && is_numeric($operacao->valor)) {
            $conta->GBP += $operacao->valor;
            return TRUE;
        } elseif ($operacao->moeda == '7' && is_numeric($operacao->valor)) {
            $conta->JPY += $operacao->valor;
            return TRUE;
        } elseif ($operacao->moeda == '8' && is_numeric($operacao->valor)) {
            $conta->NOK += $operacao->valor;
            return TRUE;
        } elseif ($operacao->moeda == '9' && is_numeric($operacao->valor)) {
            $conta->SEK += $operacao->valor;
            return TRUE;
        } elseif ($operacao->moeda == '10' && is_numeric($operacao->valor)) {
            $conta->USD += $operacao->valor;
            return TRUE;
        } elseif ($operacao->moeda == '11' && is_numeric($operacao->valor)) {
            $conta->BRL += $operacao->valor;
            return TRUE;
        } else {
            return response()->json([
                "message" => "Moeda ou valor inválido."
              ], 402);
        }
    }

    public function saque(Operacao $operacao, Conta $conta)
    {
        if($operacao->moeda == '1' && is_numeric($operacao->valor)){
            return $this->saqueAUD($operacao, $conta);       
        }elseif ($operacao->moeda == '2' && is_numeric($operacao->valor)) {
            return $this->saqueCAD($operacao, $conta);       
        }elseif ($operacao->moeda == '3' && is_numeric($operacao->valor)) {
            return $this->saqueCHF($operacao, $conta);
        }elseif ($operacao->moeda == '4' && is_numeric($operacao->valor)) {
            return $this->saqueDDK($operacao, $conta);
        }elseif ($operacao->moeda == '5' && is_numeric($operacao->valor)) {
            return $this->saqueEUR($operacao, $conta);
        }elseif ($operacao->moeda == '6' && is_numeric($operacao->valor)) {
            return $this->saqueGBP($operacao, $conta);
        }elseif ($operacao->moeda == '7' && is_numeric($operacao->valor)) {
            return $this->saqueJPY($operacao, $conta);
        }elseif ($operacao->moeda == '8' && is_numeric($operacao->valor)) {
            return $this->saqueNOK($operacao, $conta);
        }elseif ($operacao->moeda == '9' && is_numeric($operacao->valor)) {
            return $this->saqueSEK($operacao, $conta);
        }elseif ($operacao->moeda == '10' && is_numeric($operacao->valor)) {
            return $this->saqueUSD($operacao, $conta);
        }elseif ($operacao->moeda == '11' && is_numeric($operacao->valor)) {
            return $this->saqueBRL($operacao, $conta);
        }else{
            return FALSE;
        }

    }

    public function saqueAUD(Operacao $operacao, Conta $conta)
    {
        if($operacao->valor <= $conta->AUD){
            $conta->AUD -= $operacao->valor;
            return TRUE;
        }else{

            if($conta->CAD > 0){
                $cotacao_aud = $this->exibirCotacao(1); 
                $cotacao_cad = $this->exibirCotacao(2);
                $aux = ($conta->CAD * $cotacao_cad['value'][4]['cotacaoCompra'] / $cotacao_aud['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->CAD =  ($aux * $cotacao_aud['value'][4]['cotacaoCompra'] / $cotacao_cad['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->CHF > 0) {
                $cotacao_aud = $this->exibirCotacao(1); 
                $cotacao_chf = $this->exibirCotacao(3);
                $aux = ($conta->CHF * $cotacao_chf['value'][4]['cotacaoCompra'] / $cotacao_aud['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->CHF =  ($aux * $cotacao_aud['value'][4]['cotacaoCompra'] / $cotacao_chf['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->DDK > 0) {
                $cotacao_aud = $this->exibirCotacao(1); 
                $cotacao_ddk = $this->exibirCotacao(4);
                $aux = ($conta->DDK * $cotacao_ddk['value'][4]['cotacaoCompra'] / $cotacao_aud['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->DDK =  ($aux * $cotacao_aud['value'][4]['cotacaoCompra'] / $cotacao_ddk['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            
            }elseif ($conta->EUR > 0) {
                $cotacao_aud = $this->exibirCotacao(1); 
                $cotacao_eur = $this->exibirCotacao(5);
                $aux = ($conta->EUR * $cotacao_eur['value'][4]['cotacaoCompra'] / $cotacao_aud['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->EUR =  ($aux * $cotacao_aud['value'][4]['cotacaoCompra'] / $cotacao_eur['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->GBP > 0) {
                $cotacao_aud = $this->exibirCotacao(1); 
                $cotacao_gbp = $this->exibirCotacao(6);
                $aux = ($conta->GBP * $cotacao_gbp['value'][4]['cotacaoCompra'] / $cotacao_aud['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->GBP =  ($aux * $cotacao_aud['value'][4]['cotacaoCompra'] / $cotacao_gbp['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->JPY > 0) {
                $cotacao_aud = $this->exibirCotacao(1); 
                $cotacao_jpy = $this->exibirCotacao(7);
                $aux = ($conta->JPY * $cotacao_jpy['value'][4]['cotacaoCompra'] / $cotacao_aud['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->JPY =  ($aux * $cotacao_aud['value'][4]['cotacaoCompra'] / $cotacao_jpy['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->NOK > 0) {
                $cotacao_aud = $this->exibirCotacao(1); 
                $cotacao_nok = $this->exibirCotacao(8);
                $aux = ($conta->NOK * $cotacao_nok['value'][4]['cotacaoCompra'] / $cotacao_aud['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->NOK =  ($aux * $cotacao_aud['value'][4]['cotacaoCompra'] / $cotacao_nok['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->SEK > 0) {
                $cotacao_aud = $this->exibirCotacao(1); 
                $cotacao_sek = $this->exibirCotacao(9);
                $aux = ($conta->SEK * $cotacao_sek['value'][4]['cotacaoCompra'] / $cotacao_aud['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->SEK =  ($aux * $cotacao_aud['value'][4]['cotacaoCompra'] / $cotacao_sek['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->USD > 0) {
                $cotacao_aud = $this->exibirCotacao(1); 
                $cotacao_usd = $this->exibirCotacao(10);
                $aux = ($conta->USD * $cotacao_usd['value'][4]['cotacaoCompra'] / $cotacao_aud['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->USD =  ($aux * $cotacao_aud['value'][4]['cotacaoCompra'] / $cotacao_usd['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            } elseif ($conta->BRL > 0) {
                $cotacao_aud = $this->exibirCotacao(1); 
                $aux = ($conta->BRL / $cotacao_aud['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->BRL =  ($aux *  $cotacao_aud['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }else {
                return FALSE;
            }
            
            
        }

    }

    public function saqueCAD(Operacao $operacao, Conta $conta)
    {
        if($operacao->valor <= $conta->CAD){
            $conta->CAD -= $operacao->valor;
            return TRUE;
        }else{

            if($conta->AUD > 0){
                $cotacao_aud = $this->exibirCotacao(1); 
                $cotacao_cad = $this->exibirCotacao(2);
                $aux = ($conta->AUD * $cotacao_aud['value'][4]['cotacaoCompra'] / $cotacao_cad['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->AUD =  ($aux * $cotacao_cad['value'][4]['cotacaoCompra'] / $cotacao_aud['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->CHF > 0) {
                $cotacao_cad = $this->exibirCotacao(2); 
                $cotacao_chf = $this->exibirCotacao(3);
                $aux = ($conta->CHF * $cotacao_chf['value'][4]['cotacaoCompra'] / $cotacao_cad['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->CHF =  ($aux * $cotacao_cad['value'][4]['cotacaoCompra'] / $cotacao_chf['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->DDK > 0) {
                $cotacao_cad = $this->exibirCotacao(2); 
                $cotacao_ddk = $this->exibirCotacao(4);
                $aux = ($conta->DDK * $cotacao_ddk['value'][4]['cotacaoCompra'] / $cotacao_cad['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->DDK =  ($aux * $cotacao_acad['value'][4]['cotacaoCompra'] / $cotacao_ddk['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            
            }elseif ($conta->EUR > 0) {
                $cotacao_cad = $this->exibirCotacao(2); 
                $cotacao_eur = $this->exibirCotacao(5);
                $aux = ($conta->EUR * $cotacao_eur['value'][4]['cotacaoCompra'] / $cotacao_['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->EUR =  ($aux * $cotacao_cad['value'][4]['cotacaoCompra'] / $cotacao_eur['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->GBP > 0) {
                $cotacao_cad = $this->exibirCotacao(2); 
                $cotacao_gbp = $this->exibirCotacao(6);
                $aux = ($conta->GBP * $cotacao_gbp['value'][4]['cotacaoCompra'] / $cotacao_cad['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->GBP =  ($aux * $cotacao_cad['value'][4]['cotacaoCompra'] / $cotacao_gbp['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->JPY > 0) {
                $cotacao_cad = $this->exibirCotacao(2); 
                $cotacao_jpy = $this->exibirCotacao(7);
                $aux = ($conta->JPY * $cotacao_jpy['value'][4]['cotacaoCompra'] / $cotacao_cad['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->JPY =  ($aux * $cotacao_cad['value'][4]['cotacaoCompra'] / $cotacao_jpy['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->NOK > 0) {
                $cotacao_cad = $this->exibirCotacao(2); 
                $cotacao_nok = $this->exibirCotacao(8);
                $aux = ($conta->NOK * $cotacao_nok['value'][4]['cotacaoCompra'] / $cotacao_cad['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->NOK =  ($aux * $cotacao_cad['value'][4]['cotacaoCompra'] / $cotacao_nok['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->SEK > 0) {
                $cotacao_cad = $this->exibirCotacao(2); 
                $cotacao_sek = $this->exibirCotacao(9);
                $aux = ($conta->SEK * $cotacao_sek['value'][4]['cotacaoCompra'] / $cotacao_cad['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->SEK =  ($aux * $cotacao_cad['value'][4]['cotacaoCompra'] / $cotacao_sek['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->USD > 0) {
                $cotacao_cad = $this->exibirCotacao(2); 
                $cotacao_usd = $this->exibirCotacao(10);
                $aux = ($conta->USD * $cotacao_usd['value'][4]['cotacaoCompra'] / $cotacao_cad['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->USD =  ($aux * $cotacao_cad['value'][4]['cotacaoCompra'] / $cotacao_usd['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            } elseif ($conta->BRL > 0) {
                $cotacao_cad = $this->exibirCotacao(2); 
                $aux = ($conta->BRL / $cotacao_cad['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->BRL =  ($aux *  $cotacao_cad['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }else {
                return FALSE;
            }
                
        }

    }

    public function saqueCHF(Operacao $operacao, Conta $conta)
    {
        if($operacao->valor <= $conta->CHF){
            $conta->CHF -= $operacao->valor;
            return TRUE;
        }else{

            if($conta->CAD > 0){
                $cotacao_chd = $this->exibirCotacao(3); 
                $cotacao_cad = $this->exibirCotacao(2);
                $aux = ($conta->CAD * $cotacao_cad['value'][4]['cotacaoCompra'] / $cotacao_chf['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->CAD =  ($aux * $cotacao_chf['value'][4]['cotacaoCompra'] / $cotacao_cad['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->AUD > 0) {
                $cotacao_aud = $this->exibirCotacao(1); 
                $cotacao_chf = $this->exibirCotacao(3);
                $aux = ($conta->AUD * $cotacao_aud['value'][4]['cotacaoCompra'] / $cotacao_chf['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->AUD =  ($aux * $cotacao_chf['value'][4]['cotacaoCompra'] / $cotacao_aud['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->DDK > 0) {
                $cotacao_chf = $this->exibirCotacao(3); 
                $cotacao_ddk = $this->exibirCotacao(4);
                $aux = ($conta->DDK * $cotacao_ddk['value'][4]['cotacaoCompra'] / $cotacao_chf['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->DDK =  ($aux * $cotacao_chf['value'][4]['cotacaoCompra'] / $cotacao_ddk['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            
            }elseif ($conta->EUR > 0) {
                $cotacao_chf = $this->exibirCotacao(3); 
                $cotacao_eur = $this->exibirCotacao(5);
                $aux = ($conta->EUR * $cotacao_eur['value'][4]['cotacaoCompra'] / $cotacao_chf['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->EUR =  ($aux * $cotacao_chf['value'][4]['cotacaoCompra'] / $cotacao_eur['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->GBP > 0) {
                $cotacao_chf = $this->exibirCotacao(3); 
                $cotacao_gbp = $this->exibirCotacao(6);
                $aux = ($conta->GBP * $cotacao_gbp['value'][4]['cotacaoCompra'] / $cotacao_chf['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->GBP =  ($aux * $cotacao_chf['value'][4]['cotacaoCompra'] / $cotacao_gbp['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->JPY > 0) {
                $cotacao_chf = $this->exibirCotacao(3); 
                $cotacao_jpy = $this->exibirCotacao(7);
                $aux = ($conta->JPY * $cotacao_jpy['value'][4]['cotacaoCompra'] / $cotacao_chf['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->JPY =  ($aux * $cotacao_chf['value'][4]['cotacaoCompra'] / $cotacao_jpy['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->NOK > 0) {
                $cotacao_chf = $this->exibirCotacao(3); 
                $cotacao_nok = $this->exibirCotacao(8);
                $aux = ($conta->NOK * $cotacao_nok['value'][4]['cotacaoCompra'] / $cotacao_chf['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->NOK =  ($aux * $cotacao_chf['value'][4]['cotacaoCompra'] / $cotacao_nok['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->SEK > 0) {
                $cotacao_chf = $this->exibirCotacao(3); 
                $cotacao_sek = $this->exibirCotacao(9);
                $aux = ($conta->SEK * $cotacao_sek['value'][4]['cotacaoCompra'] / $cotacao_chf['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->SEK =  ($aux * $cotacao_chf['value'][4]['cotacaoCompra'] / $cotacao_sek['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->USD > 0) {
                $cotacao_chf = $this->exibirCotacao(3); 
                $cotacao_usd = $this->exibirCotacao(10);
                $aux = ($conta->USD * $cotacao_usd['value'][4]['cotacaoCompra'] / $cotacao_chf['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->USD =  ($aux * $cotacao_chf['value'][4]['cotacaoCompra'] / $cotacao_usd['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            } elseif ($conta->BRL > 0) {
                $cotacao_chf = $this->exibirCotacao(3); 
                $aux = ($conta->BRL / $cotacao_chf['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->BRL =  ($aux *  $cotacao_chf['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }else {
                return FALSE;
            }
            
            
        }

    }

    public function saqueDDK(Operacao $operacao, Conta $conta)
    {
        if($operacao->valor <= $conta->DDK){
            $conta->DDK -= $operacao->valor;
            return TRUE;
        }else{

            if($conta->CAD > 0){
                $cotacao_DDK = $this->exibirCotacao(4); 
                $cotacao_cad = $this->exibirCotacao(2);
                $aux = ($conta->CAD * $cotacao_cad['value'][4]['cotacaoCompra'] / $cotacao_ddk['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->CAD =  ($aux * $cotacao_ddk['value'][4]['cotacaoCompra'] / $cotacao_cad['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->CHF > 0) {
                $cotacao_ddk = $this->exibirCotacao(4); 
                $cotacao_chf = $this->exibirCotacao(3);
                $aux = ($conta->CHF * $cotacao_chf['value'][4]['cotacaoCompra'] / $cotacao_ddk['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->CHF =  ($aux * $cotacao_ddk['value'][4]['cotacaoCompra'] / $cotacao_chf['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->AUD > 0) {
                $cotacao_aud = $this->exibirCotacao(1); 
                $cotacao_ddk = $this->exibirCotacao(4);
                $aux = ($conta->AUD * $cotacao_aud['value'][4]['cotacaoCompra'] / $cotacao_ddk['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->AUD =  ($aux * $cotacao_ddk['value'][4]['cotacaoCompra'] / $cotacao_aud['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            
            }elseif ($conta->EUR > 0) {
                $cotacao_ddk = $this->exibirCotacao(4); 
                $cotacao_eur = $this->exibirCotacao(5);
                $aux = ($conta->EUR * $cotacao_eur['value'][4]['cotacaoCompra'] / $cotacao_ddk['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->EUR =  ($aux * $cotacao_ddk['value'][4]['cotacaoCompra'] / $cotacao_eur['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->GBP > 0) {
                $cotacao_ddk = $this->exibirCotacao(4); 
                $cotacao_gbp = $this->exibirCotacao(6);
                $aux = ($conta->GBP * $cotacao_gbp['value'][4]['cotacaoCompra'] / $cotacao_ddk['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->GBP =  ($aux * $cotacao_ddk['value'][4]['cotacaoCompra'] / $cotacao_gbp['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->JPY > 0) {
                $cotacao_ddk = $this->exibirCotacao(4); 
                $cotacao_jpy = $this->exibirCotacao(7);
                $aux = ($conta->JPY * $cotacao_jpy['value'][4]['cotacaoCompra'] / $cotacao_ddk['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->JPY =  ($aux * $cotacao_ddk['value'][4]['cotacaoCompra'] / $cotacao_jpy['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->NOK > 0) {
                $cotacao_ddk = $this->exibirCotacao(4); 
                $cotacao_nok = $this->exibirCotacao(8);
                $aux = ($conta->NOK * $cotacao_nok['value'][4]['cotacaoCompra'] / $cotacao_ddk['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->NOK =  ($aux * $cotacao_ddk['value'][4]['cotacaoCompra'] / $cotacao_nok['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->SEK > 0) {
                $cotacao_ddk = $this->exibirCotacao(4); 
                $cotacao_sek = $this->exibirCotacao(9);
                $aux = ($conta->SEK * $cotacao_sek['value'][4]['cotacaoCompra'] / $cotacao_ddk['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->SEK =  ($aux * $cotacao_ddk['value'][4]['cotacaoCompra'] / $cotacao_sek['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->USD > 0) {
                $cotacao_ddk = $this->exibirCotacao(4); 
                $cotacao_usd = $this->exibirCotacao(10);
                $aux = ($conta->USD * $cotacao_usd['value'][4]['cotacaoCompra'] / $cotacao_ddk['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->USD =  ($aux * $cotacao_ddk['value'][4]['cotacaoCompra'] / $cotacao_usd['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            } elseif ($conta->BRL > 0) {
                $cotacao_ddk = $this->exibirCotacao(4); 
                $aux = ($conta->BRL / $cotacao_ddk['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->BRL =  ($aux *  $cotacao_ddk['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }else {
                return FALSE;
            }
            
            
        }

    }

    public function saqueEUR(Operacao $operacao, Conta $conta)
    {
        if($operacao->valor <= $conta->EUR){
            $conta->EUR -= $operacao->valor;
            return TRUE;
        }else{

            if($conta->CAD > 0){
                $cotacao_eur = $this->exibirCotacao(5); 
                $cotacao_cad = $this->exibirCotacao(2);
                $aux = ($conta->CAD * $cotacao_cad['value'][4]['cotacaoCompra'] / $cotacao_eur['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->CAD =  ($aux * $cotacao_eur['value'][4]['cotacaoCompra'] / $cotacao_cad['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->CHF > 0) {
                $cotacao_eur = $this->exibirCotacao(5); 
                $cotacao_chf = $this->exibirCotacao(3);
                $aux = ($conta->CHF * $cotacao_chf['value'][4]['cotacaoCompra'] / $cotacao_eur['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->CHF =  ($aux * $cotacao_eur['value'][4]['cotacaoCompra'] / $cotacao_chf['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->DDK > 0) {
                $cotacao_eur = $this->exibirCotacao(5); 
                $cotacao_ddk = $this->exibirCotacao(4);
                $aux = ($conta->DDK * $cotacao_ddk['value'][4]['cotacaoCompra'] / $cotacao_eur['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->DDK =  ($aux * $cotacao_eur['value'][4]['cotacaoCompra'] / $cotacao_ddk['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            
            }elseif ($conta->AUD > 0) {
                $cotacao_aud = $this->exibirCotacao(1); 
                $cotacao_eur = $this->exibirCotacao(5);
                $aux = ($conta->AUD * $cotacao_aud['value'][4]['cotacaoCompra'] / $cotacao_eur['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->AUD =  ($aux * $cotacao_eur['value'][4]['cotacaoCompra'] / $cotacao_aud['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->GBP > 0) {
                $cotacao_eur = $this->exibirCotacao(5); 
                $cotacao_gbp = $this->exibirCotacao(6);
                $aux = ($conta->GBP * $cotacao_gbp['value'][4]['cotacaoCompra'] / $cotacao_eur['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->GBP =  ($aux * $cotacao_eur['value'][4]['cotacaoCompra'] / $cotacao_gbp['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->JPY > 0) {
                $cotacao_eur = $this->exibirCotacao(5); 
                $cotacao_jpy = $this->exibirCotacao(7);
                $aux = ($conta->JPY * $cotacao_jpy['value'][4]['cotacaoCompra'] / $cotacao_eur['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->JPY =  ($aux * $cotacao_eur['value'][4]['cotacaoCompra'] / $cotacao_jpy['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->NOK > 0) {
                $cotacao_eur = $this->exibirCotacao(5); 
                $cotacao_nok = $this->exibirCotacao(8);
                $aux = ($conta->NOK * $cotacao_nok['value'][4]['cotacaoCompra'] / $cotacao_eur['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->NOK =  ($aux * $cotacao_eur['value'][4]['cotacaoCompra'] / $cotacao_nok['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->SEK > 0) {
                $cotacao_eur = $this->exibirCotacao(5); 
                $cotacao_sek = $this->exibirCotacao(9);
                $aux = ($conta->SEK * $cotacao_sek['value'][4]['cotacaoCompra'] / $cotacao_eur['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->SEK =  ($aux * $cotacao_eur['value'][4]['cotacaoCompra'] / $cotacao_sek['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->USD > 0) {
                $cotacao_eur = $this->exibirCotacao(5); 
                $cotacao_usd = $this->exibirCotacao(10);
                $aux = ($conta->USD * $cotacao_usd['value'][4]['cotacaoCompra'] / $cotacao_eur['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->USD =  ($aux * $cotacao_eur['value'][4]['cotacaoCompra'] / $cotacao_usd['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            } elseif ($conta->BRL > 0) {
                $cotacao_eur = $this->exibirCotacao(1); 
                $aux = ($conta->BRL / $cotacao_eur['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->BRL =  ($aux *  $cotacao_eur['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }else {
                return FALSE;
            }
            
            
        }

    }

    public function saqueGBP(Operacao $operacao, Conta $conta)
    {
        if($operacao->valor <= $conta->GBP){
            $conta->GBP -= $operacao->valor;
            return TRUE;
        }else{

            if($conta->CAD > 0){
                $cotacao_gbp = $this->exibirCotacao(6); 
                $cotacao_cad = $this->exibirCotacao(2);
                $aux = ($conta->CAD * $cotacao_cad['value'][4]['cotacaoCompra'] / $cotacao_gbp['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->CAD =  ($aux * $cotacao_gbp['value'][4]['cotacaoCompra'] / $cotacao_cad['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->CHF > 0) {
                $cotacao_gbp = $this->exibirCotacao(6); 
                $cotacao_chf = $this->exibirCotacao(3);
                $aux = ($conta->CHF * $cotacao_chf['value'][4]['cotacaoCompra'] / $cotacao_gbp['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->CHF =  ($aux * $cotacao_gbp['value'][4]['cotacaoCompra'] / $cotacao_chf['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->DDK > 0) {
                $cotacao_gbp = $this->exibirCotacao(6); 
                $cotacao_ddk = $this->exibirCotacao(4);
                $aux = ($conta->DDK * $cotacao_ddk['value'][4]['cotacaoCompra'] / $cotacao_gbp['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->DDK =  ($aux * $cotacao_gbp['value'][4]['cotacaoCompra'] / $cotacao_ddk['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            
            }elseif ($conta->EUR > 0) {
                $cotacao_gbp = $this->exibirCotacao(6); 
                $cotacao_eur = $this->exibirCotacao(5);
                $aux = ($conta->EUR * $cotacao_eur['value'][4]['cotacaoCompra'] / $cotacao_gbp['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->EUR =  ($aux * $cotacao_gbp['value'][4]['cotacaoCompra'] / $cotacao_eur['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->AUD > 0) {
                $cotacao_aud = $this->exibirCotacao(1); 
                $cotacao_gbp = $this->exibirCotacao(6);
                $aux = ($conta->AUD * $cotacao_aud['value'][4]['cotacaoCompra'] / $cotacao_gbp['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->AUD =  ($aux * $cotacao_gbp['value'][4]['cotacaoCompra'] / $cotacao_aud['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->JPY > 0) {
                $cotacao_gbp = $this->exibirCotacao(6); 
                $cotacao_jpy = $this->exibirCotacao(7);
                $aux = ($conta->JPY * $cotacao_jpy['value'][4]['cotacaoCompra'] / $cotacao_gbp['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->JPY =  ($aux * $cotacao_gbp['value'][4]['cotacaoCompra'] / $cotacao_jpy['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->NOK > 0) {
                $cotacao_gbp = $this->exibirCotacao(6); 
                $cotacao_nok = $this->exibirCotacao(8);
                $aux = ($conta->NOK * $cotacao_nok['value'][4]['cotacaoCompra'] / $cotacao_gbp['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->NOK =  ($aux * $cotacao_gbp['value'][4]['cotacaoCompra'] / $cotacao_nok['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->SEK > 0) {
                $cotacao_gbp = $this->exibirCotacao(6); 
                $cotacao_sek = $this->exibirCotacao(9);
                $aux = ($conta->SEK * $cotacao_sek['value'][4]['cotacaoCompra'] / $cotacao_gbp['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->SEK =  ($aux * $cotacao_gbp['value'][4]['cotacaoCompra'] / $cotacao_sek['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->USD > 0) {
                $cotacao_gbp = $this->exibirCotacao(6); 
                $cotacao_usd = $this->exibirCotacao(10);
                $aux = ($conta->USD * $cotacao_usd['value'][4]['cotacaoCompra'] / $cotacao_gbp['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->USD =  ($aux * $cotacao_gbp['value'][4]['cotacaoCompra'] / $cotacao_usd['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            } elseif ($conta->BRL > 0) {
                $cotacao_gbp = $this->exibirCotacao(6); 
                $aux = ($conta->BRL / $cotacao_gbp['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->BRL =  ($aux *  $cotacao_gbp['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }else {
                return FALSE;
            }
            
            
        }

    }

    public function saqueJPY(Operacao $operacao, Conta $conta)
    {
        if($operacao->valor <= $conta->JPY){
            $conta->JPY -= $operacao->valor;
            return TRUE;
        }else{

            if($conta->CAD > 0){
                $cotacao_jpy = $this->exibirCotacao(7); 
                $cotacao_cad = $this->exibirCotacao(2);
                $aux = ($conta->CAD * $cotacao_cad['value'][4]['cotacaoCompra'] / $cotacao_jpy['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->CAD =  ($aux * $cotacao_jpy['value'][4]['cotacaoCompra'] / $cotacao_cad['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->CHF > 0) {
                $cotacao_jpy = $this->exibirCotacao(7); 
                $cotacao_chf = $this->exibirCotacao(3);
                $aux = ($conta->CHF * $cotacao_chf['value'][4]['cotacaoCompra'] / $cotacao_jpy['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->CHF =  ($aux * $cotacao_jpy['value'][4]['cotacaoCompra'] / $cotacao_chf['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->DDK > 0) {
                $cotacao_jpy = $this->exibirCotacao(7); 
                $cotacao_ddk = $this->exibirCotacao(4);
                $aux = ($conta->DDK * $cotacao_ddk['value'][4]['cotacaoCompra'] / $cotacao_jpy['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->DDK =  ($aux * $cotacao_jpy['value'][4]['cotacaoCompra'] / $cotacao_ddk['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            
            }elseif ($conta->EUR > 0) {
                $cotacao_jpy = $this->exibirCotacao(7); 
                $cotacao_eur = $this->exibirCotacao(5);
                $aux = ($conta->EUR * $cotacao_eur['value'][4]['cotacaoCompra'] / $cotacao_jpy['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->EUR =  ($aux * $cotacao_jpy['value'][4]['cotacaoCompra'] / $cotacao_eur['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->GBP > 0) {
                $cotacao_jpy = $this->exibirCotacao(7); 
                $cotacao_gbp = $this->exibirCotacao(6);
                $aux = ($conta->GBP * $cotacao_gbp['value'][4]['cotacaoCompra'] / $cotacao_jpy['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->GBP =  ($aux * $cotacao_jpy['value'][4]['cotacaoCompra'] / $cotacao_gbp['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->AUD > 0) {
                $cotacao_aud = $this->exibirCotacao(1); 
                $cotacao_jpy = $this->exibirCotacao(7);
                $aux = ($conta->AUD * $cotacao_aud['value'][4]['cotacaoCompra'] / $cotacao_jpy['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->AUD =  ($aux * $cotacao_jpy['value'][4]['cotacaoCompra'] / $cotacao_aud['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->NOK > 0) {
                $cotacao_jpy = $this->exibirCotacao(7); 
                $cotacao_nok = $this->exibirCotacao(8);
                $aux = ($conta->NOK * $cotacao_nok['value'][4]['cotacaoCompra'] / $cotacao_jpy['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->NOK =  ($aux * $cotacao_jpy['value'][4]['cotacaoCompra'] / $cotacao_nok['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->SEK > 0) {
                $cotacao_jpy = $this->exibirCotacao(7); 
                $cotacao_sek = $this->exibirCotacao(9);
                $aux = ($conta->SEK * $cotacao_sek['value'][4]['cotacaoCompra'] / $cotacao_jpy['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->SEK =  ($aux * $cotacao_jpy['value'][4]['cotacaoCompra'] / $cotacao_sek['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->USD > 0) {
                $cotacao_jpy = $this->exibirCotacao(7); 
                $cotacao_usd = $this->exibirCotacao(10);
                $aux = ($conta->USD * $cotacao_usd['value'][4]['cotacaoCompra'] / $cotacao_jpy['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->USD =  ($aux * $cotacao_jpy['value'][4]['cotacaoCompra'] / $cotacao_usd['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            } elseif ($conta->BRL > 0) {
                $cotacao_jpy = $this->exibirCotacao(7); 
                $aux = ($conta->BRL / $cotacao_jpy['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->BRL =  ($aux *  $cotacao_jpy['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }else {
                return FALSE;
            }
            
            
        }

    }

    public function saqueNOK(Operacao $operacao, Conta $conta)
    {
        if($operacao->valor <= $conta->NOK){
            $conta->NOK -= $operacao->valor;
            return TRUE;
        }else{

            if($conta->CAD > 0){
                $cotacao_nok = $this->exibirCotacao(8); 
                $cotacao_cad = $this->exibirCotacao(2);
                $aux = ($conta->CAD * $cotacao_cad['value'][4]['cotacaoCompra'] / $cotacao_nok['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->CAD =  ($aux * $cotacao_nok['value'][4]['cotacaoCompra'] / $cotacao_cad['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->CHF > 0) {
                $cotacao_nok = $this->exibirCotacao(8); 
                $cotacao_chf = $this->exibirCotacao(3);
                $aux = ($conta->CHF * $cotacao_chf['value'][4]['cotacaoCompra'] / $cotacao_nok['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->CHF =  ($aux * $cotacao_nok['value'][4]['cotacaoCompra'] / $cotacao_chf['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->DDK > 0) {
                $cotacao_nok = $this->exibirCotacao(8); 
                $cotacao_ddk = $this->exibirCotacao(4);
                $aux = ($conta->DDK * $cotacao_ddk['value'][4]['cotacaoCompra'] / $cotacao_nok['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->DDK =  ($aux * $cotacao_nok['value'][4]['cotacaoCompra'] / $cotacao_ddk['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            
            }elseif ($conta->EUR > 0) {
                $cotacao_nok = $this->exibirCotacao(8); 
                $cotacao_eur = $this->exibirCotacao(5);
                $aux = ($conta->EUR * $cotacao_eur['value'][4]['cotacaoCompra'] / $cotacao_nok['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->EUR =  ($aux * $cotacao_nok['value'][4]['cotacaoCompra'] / $cotacao_eur['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->GBP > 0) {
                $cotacao_nok = $this->exibirCotacao(8); 
                $cotacao_gbp = $this->exibirCotacao(6);
                $aux = ($conta->GBP * $cotacao_gbp['value'][4]['cotacaoCompra'] / $cotacao_nok['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->GBP =  ($aux * $cotacao_nok['value'][4]['cotacaoCompra'] / $cotacao_gbp['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->JPY > 0) {
                $cotacao_nok = $this->exibirCotacao(8); 
                $cotacao_jpy = $this->exibirCotacao(7);
                $aux = ($conta->JPY * $cotacao_jpy['value'][4]['cotacaoCompra'] / $cotacao_nok['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->JPY =  ($aux * $cotacao_nok['value'][4]['cotacaoCompra'] / $cotacao_jpy['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->AUD > 0) {
                $cotacao_aud = $this->exibirCotacao(1); 
                $cotacao_nok = $this->exibirCotacao(8);
                $aux = ($conta->AUD * $cotacao_aud['value'][4]['cotacaoCompra'] / $cotacao_nok['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->AUD =  ($aux * $cotacao_nok['value'][4]['cotacaoCompra'] / $cotacao_aud['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->SEK > 0) {
                $cotacao_nok = $this->exibirCotacao(8); 
                $cotacao_sek = $this->exibirCotacao(9);
                $aux = ($conta->SEK * $cotacao_sek['value'][4]['cotacaoCompra'] / $cotacao_nok['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->SEK =  ($aux * $cotacao_nok['value'][4]['cotacaoCompra'] / $cotacao_sek['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->USD > 0) {
                $cotacao_nok = $this->exibirCotacao(8); 
                $cotacao_usd = $this->exibirCotacao(10);
                $aux = ($conta->USD * $cotacao_usd['value'][4]['cotacaoCompra'] / $cotacao_nok['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->USD =  ($aux * $cotacao_aud['value'][4]['cotacaoCompra'] / $cotacao_usd['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            } elseif ($conta->BRL > 0) {
                $cotacao_nok = $this->exibirCotacao(8); 
                $aux = ($conta->BRL / $cotacao_nok['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->BRL =  ($aux *  $cotacao_nok['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }else {
                return FALSE;
            }
            
            
        }

    }

    public function saqueSEK(Operacao $operacao, Conta $conta)
    {
        if($operacao->valor <= $conta->SEK){
            $conta->SEK -= $operacao->valor;
            return TRUE;
        }else{

            if($conta->CAD > 0){
                $cotacao_sek = $this->exibirCotacao(9); 
                $cotacao_cad = $this->exibirCotacao(2);
                $aux = ($conta->CAD * $cotacao_cad['value'][4]['cotacaoCompra'] / $cotacao_sek['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->CAD =  ($aux * $cotacao_sek['value'][4]['cotacaoCompra'] / $cotacao_cad['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->CHF > 0) {
                $cotacao_sek = $this->exibirCotacao(9); 
                $cotacao_chf = $this->exibirCotacao(3);
                $aux = ($conta->CHF * $cotacao_chf['value'][4]['cotacaoCompra'] / $cotacao_sek['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->CHF =  ($aux * $cotacao_sek['value'][4]['cotacaoCompra'] / $cotacao_chf['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->DDK > 0) {
                $cotacao_sek = $this->exibirCotacao(9); 
                $cotacao_ddk = $this->exibirCotacao(4);
                $aux = ($conta->DDK * $cotacao_ddk['value'][4]['cotacaoCompra'] / $cotacao_sek['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->DDK =  ($aux * $cotacao_sek['value'][4]['cotacaoCompra'] / $cotacao_ddk['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            
            }elseif ($conta->EUR > 0) {
                $cotacao_sek = $this->exibirCotacao(9); 
                $cotacao_eur = $this->exibirCotacao(5);
                $aux = ($conta->EUR * $cotacao_eur['value'][4]['cotacaoCompra'] / $cotacao_sek['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->EUR =  ($aux * $cotacao_sek['value'][4]['cotacaoCompra'] / $cotacao_eur['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->GBP > 0) {
                $cotacao_sek = $this->exibirCotacao(9); 
                $cotacao_gbp = $this->exibirCotacao(6);
                $aux = ($conta->GBP * $cotacao_gbp['value'][4]['cotacaoCompra'] / $cotacao_sek['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->GBP =  ($aux * $cotacao_sek['value'][4]['cotacaoCompra'] / $cotacao_gbp['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->JPY > 0) {
                $cotacao_sek = $this->exibirCotacao(9); 
                $cotacao_jpy = $this->exibirCotacao(7);
                $aux = ($conta->JPY * $cotacao_jpy['value'][4]['cotacaoCompra'] / $cotacao_sek['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->JPY =  ($aux * $cotacao_sek['value'][4]['cotacaoCompra'] / $cotacao_jpy['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->NOK > 0) {
                $cotacao_sek = $this->exibirCotacao(9); 
                $cotacao_nok = $this->exibirCotacao(8);
                $aux = ($conta->NOK * $cotacao_nok['value'][4]['cotacaoCompra'] / $cotacao_sek['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->NOK =  ($aux * $cotacao_sek['value'][4]['cotacaoCompra'] / $cotacao_nok['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->AUD > 0) {
                $cotacao_aud = $this->exibirCotacao(1); 
                $cotacao_sek = $this->exibirCotacao(9);
                $aux = ($conta->AUD * $cotacao_aud['value'][4]['cotacaoCompra'] / $cotacao_sek['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->AUD =  ($aux * $cotacao_sek['value'][4]['cotacaoCompra'] / $cotacao_aud['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->USD > 0) {
                $cotacao_sek = $this->exibirCotacao(9); 
                $cotacao_usd = $this->exibirCotacao(10);
                $aux = ($conta->USD * $cotacao_usd['value'][4]['cotacaoCompra'] / $cotacao_sek['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->USD =  ($aux * $cotacao_sek['value'][4]['cotacaoCompra'] / $cotacao_usd['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            } elseif ($conta->BRL > 0) {
                $cotacao_sek = $this->exibirCotacao(9); 
                $aux = ($conta->BRL / $cotacao_sek['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->BRL =  ($aux *  $cotacao_sek['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }else {
                return FALSE;
            }
            
            
        }

    }

    public function saqueUSD(Operacao $operacao, Conta $conta)
    {
        if($operacao->valor <= $conta->USD){
            $conta->USD -= $operacao->valor;
            return TRUE;
        }else{

            if($conta->CAD > 0){
                $cotacao_usd = $this->exibirCotacao(10); 
                $cotacao_cad = $this->exibirCotacao(2);
                $aux = ($conta->CAD * $cotacao_cad['value'][4]['cotacaoCompra'] / $cotacao_usd['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->CAD =  ($aux * $cotacao_usd['value'][4]['cotacaoCompra'] / $cotacao_cad['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->CHF > 0) {
                $cotacao_usd = $this->exibirCotacao(10); 
                $cotacao_chf = $this->exibirCotacao(3);
                $aux = ($conta->CHF * $cotacao_chf['value'][4]['cotacaoCompra'] / $cotacao_usd['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->CHF =  ($aux * $cotacao_usd['value'][4]['cotacaoCompra'] / $cotacao_chf['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->DDK > 0) {
                $cotacao_usd = $this->exibirCotacao(10); 
                $cotacao_ddk = $this->exibirCotacao(4);
                $aux = ($conta->DDK * $cotacao_ddk['value'][4]['cotacaoCompra'] / $cotacao_usd['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->DDK =  ($aux * $cotacao_usd['value'][4]['cotacaoCompra'] / $cotacao_ddk['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            
            }elseif ($conta->EUR > 0) {
                $cotacao_usd = $this->exibirCotacao(10); 
                $cotacao_eur = $this->exibirCotacao(5);
                $aux = ($conta->EUR * $cotacao_eur['value'][4]['cotacaoCompra'] / $cotacao_usd['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->EUR =  ($aux * $cotacao_usd['value'][4]['cotacaoCompra'] / $cotacao_eur['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->GBP > 0) {
                $cotacao_usd = $this->exibirCotacao(10); 
                $cotacao_gbp = $this->exibirCotacao(6);
                $aux = ($conta->GBP * $cotacao_gbp['value'][4]['cotacaoCompra'] / $cotacao_usd['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->GBP =  ($aux * $cotacao_usd['value'][4]['cotacaoCompra'] / $cotacao_gbp['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->JPY > 0) {
                $cotacao_usd = $this->exibirCotacao(10); 
                $cotacao_jpy = $this->exibirCotacao(7);
                $aux = ($conta->JPY * $cotacao_jpy['value'][4]['cotacaoCompra'] / $cotacao_usd['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->JPY =  ($aux * $cotacao_usd['value'][4]['cotacaoCompra'] / $cotacao_jpy['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->NOK > 0) {
                $cotacao_usd = $this->exibirCotacao(10); 
                $cotacao_nok = $this->exibirCotacao(8);
                $aux = ($conta->NOK * $cotacao_nok['value'][4]['cotacaoCompra'] / $cotacao_usd['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->NOK =  ($aux * $cotacao_usd['value'][4]['cotacaoCompra'] / $cotacao_nok['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->SEK > 0) {
                $cotacao_usd = $this->exibirCotacao(10); 
                $cotacao_sek = $this->exibirCotacao(9);
                $aux = ($conta->SEK * $cotacao_sek['value'][4]['cotacaoCompra'] / $cotacao_usd['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->SEK =  ($aux * $cotacao_usd['value'][4]['cotacaoCompra'] / $cotacao_sek['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->AUD > 0) {
                $cotacao_aud = $this->exibirCotacao(1); 
                $cotacao_usd = $this->exibirCotacao(10);
                $aux = ($conta->AUD * $cotacao_aud['value'][4]['cotacaoCompra'] / $cotacao_usd['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->AUD =  ($aux * $cotacao_usd['value'][4]['cotacaoCompra'] / $cotacao_aud['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            } elseif ($conta->BRL > 0) {
                $cotacao_usd = $this->exibirCotacao(10); 
                $aux = ($conta->BRL / $cotacao_usd['value'][4]['cotacaoVenda']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->BRL =  ($aux *  $cotacao_usd['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }else {
                return FALSE;
            }
            
            
        }

    }

    public function saqueBRL(Operacao $operacao, Conta $conta)
    {
        if($operacao->valor <= $conta->BRL){
            $conta->BRL -= $operacao->valor;
            return TRUE;
        }else{

            if($conta->CAD > 0){
                $cotacao_cad = $this->exibirCotacao(2);
                $aux = ($conta->CAD * $cotacao_cad['value'][4]['cotacaoCompra']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->CAD =  ($aux / $cotacao_cad['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->CHF > 0) {
                $cotacao_chf = $this->exibirCotacao(3);
                $aux = ($conta->CHF * $cotacao_chf['value'][4]['cotacaoCompra']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->CHF =  ($aux / $cotacao_chf['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->AUD > 0) {
                $cotacao_aud = $this->exibirCotacao(1);
                $aux = ($conta->AUD * $cotacao_aud['value'][4]['cotacaoCompra']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->AUD =  ($aux / $cotacao_aud['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->DDK > 0) {
                $cotacao_ddk = $this->exibirCotacao(4);
                $aux = ($conta->DDK * $cotacao_ddk['value'][4]['cotacaoCompra']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->DDK =  ($aux / $cotacao_ddk['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            
            }elseif ($conta->EUR > 0) {
                $cotacao_eur = $this->exibirCotacao(5);
                $aux = ($conta->EUR * $cotacao_eur['value'][4]['cotacaoCompra']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->EUR =  ($aux / $cotacao_eur['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->GBP > 0) {
                $cotacao_gbp = $this->exibirCotacao(6);
                $aux = ($conta->GBP * $cotacao_gbp['value'][4]['cotacaoCompra']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->GBP =  ($aux / $cotacao_gbp['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->JPY > 0) {
                $cotacao_jpy = $this->exibirCotacao(7);
                $aux = ($conta->JPY * $cotacao_jpy['value'][4]['cotacaoCompra']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->JPY =  ($aux / $cotacao_jpy['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->NOK > 0) {
                $cotacao_nok = $this->exibirCotacao(8);
                $aux = ($conta->NOK * $cotacao_nok['value'][4]['cotacaoCompra']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->NOK =  ($aux / $cotacao_nok['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->SEK > 0) {
                $cotacao_sek = $this->exibirCotacao(9);
                $aux = ($conta->SEK * $cotacao_sek['value'][4]['cotacaoCompra']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->SEK =  ($aux / $cotacao_sek['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }elseif ($conta->USD > 0) {
                $cotacao_usd = $this->exibirCotacao(10);
                $aux = ($conta->USD * $cotacao_usd['value'][4]['cotacaoCompra']);
                if($operacao->valor <= $aux){
                    $aux -= $operacao->valor;
                    $conta->USD =  ($aux / $cotacao_usd['value'][4]['cotacaoVenda']);
                    return TRUE;
                }
            }else {
                return FALSE;
            }
            
            
        }

    }
}

