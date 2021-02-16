@include('header')


        <div class="main-panel">
          <div class="content-wrapper">
            <div class="row">
              <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-13">
                        <div class="d-flex align-items-center align-self-start">
                        <?php $faturamento_mensal = Session::get('faturamento_mensal'); ?>
                          <h3 class="mb-0">{{ $faturamento_mensal }}</h3>
                          <!-- <p class="text-success ml-2 mb-0 font-weight-medium">+3.5%</p> -->
                        </div>
                      </div>
                    </div>
                    <h6 class="text-muted font-weight-normal">Faturamento Mensal</h6>
                  </div>
                </div>
              </div>
              <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-13">
                        <div class="d-flex align-items-center align-self-start">
                        <?php $receita_total_grafico = Session::get('total_receita_faturamento');
                              $receita_total_grafico = 'R$ ' . number_format($receita_total_grafico, 2, ',', '.')
                        ?>
                          <h3 class="mb-0">{{ $receita_total_grafico }}</h3>
                        </div>
                      </div>
                    </div>
                    <h6 class="text-muted font-weight-normal">Receita Total Gráfico</h6>
                  </div>
                </div>
              </div>
              <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-13">
                        <div class="d-flex align-items-center align-self-start">
                        <?php $faturamento_anual = Session::get('faturamento_anual'); ?>
                          <h3 class="mb-0">{{ ltrim($faturamento_anual) }}</h3>
                        </div>
                      </div>
                    </div>
                    <h6 class="text-muted font-weight-normal">Faturamento Anual</h6>
                  </div>
                </div>
              </div>
              <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-13">
                        <div class="d-flex align-items-center align-self-start">
                        <?php $carregamento_anual = Session::get('carregamento_mensal'); ?>
                          <h3 class="mb-0">{{ $carregamento_anual }}</h3>
                        </div>
                      </div>
                    </div>
                    <h6 class="text-muted font-weight-normal">Volume Carregado Mensal</h6>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">

                <?php
                if(Session::get('faturamento_frota_motorista')){
                    $motora = Session::get('faturamento_frota_motorista');
                }else{
                    $motora[0]['motorista'][0] = 'vazio';

                }

                if(Session::get('motoristasComparar')){
                    $placas = Session::get('motoristasComparar');
                }else{
                    $placas[0][0] = 'vazio';

                }

                if(Session::get('tipo_filtro')){
                  $tipo_filtro = Session::get('tipo_filtro');
                }


                    // $tipo_filtro = 'placa';
                    //print_r($tipo_filtro);exit;
                    // print_r(count($motora));

                ?>


              <div class="col-lg-12 grid-margin ">
                <div class="card">
                  <div class="card-body" style="height:130%;">
                    <h4 class="card-title">Bar chart
                        <a  href="" data-toggle="modal" data-target="#modalFiltro">
                            <i class="mdi mdi-filter" style="margin-left: 99%;" title="Filtro" ></i>
                        </a>
                    </h4>
                    <canvas id="barChart" style="height: 120px; display: block; width: 451px;"></canvas>
                  </div>
                </div>
              </div>
              <div class="modal fade" id="modalFiltro" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                  <div class="modal-content" style=" width: 1000px; height: 300px; margin-left: -45%;  margin-top: 45%;">
                    <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">Filtros</h5>
                      <button type="button" class="close" style="color: white;" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body" >
                      <form enctype="multipart/form-data" action="javascript:void(0)" method="post" id="frmFiltrar">
                        @csrf
                        <div class="form-group hover"  id="blocoMotorista"  onblur="mostrarPlaca();" >
                            <label>Motorista</label>
                            <select class="js-example-basic-multiple" multiple="multiple" id="motorista" name="motorista[]" style="width:100%">

                                    @for($i = 0; $i < count($motora); $i++)
                                        @foreach ($arrayMotoristas as $arrayMotorista)
                                            <?php if($motora[$i]['motorista'][0] == $arrayMotorista['motorista'] && $tipo_filtro == 'motorista'){ ?>
                                                <option selected value="<?=$motora[$i]['motorista'][0]?>"><?=$motora[$i]['motorista'][0]?></option>
                                            <?php }else{?>
                                                <option value="<?=$arrayMotorista['motorista']?>"><?=$arrayMotorista['motorista']?></option>
                                            <?php }?>
                                        @endforeach
                                    @endfor

                            </select>
                          </div>

                          <?php $placasFiltro = Session::get('placas_filtro');?>
                          <div class="form-group" id="blocoPlaca">
                            <label>Placa</label>
                            <select class="js-example-basic-multiple" multiple="multiple" id="placa[]" name="placa[]" style="width:100%">
                                @for($i = 0; $i < count($placas); $i++)
                                    @foreach ($arrayPlacas as $arrayPlaca)
                                        <?php if($placas[$i][0] == $arrayPlaca['placa'] && $tipo_filtro == 'placa'){ ?>
                                            <option selected value="<?=$placas[$i][0]?>"><?=$placas[$i][0]?></option>
                                        <?php }else{?>
                                            <option value="<?=$arrayPlaca['placa']?>"><?=$arrayPlaca['placa']?></option>
                                        <?php }?>
                                    @endforeach
                                @endfor
                            </select>
                          </div>


                          <div class="form-group row">

                            <label for="example-date-input" class="col-2 col-form-label" >Data Inicial</label>
                            <div class="col-3" >
                                <input class="form-control" type="date" id="dataInicial" name="dataInicial" value="{{date("Y-m-01")}}" id="example-date-input">
                            </div>

                            <label for="example-date-input" class="col-2 col-form-label">Data Final (opicional)</label>
                            <div class="col-3" >
                                <input class="form-control" type="date" id="dataFinal" name="dataFinal" value="{{date("Y-m-d")}}" id="example-date-input">
                            </div>
                            <div class="form-check form-check-flat form-check-primary">
                                <label class="form-check-label">
                                <input type="checkbox" id="salvar_filtro" name="salvar_filtro" class="form-check-input"> Salvar filtro </label>
                            </div>

                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                            <!-- <button type="button" class="btn btn-warning" onclick="limparFiltro();">Limpar filtros</button> -->
                            <button type="button" class="btn btn-primary" onclick="filtrar();">Aplicar filtros</button>
                          </div>
                      </form>
                    </div>

                  </div>
                </div>
              </div>
            </div>
           @if(Session::get('motoristas') != NULL)
           <?php $motoristas = Session::get('motoristas'); ?>
            @endif

            <div class="row ">
              <div class="col-12 grid-margin">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Faturamento Frota</h4>
                    <div class="table-responsive">
                      <table class="table">
                      <?php $faturamento_frota = Session::get('faturamento_frota');
                                  $faturamento_frota_motorista = Session::get('faturamento_frota_motorista');
                                $i = 0;
                            ?>
                        <thead>
                          <tr>
                            <th>
                              <div class="form-check form-check-muted m-0">
                                <label class="form-check-label">
                                </label>
                              </div>
                            </th>
                            <th> Motorista </th>
                            <th> Placa </th>
                            <th> Soma da receita </th>
                            @if(isset($faturamento_frota_motorista))
                                <th> Soma total da receita </th>
                            @endif
                          </tr>
                        </thead>
                        <tbody>

                            <!-- Se vier filtro das placas -->
                            @if(isset($faturamento_frota))
                                @foreach ($faturamento_frota as $faturamento)
                                <?php $i++; ?>
                            <tr>
                                <td>
                                <div class="form-check form-check-muted m-0">
                                    @if($i == 1)
                                        <img src="assets/images/faces/ouro.png" alt="">
                                    @endif
                                    @if($i == 2)
                                        <img src="assets/images/faces/prata.png" alt="">
                                    @endif
                                    @if($i == 3)
                                        <img src="assets/images/faces/bronze.png" alt="">
                                    @endif
                                    @if($i != 1 && $i != 2 && $i != 3)
                                        <span>{{$i}}º</span>
                                    @endif

                                </div>
                                </td>
                                <td>
                                    <span class="pl-2">
                                        @foreach ($faturamento['motorista'] as $motorista)
                                            <p>
                                                {{ trim($motorista) }}
                                            </p>
                                        @endforeach
                                    </span>
                                </td>
                                <?php
                                    $resultado = '';
                                    $resultado = str_replace(' ', '', $faturamento['placa']);
                                    $resultado = substr_replace($resultado, '-', 3, 0);
                                ?>
                                    <td>
                                        {{ $resultado }}
                                    </td>

                                    <td>
                                        @foreach ($faturamento['faturamento'] as $faturamento)
                                            <p>
                                                R$: {{ trim($faturamento) }}
                                            </p>
                                        @endforeach
                                    </td>
                                <td>
                                </td>
                            </tr>
                            @endforeach
                          @endif

                          <!-- Se vier filtro dos motorista -->
                          @if(isset($faturamento_frota_motorista))
                                @foreach ($faturamento_frota_motorista as $faturamento_motorista)
                                <?php $i++; ?>
                            <tr>
                                <td>
                                <div class="form-check form-check-muted m-0">
                                    @if($i == 1)
                                        <img src="assets/images/faces/ouro.png" alt="">
                                    @endif
                                    @if($i == 2)
                                        <img src="assets/images/faces/prata.png" alt="">
                                    @endif
                                    @if($i == 3)
                                        <img src="assets/images/faces/bronze.png" alt="">
                                    @endif
                                    @if($i != 1 && $i != 2 && $i != 3)
                                        <span>{{$i}}º</span>
                                    @endif

                                </div>
                                </td>
                                <td>
                                    <span class="pl-2">
                                        <p>
                                            {{ trim($faturamento_motorista['motorista'][0]) }}
                                        </p>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                        foreach($faturamento_motorista['placa'] as $placas){
                                            $resultado = str_replace(' ', '', $placas);
                                            $resultado = substr_replace($resultado, '-', 3, 0);?>

                                            {{ $resultado }} <br><br>

                                        <?php }
                                    ?>
                                </td>

                                    <td>
                                        @foreach ($faturamento_motorista['valor_placas'] as $valor_placa)
                                            <p>
                                                {{ trim($valor_placa) }}
                                            </p>
                                        @endforeach
                                    </td>

                                    <td>
                                        <p>
                                             {{ trim($faturamento_motorista['valor_total'][0]) }}
                                        </p>
                                    </td>
                                <td>
                                </td>
                            </tr>
                            @endforeach
                          @endif


                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Mês-->
            <?php $declinio = Session::get('declinio'); ?>
            @if(isset($declinio))
            <div class="row ">
              <div class="col-4 grid-margin" >
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title" >
                    DESEMPENHO THOOR 2020
                    </h4>
                    <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th> </th>
                                <th></th>
                                <th>  </th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($declinio as $anoAnterior)
                          <tr>
                            <td>{{ $anoAnterior['mes_anterior'] }}</td>
                            <td> R$:</td>
                            <td> {{ number_format($anoAnterior['valor_mes_anterior'], 2, ',', '.') }} </td>
                          </tr>
                        @endforeach
                          <tr>
                            <td style="color: white;" > TOTAL</td>
                            <td style="color: white;"> R$:</td>
                            <td style="color: white;"> {{ number_format(Session::get('totalMesAnoPassado'), 2, ',', '.') }} </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Segunda -->
              <div class="col-4 grid-margin" >
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title" >
                    DESEMPENHO THOOR  2021
                    </h4>
                    <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th> </th>
                                <th></th>
                                <th> </th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($declinio as $anoAtual)
                          <tr>
                            <td>{{ $anoAtual['mes_atual'] }}</td>
                            <td> R$:</td>
                            <td> {{ number_format($anoAtual['valor_mes_atual'], 2, ',', '.') }} </td>
                          </tr>
                        @endforeach
                            <td style="color: white;" > TOTAL</td>
                            <td style="color: white;"> R$:</td>
                            <td style="color: white;"> {{ number_format(Session::get('totalMesAnoAtual'), 2, ',', '.') }} </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Fim Segunda -->
              <!-- terçeira -->
              <div class="col-4 grid-margin" >
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">
                    DECLÍNIO/CRESCIMENTO
                    </h4>
                    <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody >
                        @foreach($declinio as $anoAnterior)
                            @if($anoAnterior['valor_mes_anterior'] > $anoAnterior['valor_mes_atual'] && $anoAnterior['valor_mes_atual'] <> 0)
                                <tr style="color: red">
                                    <td style="padding: 5px" ><span class="mdi mdi-arrow-bottom-left icon-item" ></span></td>
                                    <td style="padding: 5px" >R$: {{ number_format($anoAnterior['total_meses'], 2, ',', '.') }} </td>
                                    <td  >-{{ number_format($anoAnterior['percentual'], 2, ',', '.') }}% </td>
                                </tr>
                            @endif
                            @if($anoAnterior['valor_mes_atual'] == 0)
                                <tr style="color: green">
                                    <td style="padding: 5px" ><span class="mdi mdi-arrow-bottom-left icon-item" ></span></td>
                                    <td style="padding: 5px" >R$: {{ number_format($anoAnterior['total_meses'], 2, ',', '.') }} </td>
                                    <td  >{{ number_format($anoAnterior['percentual'], 2, ',', '.') }}% </td>
                                </tr>
                            @endif
                            @if($anoAnterior['valor_mes_anterior'] < $anoAnterior['valor_mes_atual'])
                                <tr style="color: green">
                                    <td style="padding: 5px"><span class="mdi mdi-arrow-top-right icon-item" ></span></td>
                                    <td style="padding: 5px" >R$: {{ number_format($anoAnterior['total_meses'], 2, ',', '.') }} </td>
                                    <td  >{{ number_format($anoAnterior['percentual'], 2, ',', '.') }}% </td>
                                </tr>
                            @endif
                        @endforeach
                          <tr>
                            <td style="color: white;" style="padding: 5px"> TOTAL</td>
                            @if(Session::get('totalMesAnoPassado') > Session::get('totalMesAnoAtual'))
                              <td style="color: red;" style="padding: 5px"> R$: {{ number_format(Session::get('totalGeralCrescimentoDeclinio'), 2, ',', '.') }} </td>
                              <td style="color: red;" >-{{ number_format(Session::get('totalMediaPercentual'), 2, ',', '.') }}% </td>
                            @endif
                            @if(Session::get('totalMesAnoPassado') < Session::get('totalMesAnoAtual'))
                              <td style="color: green;" style="padding: 5px"> R$: {{ number_format(Session::get('totalGeralCrescimentoDeclinio'), 2, ',', '.') }} </td>
                              <td style="color: green;" >{{ number_format(Session::get('totalMediaPercentual'), 2, ',', '.') }}% </td>
                            @endif
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Fim Porcentagem -->

            </div>
             @endif

            <?php
                $informacoesTabela = Session::get('informacoesTabela');
                $totalFaturamentoMes = Session::get('totalFaturamentoMes');
            ?>
            @if(isset($informacoesTabela) && isset($totalFaturamentoMes))
                <div class="row ">
                <div class="col-12 grid-margin">
                    <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">FATURAMENTO POR TOMADOR</h4>
                        <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>

                                </th>
                                <th> CLIENTE / TOMADOR </th>
                                <th> VALOR FRETE </th>
                                <th> VALOR % </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($informacoesTabela as $totalDistribuidoras)
                                <tr>
                                    <td>
                                    <!-- <div class="form-check form-check-muted m-0">
                                        <label class="form-check-label">
                                        <img src="assets/images/faces/araguaia.jpeg" alt="image" />
                                        </label>
                                    </div> -->
                                    </td>
                                    <td>
                                    <?php
                                        $tomadorSemEspacos = str_replace(' ', '',  $totalDistribuidoras['tomador']);
                                        $araguaia = 'ARAGUAIADISTRIBUIDORADECOMBUSTIVEISS/A';
                                        $araguai2 = 'ARAGUAIADISTRIB.DECOMBUSTIVEISS/A';
                                        $fam = 'FANDISTRIBUIDORADEPETROLEOLTDA';
                                        $phenix = 'PHOENIXDISTDECOMBUSTIVEISS/A';
                                        $tabocao = 'DISTRIBUIDORATABOCAOLTDA';
                                        $larco = 'LARCOCOMERCIALDEPRODUTOSDEPETROLEOLTDA.';
                                        $larco2 = 'LARCOCOMERCIALDEPRODUTOSDEPETROLEOLTDA';
                                        $larco3 = 'LARCOCOMERCIALDEPRODUTOSDEPETROLEO';
                                        $federal = 'FEDERALDISTRIBUIDORADEPETROLEOLTDA';
                                        $petro = 'PetroballDistribuidoradePetroleoLtda';
                                        $tdc = 'TDCDISTRIBUIDORADECOMBUSTIVEISS/A';
                                        $denusa = 'DENUSA-DESTILARIANOVAUNIAOS/A';
                                        $rio = 'DISTRIBUIDORARIOBRANCODEPETROLEOLTD';
                                        $petroquality = 'PETROQUALITYDISTRDECOMBLTDA';
                                        $tower = 'TOWERBRASILPETROLEOLTDA';
                                        $petroquality2 = 'PETROQUALITYDISTRIBUIDORADECOMBUSTIVEISLTDA';
                                        $jales = 'JallesMachadoS/A';
                                        $jales2 = 'JallesMachadoS.A.';
                                        $gol = 'GOLCOMBUSTIVEISS.A';

                                    ?>
                                    @if($araguaia == $tomadorSemEspacos || $araguai2 == $tomadorSemEspacos)
                                        <img  id="imagem-Fotos" src="assets/images/faces/logos/ARAGUAIA.png" alt="image" />
                                    @endif
                                    @if($petroquality == $tomadorSemEspacos || $petroquality2 == $tomadorSemEspacos)
                                        <img  id="imagem-Fotos" src="assets/images/faces/logos/ARAGUAIA.png" alt="image" />
                                    @endif
                                    @if($tower == $tomadorSemEspacos)
                                        <img  id="imagem-Fotos" src="assets/images/faces/logos/TOWER.jpeg" alt="image" />
                                    @endif
                                    @if($jales == $tomadorSemEspacos || $jales2 == $tomadorSemEspacos)
                                        <img  id="imagem-Fotos" src="assets/images/faces/logos/JALLESMACHADO.png" alt="image" />
                                    @endif
                                    @if($gol == $tomadorSemEspacos)
                                        <img  id="imagem-Fotos" src="assets/images/faces/logos/GOL.jpeg" alt="image" />
                                    @endif
                                    @if($fam == $tomadorSemEspacos)
                                        <img id="imagem-Fotos" src="assets/images/faces/logos/FAN.png" alt="image" />
                                    @endif
                                    @if($phenix == $tomadorSemEspacos)
                                        <img id="imagem-Fotos" src="assets/images/faces/logos/das.jpeg"  alt="image" />
                                    @endif
                                    @if($tabocao == $tomadorSemEspacos)
                                        <img id="imagem-Fotos" src="assets/images/faces/logos/TABOCAO.jpg"  alt="image" />
                                    @endif
                                    @if($larco == $tomadorSemEspacos || $larco2 == $tomadorSemEspacos || $larco3 == $tomadorSemEspacos)
                                        <img id="imagem-Fotos" src="assets/images/faces/logos/LARCO.jpg"  alt="image" />
                                    @endif
                                    @if($federal == $tomadorSemEspacos)
                                        <img id="imagem-Fotos" src="assets/images/faces/logos/FEDERAL.jpg"  alt="image" />
                                    @endif
                                    @if($petro == $tomadorSemEspacos)
                                        <img id="imagem-Fotos" src="assets/images/faces/logos/PETROBALL.jpg"  alt="image" />
                                    @endif
                                    @if($tdc == $tomadorSemEspacos)
                                        <img id="imagem-Fotos" src="assets/images/faces/logos/sem-imagem.png"  alt="image" />
                                    @endif
                                    @if($denusa == $tomadorSemEspacos)
                                        <img id="imagem-Fotos" src="assets/images/faces/logos/DENUSA.jpg"  alt="image" />
                                    @endif
                                    @if($rio == $tomadorSemEspacos)
                                        <img id="imagem-Fotos" src="assets/images/faces/logos/rio.jpg"  alt="image" />
                                    @endif

                                    @if($tomadorSemEspacos != $larco3 && $tomadorSemEspacos != $larco2 && $tomadorSemEspacos != $jales2 && $tomadorSemEspacos != $gol && $tomadorSemEspacos != $jales && $tomadorSemEspacos != $petroquality2 && $tomadorSemEspacos != $tower && $tomadorSemEspacos != $petroquality && $tomadorSemEspacos != $araguai2 && $tomadorSemEspacos != $rio && $tomadorSemEspacos != $denusa && $tomadorSemEspacos != $tdc && $tomadorSemEspacos !=$petro && $tomadorSemEspacos != $federal && $tomadorSemEspacos !=$larco && $tomadorSemEspacos != $tabocao && $tomadorSemEspacos != $phenix && $tomadorSemEspacos !=$fam && $tomadorSemEspacos !=$araguaia)
                                    <img id="imagem-Fotos" src="assets/images/faces/logos/sem-imagem.png"  alt="image" />
                                    @endif


                                    <span class="pl-2">{{ $totalDistribuidoras['tomador'] }}</span>
                                    </td>
                                    <td> R$: {{ $totalDistribuidoras['total_faturado_mes'] }} </td>
                                    <td> {{ $totalDistribuidoras['percentual'] }} % </td>
                                </tr>
                            @endforeach

                            <tr>
                                <td>  </td>
                                <td>TOTAL CONHECIMENTOS  </td>
                                <td style="color: white;">R$: {{ $totalFaturamentoMes }}  </td>
                                <td>  </td>
                            </tr>
                            </tbody>
                        </table>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
            @endif

          <!-- content-wrapper ends -->
          <!-- partial:partials/_footer.html -->
          <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
              <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2020. THOOR, TODOS OS DIREITOS RESERVADOS.</span>
              <!-- <span class="text-muted float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Hand-crafted & made with <i class="mdi mdi-heart text-danger"></i></span> -->
            </div>
          </footer>
          <!-- partial -->
        </div>
@include('footer')
