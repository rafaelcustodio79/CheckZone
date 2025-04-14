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

$sqlAgentes = "SELECT * FROM agentes_carga WHERE ativo = 1";
$stmtAgentes = $PDO->prepare($sqlAgentes);
$stmtAgentes->execute();
$dadosAgentes = [];
$dadosAgentes = $stmtAgentes->fetchAll(PDO::FETCH_ASSOC);

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
.nav-pills .nav-link.active,
.nav-pills .show>.nav-link {
    color: #fff;
    background-color: #003a50 !important;
}

.texto-destaque-1 {
    color: #003a50;
}

.bg-destaque-1 {
    background-color: #003a50;
    color: #fff;
}

.callout.callout-1 {
    border-left-color: #003a50;
}

.texto-destaque-2 {
    color: #acc494;
}

.bg-destaque-2 {
    background-color: #acc494;
}

.callout.callout-2 {
    border-left-color: #acc494;
}

.texto-destaque-3 {
    color: #27aaa5;
}

.bg-destaque-3 {
    background-color: #27aaa5;
}

.callout.callout-3 {
    border-left-color: #27aaa5;
}

.texto-destaque-4 {
    color: #00797b;
}

.bg-destaque-4 {
    background-color: #00797b;
}

.callout.callout-4 {
    border-left-color: #00797b;
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
                <h1>Cotação de Frete Internacional</h1>
                <small>Proposta</small>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item">Cotação de Frete Internacional</li>
                    <li class="breadcrumb-item active">Proposta</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4 col-md-12 col-sm-12">

                <!-- we are adding the accordion ID so Bootstrap's collapse plugin detects it -->
                <div id="accordion">
                    <div class="card card-info">
                        <div class="card-header">
                            <h4 class="card-title w-100">
                                <a class="d-block w-100" data-toggle="collapse" href="#collapseOne">
                                    Abertura
                                </a>
                            </h4>
                        </div>
                        <div id="collapseOne" class="collapse show" data-parent="#accordion">
                            <div class="card-body">
                                <small>Nº Proc. ComexManager:</small> <strong
                                    class="badge badge-info"><?= $dados['n_proc_logix']; ?></strong>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <small>Nº Proc. Interno:</small> <strong
                                    class="badge bg-destaque-1"><?= $dados['n_proc_interno']; ?></strong>

                                <hr>

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
                        </div>
                    </div>
                    <div class="card card-info">
                        <div class="card-header">
                            <h4 class="card-title w-100">
                                <a class="d-block w-100" data-toggle="collapse" href="#collapseTwo">
                                    Carga
                                </a>
                            </h4>
                        </div>
                        <div id="collapseTwo" class="collapse" data-parent="#accordion">
                            <div class="card-body">

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
                        </div>
                    </div>
                    <div class="card card-info">
                        <div class="card-header">
                            <h4 class="card-title w-100">
                                <a class="d-block w-100" data-toggle="collapse" href="#collapseThree">
                                    Produto
                                </a>
                            </h4>
                        </div>
                        <div id="collapseThree" class="collapse" data-parent="#accordion">
                            <div class="card-body">
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
                        </div>
                    </div>
                    <div class="card card-info">
                        <div class="card-header">
                            <h4 class="card-title w-100">
                                <a class="d-block w-100" data-toggle="collapse" href="#collapseFour">
                                    Demais informações
                                </a>
                            </h4>
                        </div>
                        <div id="collapseFour" class="collapse" data-parent="#accordion">
                            <div class="card-body">

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
                </div>

            </div>
            <!-- /.col -->

            <div class="col-lg-8 col-md-12 col-sm-12">

                <div class="row">
                    <div class="col-12">
                        <!-- Custom Tabs -->
                        <div class="card">
                            <div class="card-header d-flex p-0">
                                <h3 class="card-title p-3">Cadastrando Proposta</h3>
                                <ul class="nav nav-pills ml-auto p-2">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#agente_carga" data-toggle="tab">
                                            Agente de Carga
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#origem_destino" data-toggle="tab">
                                            Origem / Destino
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#frete_tx" data-toggle="tab">
                                            Fretes / Taxas
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#condicoes" data-toggle="tab">
                                            Condições
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#complementos" data-toggle="tab">
                                            Complementos
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#all_in" data-toggle="tab">
                                            All In
                                        </a>
                                    </li>
                                </ul>
                            </div><!-- /.card-header -->
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="agente_carga">
                                        <div class="form-group">
                                            <label for="agente_carga">Escolha uma Empresa</label>
                                            <div class="row">
                                                <?php foreach ($dadosAgentes as $agente) : ?>
                                                <div class="col-md-6 mb-3">
                                                    <label
                                                        class="d-flex align-items-center border rounded p-2 w-100 empresa-item"
                                                        style="cursor: pointer;">
                                                        <input type="radio" name="agente_carga"
                                                            value="<?= $agente['id_agente']; ?>"
                                                            data-logo="img/logos/<?= $agente['logo']; ?>"
                                                            class="empresa-radio d-none">
                                                        <img src="img/logos/<?= $agente['logo']; ?>"
                                                            alt="<?= $agente['nome_empresa']; ?>"
                                                            class="logo-img me-2 border rounded"
                                                            style="width: 100px; height: 60px; object-fit: contain;">
                                                        <span class="empresa-nome" style="padding-left: 15px;">
                                                            <?= $agente['nome_empresa']; ?>
                                                        </span>
                                                    </label>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- /.tab-pane -->
                                    <div class="tab-pane" id="origem_destino">

                                        <div class="row">

                                            <div class="col-md-6">
                                                <div class="callout callout-1">
                                                    <h5><i class="fas fa-map-marked-alt"></i> Origem</h5>

                                                    <div class="form-group">
                                                        <label>Pa&iacute;s</label>
                                                        <br clear="all">
                                                        <select id="frm_pais" name="pais_origem" required
                                                            class="form-control" style="width: 100%;">
                                                            <option value="" selected>Selecione um país</option>
                                                            <option value="Afeganistao">Afeganistão</option>
                                                            <option value="Africa do Sul">África do Sul</option>
                                                            <option value="Albania">Albânia</option>
                                                            <option value="Alemanha">Alemanha</option>
                                                            <option value="Andorra">Andorra</option>
                                                            <option value="Angola">Angola</option>
                                                            <option value="Arabia Saudita">Arábia Saudita</option>
                                                            <option value="Argelia">Argélia</option>
                                                            <option value="Argentina">Argentina</option>
                                                            <option value="Armenia">Armênia</option>
                                                            <option value="Australia">Austrália</option>
                                                            <option value="Austria">Áustria</option>
                                                            <option value="Azerbaijao">Azerbaijão</option>
                                                            <option value="Bahamas">Bahamas</option>
                                                            <option value="Bangladesh">Bangladesh</option>
                                                            <option value="Belgica">Bélgica</option>
                                                            <option value="Bielorrusia">Bielorrusia</option>
                                                            <option value="Bolivia">Bolívia</option>
                                                            <option value="Bosnia e Herzegovina">Bósnia e Herzegovina
                                                            </option>
                                                            <option value="Botsuana">Botsuana</option>
                                                            <option value="Brasil">Brasil</option>
                                                            <option value="Brunei">Brunei</option>
                                                            <option value="Bulgaria">Bulgária</option>
                                                            <option value="Burkina Fasso">Burkina Fasso</option>
                                                            <option value="Butao">Butão</option>
                                                            <option value="Camaroes">Camarões</option>
                                                            <option value="Camboja">Camboja</option>
                                                            <option value="Canada">Canadá</option>
                                                            <option value="Cazaquistao">Cazaquistão</option>
                                                            <option value="Chile">Chile</option>
                                                            <option value="China">China</option>
                                                            <option value="Chipre">Chipre</option>
                                                            <option value="Colombia">Colômbia</option>
                                                            <option value="Coreia do Norte">Coréia do Norte</option>
                                                            <option value="Coreia do Sul">Coréia do Sul</option>
                                                            <option value="Costa do Marfim">Costa do Marfim</option>
                                                            <option value="Costa Rica">Costa Rica</option>
                                                            <option value="Croacia">Croácia</option>
                                                            <option value="Cuba">Cuba</option>
                                                            <option value="Dinamarca">Dinamarca</option>
                                                            <option value="Egito">Egito</option>
                                                            <option value="El Salvador">El Salvador</option>
                                                            <option value="Emirados Arabes Unidos">Emirados Árabes
                                                                Unidos</option>
                                                            <option value="Equador">Equador</option>
                                                            <option value="Escocia">Escócia</option>
                                                            <option value="Eslovaquia">Eslováquia</option>
                                                            <option value="Eslovenia">Eslovênia</option>
                                                            <option value="Espanha">Espanha</option>
                                                            <option value="Estados Unidos">Estados Unidos</option>
                                                            <option value="Estonia">Estônia</option>
                                                            <option value="Etiopia">Etiópia</option>
                                                            <option value="Finlandia">Finlândia</option>
                                                            <option value="Franca">França</option>
                                                            <option value="Gabao">Gabão</option>
                                                            <option value="Gambia">Gâmbia</option>
                                                            <option value="Gana">Gana</option>
                                                            <option value="Georgia">Geórgia</option>
                                                            <option value="Gibraltar">Gibraltar</option>
                                                            <option value="Granada">Granada</option>
                                                            <option value="Grecia">Grécia</option>
                                                            <option value="Groelandia">Groelândia</option>
                                                            <option value="Guatemala">Guatemala</option>
                                                            <option value="Guine Equatorial">Guiné Equatorial</option>
                                                            <option value="Guine-Bissau">Guiné-bissau</option>
                                                            <option value="Guine">Guiné</option>
                                                            <option value="Haiti">Haiti</option>
                                                            <option value="Holanda">Holanda</option>
                                                            <option value="Honduras">Honduras</option>
                                                            <option value="Hong Kong">Hong Kong</option>
                                                            <option value="Hungria">Hungria</option>
                                                            <option value="India">Índia</option>
                                                            <option value="Indonesia">Indonésia</option>
                                                            <option value="Inglaterra">Inglaterra</option>
                                                            <option value="Ira">Irã</option>
                                                            <option value="Iraque">Iraque</option>
                                                            <option value="Irlanda">Irlanda</option>
                                                            <option value="Islandia">Islândia</option>
                                                            <option value="Israel">Israel</option>
                                                            <option value="Italia">Itália</option>
                                                            <option value="Jamaica">Jamaica</option>
                                                            <option value="Japao">Japão</option>
                                                            <option value="Jordania">Jordânia</option>
                                                            <option value="Kosovo">Kosovo</option>
                                                            <option value="Laos">Laos</option>
                                                            <option value="Letonia">Letonia</option>
                                                            <option value="Libia">Líbia</option>
                                                            <option value="Liechtenstein">Liechtenstein</option>
                                                            <option value="Lituania">Lituânia</option>
                                                            <option value="Luxemburgo">Luxemburgo</option>
                                                            <option value="Macedonia do Norte">Macedônia do Norte
                                                            </option>
                                                            <option value="Madagascar">Madagascar</option>
                                                            <option value="Malasia">Malásia</option>
                                                            <option value="Malawi">Malawi</option>
                                                            <option value="Maldivas">Maldivas</option>
                                                            <option value="Malta">Malta</option>
                                                            <option value="Marrocos">Marrocos</option>
                                                            <option value="Mexico">México</option>
                                                            <option value="Mocambique">Moçambique</option>
                                                            <option value="Moldavia">Moldávia</option>
                                                            <option value="Mongolia">Mongólia</option>
                                                            <option value="Montenegro">Montenegro</option>
                                                            <option value="Nepal">Nepal</option>
                                                            <option value="Nicaragua">Nicarágua</option>
                                                            <option value="Niger">Niger</option>
                                                            <option value="Nigeria">Nigéria</option>
                                                            <option value="Noruega">Noruega</option>
                                                            <option value="Nova Zelandia">Nova Zelândia</option>
                                                            <option value="Oma">Omã</option>
                                                            <option value="Pais de Gales">País de Gales</option>
                                                            <option value="Palau">Palau</option>
                                                            <option value="Paquistao">Paquistão</option>
                                                            <option value="Paraguai">Paraguai</option>
                                                            <option value="Peru">Peru</option>
                                                            <option value="Polonia">Polônia</option>
                                                            <option value="Porto Rico">Porto Rico</option>
                                                            <option value="Portugal">Portugal</option>
                                                            <option value="Qatar">Qatar</option>
                                                            <option value="Reino Unido">Reino Unido</option>
                                                            <option value="Repubica Tcheca">República Tcheca</option>
                                                            <option value="Romenia">Romênia</option>
                                                            <option value="Ruanda">Ruanda</option>
                                                            <option value="Russia">Rússia</option>
                                                            <option value="San Marino">San Marino</option>
                                                            <option value="Senegal">Senegal</option>
                                                            <option value="Servia">Sérvia</option>
                                                            <option value="Singapura">Singapura</option>
                                                            <option value="Síria">Síria</option>
                                                            <option value="Somalia">Somália</option>
                                                            <option value="Sudao">Sudão</option>
                                                            <option value="Suecia">Suécia</option>
                                                            <option value="Suica">Suíça</option>
                                                            <option value="Suriname">Suriname</option>
                                                            <option value="Tanzania">Tanzânia</option>
                                                            <option value="Togo">Togo</option>
                                                            <option value="Tonga">Tonga</option>
                                                            <option value="Trinidad e Tobago">Trinidad & Tobago</option>
                                                            <option value="Tunisia">Tunísia</option>
                                                            <option value="Turquia">Turquia</option>
                                                            <option value="Tuvalu">Tuvalu</option>
                                                            <option value="Ucrania">Ucrânia</option>
                                                            <option value="Uganda">Uganda</option>
                                                            <option value="Uruguai">Uruguai</option>
                                                            <option value="Uzbequistao">Uzbequistão</option>
                                                            <option value="Venezuela">Venezuela</option>
                                                            <option value="Vietna">Vietnã</option>
                                                            <option value="Zambia">Zâmbia</option>
                                                            <option value="Zimbabue">Zimbábue</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Cidade</label>
                                                        <input type="text" name="cidade_origem" class="form-control"
                                                            placeholder="Digite aqui a cidade de origem" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="callout callout-2">
                                                    <h5><i class="fas fa-map-marked-alt"></i> Destino</h5>

                                                    <div class="form-group">
                                                        <label>Pa&iacute;s</label>
                                                        <br clear="all">
                                                        <select id="frm_pais" name="pais_destino" required
                                                            class="form-control" style="width: 100%;">
                                                            <option value="" selected>Selecione um país</option>
                                                            <option value="Afeganistao">Afeganistão</option>
                                                            <option value="Africa do Sul">África do Sul</option>
                                                            <option value="Albania">Albânia</option>
                                                            <option value="Alemanha">Alemanha</option>
                                                            <option value="Andorra">Andorra</option>
                                                            <option value="Angola">Angola</option>
                                                            <option value="Arabia Saudita">Arábia Saudita</option>
                                                            <option value="Argelia">Argélia</option>
                                                            <option value="Argentina">Argentina</option>
                                                            <option value="Armenia">Armênia</option>
                                                            <option value="Australia">Austrália</option>
                                                            <option value="Austria">Áustria</option>
                                                            <option value="Azerbaijao">Azerbaijão</option>
                                                            <option value="Bahamas">Bahamas</option>
                                                            <option value="Bangladesh">Bangladesh</option>
                                                            <option value="Belgica">Bélgica</option>
                                                            <option value="Bielorrusia">Bielorrusia</option>
                                                            <option value="Bolivia">Bolívia</option>
                                                            <option value="Bosnia e Herzegovina">Bósnia e Herzegovina
                                                            </option>
                                                            <option value="Botsuana">Botsuana</option>
                                                            <option value="Brasil">Brasil</option>
                                                            <option value="Brunei">Brunei</option>
                                                            <option value="Bulgaria">Bulgária</option>
                                                            <option value="Burkina Fasso">Burkina Fasso</option>
                                                            <option value="Butao">Butão</option>
                                                            <option value="Camaroes">Camarões</option>
                                                            <option value="Camboja">Camboja</option>
                                                            <option value="Canada">Canadá</option>
                                                            <option value="Cazaquistao">Cazaquistão</option>
                                                            <option value="Chile">Chile</option>
                                                            <option value="China">China</option>
                                                            <option value="Chipre">Chipre</option>
                                                            <option value="Colombia">Colômbia</option>
                                                            <option value="Coreia do Norte">Coréia do Norte</option>
                                                            <option value="Coreia do Sul">Coréia do Sul</option>
                                                            <option value="Costa do Marfim">Costa do Marfim</option>
                                                            <option value="Costa Rica">Costa Rica</option>
                                                            <option value="Croacia">Croácia</option>
                                                            <option value="Cuba">Cuba</option>
                                                            <option value="Dinamarca">Dinamarca</option>
                                                            <option value="Egito">Egito</option>
                                                            <option value="El Salvador">El Salvador</option>
                                                            <option value="Emirados Arabes Unidos">Emirados Árabes
                                                                Unidos</option>
                                                            <option value="Equador">Equador</option>
                                                            <option value="Escocia">Escócia</option>
                                                            <option value="Eslovaquia">Eslováquia</option>
                                                            <option value="Eslovenia">Eslovênia</option>
                                                            <option value="Espanha">Espanha</option>
                                                            <option value="Estados Unidos">Estados Unidos</option>
                                                            <option value="Estonia">Estônia</option>
                                                            <option value="Etiopia">Etiópia</option>
                                                            <option value="Finlandia">Finlândia</option>
                                                            <option value="Franca">França</option>
                                                            <option value="Gabao">Gabão</option>
                                                            <option value="Gambia">Gâmbia</option>
                                                            <option value="Gana">Gana</option>
                                                            <option value="Georgia">Geórgia</option>
                                                            <option value="Gibraltar">Gibraltar</option>
                                                            <option value="Granada">Granada</option>
                                                            <option value="Grecia">Grécia</option>
                                                            <option value="Groelandia">Groelândia</option>
                                                            <option value="Guatemala">Guatemala</option>
                                                            <option value="Guine Equatorial">Guiné Equatorial</option>
                                                            <option value="Guine-Bissau">Guiné-bissau</option>
                                                            <option value="Guine">Guiné</option>
                                                            <option value="Haiti">Haiti</option>
                                                            <option value="Holanda">Holanda</option>
                                                            <option value="Honduras">Honduras</option>
                                                            <option value="Hong Kong">Hong Kong</option>
                                                            <option value="Hungria">Hungria</option>
                                                            <option value="India">Índia</option>
                                                            <option value="Indonesia">Indonésia</option>
                                                            <option value="Inglaterra">Inglaterra</option>
                                                            <option value="Ira">Irã</option>
                                                            <option value="Iraque">Iraque</option>
                                                            <option value="Irlanda">Irlanda</option>
                                                            <option value="Islandia">Islândia</option>
                                                            <option value="Israel">Israel</option>
                                                            <option value="Italia">Itália</option>
                                                            <option value="Jamaica">Jamaica</option>
                                                            <option value="Japao">Japão</option>
                                                            <option value="Jordania">Jordânia</option>
                                                            <option value="Kosovo">Kosovo</option>
                                                            <option value="Laos">Laos</option>
                                                            <option value="Letonia">Letonia</option>
                                                            <option value="Libia">Líbia</option>
                                                            <option value="Liechtenstein">Liechtenstein</option>
                                                            <option value="Lituania">Lituânia</option>
                                                            <option value="Luxemburgo">Luxemburgo</option>
                                                            <option value="Macedonia do Norte">Macedônia do Norte
                                                            </option>
                                                            <option value="Madagascar">Madagascar</option>
                                                            <option value="Malasia">Malásia</option>
                                                            <option value="Malawi">Malawi</option>
                                                            <option value="Maldivas">Maldivas</option>
                                                            <option value="Malta">Malta</option>
                                                            <option value="Marrocos">Marrocos</option>
                                                            <option value="Mexico">México</option>
                                                            <option value="Mocambique">Moçambique</option>
                                                            <option value="Moldavia">Moldávia</option>
                                                            <option value="Mongolia">Mongólia</option>
                                                            <option value="Montenegro">Montenegro</option>
                                                            <option value="Nepal">Nepal</option>
                                                            <option value="Nicaragua">Nicarágua</option>
                                                            <option value="Niger">Niger</option>
                                                            <option value="Nigeria">Nigéria</option>
                                                            <option value="Noruega">Noruega</option>
                                                            <option value="Nova Zelandia">Nova Zelândia</option>
                                                            <option value="Oma">Omã</option>
                                                            <option value="Pais de Gales">País de Gales</option>
                                                            <option value="Palau">Palau</option>
                                                            <option value="Paquistao">Paquistão</option>
                                                            <option value="Paraguai">Paraguai</option>
                                                            <option value="Peru">Peru</option>
                                                            <option value="Polonia">Polônia</option>
                                                            <option value="Porto Rico">Porto Rico</option>
                                                            <option value="Portugal">Portugal</option>
                                                            <option value="Qatar">Qatar</option>
                                                            <option value="Reino Unido">Reino Unido</option>
                                                            <option value="Repubica Tcheca">República Tcheca</option>
                                                            <option value="Romenia">Romênia</option>
                                                            <option value="Ruanda">Ruanda</option>
                                                            <option value="Russia">Rússia</option>
                                                            <option value="San Marino">San Marino</option>
                                                            <option value="Senegal">Senegal</option>
                                                            <option value="Servia">Sérvia</option>
                                                            <option value="Singapura">Singapura</option>
                                                            <option value="Síria">Síria</option>
                                                            <option value="Somalia">Somália</option>
                                                            <option value="Sudao">Sudão</option>
                                                            <option value="Suecia">Suécia</option>
                                                            <option value="Suica">Suíça</option>
                                                            <option value="Suriname">Suriname</option>
                                                            <option value="Tanzania">Tanzânia</option>
                                                            <option value="Togo">Togo</option>
                                                            <option value="Tonga">Tonga</option>
                                                            <option value="Trinidad e Tobago">Trinidad & Tobago</option>
                                                            <option value="Tunisia">Tunísia</option>
                                                            <option value="Turquia">Turquia</option>
                                                            <option value="Tuvalu">Tuvalu</option>
                                                            <option value="Ucrania">Ucrânia</option>
                                                            <option value="Uganda">Uganda</option>
                                                            <option value="Uruguai">Uruguai</option>
                                                            <option value="Uzbequistao">Uzbequistão</option>
                                                            <option value="Venezuela">Venezuela</option>
                                                            <option value="Vietna">Vietnã</option>
                                                            <option value="Zambia">Zâmbia</option>
                                                            <option value="Zimbabue">Zimbábue</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Cidade</label>
                                                        <input type="text" name="cidade_destino" class="form-control"
                                                            placeholder="Digite aqui a cidade de destino" required>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="row">

                                            <div class="col-md-6">
                                                <div class="callout callout-3">
                                                    <h5><i class="fas fa-clock"></i> Transit Time</h5>

                                                    <div class="form-group">
                                                        <label>Dias</label>
                                                        <br clear="all">
                                                        <input type="number" class="form-control" value="0"
                                                            name="t_time">
                                                    </div>
                                                </div>
                                            </div>

                                            <?php if ($dadosCC[0]['tipo_carga'] == 'container') : ?>
                                            <div class="col-md-6">
                                                <div class="callout callout-4">
                                                    <h5><i class="far fa-clock"></i> Free Time</h5>

                                                    <div class="form-group">
                                                        <label>Dias</label>
                                                        <br clear="all">
                                                        <input type="number" class="form-control" value="0"
                                                            name="f_time">
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endif;?>

                                        </div>

                                        <div class="row">

                                            <div class="col-md-12">
                                                <div class="callout callout-info">
                                                    <h5><i class="fas fa-warehouse"></i> Armador ou Cia. Aérea</h5>

                                                    <div class="form-group">
                                                        <label>Empresa</label>
                                                        <input type="text" name="empresa_transporte"
                                                            class="form-control"
                                                            placeholder="Digite aqui o nome da empresa">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                    <!-- /.tab-pane -->
                                    <div class="tab-pane" id="frete_tx">

                                        <div class="row">

                                            <div class="col-md-4">
                                                <div class="callout callout-1">
                                                    <h5><i class="far fa-money-bill-alt"></i> Frete Internacional</h5>
                                                    <div class="d-flex gap-2">
                                                        <div class="input-group flex-grow-1">
                                                            <input type="text" class="form-control" name="valor_frete"
                                                                id="valorFrete" required>
                                                        </div>
                                                        <select class="form-control w-auto" name="moeda_frete">
                                                            <option value="USD ($)" selected>USD ($)</option>
                                                            <option value="EUR (€)">EUR (€)</option>
                                                            <option value="GBP (£)">GBP (£)</option>
                                                            <option value="BRL (R$)">BRL (R$)</option>
                                                            <option value="JPY (J¥)">JPY (J¥)</option>
                                                            <option value="CAD (C$)">CAD (C$)</option>
                                                            <option value="AUD (A$)">AUD (A$)</option>
                                                            <option value="ARS (AR$)">ARS (AR$)</option>
                                                            <option value="CHF (Fr)">CHF (Fr)</option>
                                                            <option value="RMB (¥)">RMB (¥)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-md-4">
                                                <div class="callout callout-2">
                                                    <h5><i class="far fa-money-bill-alt"></i> Taxa na origem</h5>
                                                    <div class="d-flex gap-2">
                                                        <div class="input-group flex-grow-1">
                                                            <input type="text" class="form-control"
                                                                name="valor_taxa_origem" id="valorTO" required>
                                                        </div>
                                                        <select class="form-control w-auto" name="moeda_taxa_origem">
                                                            <option value="USD ($)" selected>USD ($)</option>
                                                            <option value="EUR (€)">EUR (€)</option>
                                                            <option value="GBP (£)">GBP (£)</option>
                                                            <option value="BRL (R$)">BRL (R$)</option>
                                                            <option value="JPY (J¥)">JPY (J¥)</option>
                                                            <option value="CAD (C$)">CAD (C$)</option>
                                                            <option value="AUD (A$)">AUD (A$)</option>
                                                            <option value="ARS (AR$)">ARS (AR$)</option>
                                                            <option value="CHF (Fr)">CHF (Fr)</option>
                                                            <option value="RMB (¥)">RMB (¥)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="callout callout-3">
                                                    <h5><i class="far fa-money-bill-alt"></i> Taxa no destino</h5>
                                                    <div class="d-flex gap-2">
                                                        <div class="input-group flex-grow-1">
                                                            <input type="text" class="form-control"
                                                                name="valor_taxa_destino" id="valorTD" required>
                                                        </div>
                                                        <select class="form-control w-auto" name="moeda_taxa_destino">
                                                            <option value="USD ($)" selected>USD ($)</option>
                                                            <option value="EUR (€)">EUR (€)</option>
                                                            <option value="GBP (£)">GBP (£)</option>
                                                            <option value="BRL (R$)">BRL (R$)</option>
                                                            <option value="JPY (J¥)">JPY (J¥)</option>
                                                            <option value="CAD (C$)">CAD (C$)</option>
                                                            <option value="AUD (A$)">AUD (A$)</option>
                                                            <option value="ARS (AR$)">ARS (AR$)</option>
                                                            <option value="CHF (Fr)">CHF (Fr)</option>
                                                            <option value="RMB (¥)">RMB (¥)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="row">

                                            <div class="col-md-4">
                                                <div class="callout callout-1">
                                                    <h5><i class="far fa-money-bill-alt"></i> Outras taxas</h5>
                                                    <div class="d-flex gap-2">
                                                        <div class="input-group flex-grow-1">
                                                            <input type="text" class="form-control"
                                                                name="valor_outras_taxas" id="valorOT" required>
                                                        </div>
                                                        <select class="form-control w-auto" name="moeda_outras_taxas">
                                                            <option value="USD ($)" selected>USD ($)</option>
                                                            <option value="EUR (€)">EUR (€)</option>
                                                            <option value="GBP (£)">GBP (£)</option>
                                                            <option value="BRL (R$)">BRL (R$)</option>
                                                            <option value="JPY (J¥)">JPY (J¥)</option>
                                                            <option value="CAD (C$)">CAD (C$)</option>
                                                            <option value="AUD (A$)">AUD (A$)</option>
                                                            <option value="ARS (AR$)">ARS (AR$)</option>
                                                            <option value="CHF (Fr)">CHF (Fr)</option>
                                                            <option value="RMB (¥)">RMB (¥)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="callout callout-2">
                                                    <h5><i class="fas fa-shield-alt"></i> Valor Seguro</h5>
                                                    <div class="d-flex gap-2">
                                                        <div class="input-group flex-grow-1">
                                                            <input type="text" class="form-control" name="valor_seguro"
                                                                id="valorSeguro" required>
                                                        </div>
                                                        <select class="form-control w-auto" name="moeda_seguro">
                                                            <option value="USD ($)" selected>USD ($)</option>
                                                            <option value="EUR (€)">EUR (€)</option>
                                                            <option value="GBP (£)">GBP (£)</option>
                                                            <option value="BRL (R$)">BRL (R$)</option>
                                                            <option value="JPY (J¥)">JPY (J¥)</option>
                                                            <option value="CAD (C$)">CAD (C$)</option>
                                                            <option value="AUD (A$)">AUD (A$)</option>
                                                            <option value="ARS (AR$)">ARS (AR$)</option>
                                                            <option value="CHF (Fr)">CHF (Fr)</option>
                                                            <option value="RMB (¥)">RMB (¥)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <hr>

                                        <div class="row">

                                            <div class="col-md-8">
                                                <div class="callout callout-1" style="min-height: 102px;">
                                                    <h5><i class="fas fa-truck"></i> Transporte Nacional</h5>
                                                    <?php if ($dados['contratacao_transporte'] == 'sim') : ?>
                                                    <span class="badge badge-info"><i class="fas fa-map-marker-alt"></i>
                                                        Para:</span>
                                                    <?= $dados['endereco_entrega']; ?>
                                                    <?php else : ?>
                                                    <span class="badge badge-danger">Não</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="callout callout-3">
                                                    <h5><i class="far fa-money-bill-alt"></i> Frete Nacional</h5>
                                                    <div class="d-flex gap-2">
                                                        <div class="input-group flex-grow-1">
                                                            <input type="text" class="form-control"
                                                                name="valor_frete_nacional" id="valorFN" required>
                                                        </div>
                                                        <select class="form-control w-auto" name="moeda_frete_nacional">
                                                            <option value="USD ($)" selected>USD ($)</option>
                                                            <option value="EUR (€)">EUR (€)</option>
                                                            <option value="GBP (£)">GBP (£)</option>
                                                            <option value="BRL (R$)">BRL (R$)</option>
                                                            <option value="JPY (J¥)">JPY (J¥)</option>
                                                            <option value="CAD (C$)">CAD (C$)</option>
                                                            <option value="AUD (A$)">AUD (A$)</option>
                                                            <option value="ARS (AR$)">ARS (AR$)</option>
                                                            <option value="CHF (Fr)">CHF (Fr)</option>
                                                            <option value="RMB (¥)">RMB (¥)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                    <div class="tab-pane" id="condicoes">

                                        <div class="row">

                                            <div class="col-md-6">
                                                <div class="callout callout-1">
                                                    <h5><i class="far fa-calendar-check"></i> Prazo de pagamento</h5>
                                                    <div class="form-group">
                                                        <select name="prazo_pgto" class="form-control">
                                                            <option
                                                                value="O pagamento deverá ser efetuado no ato da chegada da carga ao aeroporto/ou Porto">
                                                                O pagamento deverá ser efetuado no ato da chegada da
                                                                carga ao
                                                                aeroporto/ou Porto</option>
                                                            <option
                                                                value="O pagamento deverá ser efetuado no ato da entrega da carga ao Endereço de Destino;">
                                                                O pagamento deverá ser efetuado no ato da entrega da
                                                                carga ao
                                                                Endereço de Destino;</option>
                                                            <option
                                                                value="O pagamento poderá ser efetuado no prazo de 5 dias após a emissão (e envio) da fatura;">
                                                                O pagamento poderá ser efetuado no prazo de 5 dias após
                                                                a emissão (e
                                                                envio) da fatura;</option>
                                                            <option
                                                                value="O pagamento poderá ser efetuado no prazo de 10 dias após a emissão (e envio) da fatura;">
                                                                O pagamento poderá ser efetuado no prazo de 10 dias após
                                                                a emissão
                                                                (e envio) da fatura;</option>
                                                            <option
                                                                value="O pagamento poderá ser efetuado no prazo de 15 dias após a emissão (e envio) da fatura;">
                                                                O pagamento poderá ser efetuado no prazo de 15 dias após
                                                                a emissão
                                                                (e envio) da fatura; </option>
                                                            <option
                                                                value="O pagamento poderá ser efetuado no prazo de 30 dias após a emissão (e envio) da fatura;">
                                                                O pagamento poderá ser efetuado no prazo de 30 dias após
                                                                a emissão
                                                                (e envio) da fatura;</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="callout callout-2">
                                                    <h5><i class="fas fa-percent"></i> Spread</h5>
                                                    <div class="form-group">
                                                        <select name="spread" class="form-control">
                                                            <option value="Ptax + 1%">Ptax + 1%</option>
                                                            <option value="Ptax + 2%">Ptax + 2%</option>
                                                            <option value="Ptax + 3%" selected>Ptax + 3%</option>
                                                            <option value="Ptax + 4%">Ptax + 4%</option>
                                                            <option value="Ptax + 5%">Ptax + 5%</option>
                                                            <option value="Ptax + 6%">Ptax + 6%</option>
                                                            <option value="Ptax + 7%">Ptax + 7%</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="callout callout-3">
                                                    <h5><i class="fas fa-calendar-alt"></i> Validade da proposta</h5>
                                                    <div class="form-group">
                                                        <div class="input-group date">
                                                            <input type="date" name="validade_proposta"
                                                                class="form-control pull-right" required>
                                                        </div>
                                                        <!-- /.input group -->
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                    <div class="tab-pane" id="complementos">

                                        <div class="row">

                                            <div class="col-md-4">
                                                <div class="callout callout-1" style="min-height: 143px;">
                                                    <h5>
                                                        <i class="fas fa-upload"></i>
                                                        Detalhamento da proposta<br>
                                                        <small>Upload de arquivo (PDF)</small>
                                                    </h5>
                                                    <div class="form-group">
                                                        <input type="file" id="exampleInputFile" name="arquivo"
                                                            required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-8">
                                                <div class="callout callout-2">
                                                    <h5><i class="fas fa-comment-dots"></i> Observações</h5>
                                                    <div class="form-group">
                                                        <textarea name="obs" class="form-control" id="obs"
                                                            placeholder="Digite aqui alguma observação a fazer..."></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                    <div class="tab-pane" id="all_in">

                                        <div class="row">

                                            <div class="col-md-4">
                                                <div class="callout callout-1 bg-destaque-2" style="min-height: 150px;">
                                                    <h5><i class="far fa-money-bill-alt"></i> All In</h5>
                                                    <div class="d-flex gap-2">
                                                        <div class="input-group flex-grow-1">
                                                            <input type="text" class="form-control" name="valor_all_in"
                                                                id="valorAllIn" required>
                                                        </div>
                                                        <select class="form-control w-auto" name="moeda_all_in">
                                                            <option value="USD ($)" selected>USD ($)</option>
                                                            <option value="EUR (€)">EUR (€)</option>
                                                            <option value="GBP (£)">GBP (£)</option>
                                                            <option value="BRL (R$)">BRL (R$)</option>
                                                            <option value="JPY (J¥)">JPY (J¥)</option>
                                                            <option value="CAD (C$)">CAD (C$)</option>
                                                            <option value="AUD (A$)">AUD (A$)</option>
                                                            <option value="ARS (AR$)">ARS (AR$)</option>
                                                            <option value="CHF (Fr)">CHF (Fr)</option>
                                                            <option value="RMB (¥)">RMB (¥)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-md-4">
                                                <div class="callout callout-3">
                                                    <h5><i class="fas fa-shield-alt"></i> Seguro</h5>
                                                    <div class="form-group">
                                                        Você contemplou os valores de <strong>seguro</strong> ao all
                                                        in?<br>
                                                        <input type="radio" name="inclui_seguro_allin" value="1"> Sim
                                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                                        <input type="radio" name="inclui_seguro_allin" value="0"> Não
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="callout callout-4">
                                                    <h5><i class="fas fa-truck"></i> Frete Nacional</h5>
                                                    <div class="form-group">
                                                        Você contemplou os valores de <strong>Frete Nacional</strong> ao
                                                        valor do All in?<br>
                                                        <input type="radio" name="inclui_frete_allin" value="1"> Sim
                                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                                        <input type="radio" name="inclui_frete_allin" value="0"> Não
                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                        <!-- /.tab-pane -->
                                    </div>
                                    <!-- /.tab-content -->
                                </div><!-- /.card-body -->
                            </div>
                            <!-- ./card -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                    <!-- END CUSTOM TABS -->

                </div>

            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
</section>
<!-- /.content -->


<script>
document.addEventListener("DOMContentLoaded", function() {
    const radioButtons = document.querySelectorAll(".empresa-radio");
    const empresaItems = document.querySelectorAll(".empresa-item");

    radioButtons.forEach(radio => {
        radio.addEventListener("change", function() {
            empresaItems.forEach(item => item.classList.remove("bg-info",
                "text-white")); // Remove destaque
            this.closest(".empresa-item").classList.add("bg-info",
                "text-white"); // Adiciona destaque
        });
    });
});

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