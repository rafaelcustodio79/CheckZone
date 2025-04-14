<?php
$PDO = db_connect();

$editar_1 = isset($_SESSION['editar_1']) ? $_SESSION['editar_1'] : null;
$editar_2 = isset($_SESSION['editar_2']) ? $_SESSION['editar_2'] : null;
$editar_3 = isset($_SESSION['editar_3']) ? $_SESSION['editar_3'] : null;
$editar_4 = isset($_SESSION['editar_4']) ? $_SESSION['editar_4'] : null;
$editar_5 = isset($_SESSION['editar_5']) ? $_SESSION['editar_5'] : null;

# Verifica se já existe COTAÇÃO
$idCotacao = isset($_SESSION['idCotacao']) ? $_SESSION['idCotacao'] : null;

$liCarga = isset($_GET['liCarga']) ? $_GET['liCarga'] : '';

$PDOVerificaCot = db_connect();
//$sql = "SELECT * FROM usuario u INNER JOIN empresa emp ON u.id_empresa = emp.id_empresa WHERE u.id = :id";
$sqlCA = "SELECT * FROM cotacao_abertura WHERE id = :idCotacao";
$stmtCA = $PDOVerificaCot->prepare($sqlCA);
$stmtCA->bindParam(':idCotacao', $idCotacao, PDO::PARAM_STR);
$stmtCA->execute();
$dadosCA = [];
$dadosCA = $stmtCA->fetchAll(PDO::FETCH_ASSOC);

$sqlCO = "SELECT * FROM cotacao_origem WHERE id_cotacao = :idCotacao";
$stmtCO = $PDOVerificaCot->prepare($sqlCO);
$stmtCO->bindParam(':idCotacao', $idCotacao, PDO::PARAM_STR);
$stmtCO->execute();
$dadosCO = [];
$dadosCO = $stmtCO->fetchAll(PDO::FETCH_ASSOC);

$sqlCD = "SELECT * FROM cotacao_destino WHERE id_cotacao = :idCotacao";
$stmtCD = $PDOVerificaCot->prepare($sqlCD);
$stmtCD->bindParam(':idCotacao', $idCotacao, PDO::PARAM_STR);
$stmtCD->execute();
$dadosCD = [];
$dadosCD = $stmtCD->fetchAll(PDO::FETCH_ASSOC);

$sqlCC = "SELECT * FROM cotacao_carga WHERE id_cotacao = :idCotacao";
$stmtCC = $PDOVerificaCot->prepare($sqlCC);
$stmtCC->bindParam(':idCotacao', $idCotacao, PDO::PARAM_STR);
$stmtCC->execute();
$dadosCC = [];
$dadosCC = $stmtCC->fetchAll(PDO::FETCH_ASSOC);

$sqlCM = "SELECT * FROM cotacao_mercadoria WHERE id_cotacao = :idCotacao";
$stmtCM = $PDOVerificaCot->prepare($sqlCM);
$stmtCM->bindParam(':idCotacao', $idCotacao, PDO::PARAM_STR);
$stmtCM->execute();
$dadosCM = [];
$dadosCM = $stmtCM->fetchAll(PDO::FETCH_ASSOC);

if (isset($dadosCC[0]['tipo_carga'])) {
    switch ($dadosCC[0]['tipo_carga']) {
        case 'solta':
            $liCarga = 1;
            break;
        case 'container':
            $liCarga = 2;
            break;
        case 'solução agente':
            $liCarga = 3;
            break;
        default:
            $liCarga = '';
            break;
    }
} else {
    $liCarga = '';
}
$etapa = isset($_SESSION['etapa']) ? $_SESSION['etapa'] : null;

$iconeAbertura =  '';
$iconeOrigem =  '';
$iconeDestino = '';
$iconeCarga = '';
$iconeMercadoria = '';
$iconeDC = '';

switch ($etapa) {
    case 1:
        $etapa1 = 'active';
        $etapashow1 = 'show active';
        break;
    case 2:
        $etapa2 = 'active';
        $etapashow2 = 'show active';
        $iconeAbertura =  '<i class="fas fa-check text-success"></i>';
        $iconeOrigem = '<i class="fas fa-spinner text-muted"></i>';	
        break;
    case 3:
        $etapa3 = 'active';
        $etapashow3 = 'show active';
        $iconeAbertura =  '<i class="fas fa-check text-success"></i>';
        $iconeOrigem =  '<i class="fas fa-check text-success"></i>';
        $iconeDestino = '<i class="fas fa-spinner text-muted"></i>';	
        break;
    case 4:
        $etapa4 = 'active';
        $etapashow4 = 'show active';
        $iconeAbertura =  '<i class="fas fa-check text-success"></i>';
        $iconeOrigem =  '<i class="fas fa-check text-success"></i>';
        $iconeDestino = '<i class="fas fa-check text-success"></i>';
        $iconeCarga =  '<i class="fas fa-spinner text-muted"></i>';
        break;
    case 5:
        $etapa5 = 'active';
        $etapashow5 = 'show active';
        $iconeAbertura =  '<i class="fas fa-check text-success"></i>';
        $iconeOrigem =  '<i class="fas fa-check text-success"></i>';
        $iconeDestino = '<i class="fas fa-check text-success"></i>';
        $iconeCarga =  '<i class="fas fa-check text-success"></i>';
        $iconeMercadoria =  '<i class="fas fa-spinner text-muted"></i>';
        break;
    case 6:
        $etapa6 = 'active';
        $etapashow6 = 'show active';
        $iconeAbertura =  '<i class="fas fa-check text-success"></i>';
        $iconeOrigem =  '<i class="fas fa-check text-success"></i>';
        $iconeDestino = '<i class="fas fa-check text-success"></i>';
        $iconeCarga =  '<i class="fas fa-check text-success"></i>';
        $iconeMercadoria =  '<i class="fas fa-check text-success"></i>';
        $iconeDC =  '<i class="fas fa-spinner text-muted"></i>';
        break;
    default:
        $etapa1 = 'active';
        $etapashow1 = 'show active';
        $iconeAbertura =  '<i class="fas fa-spinner text-muted"></i>';
        $iconeOrigem =  '';
        $iconeDestino = '';
        $iconeCarga = '';
        $iconeMercadoria = '';
        $iconeDC = '';
        break;
}
?>
<style>
.img-flag-pais {
    width: 25px
}

.select2-container--default .select2-selection--single {
    background-color: #fff;
    border: 1px solid #aaa;
    border-radius: 4px;
    line-height: 40px;
    height: 35px;
}

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
</style>

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Frete Internacional</h1>
                <small>Cotação</small>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item">Frete Internacional</li>
                    <li class="breadcrumb-item active">Cotação</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">

    <div class="container-fluid">

        <div class="col-12 col-sm-12">

            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-luggage-cart"></i>
                        Realizando minha cotação
                    </h3>
                </div>
                <div class="card-body">

                    <div class="row">
                        <div class="col-5 col-sm-3">
                            <div class="nav flex-column nav-tabs h-100" id="vert-tabs-tab" role="tablist"
                                aria-orientation="vertical">
                                <a class="nav-link <?= $etapa1;?>" id="vert-tabs-Abertura-tab" data-toggle="pill"
                                    href="#vert-tabs-Abertura" role="tab" aria-controls="vert-tabs-Abertura"
                                    aria-selected="true">
                                    <strong>Abertura</strong> <?=$iconeAbertura;?><br>
                                    <?php if (!empty($dadosCA)) : ?>
                                    <?php if ($dadosCA[0]['modal'] == 'aer') {
                                        $txtModal =  'Aéreo';
                                        $icone = "<i class='fas fa-plane'></i>";
                                    } elseif ($dadosCA[0]['modal'] == 'mar') {
                                        $txtModal = 'Marítimo';
                                        $icone = "<i class='fas fa-ship'></i>";
                                    } else {
                                        $txtModal = 'Rodoviário';
                                        $icone = "<i class='fas fa-truck'></i>";
                                    } ?>
                                    <?= $icone; ?>
                                    <?= $txtModal; ?> |
                                    <?= $dadosCA[0]['incoterms']; ?>
                                    <?php endif; ?>
                                </a>
                                <a class="nav-link <?= $etapa2;?>" id="vert-tabs-Origem-tab" data-toggle="pill"
                                    href="#vert-tabs-Origem" role="tab" aria-controls="vert-tabs-Origem"
                                    aria-selected="false">
                                    <strong>Origem</strong> <?=$iconeOrigem;?><br>
                                    <?php if (!empty($dadosCO)) : ?>
                                    <img src="img/flags/<?= $dadosCO[0]['pais_origem']; ?>.png" style="max-width:20px;">
                                    <?= $dadosCO[0]['pais_origem']; ?>
                                    |
                                    <?= $dadosCO[0]['cidade_origem']; ?>
                                    <?php endif; ?>
                                </a>
                                <a class="nav-link <?= $etapa3;?>" id="vert-tabs-Destino-tab" data-toggle="pill"
                                    href="#vert-tabs-Destino" role="tab" aria-controls="vert-tabs-Destino"
                                    aria-selected="false">
                                    <strong>Destino</strong> <?=$iconeDestino;?><br>
                                    <?php if (!empty($dadosCD)) : ?>
                                    <img src="img/flags/<?= $dadosCD[0]['pais_destino']; ?>.png"
                                        style="max-width:20px;">
                                    <?= $dadosCD[0]['pais_destino']; ?>
                                    |
                                    <?= $dadosCD[0]['cidade_destino']; ?>
                                    <?php endif; ?>
                                </a>
                                <a class="nav-link <?= $etapa4;?>" id="vert-tabs-Carga-tab" data-toggle="pill"
                                    href="#vert-tabs-Carga" role="tab" aria-controls="vert-tabs-Carga"
                                    aria-selected="false">
                                    <strong>Carga</strong> <?=$iconeCarga;?><br>
                                    <?php if (!empty($dadosCC)) : ?>
                                    <?php if ($dadosCC[0]['tipo_carga'] === 'solta') : ?>
                                    <i class="fas fa-boxes"></i> Carga
                                    Compartilhada (LCL)
                                    <?php endif; ?>
                                    <?php if ($dadosCC[0]['tipo_carga'] === 'container') : ?>
                                    <img src="img/cont2.png" style="max-width: 35px;"> Container (FCL)
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </a>
                                <a class="nav-link <?= $etapa5;?>" id="vert-tabs-Mercadoria-tab" data-toggle="pill"
                                    href="#vert-tabs-Mercadoria" role="tab" aria-controls="vert-tabs-Mercadoria"
                                    aria-selected="false">
                                    <strong>Mercadoria</strong> <?=$iconeMercadoria;?><br>
                                    <?php if (!empty($dadosCM)) : ?>
                                    <?= mb_strimwidth($dadosCM[0]['produto'], 0, 30, "..."); ?>
                                    <?php endif; ?>
                                </a>
                                <a class="nav-link <?= $etapa6;?>" id="vert-tabs-info-comp-tab" data-toggle="pill"
                                    href="#vert-tabs-info-comp" role="tab" aria-controls="vert-tabs-info-comp"
                                    aria-selected="false">
                                    <strong>Dados Complementares</strong> <?=$iconeDC;?>
                                </a>
                            </div>
                        </div>

                        <div class="col-7 col-sm-9">
                            <div class="tab-content" id="vert-tabs-tabContent">
                                <div class="tab-pane text-left fade <?= $etapashow1;?>" id="vert-tabs-Abertura"
                                    role="tabpanel" aria-labelledby="vert-tabs-Abertura-tab">

                                    <?php if (empty($dadosCA)) : ?>
                                    <form name="abertura" action="frete/grava_cotacao_abertura.php" method="post">
                                        <input type="hidden" name="tipo_cotacao" value=0>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <h5 class="texto-destaque-4">Vamos iniciar o pedido!</h5>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>
                                                        Nº Processo Interno
                                                        <sup><i class="fa fa-fw fa-question-circle" data-toggle="modal"
                                                                data-target="#modal-default-PI"
                                                                title="Explicação para codificar pedido"
                                                                style="cursor:pointer"></i></sup>
                                                    </label>
                                                    <input type="text" name="proc_interno" class="form-control"
                                                        placeholder="Digite aqui seu número de processo" maxlength="50">
                                                </div>
                                            </div>

                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Modal</label>
                                                    <select id="frm_brand" name="modal" required class="form-control"
                                                        onchange="mostrarOcultarSelect()">
                                                        <option value="" selected>Selecione um Modal</option>
                                                        <option value="aer">Aéreo</option>
                                                        <option value="mar">Marítimo</option>
                                                        <option value="rod">Rodoviário</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>
                                                        Incoterms <sup><i class="fa fa-fw fa-question-circle"
                                                                data-toggle="modal" data-target="#modal-default"
                                                                title="Explicação dos tipos de Incoterms"
                                                                style="cursor:pointer"></i></sup>
                                                    </label>
                                                    <br clear="all">
                                                    <select id='aer' name="incoterms-aer" required class="form-control"
                                                        style="display: none;">
                                                        <option value="FCA">FCA</option>
                                                        <option value="EXW">EXW</option>
                                                    </select>

                                                    <select id='mar' name="incoterms-mar" required class="form-control"
                                                        style="display: none;">
                                                        <option value="FAS">FAS</option>
                                                        <option value="FOB">FOB</option>
                                                        <option value="FCA">FCA</option>
                                                        <option value="EXW">EXW</option>
                                                    </select>

                                                    <select id='rod' name="incoterms-rod" required class="form-control"
                                                        style="display: none;">
                                                        <option value="FCA">FCA</option>
                                                        <option value="EXW">EXW</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-footer text-right">
                                            <button type="submit" class="btn btn-info"
                                                title="Salvar e ir para próxima etapa!">Próxima
                                                etapa <i class="far fa-hand-point-right"></i></button>
                                        </div>
                                    </form>
                                    <?php else : ?>

                                    <form name="abertura" action="frete/edita_cotacao_abertura.php" method="post">
                                        <input type="hidden" name="idCotacao" value=<?= $idCotacao; ?> />

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <h5 class="texto-destaque-4">Edite as informações que achar
                                                        necessário.</h5>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>
                                                        Nº Processo Interno
                                                        <sup><i class="fa fa-fw fa-question-circle" data-toggle="modal"
                                                                data-target="#modal-default-PI"
                                                                title="Explicação para codificar pedido"
                                                                style="cursor:pointer"></i></sup>
                                                    </label>
                                                    <input type="text" name="proc_interno" class="form-control"
                                                        value="<?= $dadosCA[0]['n_proc_interno']; ?>" maxlength="50">
                                                </div>
                                            </div>

                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Modal</label>
                                                    <select id="frm_brand" name="modal" required class="form-control"
                                                        onchange="mostrarOcultarSelect()">
                                                        <option value="" disabled>Selecione um Modal</option>
                                                        <option value="aer"
                                                            <?= ($dadosCA[0]['modal'] == 'aer') ? 'selected' : ''; ?>>
                                                            Aéreo</option>
                                                        <option value="mar"
                                                            <?= ($dadosCA[0]['modal'] == 'mar') ? 'selected' : ''; ?>>
                                                            Marítimo</option>
                                                        <option value="rod"
                                                            <?= ($dadosCA[0]['modal'] == 'rod') ? 'selected' : ''; ?>>
                                                            Rodoviário</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>
                                                        Incoterms <sup><i class="fa fa-fw fa-question-circle"
                                                                data-toggle="modal" data-target="#modal-default"
                                                                title="Explicação dos tipos de Incoterms"
                                                                style="cursor:pointer"></i></sup>
                                                    </label>
                                                    <br clear="all">
                                                    <select id='aer' name="incoterms-aer" required class="form-control"
                                                        style="display: <?= ($dadosCA[0]['modal'] == 'aer') ? 'block' : 'none'; ?>;">
                                                        <option value="FCA"
                                                            <?= ($dadosCA[0]['incoterms'] == 'FCA') ? 'selected' : ''; ?>>
                                                            FCA</option>
                                                        <option value="EXW"
                                                            <?= ($dadosCA[0]['incoterms'] == 'EXW') ? 'selected' : ''; ?>>
                                                            EXW</option>
                                                    </select>

                                                    <select id='mar' name="incoterms-mar" required class="form-control"
                                                        style="display: <?= ($dadosCA[0]['modal'] == 'mar') ? 'block' : 'none'; ?>;">
                                                        <option value="FAS"
                                                            <?= ($dadosCA[0]['incoterms'] == 'FAS') ? 'selected' : ''; ?>>
                                                            FAS</option>
                                                        <option value="FOB"
                                                            <?= ($dadosCA[0]['incoterms'] == 'FOB') ? 'selected' : ''; ?>>
                                                            FOB</option>
                                                        <option value="FCA"
                                                            <?= ($dadosCA[0]['incoterms'] == 'FCA') ? 'selected' : ''; ?>>
                                                            FCA</option>
                                                        <option value="EXW"
                                                            <?= ($dadosCA[0]['incoterms'] == 'EXW') ? 'selected' : ''; ?>>
                                                            EXW</option>
                                                    </select>

                                                    <select id='rod' name="incoterms-rod" required class="form-control"
                                                        style="display: <?= ($dadosCA[0]['modal'] == 'rod') ? 'block' : 'none'; ?>;">
                                                        <option value="FCA"
                                                            <?= ($dadosCA[0]['incoterms'] == 'FCA') ? 'selected' : ''; ?>>
                                                            FCA</option>
                                                        <option value="EXW"
                                                            <?= ($dadosCA[0]['incoterms'] == 'EXW') ? 'selected' : ''; ?>>
                                                            EXW</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-footer text-right">
                                            <button type="submit" class="btn btn-info"
                                                title="Salvar as alterações">Atualizar <i
                                                    class="fas fa-sync-alt"></i></button>
                                        </div>
                                    </form>
                                    <?php endif; ?>

                                </div>
                                <div class="tab-pane fade <?= $etapashow2;?>" id="vert-tabs-Origem" role="tabpanel"
                                    aria-labelledby="vert-tabs-Origem-tab">

                                    <?php if (empty($dadosCO)) : ?>
                                    <form name="origem" action="frete/grava_cotacao_origem.php" method="post">
                                        <input type="hidden" name="idCotacao" value="<?= $idCotacao; ?>">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <h5 class="texto-destaque-4">Por onde sua carga está saindo?
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Localização</label>
                                                    <br clear="all">Selecione um país
                                                    <select id="frm_pais" name="pais" required class="form-control"
                                                        style="width: 100%;">
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
                                                        <option value="China" selected>China</option>
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
                                                        <option value="Emirados Arabes Unidos">Emirados Árabes Unidos
                                                        </option>
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
                                                        <option value="Macedonia do Norte">Macedônia do Norte</option>
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
                                                        <option value="Republica Tcheca">República Tcheca</option>
                                                        <option value="Romenia">Romênia</option>
                                                        <option value="Ruanda">Ruanda</option>
                                                        <option value="Russia">Rússia</option>
                                                        <option value="San Marino">San Marino</option>
                                                        <option value="Senegal">Senegal</option>
                                                        <option value="Servia">Sérvia</option>
                                                        <option value="Singapura">Singapura</option>
                                                        <option value="Siria">Síria</option>
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
                                            </div>

                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Cidade</label>
                                                    <br><br>
                                                    <input type="text" name="cidade" class="form-control"
                                                        placeholder="Digite aqui a cidade de origem" required>
                                                </div>
                                            </div>

                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <?php
                                                    if (isset($dadosCA[0]['incoterms'])) {
                                                        if ($dadosCA[0]['incoterms'] == 'EXW') {
                                                            $txtRequerido = 'required';
                                                        } else {
                                                            $txtRequerido = 'disabled';
                                                        }
                                                    } else {
                                                        $txtRequerido = 'disabled';
                                                    }
                                                    ?>
                                                    <label>Endereço de coleta<br> <small>(Atenção - Informar
                                                            zipcode)</small></label>
                                                    <br clear="all">
                                                    <textarea name="endereco_coleta" class="form-control"
                                                        placeholder="INCOTERMS EXW - Digite o endereço de coleta."
                                                        <?= $txtRequerido; ?>></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-footer text-right">
                                            <button type="submit" class="btn btn-info"
                                                title="Salvar e ir para próxima etapa!">Próxima etapa <i
                                                    class="far fa-hand-point-right"></i></button>
                                        </div>
                                    </form>

                                    <?php else : ?>

                                    <form name="origem" action="frete/edita_cotacao_origem.php" method="post">
                                        <input type="hidden" name="idCotacao" value="<?= $idCotacao; ?>">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <h5 class="texto-destaque-4">Edite as informações que achar
                                                        necessário.
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Localização</label>
                                                    <br clear="all"><br>
                                                    <select id="frm_pais" name="pais" required class="form-control"
                                                        style="width: 100%;">
                                                        <option value="<?= $dadosCO[0]['pais_origem']; ?>" selected>
                                                            <?= $dadosCO[0]['pais_origem']; ?></option>
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
                                                        <option value="Emirados Arabes Unidos">Emirados Árabes Unidos
                                                        </option>
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
                                                        <option value="Macedonia do Norte">Macedônia do Norte</option>
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
                                                        <option value="Republica Tcheca">República Tcheca</option>
                                                        <option value="Romenia">Romênia</option>
                                                        <option value="Ruanda">Ruanda</option>
                                                        <option value="Russia">Rússia</option>
                                                        <option value="San Marino">San Marino</option>
                                                        <option value="Senegal">Senegal</option>
                                                        <option value="Servia">Sérvia</option>
                                                        <option value="Singapura">Singapura</option>
                                                        <option value="Siria">Síria</option>
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
                                            </div>

                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Cidade</label>
                                                    <br><br>
                                                    <input type="text" name="cidade" class="form-control"
                                                        value="<?= $dadosCO[0]['cidade_origem']; ?>" required>
                                                </div>
                                            </div>

                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <?php
                                                    if (isset($dadosCA[0]['incoterms'])) {
                                                        if ($dadosCA[0]['incoterms'] == 'EXW') {
                                                            $txtRequerido = 'required';
                                                        } else {
                                                            $txtRequerido = 'disabled';
                                                        }
                                                    } else {
                                                        $txtRequerido = 'disabled';
                                                    }
                                                    ?>
                                                    <label>Endereço de coleta<br> <small>(Atenção - Informar
                                                            zipcode)</small></label>
                                                    <br clear="all">
                                                    <textarea name="endereco_coleta" class="form-control"
                                                        <?= $txtRequerido; ?>>
                                                        <?= $dadosCO[0]['endereco_coleta']; ?>
                                                    </textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-footer text-right">
                                            <button type="submit" class="btn btn-info"
                                                title="Salvar as alterações">Atualizar <i
                                                    class="fas fa-sync-alt"></i></button>
                                        </div>
                                    </form>
                                    <?php endif; ?>

                                </div>
                                <div class="tab-pane fade <?= $etapashow3;?>" id="vert-tabs-Destino" role="tabpanel"
                                    aria-labelledby="vert-tabs-Destino-tab">

                                    <?php if (empty($dadosCD)) : ?>
                                    <form name="origem" action="frete/grava_cotacao_destino.php" method="post">
                                        <input type="hidden" name="idCotacao" value="<?= $idCotacao; ?>">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <h5 class="texto-destaque-4">Para onde sua carga será enviada?
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Localização <small>Selecione o país</small></label>
                                                    <select id="frm_pais" name="pais" required class="form-control"
                                                        style="width: 100%;">
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
                                                        <option value="Brasil" selected>Brasil</option>
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
                                                        <option value="Emirados Arabes Unidos">Emirados Árabes Unidos
                                                        </option>
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
                                                        <option value="Macedonia do Norte">Macedônia do Norte</option>
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
                                                        <option value="Republica Tcheca">República Tcheca</option>
                                                        <option value="Romenia">Romênia</option>
                                                        <option value="Ruanda">Ruanda</option>
                                                        <option value="Russia">Rússia</option>
                                                        <option value="San Marino">San Marino</option>
                                                        <option value="Senegal">Senegal</option>
                                                        <option value="Servia">Sérvia</option>
                                                        <option value="Singapura">Singapura</option>
                                                        <option value="Siria">Síria</option>
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
                                            </div>

                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Cidade</label><br>
                                                    <input type="text" name="cidade" class="form-control"
                                                        placeholder="Digite a cidade de destino" required>
                                                </div>
                                            </div>

                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <?php
                                                    if (isset($dadosCA[0]['incoterms'])) {
                                                        if ($dadosCA[0]['incoterms'] === 'DAP' || $dadosCA[0]['incoterms'] === 'DDP' || $dadosCA[0]['incoterms'] === 'DPU') {
                                                            $txtRequerido2 = 'required';
                                                        } else {
                                                            $txtRequerido2 = 'disabled';
                                                        }
                                                    } else {
                                                        $txtRequerido2 = 'disabled';
                                                    }
                                                    ?>
                                                    <label>Endereço no destino</label>
                                                    <br clear="all">
                                                    <textarea name="endereco_no_destino" class="form-control"
                                                        placeholder="INCOTERMS DAP/DDP/DPU - Digite o endereço no destino."
                                                        <?= $txtRequerido2; ?>></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-footer text-right">
                                            <button type="submit" class="btn btn-info"
                                                title="Salvar e ir para próxima etapa!">Próxima etapa <i
                                                    class="far fa-hand-point-right"></i></button>
                                        </div>
                                    </form>
                                    <?php else : ?>
                                    <form name="origem" action="frete/edita_cotacao_destino.php" method="post">
                                        <input type="hidden" name="idCotacao" value="<?= $idCotacao; ?>">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <h5 class="texto-destaque-4">Edite as informações de destino
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Localização</label>
                                                    <select id="frm_pais" name="pais" required class="form-control"
                                                        style="width: 100%;">
                                                        <option value="<?= $dadosCD[0]['pais_destino']; ?>" selected>
                                                            <?= $dadosCD[0]['pais_destino']; ?></option>
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
                                                        <option value="Emirados Arabes Unidos">Emirados Árabes Unidos
                                                        </option>
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
                                                        <option value="Macedonia do Norte">Macedônia do Norte</option>
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
                                                        <option value="Republica Tcheca">República Tcheca</option>
                                                        <option value="Romenia">Romênia</option>
                                                        <option value="Ruanda">Ruanda</option>
                                                        <option value="Russia">Rússia</option>
                                                        <option value="San Marino">San Marino</option>
                                                        <option value="Senegal">Senegal</option>
                                                        <option value="Servia">Sérvia</option>
                                                        <option value="Singapura">Singapura</option>
                                                        <option value="Siria">Síria</option>
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
                                            </div>

                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Cidade</label>
                                                    <input type="text" name="cidade" class="form-control"
                                                        value="<?= $dadosCD[0]['cidade_destino']; ?>" required>
                                                </div>
                                            </div>

                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <?php
                                                    if (isset($dadosCA[0]['incoterms'])) {
                                                        if ($dadosCA[0]['incoterms'] === 'DAP' || $dadosCA[0]['incoterms'] === 'DDP' || $dadosCA[0]['incoterms'] === 'DPU') {
                                                            $txtRequerido2 = 'required';
                                                        } else {
                                                            $txtRequerido2 = 'disabled';
                                                        }
                                                    } else {
                                                        $txtRequerido2 = 'disabled';
                                                    }
                                                    ?>
                                                    <label>Endereço no destino</label>
                                                    <br clear="all">
                                                    <textarea name="endereco_no_destino" class="form-control"
                                                        <?= $txtRequerido2; ?>>
                                                        <?= $dadosCD[0]['endereco_no_destino']; ?>
                                                    </textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-footer text-right">
                                            <button type="submit" class="btn btn-info"
                                                title="Salvar as alterações">Atualizar <i
                                                    class="fas fa-sync-alt"></i></button>
                                        </div>
                                    </form>
                                    <?php endif; ?>

                                </div>
                                <div class="tab-pane fade <?= $etapashow4;?>" id="vert-tabs-Carga" role="tabpanel"
                                    aria-labelledby="vert-tabs-Carga-tab">

                                    <?php if (!isset($dadosCA[0]['modal'])) : ?>
                                    <p>Cadastre primeiramente os dados de ABERTURA para visualizar as opções de tipo de
                                        carga.</p>
                                    <?php else : ?>

                                    <form name="origem" action="frete/grava_cotacao_carga.php" method="post">
                                        <input type="hidden" name="idCotacao" value="<?= $idCotacao; ?>">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <h5 class="texto-destaque-4">Como sua carga será transportada?
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">

                                            <div class="col-lg-12">

                                                <div id="accordion">

                                                    <div class="card card-info">
                                                        <div class="card-header bg-destaque-4">
                                                            <h4 class="card-title w-100">
                                                                <a class="d-block w-100" data-toggle="collapse"
                                                                    href="#collapseOne">
                                                                    <i class="fas fa-boxes"></i> Carga Compartilhada
                                                                    (LCL)
                                                                </a>
                                                            </h4>
                                                        </div>
                                                        <div id="collapseOne"
                                                            class="collapse <?= $liCarga != 2 ? 'show'  : '' ?>"
                                                            data-parent="#accordion">
                                                            <div class="card-body">

                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <button type="button" data-toggle="modal"
                                                                            data-target="#modalCargaSolta"
                                                                            class="btn bg-destaque-1"><i
                                                                                class="fa fa-fw fa-plus"></i> Incluir
                                                                            Embalagem</button>
                                                                    </div>
                                                                </div>

                                                                <br>

                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <table class="table table-striped table-hover"
                                                                            style="font-size: 12px;">
                                                                            <tr>
                                                                                <th>Embalagem</th>
                                                                                <th class="text-center">Qt.</th>
                                                                                <th class="text-center">
                                                                                    Dimensões<br>
                                                                                    (Comprimento - cm)</th>
                                                                                <th class="text-center">
                                                                                    Dimensões<br>
                                                                                    (Largura - cm)</th>
                                                                                <th class="text-center">
                                                                                    Dimensões<br>
                                                                                    (Altura - cm)</th>
                                                                                <th class="text-center">CBM</th>
                                                                                <th>Resumo do produto</th>
                                                                                <th class="text-center"></th>
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
                                                                                </td class="text-center">
                                                                                <td>
                                                                                    <?=$dadoCarga['resumo_produto'];?>
                                                                                </td>
                                                                                <td>
                                                                                    <a href="frete/deletar_embalagem.php?idCotacao=<?=$idCotacao;?>&idEmbalagem=<?=$dadoCarga['id'];?>"
                                                                                        onclick="confirm('Tem certeza que deseja apagar?');">
                                                                                        <i class="fa fa-trash"
                                                                                            title="Apagar embalagem"></i>
                                                                                    </a>
                                                                                </td>
                                                                            </tr>
                                                                            <?php endforeach; ?>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                                <div class="box-footer text-right">
                                                                    <button type="submit" class="btn btn-info"
                                                                        title="Salvar e ir para próxima etapa!">Próxima
                                                                        etapa <i
                                                                            class="far fa-hand-point-right"></i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="card">
                                                        <div class="card-header bg-destaque-2">
                                                            <h4 class="card-title w-100">
                                                                <a class="d-block w-100" data-toggle="collapse"
                                                                    href="#collapseTwo">
                                                                    <img src="img/cont.webp"
                                                                        style="max-width: 25px;"></img> Container (FCL)
                                                                </a>
                                                            </h4>
                                                        </div>
                                                        <div id="collapseTwo"
                                                            class="collapse <?= $liCarga == 2 ? 'show'  : '' ?>"
                                                            data-parent="#accordion">
                                                            <div class="card-body">
                                                                <?php if ($dadosCA[0]['modal'] == 'mar' || $dadosCA[0]['modal'] == 'rod') : ?>

                                                                <span class="badge badge-primary"><i
                                                                        class="fa fa-info-circle"></i>
                                                                    Atenção</span><br>
                                                                <p
                                                                    style="border-bottom: 1px solid #ccc;padding-bottom:5px;">
                                                                    Nesta opção selecionada, os
                                                                    agentes de carga irão elaborar uma proposta para
                                                                    assegurar a reserva de um contêiner
                                                                    inteiro, garantindo que sua mercadoria tenha espaço
                                                                    exclusivo no transporte
                                                                    marítimo, de acordo com as necessidades e
                                                                    características do envio.</p>

                                                                <div class="row">
                                                                    <div class="col-lg-12">

                                                                        <button type="button" data-toggle="modal"
                                                                            data-target="#modalCargaCompartilhada"
                                                                            class="btn bg-destaque-1"><i
                                                                                class="fa fa-fw fa-plus"></i> Incluir
                                                                            Conteiner</button>

                                                                    </div>
                                                                </div>

                                                                <br>

                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <table class="table table-striped table-hover">
                                                                            <tr>
                                                                                <th class="text-center">Quantidade</th>
                                                                                <th class="text-center">Tipo do
                                                                                    Container</th>
                                                                                <th class="text-center">Peso Total</th>
                                                                                <th class="text-center">Un. Medida</th>
                                                                                <th class="text-center"></th>
                                                                            </tr>
                                                                            <?php foreach($dadosCC as $dadoCarga) : ?>
                                                                            <tr>
                                                                                <td class="text-center">
                                                                                    <?=$dadoCarga['qt_c'];?>
                                                                                </td>
                                                                                <td class="text-center">
                                                                                    <?=$dadoCarga['tipo_container'];?>
                                                                                </td>
                                                                                <td class="text-center">
                                                                                    <?=$dadoCarga['peso_c'];?>
                                                                                </td>
                                                                                <td class="text-center">
                                                                                    <?=$dadoCarga['tipo_peso_c'];?>
                                                                                </td>
                                                                                <td>
                                                                                    <a href="frete/deletar_container.php?idCotacao=<?=$idCotacao;?>&idContainer=<?=$dadoCarga['id'];?>"
                                                                                        onclick="confirm('Tem certeza que deseja apagar?');">
                                                                                        <i class="fa fa-trash"
                                                                                            title="Apagar container"></i>
                                                                                    </a>
                                                                                </td>
                                                                            </tr>
                                                                            <?php endforeach; ?>
                                                                        </table>
                                                                    </div>

                                                                </div>
                                                                <div class="box-footer text-right">
                                                                    <a href="frete/grava_cotacao_carga.php"
                                                                        class="btn btn-info">Próxima
                                                                        etapa <i
                                                                            class="far fa-hand-point-right"></i></a>
                                                                </div>

                                                                <?php else : ?>

                                                                <span class="badge badge-primary">
                                                                    <i class="fas fa-info-circle"></i> Aviso</span><br>
                                                                Este tipo de trasporte só poderá ser escolhido em caso
                                                                de modal: <span class="text-info">Marítimo ou
                                                                    Rodoviário</span>
                                                                <?php endif;?>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                        <div class="row">

                                            <div class="col-lg-12">
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-exclamation-triangle"></i> Atenção</span><br>
                                                Clique em uma das opções acima para informar como sua carga será
                                                transportada, em seguida insira as informações da sua carga como:
                                                Embalagem/Peso/Dimensão/CBM. Sem essas informações os agentes não irão
                                                conseguir fazer a cotação de frete.
                                            </div>

                                        </div>
                                    </form>
                                    <?php endif; ?>

                                </div>
                                <div class="tab-pane fade <?= $etapashow5;?>" id="vert-tabs-Mercadoria" role="tabpanel"
                                    aria-labelledby="vert-tabs-Mercadoria-tab">

                                    <?php if (empty($dadosCM)) : ?>

                                    <form name="mercadoria" action="frete/grava_cotacao_mercadoria.php" method="post">
                                        <input type="hidden" name="idCotacao" value="<?= $idCotacao; ?>">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <h5 class="texto-destaque-4">O que você está transportando?
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Produto</label>
                                                    <input type="text" name="produto" class="form-control"
                                                        placeholder="Insira o nome e NCM do seu produto" required>
                                                </div>
                                            </div>

                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label>Perecível</label>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="perecivelSim" name="perecivel"
                                                            class="custom-control-input" value="sim">
                                                        <label class="custom-control-label"
                                                            for="perecivelSim">Sim</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="perecivelNao" name="perecivel"
                                                            class="custom-control-input" value="não" checked>
                                                        <label class="custom-control-label"
                                                            for="perecivelNao">Não</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label>Empilhável</label>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="empilhavelSim" name="empilhavel"
                                                            class="custom-control-input" value="sim">
                                                        <label class="custom-control-label"
                                                            for="empilhavelSim">Sim</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="empilhavelNao" name="empilhavel"
                                                            class="custom-control-input" value="não" checked>
                                                        <label class="custom-control-label"
                                                            for="empilhavelNao">Não</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label>Perigoso <sup><i class="fa fa-fw fa-question-circle"
                                                                data-toggle="modal"
                                                                data-target="#modal-default-perigoso" title="ATENÇÃO"
                                                                style="cursor:pointer"></i></sup></label>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="perigosoSim" name="perigoso"
                                                            class="custom-control-input" value="sim">
                                                        <label class="custom-control-label"
                                                            for="perigosoSim">Sim</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="perigosoNao" name="perigoso"
                                                            class="custom-control-input" value="não" checked>
                                                        <label class="custom-control-label"
                                                            for="perigosoNao">Não</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label>Refrigeração</label>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="refrigeracaoSim" name="refrigeracao"
                                                            class="custom-control-input" value="sim"
                                                            onchange="mostrarOcultarTemperatura()">
                                                        <label class="custom-control-label"
                                                            for="refrigeracaoSim">Sim</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="refrigeracaoNao" name="refrigeracao"
                                                            class="custom-control-input" value="não" checked
                                                            onchange="mostrarOcultarTemperatura()">
                                                        <label class="custom-control-label"
                                                            for="refrigeracaoNao">Não</label>
                                                    </div>
                                                    <input type="text" class="form-control mt-2" name="temp_refrig"
                                                        id="temp_refrig" placeholder="Temperatura?"
                                                        style="display: none;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-5">
                                                <div class="form-group">
                                                    <label>Valor Total</label>
                                                    <br clear="all">
                                                    <!--<small class="text-yellow"><i class="fa fa-warning"></i> ATENÇAO<br> Digite o valor completo, sem nenhuma pontuação ou separador!</small>-->
                                                    <table>
                                                        <tr>
                                                            <td>
                                                                <div class="input-group">
                                                                    <span class="input-group-addon"><i
                                                                            class="fa fa-money"></i></span>
                                                                    <input type="text" class="form-control"
                                                                        name="valor_total" id="valorTotal" required>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="moeda">
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
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label>Peso Total</label>
                                                    <br clear="all">
                                                    <input type="text" name="peso_total" id="peso_total"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-1">
                                                <div class="form-group">
                                                    <label>&nbsp;</label>
                                                    <br clear="all">
                                                    <select name="tipo_peso_total" class="form-control">
                                                        <option value="kg">kg</option>
                                                        <option value="gr">gr</option>
                                                        <option value="ton">ton</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-footer text-right">
                                            <button type="submit" class="btn btn-info"
                                                title="Salvar e ir para próxima etapa!">Próxima
                                                etapa <i class="far fa-hand-point-right"></i></button>
                                        </div>
                                    </form>

                                    <?php else : ?>

                                    <form name="mercadoria" action="frete/edita_cotacao_mercadoria.php" method="post">
                                        <input type="hidden" name="idCotacao" value="<?= $idCotacao; ?>">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <h5 class="texto-destaque-4">O que você está transportando?</h5>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Produto</label>
                                                    <input type="text" name="produto" class="form-control"
                                                        placeholder="Insira o nome e NCM do seu produto" required
                                                        value="<?= htmlspecialchars($dadosCM[0]['produto']); ?>">
                                                </div>
                                            </div>

                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label>Perecível</label>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="perecivelSim" name="perecivel"
                                                            class="custom-control-input" value="sim"
                                                            <?= ($dadosCM[0]['perecivel'] == 'sim') ? 'checked' : ''; ?>>
                                                        <label class="custom-control-label"
                                                            for="perecivelSim">Sim</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="perecivelNao" name="perecivel"
                                                            class="custom-control-input" value="não"
                                                            <?= ($dadosCM[0]['perecivel'] == 'não') ? 'checked' : ''; ?>>
                                                        <label class="custom-control-label"
                                                            for="perecivelNao">Não</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label>Empilhável</label>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="empilhavelSim" name="empilhavel"
                                                            class="custom-control-input" value="sim"
                                                            <?= ($dadosCM[0]['empilhavel'] == 'sim') ? 'checked' : ''; ?>>
                                                        <label class="custom-control-label"
                                                            for="empilhavelSim">Sim</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="empilhavelNao" name="empilhavel"
                                                            class="custom-control-input" value="não"
                                                            <?= ($dadosCM[0]['empilhavel'] == 'não') ? 'checked' : ''; ?>>
                                                        <label class="custom-control-label"
                                                            for="empilhavelNao">Não</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label>Perigoso</label>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="perigosoSim" name="perigoso"
                                                            class="custom-control-input" value="sim"
                                                            <?= ($dadosCM[0]['perigoso'] == 'sim') ? 'checked' : ''; ?>>
                                                        <label class="custom-control-label"
                                                            for="perigosoSim">Sim</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="perigosoNao" name="perigoso"
                                                            class="custom-control-input" value="não"
                                                            <?= ($dadosCM[0]['perigoso'] == 'não') ? 'checked' : ''; ?>>
                                                        <label class="custom-control-label"
                                                            for="perigosoNao">Não</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label>Refrigeração</label>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="refrigeracaoSim" name="refrigeracao"
                                                            class="custom-control-input" value="sim"
                                                            <?= ($dadosCM[0]['refrigeracao'] == 'sim') ? 'checked' : ''; ?>>
                                                        <label class="custom-control-label"
                                                            for="refrigeracaoSim">Sim</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="refrigeracaoNao" name="refrigeracao"
                                                            class="custom-control-input" value="não"
                                                            <?= ($dadosCM[0]['refrigeracao'] == 'não') ? 'checked' : ''; ?>>
                                                        <label class="custom-control-label"
                                                            for="refrigeracaoNao">Não</label>
                                                    </div>
                                                    <input type="text" class="form-control mt-2" name="temp_refrig"
                                                        id="temp_refrig" placeholder="Temperatura?"
                                                        value="<?= htmlspecialchars($dadosCM[0]['temp_refrig']); ?>"
                                                        style="<?= ($dadosCM[0]['refrigeracao'] == 'sim') ? '' : 'display: none;'; ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-5">
                                                <div class="form-group">
                                                    <label>Valor Total</label>
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><i
                                                                class="fa fa-money"></i></span>
                                                        <input type="text" class="form-control" id="valorTotal"
                                                            name="valor_total"
                                                            value="<?= $dadosCM[0]['valor_total']; ?>" required>
                                                        <select class="form-control" name="moeda">
                                                            <option value="<?= $dadosCM[0]['moeda']; ?>" selected>
                                                                <?= $dadosCM[0]['moeda']; ?></option>
                                                            <option value="USD ($)">USD ($)</option>
                                                            <option value="EUR (€)">EUR (€)</option>
                                                            <option value="GBP (£)">GBP (£)</option>
                                                            <option value="BRL (R$)">BRL (R$)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label>Peso Total</label>
                                                    <span class="input-group-addon"><i class="fa fa-money"></i></span>
                                                    <input type="text" class="form-control" name="valor_total"
                                                        value="<?= $dadosCM[0]['peso_total']; ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-1">
                                                <div class="form-group">
                                                    <label>&nbsp;</label>
                                                    <select name="tipo_peso_total" class="form-control">
                                                        <option value="<?= $dadosCM[0]['tipo_peso_total']; ?>" selected>
                                                            <?= $dadosCM[0]['tipo_peso_total']; ?></option>
                                                        <option value="kg">kg</option>
                                                        <option value="gr">gr</option>
                                                        <option value="ton">ton</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="box-footer text-right">
                                            <button type="submit" class="btn btn-info" title="Salvar alterações">Salvar
                                                <i class="far fa-save"></i></button>
                                        </div>
                                    </form>

                                    <?php endif; ?>

                                </div>
                                <div class="tab-pane fade <?= $etapashow6;?>" id="vert-tabs-info-comp" role="tabpanel"
                                    aria-labelledby="vert-tabs-info-comp-tab">

                                    <!--INICIO FORM BASE -->
                                    <form name="dados-cotacao" method="post" action="frete/grava_cotacao_finaliza.php"
                                        enctype="multipart/form-data">

                                        <input type="hidden" name="idCotacao" value="<?= $idCotacao; ?>">

                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="exampleInputFile">
                                                        Upload de documentos<br> (Ex.: Packing List, Invoice,
                                                        MSDS, proforma)
                                                        <sup><i class="fa fa-fw fa-question-circle" data-toggle="modal"
                                                                data-target="#modal-default-UD"
                                                                title="Explicação para tipos de arquivos"
                                                                style="cursor:pointer"></i></sup>
                                                    </label>
                                                    <div class="input-group">
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input"
                                                                id="exampleInputFile" name="arquivo">
                                                            <label class="custom-file-label"
                                                                for="exampleInputFile">Escolha o arquivo</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label>Embarque previsto <sup><i class="fa fa-fw fa-question-circle"
                                                                data-toggle="modal" data-target="#modal-default-DE"
                                                                title="Explicação para data de embarque"
                                                                style="cursor:pointer"></i></sup></label>

                                                    <div class="input-group date">
                                                        <input type="date" name="embarque_previsto"
                                                            class="form-control pull-right" required>
                                                    </div>
                                                    <!-- /.input group -->
                                                </div>

                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <div class="form-group">
                                                        <label>Observações</label>
                                                        <textarea name="obs" class="form-control" rows="2"
                                                            placeholder="Digite aqui alguma observação a fazer..."></textarea>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label>Situação da Carga: <small>Sua carga já está
                                                            pronta?</small></label>
                                                    <select class="form-control" name="carga_pronta">
                                                        <option value="Sim, a carga já está pronta." selected>Sim, a
                                                            carga já está pronta.
                                                        </option>
                                                        <option
                                                            value="Não, a carga ficará pronta dentro da próxima semana.">
                                                            Não, a carga
                                                            ficará pronta dentro da próxima semana.</option>
                                                        <option
                                                            value="Não, a carga ficará pronta nas próximas 2 semanas.">
                                                            Não, a carga
                                                            ficará pronta nas próximas 2 semanas.</option>
                                                        <option
                                                            value="Não, a carga ainda não está pronta e não tenho informações de prontidão.">
                                                            Não, a carga ainda não está pronta e não tenho
                                                            informações de prontidão.
                                                        </option>
                                                        <option
                                                            value="Não, a carga ainda não está pronta; estou realizando um estudo para minha importação.">
                                                            Não, a carga ainda não está pronta; estou
                                                            realizando um estudo para minha
                                                            importação.</option>
                                                    </select>
                                                </div>

                                            </div>
                                        </div>

                                        <hr>

                                        <div class="row">
                                            <div class="col-lg-4">

                                                <div class="form-group">
                                                    <label>Contratação de Seguro</label>
                                                    <p>Deseja <strong>contratar seguro</strong> na cotação?</p>
                                                    <div class="d-flex">
                                                        <div class="custom-control custom-radio mr-3">
                                                            <input class="custom-control-input" type="radio"
                                                                id="seguroSim" name="contratacao_seguro" value="sim"
                                                                checked>
                                                            <label for="seguroSim"
                                                                class="custom-control-label">Sim</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input class="custom-control-input" type="radio"
                                                                id="seguroNao" name="contratacao_seguro" value="não">
                                                            <label for="seguroNao"
                                                                class="custom-control-label">Não</label>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>
                                                        Transporte Nacional
                                                        <i class="fas fa-info-circle texto-destaque-3"
                                                            style="cursor: pointer;" data-bs-toggle="tooltip"
                                                            data-bs-placement="bottom"
                                                            title="Deseja fazer a cotação para o transporte nacional do aeroporto/porto até o seu endereço?"></i>
                                                    </label>
                                                    <br>
                                                    <div class="custom-control custom-radio custom-control-inline">
                                                        <input class="custom-control-input" type="radio"
                                                            id="transporteSim" name="contratacao_transporte" value="sim"
                                                            onclick="exibirEnderecoTN()">
                                                        <label for="transporteSim"
                                                            class="custom-control-label">Sim</label>
                                                    </div>
                                                    <div class="custom-control custom-radio custom-control-inline">
                                                        <input class="custom-control-input" type="radio"
                                                            id="transporteNao" name="contratacao_transporte" value="não"
                                                            onclick="ocultarEnderecoTN()" checked>
                                                        <label for="transporteNao"
                                                            class="custom-control-label">Não</label>
                                                    </div>

                                                    <div id="enderecoTNDiv" style="display:none; margin-top: 10px;">
                                                        <label>Endereço de entrega (Atenção - Informar CEP)</label>
                                                        <textarea name="endereco_tn" class="form-control"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>
                                                        Moeda All in
                                                        <i class="fas fa-info-circle texto-destaque-3"
                                                            style="cursor: pointer;" data-bs-toggle="tooltip"
                                                            data-bs-placement="bottom"
                                                            title="Selecione abaixo, preferencialmente, qual a moeda a ser enviada pelos agenntes no All In"></i></label>
                                                    <select class="form-control" name="moeda_allin">
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

                                        <hr>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label>Termos e Condições</label>
                                                    <p>Para garantir total transparência e segurança, é fundamental que
                                                        você leia os <strong>Termos e Condições de Uso</strong> da
                                                        plataforma. Este documento contém informações essenciais sobre
                                                        seus direitos, responsabilidades e como protegemos seus dados.
                                                        Leia atentamente o documento <a
                                                            href="../arquivos/termos/termos_e_condicoes_uso_logixflash.pdf"
                                                            target="_blank">clicando aqui.</a>
                                                    </p>

                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox"
                                                            id="aceitarTermos" name="aceitarTermos" required>
                                                        <label for="aceitarTermos" class="custom-control-label">
                                                            Li e aceito os Termos e Condições de Uso da plataforma.
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="box-footer text-right">

                                            <?php if(isset($idCotacao)) : ?>
                                            <a href="frete/reiniciar_cotacao.php?idCotacao=<?php echo $idCotacao; ?>"
                                                class="btn btn-danger"
                                                onclick="confirm('Tem certeza que deseja reiniciar o pedido?')">
                                                <i class="fas fa-undo-alt"></i> Reiniciar pedido
                                            </a>
                                            <?php else : ?>
                                            <button type="button" class="btn btn-danger" disabled>
                                                <i class="fas fa-undo-alt"></i> Reiniciar pedido
                                            </button>
                                            <?php endif; ?>

                                            &nbsp;&nbsp;&nbsp;

                                            <button type="submit" class="btn btn-info"
                                                onclick="return confirmSubmit()"><i class="fas fa-save"></i> Salvar e
                                                Enviar
                                                pedido</button>
                                        </div>

                                    </form>
                                    <!--FIM FORM BASE -->

                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                <!-- /.card -->
            </div>
            <!-- /.card -->

        </div>

    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->

<div class="modal fade" id="modal-default">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h4 class="modal-title">
                    <b>Incoterms®2020</b>
                    <br>
                    <small style="font-size: 12px; line-height:12px;">Termos Internacionais de Comércio (Incoterms)
                        discriminados pela International Chamber of
                        Commerce (ICC) em sua Publicação nº 723-E, de 2020.</small>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5><b>EXW</b></h5>
                <p>EX WORKS (named place of delivery) NA ORIGEM (local de entrega nomeado).</p>
                <p>O vendedor limita-se a colocar a mercadoria à disposição do comprador no estabelecimento do vendedor,
                    no prazo estabelecido, não se responsabilizando pelo desembaraço para exportação nem pelo
                    carregamento da mercadoria em qualquer veículo coletor.</p>
                <p>Utilizável em qualquer modalidade de transporte.</p>
                <p>Nota: em virtude de o comprador estrangeiro não dispor de condições legais para providenciar o
                    desembaraço para saída de bens do País, fica subentendido que esta providência é adotada pelo
                    vendedor, sob suas expensas e riscos, no caso da exportação brasileira</p>
                <hr>
                <h5><b>FCA</b></h5>
                <p>FREE CARRIER (named place of delivery) LIVRE NO TRANSPORTADOR (local de entrega nomeado).</p>
                <p>O vendedor completa suas obrigações e encerra sua responsabilidade quando entrega a mercadoria,
                    desembaraçada para a exportação, ao transportador ou a outra pessoa indicada pelo comprador, no
                    local nomeado do país de origem.</p>
                <p>Utilizável em qualquer modalidade de transporte.</p>
                <p>Comprador e vendedor poderão utilizar transporte próprio em trechos do deslocamento.</p>
                <hr>
                <h5><b>FAS</b></h5>
                <p>FREE ALONGSIDE SHIP (named port of shipment) LIVRE AO LADO DO NAVIO (porto de embarque nomeado).</p>
                <p>O vendedor encerra suas obrigações no momento em que a mercadoria é colocada, desembaraçada para
                    exportação, ao longo do costado do navio transportador indicado pelo comprador, no cais ou em
                    embarcações utilizadas para carregamento da mercadoria, no porto de embarque nomeado pelo comprador.
                </p>
                <p>Utilizável exclusivamente no transporte aquaviário (marítimo ou hidroviário interior).</p>
                <hr>
                <h5><b>FCB</b></h5>
                <p>FREE ON BOARD (named port of shipment) LIVRE A BORDO (porto de embarque nomeado).</p>
                <p>O vendedor encerra suas obrigações e responsabilidades quando a mercadoria, desembaraçada para a
                    exportação, é entregue, arrumada, a bordo do navio no porto de embarque, ambos indicados pelo
                    comprador, na data ou dentro do período acordado.</p>
                <p>Utilizável exclusivamente no transporte aquaviário (marítimo ou hidroviário interior)..</p>
                <hr>
                <h5><b>CFR</b></h5>
                <p>COST AND FREIGHT (named port of destination) CUSTO E FRETE (porto de destino nomeado).</p>
                <p>Além de arcar com obrigações e riscos previstos para o termo FOB, o vendedor contrata e paga frete e
                    custos necessários para levar a mercadoria até o porto de destino combinado.</p>
                <p>Utilizável exclusivamente no transporte aquaviário (marítimo ou hidroviário interior).</p>
                <hr>
                <h5><b>CIF</b></h5>
                <p>COST, INSURANCE AND FREIGHT (named port of destination) CUSTO, SEGURO E FRETE (porto de destino
                    nomeado).</p>
                <p>Além de arcar com obrigações e riscos previstos para o termo FOB, o vendedor contrata e paga frete,
                    custos e seguro relativos ao transporte da mercadoria até o porto de destino combinado.</p>
                <p>Utilizável exclusivamente no transporte aquaviário (marítimo ou hidroviário interior).</p>
                <hr>
                <h5><b>CPT</b></h5>
                <p>CARRIAGE PAID TO (named place of destination) TRANSPORTE PAGO ATÉ (local de destino nomeado).</p>
                <p>Além de arcar com obrigações e riscos previstos para o termo FCA, o vendedor contrata e paga frete e
                    custos necessários para levar a mercadoria até o local de destino combinado.</p>
                <p>Utilizável em qualquer modalidade de transporte.</p>
                <hr>
                <h5><b>CIP</b></h5>
                <p>CARRIAGE AND INSURANCE PAID TO (named place of destination) TRANSPORTE E SEGURO PAGOS ATÉ (local de
                    destino nomeado).</p>
                <p>Além de arcar com obrigações e riscos previstos para o termo FCA, o vendedor contrata e paga frete,
                    custos e seguro relativos ao transporte da mercadoria até o local de destino combinado.</p>
                <p>Utilizável em qualquer modalidade de transporte.</p>
                <hr>
                <h5><b>DAP</b></h5>
                <p>DELIVERED AT PLACE (named place of destination) ENTREGUE NO LOCAL (local de destino nomeado).</p>
                <p>O vendedor completa suas obrigações e encerra sua responsabilidade quando coloca a mercadoria à
                    disposição do comprador, na data ou dentro do período acordado, num local indicado no país de
                    destino, pronta para ser descarregada do veículo transportador e não desembaraçada para importação.
                </p>
                <p>Utilizável em qualquer modalidade de transporte.</p>
                <p>Comprador e vendedor poderão utilizar transporte próprio em trechos do deslocamento.</p>
                <hr>
                <h5><b>DPU</b></h5>
                <p>DELIVERED AT PLACE UNLOADED (named place of destination) ENTREGUE NO LOCAL DESCARREGADO (local de
                    destino).</p>
                <p>O vendedor completa suas obrigações e encerra sua responsabilidade quando a mercadoria é colocada à
                    disposição do comprador, na data ou dentro do período acordado, em local determinado no país de
                    destino, descarregada do veículo transportador mas não desembaraçada para importação.</p>
                <p>Utilizável em qualquer modalidade de transporte.</p>
                <p>Comprador e vendedor poderão utilizar transporte próprio em trechos do deslocamento. Termo definido
                    em substituição ao DAT, com a diferença que o DAT determinava a “entrega” exclusivamente em
                    terminais de carga, podendo o DPU ser utilizado em terminais ou qualquer outro local determinado
                    (por exemplo o armazém do comprador).</p>
                <h5><b>DDP</b></h5>
                <p>DELIVERED DUTY PAID (named place of destination) ENTREGUE COM DIREITOS PAGOS (local de destino
                    nomeado).</p>
                <p>O vendedor completa suas obrigações e encerra sua responsabilidade quando a mercadoria é colocada à
                    disposição do comprador, na data ou dentro do período acordado, no local de destino designado no
                    país importador, não descarregada do meio de transporte. O vendedor, além do desembaraço, assume
                    todos os riscos e custos, inclusive impostos, taxas e outros encargos incidentes na importação.</p>
                <p>Utilizável em qualquer modalidade de transporte.</p>
                <p>Comprador e vendedor poderão utilizar transporte próprio em trechos do deslocamento. Nota: em razão
                    de o vendedor estrangeiro não dispor de condições legais para providenciar o desembaraço para
                    entrada de bens do País, <u>este termo não pode ser utilizado na importação brasileira</u>, devendo
                    ser escolhido o DPU ou DAP no caso de preferência por condição disciplinada pela ICC.</p>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="modal-nomeEstudo">
    <div class="modal-dialog">
        <div class="modal-content bg-secondary">
            <div class="modal-header">
                <h5 class="modal-title">
                    <small>Para iniciar seu estudo de viabilidade, dê um nome para ele.</small>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row" style="display: block;">
                    <form name="inicio-pv" action="ferramentas/grava_pv_nome_estudo.php" method="post">

                        <label>Nome do estudo:</label>
                        <input type="text" name="nome_estudo" placeholder="Digite aqui o nome do estudo"
                            class="form-control" style="width:100%;">

                        <br clear="all">
                        <button type="submit" class="btn btn-default">
                            <i class="fa fa-save"></i> Salvar
                        </button>

                    </form>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <h4>Aviso Importante:</h4>
                <p>
                    Informamos que o estudo a ser realizado e os cálculos apresentados são apenas estimativas. Os
                    valores fornecidos são gerados com base na nossa metodologia de cálculo e devem ser utilizados
                    apenas como referência.
                </p>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="modal-default-PI">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title">
                    <b>ATENÇÃO</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Dê um nome para este seu pedido, assim conseguirá localizá-lo facilmente dentro da plataforma.</p>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="modal-default-CBM">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title">
                    <b>CBM (Cubic Meter) - o que é e como calcular?</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    O <strong>CBM (Cubic Meter)</strong> é uma unidade de medida que representa o volume de um objeto. A
                    fórmula para
                    calcular o CBM é:
                </p>
                <p>
                    Fórmula para cálculo <strong>em metros:</strong>
                    <li>CBM=Comprimento (m)xLargura (m)xAltura (m)</li>
                </p>
                <p>
                    Fórmula para cálculo <strong>em centímetros:</strong><br>
                    Primeiro, é necessário converter o resultado de centímetros cúbicos para metros cúbicos, dividindo
                    por 1.000.000 (já que 1 m³ = 1.000.000 cm³).
                    <li>Volume em cm³=Comprimento (cm)xLargura (cm)xAltura (cm)</li>
                </p>
                <p>
                    <strong>Exemplo:</strong><br>
                    Se um objeto tem 100 cm de comprimento, 50 cm de largura e 30 cm de altura:<br>
                    <li>Volume em cm³=100x50x30=150.000 cm³</li>
                    <li>CBM em m³=150.000/1.000.000 = 0,15 m³</li>
                </p>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="modal-default-Taxas">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title">
                    <b>ATENÇÃO</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    A taxa na origem em uma cotação de frete internacional refere-se aos custos adicionais cobrados no
                    país de origem da mercadoria antes do embarque. Esses encargos podem incluir despesas com manuseio
                    no porto ou aeroporto, documentação, taxas alfandegárias, inspeção, entre outros serviços
                    necessários para preparar o envio. Essas taxas variam de acordo com o local de origem, o tipo de
                    carga e o modal de transporte, e está presente sempre no Incoterm EXW.
                </p>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="modal-default-TaxasDA">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title">
                    <b>ATENÇÃO</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    As taxas no destino, são os custos aplicados após a chegada da mercadoria no país de destino. Elas
                    incluem despesas com manuseio no porto ou aeroporto (Capatazia), taxa de liberação de BL ou AWB
                    (documentação), inspeções, transporte interno até o destinatário (quando aplicável) e outras
                    possíveis despesas. Esses encargos variam de acordo com o local, tipo de mercadoria e o modal
                    utilizado, e estão presentes em todos os Incoterms.
                    Se tiver algum valor que não contemple, adicione ao campo de “outras despesas".
                </p>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="modal-default-perigoso">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title">
                    <b>ATENÇÃO</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    Caso a opção para carga perigosa seja "SIM", não se esqueça de incluir o documento
                    <strong>MSDS</strong> no upload!.
                </p>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="modalCargaCompartilhada">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title">
                    Incluir Container
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form name="carga" action="frete/grava_cotacao_carga_cc.php" method="post">
                    <input type="hidden" name="idCotacao" value="<?= $idCotacao; ?>">
                    <!-- Linha carga 1 -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Quantidade</label>
                                <br clear="all">
                                <input type="number" class="form-control" placeholder="0" name="quantidade_C1" required>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Tipo do Container</label>
                                <br clear="all" />
                                <select name="tipo_container1" class="form-control select2" style="width: 100%;">
                                    <option value="" selected>Selecione</option>
                                    <option value="20' Dry Box">20' Dry Box</option>
                                    <option value="40' Dry Box">40' Dry Box</option>
                                    <option value="40' HC">40' HC</option>
                                    <option value="20' Reefer">20' Reefer</option>
                                    <option value="40' Reefer">40' Reefer</option>
                                    <option value="40' NOR">40' NOR</option>
                                    <option value="20' Flat Rack">20' Flat Rack</option>
                                    <option value="40' Flat Rack">40' Flat Rack</option>
                                    <option value="20' Open Top">20' Open Top</option>
                                    <option value="40' Open Top">40' Open Top</option>
                                    <option value="20' Plataforma">20' Plataforma</option>
                                    <option value="40' Plataforma">40' Plataforma</option>
                                    <option value="Graneleiro Dry">Graneleiro Dry</option>
                                    <option value="Tanque">Tanque</option>
                                    <option value="Ventilado">Ventilado</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-10">
                            <div class="form-group">
                                <label>Peso <small class="text-muted">(Total)</small></label>
                                <br clear="all">
                                <input type="text" class="form-control" placeholder="0" name="peso_C1" value="0">
                            </div>
                        </div>

                        <div class="col-lg-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <select name="tipoPeso_C1" class="form-control">
                                    <option value="kg">kg</option>
                                    <option value="gr">gr</option>
                                    <option value="ton">ton</option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <!-- FIM Linha -->

                    <div class="text-right" style="background-color:#f6f6f6;padding:10px;">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-ban"></i>
                            Cancelar</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="modalCargaSolta">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title">
                    <b>Incluir Embalagem</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form name="carga" action="frete/grava_cotacao_carga_cs.php" method="post">
                    <input type="hidden" name="idCotacao" value="<?= $idCotacao; ?>">
                    <!-- Linha carga 1 -->
                    <div class="row">
                        <div class=" col-lg-12">
                            <div class="form-group">
                                <label>Tipo de Embalagem</label>
                                <br clear="all">
                                <select name="tipo_embalagemCS1" class="form-control select2" style="width: 100%;"
                                    required>
                                    <?php if (!isset($dadosCA)) : ?>
                                    <option>Cadastre ABERTURA primeiro</option>
                                    <?php endif; ?>
                                    <?php if ($dadosCA[0]['modal'] == 'aer') : ?>
                                    <option value="" selected>Selecione um tipo</option>
                                    <option value="Amarrado">Amarrado</option>
                                    <option value="Baú de Madeira">Baú de Madeira</option>
                                    <option value="Baú de Metal">Baú de Metal</option>
                                    <option value="Caixa de Isopor">Caixa de Isopor</option>
                                    <option value="Caixa de Madeira">Caixa de Madeira</option>
                                    <option value="Caixa de Metal">Caixa de Metal</option>
                                    <option value="Caixa de Papelão">Caixa de Papelão</option>
                                    <option value="Canudo">Canudo</option>
                                    <option value="Container">Container</option>
                                    <option value="Engradado">Engradado</option>
                                    <option value="Envelope">Envelope</option>
                                    <option value="Outros/Diversos">Outros/Diversos</option>
                                    <option value="Pacote">Pacote</option>
                                    <option value="Pallet">Pallet</option>
                                    <option value="Pallet de Madeira">Pallet de Madeira</option>
                                    <option value="Pallet de Plástico">Pallet de Plástico</option>
                                    <option value="Peça">Peça</option>
                                    <option value="Saco de Aniagem">Saco de Aniagem</option>
                                    <option value="Saco de Lona">Saco de Lona</option>
                                    <option value="Saco Plástico">Saco de Plastico</option>
                                    <option value="Tambor de Metal">Tambor de Metal</option>
                                    <option value="Tambor de Papel">Tambor de Papel</option>
                                    <option value="Tambor de Plástico">Tambor de Plástico</option>
                                    <?php endif; ?>
                                    <?php if ($dadosCA[0]['modal'] != 'aer') : ?>
                                    <option value="" selected>Selecione um tipo</option>
                                    <option value="Amarrado">Amarrado</option>
                                    <option value="Barrica de Ferro">Barrica de Ferro</option>
                                    <option value="Barrica de Fibra de Vidro">Barrica de Fibra de Vidro
                                    </option>
                                    <option value="Barrica de Plástico">Barrica de Plástico</option>
                                    <option value="Baú de Madeira">Baú de Madeira</option>
                                    <option value="Baú de Metal">Baú de Metal</option>
                                    <option value="Big Bag">Big Bag</option>
                                    <option value="Bloco">Bloco</option>
                                    <option value="Bobina">Bobina</option>
                                    <option value="Bombona">Bombona</option>
                                    <option value="Botijão">Botijão</option>
                                    <option value="Caixa de Isopor">Caixa de Isopor</option>
                                    <option value="Caixa de Madeira">Caixa de Madeira</option>
                                    <option value="Caixa de Metal">Caixa de Metal</option>
                                    <option value="Caixa de Papelão">Caixa de Papelão</option>
                                    <option value="Caixa de Plástico">Caixa de Plástico</option>
                                    <option value="Canudo">Canudo</option>
                                    <option value="Carretel">Carretel</option>
                                    <option value="Cilindro">Cilindro</option>
                                    <option value="Engradado de Madeira">Engradado de Madeira</option>
                                    <option value="Engradado de Plástico">Engradado de Plástico</option>
                                    <option value="Envelope">Envelope</option>
                                    <option value="Estrado">Estrado</option>
                                    <option value="Fardo">Fardo</option>
                                    <option value="Frasco">Frasco</option>
                                    <option value="Galão">Galão</option>
                                    <option value="Granel">Granel</option>
                                    <option value="Lata">Lata</option>
                                    <option value="Maleta">Maleta</option>
                                    <option value="Outros">Outros</option>
                                    <option value="Pacote">Pacote</option>
                                    <option value="Pallet">Pallet</option>
                                    <option value="Pallet de Madeira">Pallet de Madeira</option>
                                    <option value="Pallet de Plástico">Pallet de Plástico</option>
                                    <option value="Peça">Peça</option>
                                    <option value="Rolo">Rolo</option>
                                    <option value="Saco de Aniagem">Saco de Aniagem</option>
                                    <option value="Saco de Lona">Saco de Lona</option>
                                    <option value="Saco de Nylon">Saco de Nylon</option>
                                    <option value="Saco de Papel">Saco de Papel</option>
                                    <option value="Saco de Papelão">Saco de Papelão</option>
                                    <option value="Saco Plástico">Saco Plástico</option>
                                    <option value="Sacola">Sacola</option>
                                    <option value="San Bag">San Bag</option>
                                    <option value="Tambor de Metal">Tambor de Metal</option>
                                    <option value="Tambor de Papel">Tambor de Papel</option>
                                    <option value="Tambor de Plástico">Tambor de Plástico</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Quantidade</label>
                                <br clear="all">
                                <input type="number" class="form-control" placeholder="0" name="quantidadeCS1" required>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Dimensões (Comprimento) - [Centímetros]</label>
                                <input type="text" class="form-control" id="dim_hCS1" name="dim_hCS1"
                                    oninput="calculateCBM()">
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Dimensões (Largura) - [Centímetros]</label>
                                <input type="text" class="form-control" id="dim_lCS1" name="dim_lCS1"
                                    oninput="calculateCBM()">
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Dimensões (Altura) - [Centímetros]</label>
                                <input type="text" class="form-control" id="dim_cCS1" name="dim_cCS1"
                                    oninput="calculateCBM()">
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>CBM</label>
                                <input type="text" class="form-control" id="cbm" name="cbm"
                                    oninput="disableDimensions()">
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Resumo do Produto</label>
                                <br clear="all">
                                <textarea name="resumo_produto" class="form-control" id="resumo_produto"
                                    rows="2"></textarea>
                            </div>
                        </div>

                    </div>
                    <!-- FIM Linha -->

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">
                            <i class="fa fa-ban"></i>
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="modalProduto">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h4 class="modal-title">
                    <b>Incluir Produto</b>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formCadastrarProduto" method="post" action="ferramentas/grava_pv_produto.php">
                <div class="modal-body">
                    <input type="hidden" name="id_pv" value="<?php echo $idEstudo;?>">
                    <div class="form-group">
                        <label for="descricao">Descrição do Produto</label>
                        <input type="text" name="descricao_produto" placeholder="Digite a descrição resumida"
                            class="form-control" id="descricao" required>
                    </div>
                    <div class="form-group">
                        <label for="quantidade">Quantidade</label>
                        <input type="number" name="qt_produto" class="form-control" id="quantidade" required>
                    </div>
                    <div class="form-group">
                        <label for="peso">Peso Líquido (kg)</label>
                        <input type="text" class="form-control" name="peso_liquido" id="pesoLiquido" required>
                    </div>
                    <div class="form-group">
                        <label>Valor total do item de embarque
                            (<?=$dados['moeda_padrao'];?>)</label>
                        <input type="text" class="form-control" name="valor_total_item" id="valorTotalItem1">
                    </div>
                    <div class="form-group">
                        <label for="ncm">NCM</label>
                        <?php
                        $sql = "SELECT ncm FROM ncm WHERE CHAR_LENGTH(ncm) = 10 ORDER BY ncm ASC";
                        $stmt = $PDO->prepare($sql);
                        $stmt->execute();
                        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <select name="ncm" required class="form-control select2" style="width: 100%;">
                            <option value="" selected>Selecione</option>
                            <?php foreach ($resultados as $resultado): ?>
                            <option value="<?php echo $resultado['ncm'];?>">
                                <?php echo $resultado['ncm'];?>
                            </option>
                            <?php endforeach;?>
                        </select>
                        <div class="form-group" style="padding-top: 10px;">
                            <label for="icms">Alíquota ICMS (%)</label>
                            <input type="number" class="form-control" name="aliquota_icms" id="icms" value="18"
                                required>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">
                        <i class="fa fa-ban"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Salvar
                    </button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->


<script type="text/javascript">
window.addEventListener('DOMContentLoaded', function() {
    $('#modal-informacao').modal('show');
});

$(document).ready(function() {
    $('.input-group.date').datepicker({
        format: 'dd/mm/yyyy', // Formato da data
        autoclose: true, // Fechar automaticamente ao selecionar a data
        todayHighlight: true // Destaque a data de hoje
    });
});

function calculateCBM() {
    // Primeiro conjunto de campos
    let length1 = document.getElementById('dim_hCS1').value;
    let width1 = document.getElementById('dim_lCS1').value;
    let height1 = document.getElementById('dim_cCS1').value;

    if (length1 && width1 && height1) {
        let cbm1 = (length1 * width1 * height1) / 1000000; // Convertendo de cm³ para m³
        document.getElementById('cbm').value = cbm1.toFixed(3); // Exibir com 3 casas decimais
    }

    // Segundo conjunto de campos
    let length2 = document.getElementById('dim_hCS2').value;
    let width2 = document.getElementById('dim_lCS2').value;
    let height2 = document.getElementById('dim_cCS2').value;

    if (length2 && width2 && height2) {
        let cbm2 = (length2 * width2 * height2) / 1000000; // Convertendo de cm³ para m³
        document.getElementById('cbm2').value = cbm2.toFixed(3); // Exibir com 3 casas decimais
    }
}

function disableDimensions() {
    // Primeiro conjunto de campos
    let cbmField1 = document.getElementById('cbm').value;
    if (cbmField1) {
        document.getElementById('dim_hCS1').disabled = true;
        document.getElementById('dim_lCS1').disabled = true;
        document.getElementById('dim_cCS1').disabled = true;
    } else {
        document.getElementById('dim_hCS1').disabled = false;
        document.getElementById('dim_lCS1').disabled = false;
        document.getElementById('dim_cCS1').disabled = false;
    }

    // Segundo conjunto de campos
    let cbmField2 = document.getElementById('cbm2').value;
    if (cbmField2) {
        document.getElementById('dim_hCS2').disabled = true;
        document.getElementById('dim_lCS2').disabled = true;
        document.getElementById('dim_cCS2').disabled = true;
    } else {
        document.getElementById('dim_hCS2').disabled = false;
        document.getElementById('dim_lCS2').disabled = false;
        document.getElementById('dim_cCS2').disabled = false;
    }
}

// Função para mostrar ou esconder o campo de temperatura
function mostrarOcultarTemperatura() {
    var refrigeracaoSim = document.querySelector('input[name="refrigeracao"]:checked').value;
    var campoTemperatura = document.querySelector('input[name="temp_refrig"]');

    if (refrigeracaoSim === 'sim') {
        campoTemperatura.style.display = 'block';
    } else {
        campoTemperatura.style.display = 'none';
    }
}

function exibirEnderecoTN() {
    document.getElementById('enderecoTNDiv').style.display = 'block';
}

function ocultarEnderecoTN() {
    document.getElementById('enderecoTNDiv').style.display = 'none';
}

function confirmSubmit() {
    return confirm("Deseja realmente enviar aos AGENTES?"); // Exibe uma caixa de diálogo de confirmação
}

function mostrarOcultarSelect() {
    // Esconder todos os selects
    document.getElementById('aer').style.display = 'none';
    document.getElementById('mar').style.display = 'none';
    document.getElementById('rod').style.display = 'none';

    // Pegar o modal selecionado
    var modalSelecionado = document.getElementById('frm_brand').value;

    // Exibir apenas o select correspondente ao modal escolhido
    if (modalSelecionado) {
        document.getElementById(modalSelecionado).style.display = 'block';
    }
}

// Adiciona evento de mudança aos rádios de refrigeração
document.addEventListener("DOMContentLoaded", function() {
    var radios = document.querySelectorAll('input[name="refrigeracao"]');
    radios.forEach(function(radio) {
        radio.addEventListener('change', mostrarOcultarTemperatura);
    });

    // Oculta inicialmente o campo de temperatura
    document.querySelector('input[name="temp_refrig"]').style.display = 'none';

    // Executa a função para garantir que o estado inicial está correto
    mostrarOcultarTemperatura();
    ocultarEnderecoTN();
    mostrarOcultarSelect();
    confirmSubmit();
});

var currentDivId = 1;

function mostrarDiv() {
    var currentDiv = document.getElementById('row' + currentDivId);
    if (currentDiv) {
        currentDiv.style.display = 'block';
        currentDivId++;
    }
}

function ocultarDiv(id) {
    var div = document.getElementById(id);
    div.style.display = "none";
}

function mostrarOcultarTipoCarga() {
    var div1 = document.getElementById("div1");
    var div2 = document.getElementById("div2");

    if (document.getElementById("radioDiv1").checked) {
        div1.style.display = "block";
        div2.style.display = "none";
    } else {
        div1.style.display = "none";
        div2.style.display = "block";
    }

}

function mostrarOcultarVFI() {
    var vfiSim = document.getElementById('vfi_sim');
    var vfiNao = document.getElementById('vfi_nao');
    var valorTotalVFI = document.getElementById('valorTotalVFI');
    var valorTotalTaxas = document.getElementById('valorTotalTaxas');
    var valorTotalSeguro = document.getElementById('valorTotalSeguro');
    var mensagem = document.getElementById('mensagem');

    if (vfiNao.checked) {
        valorTotalVFI.disabled = true;
        valorTotalTaxas.disabled = true;
        valorTotalSeguro.disabled = true;
        valorTotalVFI.value = ''; // Limpar o valor quando desabilitado
        valorTotalTaxas.value = ''; // Limpar o valor quando desabilitado
        valorTotalSeguro.value = ''; // Limpar o valor quando desabilitado
        mensagem.innerHTML = 'Ao salvar você terá seu cálculo';
    } else {
        valorTotalVFI.disabled = false;
        valorTotalTaxas.disabled = false;
        valorTotalSeguro.disabled = false;
        mensagem.innerText = '';
    }
}

function mostrarOcultarArmazenagem() {
    var estimativaSim = document.getElementById('estimativaarm_sim');
    var estimativaNao = document.getElementById('estimativaarm_nao');
    var valorTotalArm = document.getElementById('valorTotalArm');

    if (estimativaNao.checked) {
        valorTotalArm.disabled = true;
        $('#modal-armazenagem').modal('show'); // Abrir a janela modal
    } else {
        valorTotalArm.disabled = false;
    }
}

function mostrarOcultarHD() {
    var sim = document.getElementById('estimativahd_sim');
    var nao = document.getElementById('estimativahd_nao');
    var campoHD = document.getElementById('valorTotalHD');

    if (sim.checked) {
        campoHD.removeAttribute('readonly');
        campoHD.value = '';
    } else if (nao.checked) {
        campoHD.setAttribute('readonly', 'readonly');
        campoHD.value = '950,00';
    }
}

function habilitarQtConteiner() {
    var tipoCarga = document.getElementById('tc_mar').value;
    var qtConteiner = document.getElementById('qtConteiner');

    if (tipoCarga === 'container_20' || tipoCarga === 'container_40') {
        qtConteiner.removeAttribute('disabled');
    } else {
        qtConteiner.setAttribute('disabled', 'disabled');
        qtConteiner.value = ''; // Clear the field if not needed
    }
}

// Chamando a função ao carregar a página para garantir a configuração inicial correta
document.addEventListener('DOMContentLoaded', function() {
    mostrarOcultarSelect();
    mostrarOcultarVFI();
    mostrarOcultarArmazenagem();
    mostrarOcultarHD();
    habilitarQtConteiner();
    // Initialize fields based on default selection
    toggleFields();
});

function removerNaoNumericos() {
    var campo = document.getElementById("valor_total");
    var campo2 = document.getElementById("valor_total_c");
    campo.value = campo.value.replace(/\D/g, "");
    campo2.value = campo2.value.replace(/\D/g, "");
}

function toggleFields() {
    const container20 = document.getElementById('container_20').checked;
    const container40 = document.getElementById('container_40').checked;
    const cargaSol = document.getElementById('carga_sol').checked;

    document.getElementById('qtde_container').disabled = !(container20 || container40);
    document.getElementById('peso_bruto').disabled = !cargaSol;
}

// Exemplo de uso: chamando a função ao digitar
var campo = document.getElementById("valor_total");
var campo2 = document.getElementById("valor_total_c");
campo.addEventListener("input", removerNaoNumericos);
campo2.addEventListener("input", removerNaoNumericos);
</script>