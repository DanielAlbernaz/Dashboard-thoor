<?php

namespace App\Http\Controllers;

use App\Conhecimentos;
use App\Filtro;
use App\Grafico;
use App\Uteis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

use function App\print_rpre;

class ControllerFiltro extends Controller
{

    function filtrar(Request $request){
        $uteis = new Uteis();

        $conhecimento = new Conhecimentos();

        $motoristas = $request->motorista;
        $placas = $request->placa;
        $dataInicial = $request->dataInicial;
        $dataFinal = $request->dataFinal;
        $salvarFiltro = $request->salvar_filtro;

        if($motoristas && $placas){
            if(empty($arrayPlacasResult)){
                $resposta = [
                    'situacao' => 'erro',
                ];
                return $resposta;
                exit;
            }
        }

        if($placas){

            // Enable query log
            /**
             * Monta array com os valores dos carregamentos
             */
            $cont = 0;
            $contador = 0;
            $arrayPlacasResult = array();
            $filtroPlaca = 1;
            for($p = 0; $p <= count($placas) - 1; $p++){
                $total = Conhecimentos::select(DB::raw("SUM(valor_frete) as total"))->where('placa', '=', $placas[$p])->whereDate('data_emissao', '>=' ,$dataInicial )->whereDate('data_emissao', '<=' ,$dataFinal)->get();

                if($total[0]['total'] <> NULL){
                    $arrayMotora[$cont] = $total[0]['total'];
                    $cont++;

                    $arrayPlacasResult[$contador] = $placas[$p];
                    $contador++;
                }

            }

            if(empty($arrayPlacasResult)){
                $resposta = [
                    'situacao' => 'warning',
                ];
                return $resposta;
                exit;
            }

            /**
             * Monta array com os momes motoristas
             */
            $arrayMotoristas = array();
            $arrayFrota = array();
            $cont = 0;
            $totalReceitaFiltro = 0;
            $contFrota = 0;
            $j = 0;
            $arrayTeste = array();
            for($i = 0; $i <= count($arrayPlacasResult) -1; $i++){
                $motoristas = Conhecimentos::select('motorista', 'placa')->distinct()->where('placa', '=', $arrayPlacasResult[$i])->whereDate('data_emissao', '>=' ,$dataInicial)->whereDate('data_emissao', '<=' ,$dataFinal)->get('motorista', 'placa');

                $tabelaFrota = Conhecimentos::select('motorista', 'placa')->distinct()->where('placa', '=', $arrayPlacasResult[$i])->whereDate('data_emissao', '>=' ,$dataInicial)->whereDate('data_emissao', '<=' ,$dataFinal)->get('motorista', 'placa');



                //nome
                //placa
                for($f = 0; $f < count($tabelaFrota); $f++){
                    $total = Conhecimentos::select(DB::raw("SUM(valor_frete) as total"))->where('placa', '=', $arrayPlacasResult[$i])->where('motorista', '=', $tabelaFrota[$f]['motorista'])->whereDate('data_emissao', '>=' ,$dataInicial )->whereDate('data_emissao', '<=' ,$dataFinal)->get();


                    $arrayFrota[$j]['motorista'][0] =  $tabelaFrota[$f]['motorista'];
                    $arrayFrota[$j]['placa'][0] = $arrayPlacasResult[$i];
                    $arrayFrota[$j]['valor_placas'][0] = number_format($total[0]['total'], 2, ',', '.');
                    $arrayFrota[$j]['valor_total'][0] = 'R$ ' . number_format($total[0]['total'], 2, ',', '.');
                    $j++;
                }


                // $totalFaturamentoPlaca = Conhecimentos::select(DB::raw("SUM(valor_frete) as total"))
                //                                 ->where('placa', '=', $placasDosMotoristas[0]->placa)
                //                                 ->where('motorista', '=', $motoristas[$p])
                //                                 ->whereDate('data_emissao', '>=' ,$dataInicial)
                //                                 ->whereDate('data_emissao', '<=' ,$dataFinal)
                //                                 ->get();


                // for($f = 0; $f < count($tabelaFrota); $f++){
                //     $total = Conhecimentos::select(DB::raw("SUM(valor_frete) as total"))->where('placa', '=', $arrayPlacasResult[$i])->where('motorista', '=', $tabelaFrota[$f]['motorista'])->whereDate('data_emissao', '>=' ,$dataInicial )->whereDate('data_emissao', '<=' ,$dataFinal)->get();
                //     $arrayFrota[$i]['motorista'][$f] = $tabelaFrota[$f]['motorista'];
                //     $arrayFrota[$i]['faturamento'][$f] = number_format($total[0]['total'], 2, ',', '.');
                //     $arrayFrota[$i]['placa'] = $arrayPlacasResult[$i];
                //     $contFrota++;
                //     $j++;
                // }

                //  /** arrray que monta pra tabela do grafico (qunado ouver mais de uma placa pro mesmo motorista)*/
                //         $arrayMotoristaFrota[$p]['motorista'][0] = $motoristas[$p] ;
                //         $arrayMotoristaFrota[$p]['placa'][$i] = $placasDosMotoristas[$i]->placa;
                //         $arrayMotoristaFrota[$p]['valor_placas'][$i] = 'R$ ' . number_format($totalFaturamentoPlaca[0]->total, 2, ',', '.');
                //         $somatorio = $somatorio + $totalFaturamentoPlaca[0]->total;
                //         $totalReceitaFiltro = $totalReceitaFiltro + $totalFaturamentoPlaca[0]->total;
                //         $arrayMotoristaFrota[$p]['valor_total'][0] = 'R$ ' . number_format($somatorio, 2, ',', '.') ;

                if(count($motoristas) > 1){
                    for($a = 0; $a <= count($motoristas)-1; $a++){
                        $arrayMotoristas[$cont][0] = $arrayPlacasResult[$i];
                        $nome = explode(' ', $motoristas[$a]['motorista']);
                        $arrayMotoristas[$cont][$a +1] = $nome[0] . ' ' . $nome[1];
                        $arrayMotoristas[$cont][$a +2] = 'R$ ' .  number_format($arrayMotora[$i], 2, ',', '.');
                    }
                    $cont++;
                }else{
                    $l = 0;
                    $arrayMotoristas[$cont][0] = $arrayPlacasResult[$i];
                    $nome = explode(' ', $motoristas[0]['motorista']);
                    $arrayMotoristas[$cont][$l+1] = $nome[0] . ' ' . $nome[1];
                    $arrayMotoristas[$cont][$l+2] = 'R$ ' . number_format($arrayMotora[$i], 2, ',', '.');
                    $cont++;
                }
                $totalReceitaFiltro = $totalReceitaFiltro + $arrayMotora[$i];
            }
            /***
             * Seta na sessão os valores do filtro para o ajax do grafico acessar pela function teste ps mudar noma da function
             */

            Session::forget('motoristas');
            Session::forget('total');
            Session::forget('faturamento_frota_motorista');
            Session::forget('faturamento_frota');
            Session::forget('total_receita_faturamento');
            Session::forget('tipo_filtro');

            // Session::put('motoristas', $arrayMotoristas);
            // Session::put('total', $arrayMotora);

            //Inicio Deleta tabela com array do grafico em seguida salva no banco o array para poder ordenar e ai sim cria a session com o svalores ajustados
            DB::table('grafico')->truncate();
            //$grafico = Grafico::find(1);
            for($i = 0; $i < count($arrayMotoristas); $i++){
                $grafico = new Grafico();
                $grafico->array_dados = serialize($arrayMotoristas[$i]);
                $grafico->total = $arrayMotora[$i];
                $grafico->save();
            }
            
            //$grafico = new Grafico();
            $grafico = Grafico::select()->orderBy('total', 'desc')->get();

            $arrayMotoristas = array();
            $arrayMotora = array();
            for($i = 0; $i < count($grafico); $i++){
                $arrayMotoristas[$i] = unserialize($grafico[$i]['array_dados']);
                $arrayMotora[$i] = $grafico[$i]['total'];
            }
            Session::put('motoristas', $arrayMotoristas);
            Session::put('total', $arrayMotora);
            //Fim Deleta tabela com array do grafico em seguida salva no banco o array para poder ordenar e ai sim cria a session com o svalores ajustados

            
            Session::put('motoristasComparar', $arrayMotoristas);            
            Session::put('total_receita_faturamento', $totalReceitaFiltro);
            Session::put('faturamento_frota_motorista', $arrayFrota);
            Session::put('tipo_filtro', 'placa');
            
            if($salvarFiltro){
                Session::put('placas_filtro', $request->placa);
                $filtros = new Filtro();
                $filtros = Filtro::find(1);
                $filtros->tipo_filtro = 1;
                $filtros->motorista = serialize(Session::get('motoristas'));
                $filtros->total = serialize(Session::get('total'));
                $filtros->faturamento_frota = serialize(Session::get('total_receita_faturamento'));
                $filtros->total_receita = serialize(Session::get('faturamento_frota'));
                $filtros->save();
            }



            $resposta = [
                'situacao' => 'success',
                'motorista' => $arrayMotoristas,
            ];
            return $resposta;
            exit;
        }
         if($motoristas){

            $placasMotorista = array();
            $totalParaGrafico = array();
            $arrayMotoristaFrota = array();
            $contadorFrota = 0;
            $somatorio = 0;
            $totalReceitaFiltro = 0;
            $cont = 0;
            for($p = 0; $p <= count($motoristas) - 1; $p++){
                $placasDosMotoristas = Conhecimentos::select('placa')
                                            ->distinct()
                                            ->where('motorista', '=', $motoristas[$p])
                                            ->whereDate('data_emissao', '>=' ,$dataInicial)
                                            ->whereDate('data_emissao', '<=' ,$dataFinal)
                                            ->get('placa');


                if(count($placasDosMotoristas) == 0){
                    $resposta = [
                        'situacao' => 'warning',
                    ];
                    return $resposta;
                    exit;
                }

                if(count($placasDosMotoristas) > 1){
                    $somatorio = 0;
                    for($i = 0; $i < count($placasDosMotoristas); $i++){

                        $totalFaturamentoPlaca = Conhecimentos::select(DB::raw("SUM(valor_frete) as total"))
                                                            ->where('placa', '=', $placasDosMotoristas[$i]->placa)
                                                            ->where('motorista', '=', $motoristas[$p])
                                                            ->whereDate('data_emissao', '>=' ,$dataInicial )
                                                            ->whereDate('data_emissao', '<=' ,$dataFinal)
                                                            ->get();

                        /** array onde monta para o grafico (qunado ouver mais de uma placa pro mesmo motorista) */
                        $placasMotorista[$cont][0] = $placasDosMotoristas[$i]->placa;
                        $placasMotorista[$cont][1] = $motoristas[$p];
                        $placasMotorista[$cont][2] = 'R$ ' . number_format($totalFaturamentoPlaca[0]->total, 2, ',', '.');
                        $totalParaGrafico[$cont] = $totalFaturamentoPlaca[0]->total;
                        $cont++;


                        /** arrray que monta pra tabela do grafico (qunado ouver mais de uma placa pro mesmo motorista)*/
                        $arrayMotoristaFrota[$p]['motorista'][0] = $motoristas[$p] ;
                        $arrayMotoristaFrota[$p]['placa'][$i] = $placasDosMotoristas[$i]->placa;
                        $arrayMotoristaFrota[$p]['valor_placas'][$i] = 'R$ ' . number_format($totalFaturamentoPlaca[0]->total, 2, ',', '.');
                        $somatorio = $somatorio + $totalFaturamentoPlaca[0]->total;
                        $totalReceitaFiltro = $totalReceitaFiltro + $totalFaturamentoPlaca[0]->total;
                        $arrayMotoristaFrota[$p]['valor_total'][0] = 'R$ ' . number_format($somatorio, 2, ',', '.') ;


                    }


                }else{
                    $somatorio = 0;
                    $totalFaturamentoPlaca = Conhecimentos::select(DB::raw("SUM(valor_frete) as total"))
                                                ->where('placa', '=', $placasDosMotoristas[0]->placa)
                                                ->where('motorista', '=', $motoristas[$p])
                                                ->whereDate('data_emissao', '>=' ,$dataInicial)
                                                ->whereDate('data_emissao', '<=' ,$dataFinal)
                                                ->get();


                    /** array onde monta para o grafico (quando ouver somente 1 placa) */
                    $placasMotorista[$cont][0] = $placasDosMotoristas[0]->placa;
                    $placasMotorista[$cont][1] = $motoristas[$p];
                    $placasMotorista[$cont][2] = 'R$ ' . number_format($totalFaturamentoPlaca[0]->total, 2, ',', '.');
                    $totalParaGrafico[$cont] = $totalFaturamentoPlaca[0]->total;
                    $cont++;


                    /** arrray que monta pra tabela do grafico (quando ouver somente 1 placa) */
                    $arrayMotoristaFrota[$p]['motorista'][0] = $motoristas[$p] ;
                    $arrayMotoristaFrota[$p]['placa'][0] = $placasDosMotoristas[0]->placa;
                    $arrayMotoristaFrota[$p]['valor_placas'][0] = 'R$ ' . number_format($totalFaturamentoPlaca[0]->total, 2, ',', '.');
                    $somatorio = $somatorio + $totalFaturamentoPlaca[0]->total;
                    $totalReceitaFiltro = $totalReceitaFiltro + $totalFaturamentoPlaca[0]->total;
                    $arrayMotoristaFrota[$p]['valor_total'][0] = 'R$ ' . number_format($somatorio, 2, ',', '.') ;
                }



            }


            Session::forget('motoristas');
            Session::forget('motoristasComparar');
            Session::forget('total');
            Session::forget('total_receita_faturamento');
            Session::forget('faturamento_frota');
            Session::forget('tipo_filtro');


            DB::table('grafico')->truncate();
            //$grafico = Grafico::find(1);
            for($i = 0; $i < count($placasMotorista); $i++){
                $grafico = new Grafico();
                $grafico->array_dados = serialize($placasMotorista[$i]);
                $grafico->total = $totalParaGrafico[$i];
                $grafico->save();
            }
            
            //$grafico = new Grafico();
            $grafico = Grafico::select()->orderBy('total', 'desc')->get();

            $placasMotorista = array();
            $totalParaGrafico = array();
            for($i = 0; $i < count($grafico); $i++){
                $placasMotorista[$i] = unserialize($grafico[$i]['array_dados']);
                $totalParaGrafico[$i] = $grafico[$i]['total'];
            }
            Session::put('motoristas', $placasMotorista);
            Session::put('total', $totalParaGrafico);






            
            Session::put('faturamento_frota_motorista', $arrayMotoristaFrota);            
            Session::put('total_receita_faturamento', $totalReceitaFiltro);

            Session::put('tipo_filtro', 'motorista');
            if($salvarFiltro){
                Session::put('placas_filtro', $request->placa);
                $filtros = new Filtro();
                $filtros = Filtro::find(1);
                $filtros->tipo_filtro = 2;
                $filtros->motorista = serialize(Session::get('motoristas'));
                $filtros->total = serialize(Session::get('total'));
                $filtros->faturamento_frota = serialize(Session::get('faturamento_frota_motorista'));
                $filtros->total_receita = serialize(Session::get('total_receita_faturamento'));
                $filtros->save();
            }

            $resposta = [
                'situacao' => 'success',
                'motorista' => $placasMotorista,
            ];
            return $resposta;
            exit;
         }
    }

    function teste()
    {
        $teste = Session::get('motoristas');
        $valores = Session::get('total');


        $retorno = [
                'teste' => $teste,
                'valores' =>$valores,
            ];
        return $retorno;
    }
}
