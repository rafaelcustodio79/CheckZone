<?php
session_start();

// inclui o arquivo de inicialização
require '../functions/globals.php';

$PDO = db_connect();

$idEmpresa = $_SESSION['empresa_id'];
$idUsuario = $_SESSION['user_id'];
$nome_estudo = isset($_POST['nome_estudo']) ? $_POST['nome_estudo'] : null;


// insere no carrinho de compras
$sql = "INSERT INTO planilha_viabilidade(id_empresa, id_usuario, nome_estudo)
                    VALUES (:id_empresa, :id_usuario, :nome_estudo)";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':id_empresa', $idEmpresa);
$stmt->bindParam(':id_usuario', $idUsuario);
$stmt->bindParam(':nome_estudo', $nome_estudo);

if ($stmt->execute()) {
    $idEstudo = $PDO->lastInsertId();
    $_SESSION['idEstudo'] = $idEstudo;
    $_SESSION['passo'] = 1;
    header(
        'Location: ../ferramentas.php?a=ferramentas&b=viabilidade_criar&flag=success&tip=Nome do estudo criado com sucesso!'
    );
} else {
    header('Location: ../ferramentas.php?a=ferramentas&b=viabilidade_criar&flag=erro&tip=Não foi possível realizar a operação.');
}