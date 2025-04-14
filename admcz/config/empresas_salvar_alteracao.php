<?php
session_start();
require '../functions/globals.php';
require '../functions/verifica-log.php';

$PDO = db_connect();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $id = $_POST['id_empresa'] ?? null;
        $razao_social = $_POST['razao_social'] ?? '';
        $nome_fantasia = $_POST['nome_fantasia'] ?? '';
        $cnpj = $_POST['cnpj'] ?? '';
        $telefone_principal = $_POST['telefone_principal'] ?? '';
        $email_principal = $_POST['email_principal'] ?? '';
        $cep = $_POST['cep'] ?? '';
        $endereco = $_POST['endereco'] ?? '';
        $numero = $_POST['numero'] ?? '';
        $complemento = $_POST['complemento'] ?? '';
        $bairro = $_POST['bairro'] ?? '';
        $cidade = $_POST['cidade'] ?? '';
        $estado = $_POST['estado'] ?? '';
        $master_id = $_POST['master_id'] ?? null;
        $plano_id = $_POST['plano_id'] ?? null;
        $status_empresa = $_POST['status_empresa'] ?? null;

        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID da empresa não informado.']);
            exit;
        }

        $sql = "UPDATE company SET 
                    razao_social = :razao_social,
                    nome_fantasia = :nome_fantasia,
                    cnpj = :cnpj,
                    telefone_principal = :telefone_principal,
                    email_principal = :email_principal,
                    cep = :cep,
                    endereco = :endereco,
                    numero = :numero,
                    complemento = :complemento,
                    bairro = :bairro,
                    cidade = :cidade,
                    estado = :estado,
                    master_id = :master_id,
                    plano_id = :plano_id,
                    status_empresa = :status_empresa
                WHERE id = :id";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':razao_social', $razao_social);
        $stmt->bindParam(':nome_fantasia', $nome_fantasia);
        $stmt->bindParam(':cnpj', $cnpj);
        $stmt->bindParam(':telefone_principal', $telefone_principal);
        $stmt->bindParam(':email_principal', $email_principal);
        $stmt->bindParam(':cep', $cep);
        $stmt->bindParam(':endereco', $endereco);
        $stmt->bindParam(':numero', $numero);
        $stmt->bindParam(':complemento', $complemento);
        $stmt->bindParam(':bairro', $bairro);
        $stmt->bindParam(':cidade', $cidade);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':master_id', $master_id);
        $stmt->bindParam(':plano_id', $plano_id);
        $stmt->bindParam(':status_empresa', $status_empresa);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar a empresa.']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>