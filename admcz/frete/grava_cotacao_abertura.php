<?php
session_start();

// inclui o arquivo de inicialização
require '../functions/globals.php';

$idUsuario = $_SESSION['user_id'];
$idEmpresa = $_SESSION['empresa_id'];
$num_proc_logix = date('Ymd') . "-" . date('His') . "/" . $idUsuario;
$num_proc_interno = isset($_POST['proc_interno']) ? $_POST['proc_interno'] : null;
$modal = isset($_POST['modal']) ? $_POST['modal'] : null;
if ($modal === 'aer') {
	$incoterms = isset($_POST['incoterms-aer']) ? $_POST['incoterms-aer'] : null;
} elseif ($modal === 'mar') {
	$incoterms = isset($_POST['incoterms-mar']) ? $_POST['incoterms-mar'] : null;
} else {
	$incoterms = isset($_POST['incoterms-rod']) ? $_POST['incoterms-rod'] : null;
}
$tipoCotacao = isset($_POST['tipoCotacao']) ? $_POST['tipoCotacao'] : null;
$tipo_cotacao = isset($_POST['tipo_cotacao']) ? $_POST['tipo_cotacao'] : null;

$data_add = date('Y-m-d');
$hora_add = date('H:i:s');

// insere no banco ABERTURA cotação
$PDO = db_connect();
$sql = "INSERT INTO cotacao_abertura(id_usuario, id_empresa, n_proc_logix, n_proc_interno, modal, incoterms, data_add, hora_add, tipo_cotacao)
		VALUES (:idUsuario, :idEmpresa, :n_proc_logix, :n_proc_interno, :modal, :incoterms, :data_add, :hora_add, :tipo_cotacao)";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':idUsuario', $idUsuario);
$stmt->bindParam(':idEmpresa', $idEmpresa);
$stmt->bindParam(':n_proc_logix', $num_proc_logix);
$stmt->bindParam(':n_proc_interno', $num_proc_interno);
$stmt->bindParam(':modal', $modal);
$stmt->bindParam(':incoterms', $incoterms);
$stmt->bindParam(':data_add', $data_add);
$stmt->bindParam(':hora_add', $hora_add);
$stmt->bindParam(':tipo_cotacao', $tipo_cotacao);

if ($stmt->execute()) {
	// Pega o ´´ultimo ID
	$idCotacao = $PDO->lastInsertId();

	// Cria uma session com o id inserido
	$_SESSION['idCotacao'] = $idCotacao;
	$_SESSION['protocolo'] = $num_proc_interno;

	// Cria um cookie
	setcookie('idCotacao', $idCotacao);

	//enviar email ADM sistema
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
	$nomeEmpresa = $dadosEmpresa[0]['nome_empresa'];
	$data_add = date('d/m/Y');

	if ($tipoCotacao == "simplificada") {
		//require "envio_email_adm_abertura_simplificada.php";
		//$pagina = "cotacao.php?pg=simplificada";
		//$_SESSION['tipo_cotacao'] = $tipoCotacao;
	} else {
		//require "envio_email_adm_abertura.php";
		//$pagina = "cotacao.php?pg=nova";
	}

	$_SESSION['etapa'] = 2;

	header('Location: ../frete.php?a=frete&b=frete_cotacao&flag=success&tip=Abertura realizada com sucesso!');
} else {
	$_SESSION['etapa'] = 1;
	header('Location: ../frete.php?a=frete&b=frete_cotacao&flag=erro&tip=Não foi possível cadastrar.');
}