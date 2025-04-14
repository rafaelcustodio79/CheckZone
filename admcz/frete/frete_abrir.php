<?php
$idCotacao = isset($_GET['idCotacao']) ? $_GET['idCotacao'] : '';

$PDO = db_connect();
//$sql = "SELECT * FROM usuario u INNER JOIN empresa emp ON u.id_empresa = emp.id_empresa WHERE u.id = :id";
$sql = "SELECT * FROM cotacao_abertura ca
        INNER JOIN cotacao_carga cc ON ca.id = cc.id_cotacao
        INNER JOIN cotacao_complemento com ON ca.id = com.id_cotacao
        INNER JOIN cotacao_destino cd ON ca.id = cd.id_cotacao
        INNER JOIN cotacao_mercadoria cm ON ca.id = cm.id_cotacao
        INNER JOIN cotacao_origem co ON ca.id = co.id_cotacao
        INNER JOIN cotacao_situacao_atual_cliente csac ON ca.id_status_situacao_atual_cliente = csac.id_status_situacao_atual_cliente
        WHERE ca.id = {$idCotacao} ORDER BY ca.data_add DESC";

$stmt = $PDO->prepare($sql);
$stmt->execute();
$dados = [];
$dados = $stmt->fetch(PDO::FETCH_ASSOC);

$sqlCC = "SELECT * FROM cotacao_carga WHERE id_cotacao = :idCotacao";
$stmtCC = $PDO->prepare($sqlCC);
$stmtCC->bindParam(':idCotacao', $idCotacao, PDO::PARAM_STR);
$stmtCC->execute();
$dadosCC = [];
$dadosCC = $stmtCC->fetchAll(PDO::FETCH_ASSOC);

function adicionarDiasUteis(
    $data,
    $dias
) {
    $data = new DateTime($data);

    for ($i = 0; $i < $dias; $i++) {
        $data->modify('+1 day');
        while ($data->format('N') >= 6) { // 6 e 7 representam sábado e domingo
            $data->modify('+1 day');
        }
    }

    return $data->format('Y-m-d');
}
?>
<style type="text/css">
.texto-destaque-1 {
    color: #003a50;
}

.bg-destaque-1 {
    background-color: #003a50;
    color: #fff;
}

.texto-destaque-2 {
    color: #acc494;
}

.bg-destaque-2 {
    background-color: #acc494;
}

.texto-destaque-3 {
    color: #27aaa5;
}

.bg-destaque-3 {
    background-color: #27aaa5;
}

.texto-destaque-4 {
    color: #00797b;
}

.bg-destaque-4 {
    background-color: #00797b;
}

.badge-ref {
    background-color: #3c5252;
    color: #FFF;
}

.badge-evento {
    background-color: #9ec092;
    color: #000;
    font-size: 16px !important;
}

.bg-ref {
    background-color: #3c5252;
    color: #FFF;
}

.bg-icone {
    background-color: #3c5252;
    color: #FFF;
}

.bg-data {
    background-color: #046868;
    color: #FFF;
}

.origem-destino {
    padding: 10px 0;
    border-bottom: 1px solid #ddd;
    margin-bottom: 15px;
}

.flag-icon {
    font-size: 24px;
    margin-right: 5px;
    width: 30px;
}

.country {
    font-size: 16px;
    font-weight: bold;
}

.transport-icon {
    font-size: 36px;
    color: #046868;
    margin-right: 5px;
}

.arrow {
    font-size: 24px;
    color: #046868;
    margin-left: 5px;
}
</style>
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Frete Internacional</h1>
                <small>Minhas Cotações</small>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item">Frete Internacional</li>
                    <li class="breadcrumb-item">Minhas Cotações</li>
                    <li class="breadcrumb-item active">Visualizando Cotação</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">

            <div class="col-md-6">
                <div class="card card-default">
                    <div class="card-header">
                        <h4 class="card-title">
                            <small>Nº Proc. ComexManager:</small> <strong
                                class="badge badge-info"><?= $dados['n_proc_logix']; ?></strong>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <small>Nº Proc. Interno:</small> <strong
                                class="badge bg-destaque-1"><?= $dados['n_proc_interno']; ?></strong>
                        </h4>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">


                        <div class="row align-items-center origem-destino">
                            <div class="col-md-4 text-center">
                                <span class="flag-icon">
                                    <img src="img/flags/<?=$dados['pais_origem'];?>.png" style="width: 40px;">
                                </span>
                                <br>
                                <span class="country"><?=$dados['pais_origem'];?></span>
                                <br>
                                <?=$dados['cidade_origem'];?>
                                <?php if (!empty($dados['endereco_coleta'])) : ?>
                                <br>
                                <?=$dados['endereco_coleta'];?>
                                <?php endif; ?>
                            </div>

                            <!-- Ícone de Transporte e Linha de Conexão -->
                            <div class="col-md-4 text-center">
                                <?php 
                                    switch ($dados['modal']) {
                                        case 'aer':
                                            $transporte = 'plane';
                                            break;
                                        case 'mar':
                                            $transporte = 'ship';
                                            break;
                                        case 'rod':
                                            $transporte = 'truck';
                                            break;
                                        default:
                                            $transporte = 'unknown'; // Caso queira ter uma opção padrão
                                            break;
                                    }                                                
                                ?>
                                <i class="fas fa-<?=$transporte;?> transport-icon fa-2x"></i>
                                <br>
                                <strong><?=$dados['incoterms'];?></strong>
                                <small>
                                    <sup>
                                        <i class="far fa-question-circle" title="Incoterm"></i>
                                    </sup>
                                </small>
                            </div>

                            <div class="col-md-4 text-center">
                                <span class="flag-icon">
                                    <img src="img/flags/<?=$dados['pais_destino'];?>.png" style="width: 40px;">
                                </span>
                                <br>
                                <span class="country"><?=$dados['pais_destino'];?></span>
                                <br>
                                <?=$dados['cidade_destino'];?>
                                <?php if (!empty($dados['endereco_no_destino'])) : ?>
                                <br>
                                <?=$dados['endereco_no_destino'];?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 text-center"><strong>Embarque Previsto:</strong><br>
                                <?=dateConvert($dados['embarque_previsto']);?>
                            </div>
                            <div class="col-md-8 text-center"><strong>Situação da carga:</strong><br>
                                <?=$dados['carga_pronta'];?>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <p>
                                    <strong>Pedido finalizado em:</strong><br>
                                    <?php
                                    $dataInicial = $dados['data_add'];
                                    $diasUteis = 2;

                                    $dataFinal = adicionarDiasUteis($dataInicial, $diasUteis);
                                    echo dateConvert($dataFinal) . ' - ' . $dados['hora_add'];
                                    ?>
                                </p>
                            </div>
                        </div>

                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->

            <div class="col-12 col-sm-6">
                <div class="card card-info card-tabs">
                    <div class="card-header p-0 pt-1">
                        <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="custom-tabs-one-Carga-tab" data-toggle="pill"
                                    href="#custom-tabs-one-Carga" role="tab" aria-controls="custom-tabs-one-Carga"
                                    aria-selected="true">Carga</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-one-Mercadoria-tab" data-toggle="pill"
                                    href="#custom-tabs-one-Mercadoria" role="tab"
                                    aria-controls="custom-tabs-one-Mercadoria" aria-selected="false">Mercadoria</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-one-Servicos-tab" data-toggle="pill"
                                    href="#custom-tabs-one-Servicos" role="tab" aria-controls="custom-tabs-one-Servicos"
                                    aria-selected="false">Serviços Extras</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-one-DC-tab" data-toggle="pill"
                                    href="#custom-tabs-one-DC" role="tab" aria-controls="custom-tabs-one-DC"
                                    aria-selected="false">Dados Complementares</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="custom-tabs-one-tabContent">
                            <div class="tab-pane fade show active" id="custom-tabs-one-Carga" role="tabpanel"
                                aria-labelledby="custom-tabs-one-Carga-tab">

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Tipo de Carga</label>
                                            <br clear="all">
                                            <?php if ($dadosCC[0]['tipo_carga'] === 'solta') : ?>
                                            <p><i class="fas fa-boxes"></i>
                                                Carga Compartilhada
                                                (LCL)</p>
                                            <?php endif; ?>
                                            <?php if ($dadosCC[0]['tipo_carga'] === 'container') : ?>
                                            <p><img src="img/cont.png" style="max-width: 30px;">
                                                Container (FCL)</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <?php if ($dadosCC[0]['tipo_carga'] === 'solta') : ?>
                                <!-- Linha carga 1 -->
                                <div class="row">
                                    <div class=" col-lg-12">
                                        <table class="table table-striped table-hover" style="font-size: 12px;">
                                            <tr>
                                                <th>Embalagem</th>
                                                <th class="text-center">Qt.</th>
                                                <th class="text-center">
                                                    Dimensões<br>
                                                    (C - cm)
                                                </th>
                                                <th class="text-center">
                                                    Dimensões<br>
                                                    (L - cm)
                                                </th>
                                                <th class="text-center">
                                                    Dimensões<br>
                                                    (A - cm)
                                                </th>
                                                <th class="text-center">CBM</th>
                                                <th>Resumo do produto</th>
                                            </tr>
                                            <?php foreach($dadosCC as $dadoCarga) : ?>
                                            <tr>
                                                <td>
                                                    <?=$dadoCarga['tipo_embalagem'];?>
                                                </td>
                                                <td class="text-center">
                                                    <?=$dadoCarga['qt'];?>
                                                </td>
                                                <td class="text-center">
                                                    <?=$dadoCarga['dim_h'];?>
                                                </td>
                                                <td class="text-center">
                                                    <?=$dadoCarga['dim_l'];?>
                                                </td>
                                                <td class="text-center">
                                                    <?=$dadoCarga['dim_c'];?>
                                                </td>
                                                <td class="text-center">
                                                    <?=$dadoCarga['cbm'];?>
                                                </td>
                                                <td>
                                                    <?=$dadoCarga['resumo_produto'];?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </table>

                                    </div>
                                </div>
                                <!-- FIM Linha -->
                                <?php else : ?>
                                <!-- Linha carga 1 -->
                                <div class="row">
                                    <div class=" col-lg-12">
                                        <table class="table table-striped">
                                            <tr>
                                                <th>Quantidade</th>
                                                <th>Tipo de Container</th>
                                                <th>Peso Total</th>
                                            </tr>
                                            <?php foreach ($dadosCC as $carga) : ?>
                                            <tr>
                                                <td><?= $carga['qt_c'] ?>
                                                </td>
                                                <td><?= $carga['tipo_container'] ?>
                                                </td>
                                                <td><?= $carga['peso_c'] ?>
                                                    <?= $carga['tipo_peso_c'] ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </table>

                                    </div>
                                </div>
                                <!-- FIM Linha -->
                                <?php endif; ?>

                            </div>
                            <div class="tab-pane fade" id="custom-tabs-one-Mercadoria" role="tabpanel"
                                aria-labelledby="custom-tabs-one-Mercadoria-tab">

                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>Produto</label>
                                            <br clear="all">
                                            <?= $dados['produto'] ?>
                                        </div>
                                    </div>

                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label>Perecível</label>
                                            <br clear="all">
                                            <?= $dados['perecivel'] ?>
                                        </div>
                                    </div>

                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label>Empilhável</label>
                                            <br clear="all">
                                            <?= $dados['empilhavel'] ?>
                                        </div>
                                    </div>

                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label>Perigoso</label>
                                            <br clear="all">
                                            <?= $dados['perigoso'] ?>
                                        </div>
                                    </div>

                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label>Refrigeração</label>
                                            <br clear="all">
                                            <?= $dados['refrigeracao'] ?>
                                            <br clear="all">
                                            <?= $dados['temp_refrig'] ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>Valor Total</label>
                                            <br clear="all">
                                            <i class="fa fa-money"></i>
                                            <?= $dados['valor_total'] ?>
                                            <?= $dados['moeda'] ?>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="tab-pane fade" id="custom-tabs-one-Servicos" role="tabpanel"
                                aria-labelledby="custom-tabs-one-Servicos-tab">

                                <div class="row">
                                    <div class="col-lg-4">
                                        <strong>Contratação de seguro:</strong>
                                        <?= $dados['contratacao_seguro']; ?>
                                    </div>
                                    <div class="col-lg-4">
                                        <strong>Despacho Aduaneiro:</strong>
                                        <?= $dados['despacho_aduaneiro']; ?>
                                    </div>
                                    <div class="col-lg-4">
                                        <strong>Contratação Transporte</strong>
                                        <?= $dados['contratacao_transporte']; ?><br>
                                        <?= $dados['endereco_entrega']; ?>
                                    </div>
                                </div>

                            </div>
                            <div class="tab-pane fade" id="custom-tabs-one-DC" role="tabpanel"
                                aria-labelledby="custom-tabs-one-DC-tab">

                                <div class="row">
                                    <div class="col-lg-8">
                                        <strong>Observações:</strong><br>
                                        <?= $dados['obs']; ?>
                                    </div>

                                    <div class="col-lg-4">
                                        <strong>Arquivo complementar:</strong> <a
                                            href='arquivos/uploads/<?= $dados['arquivo']; ?>'
                                            target='_blank'><?= $dados['arquivo']; ?></a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>

        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->


<script>
$(document).ready(function() {
    // Verifique se o parâmetro 'flag' está presente na URL
    var flagParam =
        '<?php echo isset($_GET['flag']) ? $_GET['flag'] : ''; ?>';
    var tipParam =
        '<?php echo isset($_GET['tip']) ? $_GET['tip'] : ''; ?>';

    // Se 'flag' estiver presente e for igual a 'erro', exiba a notificação de erro
    if (flagParam == 'erro') {
        toastr.error(tipParam);
    }
    if (flagParam == 'warning') {
        toastr.warning(tipParam);
    }
    if (flagParam == 'success') {
        toastr.success(tipParam);
    }
});
</script>