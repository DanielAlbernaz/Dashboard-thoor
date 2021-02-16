<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Uteis extends Model
{

}
function print_rpre($valor)
    {
        echo "<pre>";
        print_r($valor);
        echo "</pre>";
    }

function comecoMesAtual()
    {
        $data_incio = mktime(0, 0, 0, date('m') , 1 , date('Y'));
        $DataInicial = date('Y-m-d',$data_incio);
        //$DataInicial = '2010-01-01';
        return $DataInicial;
    }

function finalMesAtual()
    {
        $data_fim = mktime(23, 59, 59, date('m'), date("t"), date('Y'));
        $DataFinal = date('Y-m-d',$data_fim);
        return $DataFinal;
    }
