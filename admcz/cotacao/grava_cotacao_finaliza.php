<?php
session_start();

// inclui o arquivo de inicialização
require '../functions/globals.php';

$idCotacao = $_POST['idCotacao'] ? $_POST['idCotacao'] : null;
if (empty($idCotacao)) {
    unset($_SESSION['etapa']);
    header('Location: ../frete.php?a=frete&b=frete_cotacao&flag=erro&tip=Cadastre as informações de ABETURA primeiramente.');
    exit();
}

$PDOVerificaCot = db_connect();

$sqlCA = "SELECT * FROM cotacao_abertura WHERE id = :idCotacao";
$stmtCA = $PDOVerificaCot->prepare($sqlCA);
$stmtCA->bindParam(':idCotacao', $idCotacao, PDO::PARAM_STR);
$stmtCA->execute();
$dadosCA = [];
$dadosCA = $stmtCA->fetchAll(PDO::FETCH_ASSOC);
if (count($dadosCA) == 0) {
    $_SESSION['etapa'] = 1;
    header('Location: ../frete.php?a=frete&b=frete_cotacao&flag=erro&tip=Cadastre as informações de ABETURA primeiramente.');
    exit;
}

$sqlCO = "SELECT * FROM cotacao_origem WHERE id_cotacao = :idCotacao";
$stmtCO = $PDOVerificaCot->prepare($sqlCO);
$stmtCO->bindParam(':idCotacao', $idCotacao, PDO::PARAM_STR);
$stmtCO->execute();
$dadosCO = [];
$dadosCO = $stmtCO->fetchAll(PDO::FETCH_ASSOC);
if (count($dadosCO) == 0) {
    $_SESSION['etapa'] = 2;
    header('Location: ../frete.php?a=frete&b=frete_cotacao&flag=erro&tip=Cadastre as informações de ORIGEM primeiramente.');
    exit;
}

$sqlCD = "SELECT * FROM cotacao_destino WHERE id_cotacao = :idCotacao";
$stmtCD = $PDOVerificaCot->prepare($sqlCD);
$stmtCD->bindParam(':idCotacao', $idCotacao, PDO::PARAM_STR);
$stmtCD->execute();
$dadosCD = [];
$dadosCD = $stmtCD->fetchAll(PDO::FETCH_ASSOC);
if (count($dadosCD) == 0) {
    $_SESSION['etapa'] = 3;
    header('Location: ../frete.php?a=frete&b=frete_cotacao&flag=erro&tip=Cadastre as informações de DESTINO primeiramente.');
    exit;
}

$sqlCC = "SELECT * FROM cotacao_carga WHERE id_cotacao = :idCotacao";
$stmtCC = $PDOVerificaCot->prepare($sqlCC);
$stmtCC->bindParam(':idCotacao', $idCotacao, PDO::PARAM_STR);
$stmtCC->execute();
$dadosCC = [];
$dadosCC = $stmtCC->fetchAll(PDO::FETCH_ASSOC);
if (count($dadosCC) == 0) {
    $_SESSION['etapa'] = 4;
    header('Location: ../frete.php?a=frete&b=frete_cotacao&flag=erro&tip=Cadastre as informações de CARGA primeiramente.');
    exit;
}

$sqlCM = "SELECT * FROM cotacao_mercadoria WHERE id_cotacao = :idCotacao";
$stmtCM = $PDOVerificaCot->prepare($sqlCM);
$stmtCM->bindParam(':idCotacao', $idCotacao, PDO::PARAM_STR);
$stmtCM->execute();
$dadosCM = [];
$dadosCM = $stmtCM->fetchAll(PDO::FETCH_ASSOC);
if (count($dadosCM) == 0) {
    $_SESSION['etapa'] = 5;
    header('Location: ../frete.php?a=frete&b=frete_cotacao&flag=erro&tip=Cadastre as informações de MERCADORIA primeiramente.');
    exit;
}

$uploadDir = '../arquivos/uploads/';
$allowedExtensions = array('pdf', 'jpg', 'jpeg', 'png');
$fileName = $_FILES['arquivo']['name'];
if (!empty($fileName)) {
    $fileTmp = $_FILES['arquivo']['tmp_name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Verifica a extensão do arquivo
    if (in_array($fileExt, $allowedExtensions)) {
        $newFileName = uniqid() . '.' . $fileExt; // Gera um nome único para o arquivo

        $uploadPath = $uploadDir . $newFileName;

        // Move o arquivo temporário para o diretório de destino com o novo nome
        if (move_uploaded_file($fileTmp, $uploadPath)) {
            echo 'Arquivo enviado com sucesso!';
        } else {
            echo 'Erro ao enviar o arquivo.';
        }
    } else {
        $_SESSION['etapa'] = 6;
        header('Location: ../frete.php?a=frete&b=frete_cotacao&flag=erro&tip=Apenas arquivos PDF, JPG, JPEG e PNG são permitidos.');
        exit;
    }
    $arquivo = $newFileName;
} else {
    $arquivo = '';
}

$obs = isset($_POST['obs']) ? $_POST['obs'] : null;
$embarque_previsto = isset($_POST['embarque_previsto']) ? $_POST['embarque_previsto'] : null;
$despacho_aduaneiro = isset($_POST['despacho_aduaneiro']) ? $_POST['despacho_aduaneiro'] : null;
$contratacao_seguro = isset($_POST['contratacao_seguro']) ? $_POST['contratacao_seguro'] : null;
$carga_pronta = isset($_POST['carga_pronta']) ? $_POST['carga_pronta'] : null;
$contratacao_transporte = isset($_POST['contratacao_transporte']) ? $_POST['contratacao_transporte'] : null;
$endereco_tn = isset($_POST['endereco_tn']) ? $_POST['endereco_tn'] : null;
$moeda_allin = isset($_POST['moeda_allin']) ? $_POST['moeda_allin'] : null;
$aceita_termos = isset($_POST['aceitarTermos']) ? $_POST['aceitarTermos'] : null;

$data_fim = date('d/m/Y');
$hora_fim = date('H:i:s');

// insere no banco ABERTURA cotação
$PDO = db_connect();
$sql = "INSERT INTO cotacao_complemento
					(id_cotacao, arquivo, obs, embarque_previsto, despacho_aduaneiro, contratacao_seguro, contratacao_transporte, endereco_entrega, aceita_termos, moeda_allin, carga_pronta)
        VALUES 		(:idCotacao, :arquivo, :obs, :embarque_previsto, :despacho_aduaneiro, :contratacao_seguro, :contratacao_transporte, :endereco_entrega, :aceita_termos, :moeda_allin, :carga_pronta)";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':idCotacao', $idCotacao);
$stmt->bindParam(':arquivo', $arquivo);
$stmt->bindParam(':obs', $obs);
$stmt->bindParam(':embarque_previsto', $embarque_previsto);
$stmt->bindParam(':despacho_aduaneiro', $despacho_aduaneiro);
$stmt->bindParam(':contratacao_seguro', $contratacao_seguro);
$stmt->bindParam(':contratacao_transporte', $contratacao_transporte);
$stmt->bindParam(':endereco_entrega', $endereco_tn);
$stmt->bindParam(':aceita_termos', $aceita_termos);
$stmt->bindParam(':moeda_allin', $moeda_allin);
$stmt->bindParam(':carga_pronta', $carga_pronta);

if ($stmt->execute()) {
    $PDO = db_connect();
    $sql2 = "UPDATE cotacao_abertura SET finalizada = 1, id_status_situacao_atual_cliente = 1 WHERE id = :idCotacao";
    $stmt2 = $PDO->prepare($sql2);
    $stmt2->bindParam(':idCotacao', $idCotacao);
    $stmt2->execute();

    //enviar emails para os interessados
    $idUsuario = $_SESSION['user_id'];
    $idEmpresa = $_SESSION['empresa_id'];
    $protocolo = $_SESSION['protocolo'];

    $sqlDadosUser = "SELECT * FROM usuario WHERE id = :idUsuario";
    $stmtDU = $PDO->prepare($sqlDadosUser);
    $stmtDU->bindParam(':idUsuario', $idUsuario);
    $stmtDU->execute();
    $dadosUsuario = $stmtDU->fetchAll(PDO::FETCH_ASSOC);

    $sqlDadosEmpresa = "SELECT * FROM empresa WHERE id_empresa = :idEmpresa";
    $stmtDE = $PDO->prepare($sqlDadosEmpresa);
    $stmtDE->bindParam(':idEmpresa', $idEmpresa);
    $stmtDE->execute();
    $dadosEmpresa = $stmtDE->fetchAll(PDO::FETCH_ASSOC);

    $nomeUsuario = $dadosUsuario[0]['nome'];
    $emailUsuario = $dadosUsuario[0]['email'];
    $nomeEmpresa = $dadosEmpresa[0]['nome_empresa'];
    $emailEmpresa = $dadosEmpresa[0]['email_principal'];

    $embarque_previsto = dateConvert($embarque_previsto);

    //require "envio_email_cliente_finaliza.php";
    //require "envio_email_agentes_finaliza.php";

    // Libera a session de cotação corrente
    unset($_SESSION['idCotacao']);

    // Libera o cookie de cotaçção corrente
    unset($_COOKIE['idCotacao']);

    unset($_SESSION['etapa']);

    header(
        'Location: ../frete.php?a=frete&b=frete_listar&flag=success&tip=Cotação FINALIZADA com sucesso!'
    );
} else {
    $errorInfo = $stmt->errorInfo();
    //echo "Erro: " . $errorInfo[2];
    //die;
    $_SESSION['etapa'] = 1;
    header('Location: ../frete.php?a=frete&b=frete_cotacao&flag=erro&tip=Não foi possível cadastrar. Erro: ' . $errorInfo[2]);
    exit;
}