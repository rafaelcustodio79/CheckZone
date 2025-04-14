<?php
session_start();
require '../functions/globals.php';
require '../functions/verifica-log.php';

$PDO = db_connect();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID da empresa não informado.']);
    exit;
}

$id = $_GET['id'];

$sql = "SELECT * FROM company WHERE id = :id";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

$empresa = $stmt->fetch(PDO::FETCH_ASSOC);

if ($empresa) {
    echo json_encode($empresa);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Empresa não encontrada.']);
}
?>