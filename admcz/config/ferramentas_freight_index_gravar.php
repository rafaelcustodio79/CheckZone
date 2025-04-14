<?php
session_start();

require '../functions/globals.php';
require '../functions/verifica-log.php';

if (isset($_POST["submit"])) {

    // Conexão com o banco de dados usando PDO
    $PDO = db_connect();

    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : null;
    $pais = isset($_POST['pais']) ? $_POST['pais'] : null;
    $data_situacao = isset($_POST['data_situacao']) ? $_POST['data_situacao'] : null;
    $valor = isset($_POST['valor_fi']) ? format_to_number(clean_input($_POST['valor_fi'])) : null;

    $sql = "INSERT INTO frete_index(tipo, pais, data_situacao, valor) VALUES (:tipo, :pais, :data_situacao, :valor)";
    $stmt = $PDO->prepare($sql);

    $stmt->bindParam(':tipo', $tipo);
    $stmt->bindParam(':pais', $pais);
    $stmt->bindParam(':data_situacao', $data_situacao);
    $stmt->bindParam(':valor', $valor);

    if($stmt->execute()) {
        header('Location: ../config.php?a=config&b=ferramentas_freight_index&flag=success&tip=Informação inserida com sucesso.');
    } else {
        header('Location: ../config.php?a=config&b=ferramentas_freight_index&flag=erro&tip=Erro ao executar operação.');
    }

    $PDO = null;

}