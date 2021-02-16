<?php

namespace App\Http\Controllers;

use App\Conhecimentos;
use App\Filtro;
use App\Uteis;
use App\Distribuidora;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use function App\print_rpre;
use function App\comecoMesAtual;
use function App\finalMesAtual;
use Illuminate\Support\Facades\Session;
use App\Usuarios;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    function showLogar(Request $request)
    {
        return view('login');
    }

    function showHome(Request $request)
    {
        $uteis = new Uteis();
        $conhecimento = new Conhecimentos();


        $data_incioa = mktime(0, 0, 0, date('m') , 1 , date('Y'));
        $data_fimb = mktime(23, 59, 59, date('m'), date("t"), date('Y'));
        $DataInicial = date('Y-m-d',$data_incioa);
        $DataFinal = date('Y-m-d',$data_fimb);

        $total = Conhecimentos::select(DB::raw("SUM(valor_frete) as faturamento"))
                                ->whereDate('data_emissao', '>=' ,$DataInicial )
                                ->whereDate('data_emissao', '<=' ,$DataFinal)
                                ->get();

        $arrayFaturamentoMensal = 'R$ ' . number_format($total[0]->faturamento, 2, ',', '.');
        Session::put('faturamento_mensal', $arrayFaturamentoMensal);



        $ano = date('Y-m-d');
        $ano = explode('-', $ano);
        $comecoAno = $ano[0] . '-01' . '-01';
        $dataAtual = date('Y-m-d');



        $totalFaturamentoAnual = Conhecimentos::select(DB::raw("SUM(valor_frete) as faturamento_anual"))
                                            ->whereDate('data_emissao', '>=' ,$comecoAno )
                                            ->whereDate('data_emissao', '<=' ,$dataAtual)
                                            ->get();

        $arrayFaturamentoAnual = 'R$ ' . number_format($totalFaturamentoAnual[0]->faturamento_anual, 2, ',', '.');
        Session::put('faturamento_anual', $arrayFaturamentoAnual);




        $data_incio = mktime(0, 0, 0, date('m') , 1 , date('Y'));
        $data_fim = mktime(23, 59, 59, date('m'), date("t"), date('Y'));
        $DataInicial = date('Y-m-d',$data_incio);
        $DataFinal = date('Y-m-d',$data_fim);

        $totalCarregamentomensal = Conhecimentos::select(DB::raw("SUM(nota_volume) as carregamento_mensal"))
                                    ->whereDate('data_emissao', '>=' ,$DataInicial )
                                    ->whereDate('data_emissao', '<=' ,$DataFinal)
                                    ->get();

        $arrayCarregamentoMensal = 'R$ ' . number_format($totalCarregamentomensal[0]->carregamento_mensal, 2, ',', '.');
        Session::put('carregamento_mensal', $arrayCarregamentoMensal);




        $motoristas = $conhecimento->buscaMotoristas();
        $arrayMotoristas = $conhecimento->montaArrayMotoristas($motoristas);

        $placas = $conhecimento->buscaPlacas();
        $arrayPlacas = $conhecimento->montaArrayPlacas($placas);


        // /** Quando abrir busca filtro salvo no banco e mostra */
        $filtros = new Filtro();
        $filtros = Filtro::find(1);

        if(Session::get('motoristas') == '' && Session::get('total') == ''){
            if($filtros['tipo_filtro'] == 2){
                Session::put('motoristas', unserialize($filtros['motorista']));
                Session::put('total', unserialize($filtros['total']));
                Session::put('faturamento_frota_motorista', unserialize($filtros['faturamento_frota']));
                Session::put('total_receita_faturamento', unserialize($filtros['total_receita']));
                Session::put('tipo_filtro', 'motorista');
            }
            if($filtros['tipo_filtro'] == 1){
                Session::put('motoristas', unserialize($filtros['motorista']));
                Session::put('total', unserialize($filtros['total']));
                Session::put('total_receita_faturamento', unserialize($filtros['faturamento_frota']));
                Session::put('faturamento_frota', unserialize($filtros['total_receita']));
                Session::put('tipo_filtro', 'placa');
            }
        }

        Session::put('declinio', $this->desempenhoAnoAnteriorAtual());

        //print_rpre($this->desempenhoAnoAnteriorAtual());exit;

        $this->resultadoDistribuidoras();

        if(Auth::check() === true){
            return view('principal', compact('arrayMotoristas', 'arrayPlacas'));
        }else{
            return view('login');
        }

    }


    public function desempenhoAnoAnteriorAtual(){

        $anoAtual = date('Y');
        $anoPassado = $anoAtual - 1;

        $inicialDia = '01';
        $finalDia = '02';

        $mes = '01';
        $mes += 1;

        for($i = 1; $i <= 12; $i++){

            $datas = $this->diasMesAnoAtual($i);
            $datasAnteriores = $this->diasMesAnoAnterior($i);

            $totalCarregamentoMesAnoAtual = Conhecimentos::select(DB::raw("SUM(valor_frete) as carregamento_mensal"))
                                                            ->whereDate('data_emissao', '>=' ,$datas['inicio'])
                                                            ->whereDate('data_emissao', '<=' ,$datas['fim'])
                                                            ->get();

            $totalCarregamentoMesAnoPassado = Conhecimentos::select(DB::raw("SUM(valor_frete) as carregamento_mensal"))
                                                            ->whereDate('data_emissao', '>=' ,$datasAnteriores['inicio'])
                                                            ->whereDate('data_emissao', '<=' ,$datasAnteriores['fim'])
                                                            ->get();


            $desempenho[$i]['mes_anterior'] = $this->retornoMesAno($i);
            $desempenho[$i]['valor_mes_anterior'] = $totalCarregamentoMesAnoPassado[0]->carregamento_mensal;


            $desempenho[$i]['mes_atual'] = $this->retornoMesAno($i);
            $desempenho[$i]['valor_mes_atual'] = $totalCarregamentoMesAnoAtual[0]->carregamento_mensal;

            if($totalCarregamentoMesAnoPassado[0]->carregamento_mensal < $totalCarregamentoMesAnoAtual[0]->carregamento_mensal){
                $soma = $totalCarregamentoMesAnoPassado[0]->carregamento_mensal - $totalCarregamentoMesAnoAtual[0]->carregamento_mensal;
                $desempenho[$i]['total_meses'] = abs($soma);
            }else{
                $desempenho[$i]['total_meses'] = $totalCarregamentoMesAnoPassado[0]->carregamento_mensal - $totalCarregamentoMesAnoAtual[0]->carregamento_mensal;
            }



            if($totalCarregamentoMesAnoPassado[0]->carregamento_mensal > 0){
                if($totalCarregamentoMesAnoPassado[0]->carregamento_mensal < $totalCarregamentoMesAnoAtual[0]->carregamento_mensal){
                    $soma = $totalCarregamentoMesAnoPassado[0]->carregamento_mensal - $totalCarregamentoMesAnoAtual[0]->carregamento_mensal;
                    $desempenho[$i]['percentual'] =  abs($soma) / $totalCarregamentoMesAnoPassado[0]->carregamento_mensal * 100;
                }else{
                    $desempenho[$i]['percentual'] =  ($totalCarregamentoMesAnoPassado[0]->carregamento_mensal - $totalCarregamentoMesAnoAtual[0]->carregamento_mensal) / $totalCarregamentoMesAnoPassado[0]->carregamento_mensal * 100;
                }
            }


        }

        $totalMesAnoPassado = 0;
        for($i = 1; $i < count($desempenho); $i++){
            $totalMesAnoPassado += $desempenho[$i]['valor_mes_anterior'];
        }
        Session::put('totalMesAnoPassado', $totalMesAnoPassado);

        $totalMesAnoAtual = 0;
        for($i = 1; $i < count($desempenho); $i++){
            $totalMesAnoAtual += $desempenho[$i]['valor_mes_atual'];
        }
        Session::put('totalMesAnoAtual', $totalMesAnoAtual);

        $totalGeralCrescimentoDeclinio = 0;
        for($i = 1; $i < count($desempenho); $i++){
            $totalGeralCrescimentoDeclinio += $desempenho[$i]['total_meses'];
        }
        Session::put('totalGeralCrescimentoDeclinio', $totalGeralCrescimentoDeclinio);


        $totalMediaPercentual = 0;
        for($i = 1; $i < count($desempenho); $i++){
            $totalMediaPercentual += isset($desempenho[$i]['percentual']);
        }
        $totalMediaPercentual = $totalMediaPercentual / 12;

        if($totalMesAnoPassado > $totalGeralCrescimentoDeclinio){
            $soma = $totalMesAnoPassado - $totalMesAnoAtual;
            $totalMediaPercentual = abs($soma) / $totalMesAnoPassado * 100;
        }else{
            $totalMediaPercentual = ($totalMesAnoPassado - $totalMesAnoAtual) / $totalMesAnoPassado * 100;
        }

        Session::put('totalMediaPercentual', $totalMediaPercentual);
        //print_rpre($totalMediaPercentual);exit;

        return $desempenho;
    }

    public function resultadoDistribuidoras(){

        $tomadores = $this->tomadores();
        //print_rpre( substr($todosTomadores[0]['tomador'], 0, 5));exit;

        $totalFaturamentoMes = $this->totalMesVenda();

        $informacoesTabela = array();

        for($i = 0; $i < count($tomadores); $i++){
            $total = Conhecimentos::select(DB::raw("SUM(valor_frete) as total"))
                                    ->where('tomador', 'like', '%'. substr($tomadores[$i]['tomador'], 0, 5) . '%')
                                    ->whereDate('data_emissao', '>=' , comecoMesAtual())
                                    ->whereDate('data_emissao', '<=' , finalMesAtual())
                                    ->get();

            $informacoesTabela[$i]['tomador'] =  $tomadores[$i]['tomador'];
            $informacoesTabela[$i]['total_faturado_mes'] =  number_format($total[0]['total'], 2, ',', '.');
            $percentual = $total[0]['total'] / $totalFaturamentoMes * 100;
            $informacoesTabela[$i]['percentual'] =  number_format($percentual, 2, ',', '.');


        }
        //print_rpre($informacoesTabela);exit;

        // DB::table('distribuidora')->truncate();
         $distribuidoras = new Distribuidora();

        for($i = 1; $i < count($informacoesTabela); $i++){
            $distribuidoras->tomador = $informacoesTabela[$i]['tomador'];
            $valor = str_replace('.', '', $informacoesTabela[$i]['total_faturado_mes']);
            $valor = str_replace(',', '.', $valor);
            $distribuidoras->total_faturado_mes = $valor;
            $distribuidoras->percentual = $informacoesTabela[$i]['percentual'];
            $distribuidoras->save();
        }

        $grafico = Distribuidora::select()->orderBy('total_faturado_mes', 'desc')->get();

        $informacoesTabela = array();
        for($i = 0; $i < count($grafico); $i++){
            $informacoesTabela[$i]['tomador'] = $grafico[$i]['tomador'];
            $informacoesTabela[$i]['total_faturado_mes'] = number_format($grafico[$i]['total_faturado_mes'], 2, ',', '.');
            $informacoesTabela[$i]['percentual'] = $grafico[$i]['percentual'];
        }


        Session::put('informacoesTabela', $informacoesTabela);
        Session::put('totalFaturamentoMes', number_format($totalFaturamentoMes, 2, ',', '.'));
    }


    public function totalMesVenda(){



        $faturamentoTotal = Conhecimentos::select(DB::raw("SUM(valor_frete) as total"))
                                                ->whereDate('data_emissao', '>=' ,comecoMesAtual())
                                                ->whereDate('data_emissao', '<=' ,finalMesAtual())
                                                ->get();
        return $faturamentoTotal[0]['total'];
    }

    public function tomadores(){

        $tomadores = Conhecimentos::select('tomador')
                                        ->distinct()
                                        ->whereDate('data_emissao', '>=' ,comecoMesAtual())
                                        ->whereDate('data_emissao', '<=' ,finalMesAtual())
                                        ->get('tomador');
        $todosTomadores = array();
        $i = 0;
        foreach($tomadores as $value){
            $todosTomadores[$i]['tomador'] = $value['tomador'];
            $i++;
        }

        return $todosTomadores;
    }


    public function retornoMesAno($i){
        switch($i){
            case 1:
                return 'Janeiro';
            break;
            case 2:
                return 'Fevereiro';
            break;
            case 3:
                return 'Março';
            break;
            case 4:
                return 'Abril';
            break;
            case 5:
                return 'Maio';
            break;
            case 6:
                return 'Junho';
            break;
            case 7:
                return 'Julho';
            break;
            case 8:
                return 'Agosto';
            break;
            case 9:
                return 'Setembro';
            break;
            case 10:
                return 'Outubro';
            break;
            case 11:
                return 'Novembro';
            break;
            case 12:
                return 'Dezembro';
            break;

        }
    }


    public function validate_login(Request $request)
    {
        $uteis = new Uteis();

        $email = $request->email;
        $password = $request->password;


        $usuario = new User();
        $usuario = User::where('email', '=', $email)->get();

        if(isset($usuario[0]['email'])){

            if(Hash::check($password, $usuario[0]['password'])){
                Session::put('name_ususario_logado', $usuario[0]['name']);
                Session::put('email_ususario_logado', $usuario[0]['email']);
                Session::put('nivel_acesso_ususario_logado', $usuario[0]['nivel_acesso']);

                $credentials = [
                    'email' => $email,
                    'password' => $password
                ];

                Auth::attempt($credentials);
                $resposta = [
                    'situacao' => 'success',
                    'msg' => 'Logado com sucesso!'
                ];
                return $resposta;
            }else{
                $resposta = [
                    'situacao' => 'warning',
                    'msg' => 'Senha inválida!'
                ];
                return $resposta;
            }

        }else{
            $resposta = [
                'situacao' => 'warning',
                'msg' => 'Não a conta para esse email!'
            ];
            return $resposta;
        }

    }

    public function deslogar(Request $request)
    {
        Auth::logout();

        return view('/login');

    }


    public function diasMesAnoAtual($indice)
    {
        $anoAtual = date('Y');
        $datas = array();

        switch($indice){
            case $indice == 1:
                $datas['inicio'] = $anoAtual . '-01-' . '01' ;
                $datas['fim'] = $anoAtual . '-01-' . '31' ;
                return $datas;
                exit;
            break;

            case $indice == 2:
                $datas['inicio'] = $anoAtual . '-02-' . '01' ;
                $datas['fim'] = $anoAtual . '-02-' . '28' ;
                return $datas;
                exit;
            break;

            case $indice == 3:
                $datas['inicio'] = $anoAtual . '-03-' . '01' ;
                $datas['fim'] = $anoAtual . '-03-' . '31' ;
                return $datas;
                exit;
            break;

            case $indice == 4:
                $datas['inicio'] = $anoAtual . '-04-' . '01' ;
                $datas['fim'] = $anoAtual . '-04-' . '30' ;
                return $datas;
                exit;
            break;

            case $indice == 5:
                $datas['inicio'] = $anoAtual . '-05-' . '01' ;
                $datas['fim'] = $anoAtual . '-05-' . '31' ;
                return $datas;
                exit;
            break;

            case $indice == 6:
                $datas['inicio'] = $anoAtual . '-06-' . '01' ;
                $datas['fim'] = $anoAtual . '-06-' . '30' ;
                return $datas;
                exit;
            break;

            case $indice == 7:
                $datas['inicio'] = $anoAtual . '-07-' . '01' ;
                $datas['fim'] = $anoAtual . '-07-' . '31' ;
                return $datas;
                exit;
            break;

            case $indice == 8:
                $datas['inicio'] = $anoAtual . '-08-' . '01' ;
                $datas['fim'] = $anoAtual . '-08-' . '31' ;
                return $datas;
                exit;
            break;

            case $indice == 9:
                $datas['inicio'] = $anoAtual . '-09-' . '01' ;
                $datas['fim'] = $anoAtual . '-09-' . '30' ;
                return $datas;
                exit;
            break;

            case $indice == 10:
                $datas['inicio'] = $anoAtual . '-10-' . '01' ;
                $datas['fim'] = $anoAtual . '-10-' . '31' ;
                return $datas;
                exit;
            break;

            case $indice == 11:
                $datas['inicio'] = $anoAtual . '-11-' . '01' ;
                $datas['fim'] = $anoAtual . '-11-' . '30' ;
                return $datas;
                exit;
            break;

            case $indice == 12:
                $datas['inicio'] = $anoAtual . '-12-' . '01' ;
                $datas['fim'] = $anoAtual . '-12-' . '31' ;
                return $datas;
                exit;
            break;
        }
    }

    public function diasMesAnoAnterior($indice)
    {
        $anoAtual = date('Y');
        $anoAtual = $anoAtual - 1;
        $datas = array();

        switch($indice){
            case $indice == 1:
                $datas['inicio'] = $anoAtual . '-01-' . '01' ;
                $datas['fim'] = $anoAtual . '-01-' . '31' ;
                return $datas;
                exit;
            break;

            case $indice == 2:
                $datas['inicio'] = $anoAtual . '-02-' . '01' ;
                $datas['fim'] = $anoAtual . '-02-' . '28' ;
                return $datas;
                exit;
            break;

            case $indice == 3:
                $datas['inicio'] = $anoAtual . '-03-' . '01' ;
                $datas['fim'] = $anoAtual . '-03-' . '31' ;
                return $datas;
                exit;
            break;

            case $indice == 4:
                $datas['inicio'] = $anoAtual . '-04-' . '01' ;
                $datas['fim'] = $anoAtual . '-04-' . '30' ;
                return $datas;
                exit;
            break;

            case $indice == 5:
                $datas['inicio'] = $anoAtual . '-05-' . '01' ;
                $datas['fim'] = $anoAtual . '-05-' . '31' ;
                return $datas;
                exit;
            break;

            case $indice == 6:
                $datas['inicio'] = $anoAtual . '-06-' . '01' ;
                $datas['fim'] = $anoAtual . '-06-' . '30' ;
                return $datas;
                exit;
            break;

            case $indice == 7:
                $datas['inicio'] = $anoAtual . '-07-' . '01' ;
                $datas['fim'] = $anoAtual . '-07-' . '31' ;
                return $datas;
                exit;
            break;

            case $indice == 8:
                $datas['inicio'] = $anoAtual . '-08-' . '01' ;
                $datas['fim'] = $anoAtual . '-08-' . '31' ;
                return $datas;
                exit;
            break;

            case $indice == 9:
                $datas['inicio'] = $anoAtual . '-09-' . '01' ;
                $datas['fim'] = $anoAtual . '-09-' . '30' ;
                return $datas;
                exit;
            break;

            case $indice == 10:
                $datas['inicio'] = $anoAtual . '-10-' . '01' ;
                $datas['fim'] = $anoAtual . '-10-' . '31' ;
                return $datas;
                exit;
            break;

            case $indice == 11:
                $datas['inicio'] = $anoAtual . '-11-' . '01' ;
                $datas['fim'] = $anoAtual . '-11-' . '30' ;
                return $datas;
                exit;
            break;

            case $indice == 12:
                $datas['inicio'] = $anoAtual . '-12-' . '01' ;
                $datas['fim'] = $anoAtual . '-12-' . '31' ;
                return $datas;
                exit;
            break;
        }
    }


}
