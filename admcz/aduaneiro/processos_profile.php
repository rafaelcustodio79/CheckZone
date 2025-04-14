<?php
$idProcesso = isset($_GET['idProcesso']) ? intval($_GET['idProcesso']) : null;
$idEmpresa = isset($_GET['idEmpresa']) ? intval($_GET['idEmpresa']) : null;

if ($idProcesso=== null && $idEmpresa === null) {
    // ID inválido, redirecionar ou exibir mensagem de erro
    echo "ID inválido.";
    exit;
}

$PDO = db_connect();

$sql = "SELECT pro.*, emp.*, pat.*, proi.*, usu.nome AS gerenteConta 
        FROM processos pro
        INNER JOIN processos_aux_tipos pat ON pro.id_tipo_processo = pat.id_tipo_processo
        INNER JOIN empresa emp ON pro.id_empresa = emp.id_empresa
        INNER JOIN usuario usu ON emp.id_gerente_conta = usu.id
        LEFT JOIN processos_informacoes proi ON pro.id_processo = proi.id_processo
        WHERE pro.id_processo = :idProcesso AND emp.id_empresa = :idEmpresa";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':idProcesso', $idProcesso);
$stmt->bindParam(':idEmpresa', $idEmpresa);
$stmt->execute();
$resultados = $stmt->fetch(PDO::FETCH_ASSOC);

$folderName = $resultados['ref_calvet'].'='.$resultados['ref_cliente'];

$anoCorrente = date("Y");

$sqlDrive = "SELECT * FROM pasta_drive WHERE id_empresa = :id_empresa AND ano = :ano";
$stmtDrive = $PDO->prepare($sqlDrive);
$stmtDrive->bindParam(':id_empresa', $idEmpresa, PDO::PARAM_INT);
$stmtDrive->bindParam(':ano', $anoCorrente, PDO::PARAM_INT);
$stmtDrive->execute();
$resultadosDrive = $stmtDrive->fetch(PDO::FETCH_ASSOC);
$idPastaRaiz = $resultadosDrive['id_pasta_drive'];

#Workflow
// Primeira consulta: Buscar processos_workflow
$sqlWF = "SELECT * FROM processos_workflow pwf
        INNER JOIN processos_aux_workflow paw ON pwf.id_aux_wf = paw.id_aux_wf
        INNER JOIN usuario usu ON pwf.id_usuario = usu.id
        WHERE pwf.id_processo = :idProcesso AND pwf.id_empresa = :idEmpresa
        ORDER BY pwf.id_wf DESC";
$stmtWF = $PDO->prepare($sqlWF);
$stmtWF->bindParam(':idProcesso', $idProcesso, PDO::PARAM_INT);
$stmtWF->bindParam(':idEmpresa', $idEmpresa, PDO::PARAM_INT);
$stmtWF->execute();
$resultadosWF = $stmtWF->fetchAll(PDO::FETCH_ASSOC);

#Comentários
// Primeira consulta: Buscar processos_workflow
$sqlComent = "SELECT * FROM processos_comentarios pc
        INNER JOIN usuario usu ON pc.id_usuario = usu.id
        WHERE pc.id_processo = :idProcesso AND pc.id_empresa = :idEmpresa
        ORDER BY pc.data_hora ASC";
$stmtComent = $PDO->prepare($sqlComent);
$stmtComent->bindParam(':idProcesso', $idProcesso, PDO::PARAM_INT);
$stmtComent->bindParam(':idEmpresa', $idEmpresa, PDO::PARAM_INT);
$stmtComent->execute();
$resultadosComent = $stmtComent->fetchAll(PDO::FETCH_ASSOC);

#Rastreabilidade
$sqlR = "SELECT * FROM processos_rastreabilidade pror
        INNER JOIN empresa emp ON pror.id_empresa = emp.id_empresa
        INNER JOIN usuario usu ON pror.id_usuario = usu.id
        WHERE pror.id_empresa = :idEmpresa AND pror.id_processo = :idProcesso
        ORDER BY pror.data_hora DESC";
$stmtR = $PDO->prepare($sqlR);
$stmtR->bindParam(':idEmpresa', $idEmpresa, PDO::PARAM_INT);
$stmtR->bindParam(':idProcesso', $idProcesso, PDO::PARAM_INT);
$stmtR->execute();
$resultadosR = $stmtR->fetchAll(PDO::FETCH_ASSOC);

// Unir as duas consultas com UNION ALL e ordenar por data e hora separadamente
$sqlUnificado = "
    SELECT 'Workflow' AS origem, pwf.id_wf AS id, usu.nome AS usuario,
           DATE(pwf.data_add) AS data, TIME(pwf.data_add) AS hora, 
           paw.titulo_wf AS titulo, NULL AS comentario,
           pwf.data_acao AS dataAcao
    FROM processos_workflow pwf
    INNER JOIN processos_aux_workflow paw ON pwf.id_aux_wf = paw.id_aux_wf
    INNER JOIN usuario usu ON pwf.id_usuario = usu.id
    WHERE pwf.id_processo = :idProcesso AND pwf.id_empresa = :idEmpresa
    
    UNION ALL
    
    SELECT 'Comentário' AS origem, pc.id_comentario AS id, usu.nome AS usuario, 
           DATE(pc.data_hora) AS data, TIME(pc.data_hora) AS hora, 
           NULL AS titulo, pc.comentario,
           NULL AS dataAcao
    FROM processos_comentarios pc
    INNER JOIN usuario usu ON pc.id_usuario = usu.id
    WHERE pc.id_processo = :idProcesso AND pc.id_empresa = :idEmpresa

    ORDER BY data DESC, hora DESC
";
$stmtUnificado = $PDO->prepare($sqlUnificado);
$stmtUnificado->bindParam(':idProcesso', $idProcesso, PDO::PARAM_INT);
$stmtUnificado->bindParam(':idEmpresa', $idEmpresa, PDO::PARAM_INT);
$stmtUnificado->execute();
$resultadosUnificados = $stmtUnificado->fetchAll(PDO::FETCH_ASSOC);


?>
<style type="text/css">
.card-primary:not(.card-outline)>.card-header {
    background-color: #046868;
    color: #fff;
}

.card-primary.card-outline {
    border-top: 3px solid #046868;
}

.nav-pills .nav-link.active,
.nav-pills .show>.nav-link {
    color: #fff;
    background-color: #046868;
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
                <h1>Aduaneiro</h1>
                <small>Processo Profile</small>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php"> Home</a></li>
                    <li class="breadcrumb-item">Aduaneiro</a></li>
                    <li class="breadcrumb-item active">Processo Profile</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">

                <!-- Profile Image -->
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <?php
                            if(empty($resultados['logo'])) {
                                $fotoAssociado = 'img/logos/sem-foto.jpg';
                            } else {
                                $fotoAssociado = 'img/logos/'.$resultados['logo'];
                            }
?>
                            <img class="profile-user-img img-fluid img-circle" src="<?=$fotoAssociado;?>"
                                alt="Foto Associado">
                        </div>

                        <h3 class="profile-username text-center">
                            <?=$resultados['nome_empresa'];?>
                        </h3>

                        <p class="text-muted text-center">
                            na plataforma desde<br>
                            <b><?=dateConvert($resultados['data_add']);?></b>
                        </p>

                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>Tipo de Empresa</b>
                                <a class="float-right">
                                    <?php if($resultados['id_tipo_empresa']==1) : ?>
                                    Administrador
                                    <?php else : ?>
                                    Cliente
                                    <?php endif;?>
                                </a>
                            </li>
                            <li class="list-group-item">
                                <b>Segmento</b> <a class="float-right"><?=$resultados['segmento_empresa'];?></a>
                            </li>
                            <li class="list-group-item">
                                <b>Gerente de conta</b> <a class="float-right"><?=$resultados['gerenteConta'];?></a>
                            </li>
                        </ul>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->

                <!-- About Me Box -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Dados Gerais</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <strong><i class="far fa-address-card mr-1"></i> CNPJ</strong>

                        <p class="text-muted">
                            <?=$resultados['cnpj'];?>
                        </p>

                        <hr>

                        <strong><i class="fas fa-map-marker-alt mr-1"></i> Localização</strong>

                        <p class="text-muted">
                            <?=$resultados['localizacao'];?><br>
                        </p>

                        <hr>

                        <strong><i class="fas fa-user-tie mr-1"></i> Website</strong>

                        <p class="text-muted">
                            <?=$resultados['website'];?>
                        </p>

                        <hr>

                        <strong><i class="fas fa-address-book mr-"></i> Contatos</strong>

                        <p class="text-muted">
                            <i class="fas fa-phone"></i>
                            <?=$resultados['telefone_empresa'];?><br>
                            <i class="fas fa-envelope"></i>
                            <?=$resultados['email_principal'];?>
                        </p>
                        <?php 
                        $idPastaDrive = $resultados['id_pasta_drive'];?>

                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
            <div class="col-md-9">
                <p>
                    <i class="fas fa-user-clock"></i> Último evento registrado:&nbsp;&nbsp;
                    <span class="badge badge-evento">
                        <?php if ($resultadosWF) : ?>
                        <?=$resultadosWF[0]['titulo_wf'];?>
                        <?php else :?>
                        ---
                        <?php endif;?>
                    </span>
                </p>
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a class="nav-link active" href="#informacoes" data-toggle="tab"><i
                                        class="fas fa-info-circle"></i> Informações</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#workflow" data-toggle="tab"><i class="fas fa-history"></i>
                                    Workflow</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#followup" data-toggle="tab"><i
                                        class="fas fa-hourglass-half"></i> FollowUp</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#honorarios" data-toggle="tab"><i
                                        class="fas fa-money-bill"></i> Honorários</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#rastreabilidade" data-toggle="tab"><i
                                        class="fas fa-shoe-prints"></i> Rastreabilidade</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#documentos" data-toggle="tab"><i
                                        class="fab fa-google-drive"></i> Documentos</a>
                            </li>
                        </ul>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">

                            <div class="active tab-pane" id="informacoes">
                                <p>
                                    Ref. Cliente: <strong
                                        class="badge badge-info"><?=$resultados['ref_cliente'];?></strong>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    Ref. Calvet: <strong
                                        class="badge badge-ref"><?=$resultados['ref_calvet'];?></strong>
                                </p>

                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Detalhes da
                                            <strong><?=$resultados['titulo_tipo'];?></strong>
                                        </h5>
                                    </div>

                                    <div class="card-body">
                                        <div class="row align-items-center origem-destino">
                                            <div class="col-md-4 text-center">
                                                <span class="flag-icon">
                                                    <img src="img/flags/<?=$resultados['pais_origem'];?>.png"
                                                        style="width: 40px;">
                                                </span>
                                                <span class="country"><?=$resultados['pais_origem'];?></span>
                                            </div>

                                            <!-- Ícone de Transporte e Linha de Conexão -->
                                            <div class="col-md-4 text-center">
                                                <?php 
                                                switch ($resultados['id_tipo_processo']) {
                                                    case 1:
                                                    case 4:
                                                        $transporte = 'plane';
                                                        break;
                                                    case 2:
                                                    case 5:
                                                        $transporte = 'ship';
                                                        break;
                                                    case 3:
                                                    case 6:
                                                        $transporte = 'truck';
                                                        break;
                                                    default:
                                                        $transporte = 'unknown'; // Caso queira ter uma opção padrão
                                                        break;
                                                }                                                
                                                ?>
                                                <i class="fas fa-<?=$transporte;?> transport-icon"></i>
                                                <br>
                                                <strong><?=$resultados['incoterms'];?></strong>
                                                <small>
                                                    <sup>
                                                        <i class="far fa-question-circle" title="Incoterm"></i>
                                                    </sup>
                                                </small>
                                            </div>

                                            <div class="col-md-4 text-center">
                                                <span class="flag-icon">
                                                    <img src="img/flags/<?=$resultados['pais_destino'];?>.png"
                                                        style="width: 40px;">
                                                </span>
                                                <span class="country"><?=$resultados['pais_destino'];?></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 text-center"><strong>Porto Descarga:</strong><br>
                                                <?=$resultados['porto_descarga'];?>
                                            </div>
                                            <div class="col-md-4 text-center"><strong>Porto Destino:</strong><br>
                                                <?=$resultados['porto_destino'];?>
                                            </div>
                                            <div class="col-md-4 text-center"><strong>Terminal Descarga:</strong><br>
                                                <?=$resultados['terminal_descarga'];?>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Informações do Desembaraço</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4"><strong>Nº LI:</strong> <?=$resultados['n_li'];?>
                                            </div>
                                            <div class="col-md-8"><strong>Status LI:</strong>
                                                <?=$resultados['status_li'];?></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4"><strong>Nº DI:</strong> <?=$resultados['n_di'];?>
                                            </div>
                                            <div class="col-md-4"><strong>Data registro DI:</strong>
                                                <?= isset($resultados['registro_di']) && $resultados['registro_di'] ? dateConvert($resultados['registro_di']) : "Sem data registrada"; ?>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Parametrização:</strong>
                                                <?php if ($resultados['parametrizacao']=='Canal Verde') : ?>
                                                <span class="badge badge-success">Canal Verde</span>
                                                <?php elseif ($resultados['parametrizacao']=='Canal Vermelho') : ?>
                                                <span class="badge badge-danger">Canal Vermelho</span>
                                                <?php elseif (($resultados['parametrizacao']=='Canal Amarelo')) : ?>
                                                <span class="badge badge-warning">Canal Amarelo</span>
                                                <?php elseif (($resultados['parametrizacao']=='Canal Cinza')) : ?>
                                                <span class="badge badge-info">Canal Cinza</span>
                                                <?php else :?>
                                                <span class="badge badge-default">Não informado</span>
                                                <?php endif;?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12"><strong>Observações:</strong>
                                                <?=$resultados['observacoes'];?></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Informações de Carga</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12"><strong>Descrição Produto:</strong>
                                                <?=$resultados['descricao_produto'];?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-8"><strong>Exportador:</strong>
                                                <?=$resultados['exportador'];?>
                                            </div>
                                            <div class="col-md-4"><strong>Nº da Invoice:</strong>
                                                <?=$resultados['n_invoice'];?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-8"><strong>Carga:</strong> <?=$resultados['carga'];?>
                                            </div>
                                            <div class="col-md-4"><strong>Quantidade:</strong>
                                                <?=$resultados['quantidade'];?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-8"><strong>Nome do navio:</strong>
                                                <?=$resultados['nome_navio'];?></div>
                                            <div class="col-md-4"><strong>BL:</strong> <?=$resultados['bl'];?></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4"><strong>ETA:</strong><br>
                                                <?= isset($resultados['eta']) && $resultados['eta'] ? dateConvert($resultados['eta']) : "Sem data confirmada"; ?>
                                            </div>
                                            <div class="col-md-4"><strong>Confirmação de Chegada:</strong><br>
                                                <?= isset($resultados['confirmacao_chegada']) && $resultados['confirmacao_chegada'] ? dateConvert($resultados['confirmacao_chegada']) : "Sem data confirmada"; ?>
                                            </div>
                                            <div class="col-md-4"><strong>Vencimento da armazenagem:</strong><br>
                                                <?= isset($resultados['vencimento_armazenagem']) && $resultados['vencimento_armazenagem'] ? dateConvert($resultados['vencimento_armazenagem']) : "Sem data"; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>

                            <div class="tab-pane" id="workflow">
                                <p>Histórco / Workflow</p>
                                <table id="listaWorkflow" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Data Vigente</th>
                                            <th>Ação realizada</th>
                                            <th>Registrado por</th>
                                            <th>Registrado em</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($resultadosWF as $wf) : ?>
                                        <tr>
                                            <td>
                                                <?=dateConvert($wf['data_acao']);?></td>
                                            <td>
                                                <span class="badge badge-info">
                                                    <?=$wf['titulo_wf'];?>
                                                </span>
                                            </td>
                                            <td><?=$wf['nome'];?></td>
                                            <td><?=dateConvert($wf['data_add']);?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="tab-pane" id="followup">

                                <!-- Timelime example  -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- The time line -->
                                        <div class="timeline">
                                            <?php 
    $dataAnterior = null;
    foreach ($resultadosUnificados as $resultadoU) : 
        // Converte a data para o formato desejado
        $dataAtual = dateConvert($resultadoU['data']);

        // Exibe a data apenas se for diferente da data anterior
        if ($dataAtual !== $dataAnterior): 
    ?>
                                            <!-- timeline time label -->
                                            <div class="time-label">
                                                <span
                                                    style="background-color: #046868; color:#FFF;"><?= $dataAtual; ?></span>
                                            </div>
                                            <!-- /.timeline-label -->
                                            <?php 
            // Atualiza a dataAnterior para a nova data
            $dataAnterior = $dataAtual;
        endif; 
        ?>

                                            <!-- timeline item -->
                                            <div>
                                                <?php if($resultadoU['origem'] == 'Comentário') : ?>
                                                <i class="fas fa-comments bg-icone"></i>
                                                <?php else : ?>
                                                <i class="fas fa-history bg-info"></i>
                                                <?php endif; ?>

                                                <div class="timeline-item">
                                                    <span class="time"><i class="fas fa-clock"></i>
                                                        <?= $resultadoU['hora']; ?></span>
                                                    <h3 class="timeline-header">
                                                        <?= htmlspecialchars($resultadoU['origem']); ?></h3>
                                                    <div class="timeline-body">
                                                        <?php if($resultadoU['origem'] == 'Comentário') : ?>
                                                        <?= $resultadoU['comentario']; ?>
                                                        <small>Registrado por: <a
                                                                href="#"><?= $resultadoU['usuario']; ?></a></small>
                                                        <?php else : ?>
                                                        <strong><?= dateConvert($resultadoU['dataAcao']); ?></strong> -
                                                        <?= htmlspecialchars($resultadoU['titulo']); ?>
                                                        <br>
                                                        <small>Registrado por: <a
                                                                href="#"><?= $resultadoU['usuario']; ?></a></small>
                                                        <?php endif;?>

                                                    </div>
                                                </div>
                                            </div>
                                            <!-- END timeline item -->
                                            <?php endforeach; ?>
                                        </div>

                                    </div>
                                    <!-- /.col -->
                                </div>

                            </div>

                            <div class="tab-pane" id="honorarios">

                                <table id="listaBanners" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Serviço</th>
                                            <th class="text-center">Forma de cálculo (Tipo)</th>
                                            <th class="text-center">Forma de cálculo (Valor)</th>
                                            <th class="text-center">Valor do Serviço</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT * FROM honorarios_aux_servicos
                                        WHERE id_empresa = :idEmpresa
                                        ORDER BY servico ASC";
                                        $stmt = $PDO->prepare($sql);
                                        $stmt->bindParam(':idEmpresa', $idEmpresa);
                                        $stmt->execute();
                                        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                        $sql = "SELECT * FROM empresa emp
                                                LEFT JOIN honorarios hon ON emp.id_empresa = hon.id_empresa
                                                WHERE emp.id_empresa = :idEmpresa
                                                ORDER BY emp.id_empresa DESC";
                                        $stmt = $PDO->prepare($sql);
                                        $stmt->bindParam(':idEmpresa', $idEmpresa);
                                        $stmt->execute();
                                        $resultadosHon = $stmt->fetch(PDO::FETCH_ASSOC);
                                        ?>
                                        <?php foreach ($resultados as $resultado) : ?>
                                        <tr>
                                            <td>
                                                <?= $resultado['servico']; ?>
                                            </td>
                                            <td class="text-center">
                                                <?= $resultado['fc_tipo']; ?>
                                            </td>
                                            <td class="text-center">
                                                <?= number_format($resultado['fc_valor'], 2, ',', '.'); ?>
                                            </td>
                                            <td class="text-right">
                                                R$
                                                <?= number_format($resultado['valor_servico'], 2, ',', '.'); ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <tr class="fundo-celula-1">
                                            <td colspan="3" class="text-right">
                                                <strong>HONORÁRIO</strong>
                                            </td>
                                            <td class="text-right">
                                                <strong>
                                                    R$
                                                    <?= number_format($resultadosHon['valor_honorarios'] ?? 0, 2, ',', '.'); ?>

                                                </strong>
                                            </td>
                                        </tr>
                                        <tr class="fundo-celula-2">
                                            <td colspan="3" class="text-right">
                                                <strong>ISS</strong>
                                            </td>
                                            <td class="text-right">
                                                <?php
                                $iss = $resultadosHon['valor_honorarios'] * 0.05;
?>
                                                <strong>
                                                    R$
                                                    <?= number_format($iss, 2, ',', '.'); ?>
                                                </strong>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>

                            </div>

                            <div class="tab-pane" id="rastreabilidade">
                                <p>Rastreabilidade</p>
                                <table id="listaRastreabilidade" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tipo de atividade</th>
                                            <th>Registrado em</th>
                                            <th>Registrado por</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($resultadosR as $rastreabilidade) : ?>
                                        <tr>
                                            <td><?=$rastreabilidade['local'];?></td>
                                            <td><?=dateConvert($rastreabilidade['data_hora']);?></td>
                                            <td><?=$rastreabilidade['nome'];?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="tab-pane" id="documentos">
                                <p>Documentos do Google Drive</p>
                                <?php
    $apiKey = $googleApiKey;

    $parentFolderId = $idPastaRaiz;

    // Buscando a pasta pelo nome dentro da pasta raiz
    $url = "https://www.googleapis.com/drive/v3/files?q=name='" . urlencode($folderName) . "'%20and%20mimeType='application/vnd.google-apps.folder'%20and%20'$parentFolderId'%20in%20parents&key=" . $apiKey . "&fields=files(id,name,mimeType,webViewLink)";
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    // Se a pasta for encontrada, buscar os arquivos nela
    if (isset($data['files'][0])) {
        $folderId = $data['files'][0]['id']; // ID da pasta encontrada
        $folderData = file_get_contents("https://www.googleapis.com/drive/v3/files?q='$folderId'%20in%20parents&key=" . $apiKey . "&fields=files(id,name,mimeType,webViewLink)");
        $folderContent = json_decode($folderData, true);
    }
?>

                                <div class="container mt-4">
                                    <div class="row">
                                        <?php if (isset($folderContent['files'])): ?>
                                        <?php foreach ($folderContent['files'] as $file): ?>
                                        <?php
            $fileName = htmlspecialchars($file['name']);
            $fileId = $file['id'];
            $fileLink = htmlspecialchars($file['webViewLink']) . "?usp=sharing";
            $isFolder = $file['mimeType'] === 'application/vnd.google-apps.folder';
            $icon = $isFolder ? 'fas fa-folder' : 'fas fa-file';
            $target = 'target="_blank"';
        ?>

                                        <div class="col-md-3 mb-4">
                                            <a href="<?php echo $fileLink; ?>" <?php echo $target; ?>
                                                class="file-block d-block text-decoration-none">
                                                <div class="file-icon text-center">
                                                    <i class="<?php echo $icon; ?> fa-3x text-secondary"></i>
                                                </div>
                                                <div class="file-name text-center mt-2">
                                                    <?php echo $fileName; ?>
                                                </div>
                                            </a>
                                        </div>
                                        <?php endforeach; ?>
                                        <?php else: ?>
                                        <p>Nenhum arquivo ou pasta encontrado na pasta "<?php echo $folderName; ?>".</p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <style>
                                .file-block {
                                    padding: 10px;
                                    background-color: #f8f9fa;
                                    border: 1px solid #e9ecef;
                                    border-radius: 8px;
                                    transition: background-color 0.3s;
                                }

                                .file-block:hover {
                                    background-color: #e2e6ea;
                                }

                                .file-icon i {
                                    color: #5f6368;
                                }

                                .file-name {
                                    font-size: 14px;
                                    color: #202124;
                                    white-space: nowrap;
                                    overflow: hidden;
                                    text-overflow: ellipsis;
                                }
                                </style>

                            </div>


                        </div>
                        <!-- /.tab-content -->
                    </div><!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<!-- PDF html2pdf -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>

<script>
var printButtons = document.querySelectorAll('.btnImprimir');

printButtons.forEach(function(button) {
    button.addEventListener('click', function(event) {
        // Impede que o link navegue para outra página
        event.preventDefault();

        // Seleciona a carteirinha mais próxima do botão clicado
        var card = button.parentElement.previousElementSibling.querySelector('.card-virtual');

        // Configurações para o PDF
        var opt = {
            margin: 1,
            filename: 'carteirinha_virtual.pdf',
            image: {
                type: 'jpeg',
                quality: 0.98
            },
            html2canvas: {
                scale: 2
            },
            jsPDF: {
                unit: 'in',
                format: 'A4',
                orientation: 'portrait'
            }
        };

        // Gera o PDF para a carteirinha correspondente
        html2pdf().from(card).set(opt).save();
    });
});
</script>