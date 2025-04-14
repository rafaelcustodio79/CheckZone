<?php
session_start();
require '../functions/globals.php';
require '../functions/verifica-log.php';

$PDO = db_connect();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $id = $_POST['id_usuario'] ?? null;
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $telefone_usuario = $_POST['telefone_usuario'] ?? '';
        $company_id = $_POST['company_id'] ?? null;
        $user_type_id = $_POST['user_type_id'] ?? null;
        $user_status = $_POST['user_status'] ?? null;
        $senha = trim($_POST['senha']);
        $confirmarSenha = trim($_POST['confirmar_senha']);

        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID do usuário não informado.']);
            exit;
        }

        // Verifica se as senhas coincidem
        if ($senha !== $confirmarSenha) {
            echo json_encode(['status' => 'error', 'message' => 'As senhas não coincidem!']);
            exit();
        }

        // Gera o hash seguro da senha
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $sql = "UPDATE users SET 
                nome = :nome, 
                email = :email, 
                telefone_usuario = :telefone_usuario, 
                company_id = :company_id, 
                users_type_id = :user_type_id, 
                user_status = :user_status,
                senha_hash = :senhaHash
                WHERE id = :id";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':telefone_usuario', $telefone_usuario, PDO::PARAM_STR);
        $stmt->bindParam(':company_id', $company_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_type_id', $user_type_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_status', $user_status, PDO::PARAM_INT);
        $stmt->bindParam(':senhaHash', $senhaHash, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

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