<?php

namespace App\Http\Controllers;

use App\Conhecimentos;
use Illuminate\Http\Request;
use App\Uteis;
use Illuminate\Support\Facades\DB;
use function App\print_rpre;

class ControllerXml extends Controller
{
    public function salvarXml(Request $request){
        $uteis = new Uteis();

        $files = $request->file('arquivo');
        $fp = fopen($files, "r");

        $arquivo = array();
        $i = 0;
        while(!feof($fp)){
            if($linha = fgets($fp)){
                if($linha != ''){
                    $arquivo[$i]['linha'] = $linha;
                    $i++;
                    
                }
            }

        }
        
        $arquivoFormatado = array(); // daniel@daniel.com.br
        $contador = 0;
        for($i = 0; $i < count($arquivo); $i++){
            $linhaExplodida = explode(';', $arquivo[$i]['linha']);
            if(count($linhaExplodida) > 1){
                $arquivoFormatado[$contador] = $linhaExplodida;
                $contador++;
            }

        }
        ///print_rpre($arquivoFormatado);exit;
        for($i = 0; $i < count($arquivoFormatado); $i++){

            $existeCte = Conhecimentos::where('numero_cte', '=', $arquivoFormatado[$i][0])->get();

            if(!isset($existeCte[0]->numero_cte)){
                $conhecimentos = new Conhecimentos();
                $conhecimentos->numero_cte = (isset($arquivoFormatado[$i][0]) ? trim($arquivoFormatado[$i][0]) : '');
                $conhecimentos->nota_fiscal = (isset($arquivoFormatado[$i][1]) ? trim($arquivoFormatado[$i][1]) : '');
                $conhecimentos->nota_valor = (isset($arquivoFormatado[$i][2]) ? $arquivoFormatado[$i][2] : NULL );
                $conhecimentos->nota_volume = (isset($arquivoFormatado[$i][3]) ? $arquivoFormatado[$i][3] : NULL);
                $conhecimentos->nota_peso = (isset($arquivoFormatado[$i][4]) ? $arquivoFormatado[$i][4]: NULL);
                $conhecimentos->valor_frete = (isset($arquivoFormatado[$i][5]) ? $arquivoFormatado[$i][5] : NULL);
                $conhecimentos->data_emissao = (isset($arquivoFormatado[$i][6]) ? $arquivoFormatado[$i][6] : NULL);
                $conhecimentos->tomador = (isset($arquivoFormatado[$i][7]) ? trim($arquivoFormatado[$i][7]) : '');
                $conhecimentos->remetente = (isset($arquivoFormatado[$i][8]) ? trim($arquivoFormatado[$i][8]) : '');
                $conhecimentos->destinatario = (isset($arquivoFormatado[$i][9]) ? trim($arquivoFormatado[$i][9]) : '');
                $conhecimentos->motorista = (isset($arquivoFormatado[$i][10]) ? trim($arquivoFormatado[$i][10]) : '');
                $conhecimentos->proprietario = (isset($arquivoFormatado[$i][11]) ? trim($arquivoFormatado[$i][11]) : '');
                $conhecimentos->cidade_origem = (isset($arquivoFormatado[$i][12]) ? trim($arquivoFormatado[$i][12]) : '');
                $conhecimentos->cidade_destino = (isset($arquivoFormatado[$i][13]) ? trim($arquivoFormatado[$i][13]) : '');
                $conhecimentos->situacao = (isset($arquivoFormatado[$i][14]) ? trim($arquivoFormatado[$i][14]) : '');
                $conhecimentos->tipo_cte = (isset($arquivoFormatado[$i][15]) ? trim($arquivoFormatado[$i][15]) : '');
                $conhecimentos->mercadoria = (isset($arquivoFormatado[$i][16]) ? trim($arquivoFormatado[$i][16]) : '');
                if(isset($arquivoFormatado[$i][17])){
                    $arquivoFormatado[$i][17] = str_replace(' ', '', $arquivoFormatado[$i][17]);
                    $conhecimentos->placa = trim($arquivoFormatado[$i][17]);
                }else{
                    $conhecimentos->placa = '';
                }

                $conhecimentos->save();
            }
        }

        $retorno = [
            'situacao' => 'success',
            'msg' => 'Importação bem sucedida!',
        ];

        return $retorno;

    }

    function deletarTudo(Request $request){

        $flight = Conhecimentos::select()->delete();

        return view('importarXml');
    }

}
