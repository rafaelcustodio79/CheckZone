<?php
session_start();
require '../functions/globals.php';
require '../functions/verifica-log.php';

$PDO = db_connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = htmlspecialchars(trim($_POST['nome']));
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $telefone = trim($_POST['telefone_usuario']);
    $senha = trim($_POST['senha']);
    $confirmarSenha = trim($_POST['confirmar_senha']);
    $tipo_usuario = filter_input(INPUT_POST, 'user_type_id', FILTER_SANITIZE_NUMBER_INT);
    $id_empresa = filter_input(INPUT_POST, 'company_id', FILTER_SANITIZE_NUMBER_INT);

    $user_status = 1; // Ativo por padrão

    // Verifica se as senhas coincidem
    if ($senha !== $confirmarSenha) {
        header('Location: ../config.php?a=config&b=usuarios&flag=erro&tip=As senhas não coincidem!');
        exit();
    }

    // Gera o hash seguro da senha
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // Upload da foto do usuário
    $newFileName = null;
    if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $foto = $_FILES['foto'];
        $uploadDir = '../img/users/';
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $allowedMimeTypes = ['image/jpeg', 'image/png'];

        $ext = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
        $fileMimeType = mime_content_type($foto['tmp_name']);

        if (in_array($ext, $allowedExtensions) && in_array($fileMimeType, $allowedMimeTypes)) {
            $newFileName = uniqid() . '.' . $ext;
            $uploadFile = $uploadDir . $newFileName;

            if (!move_uploaded_file($foto['tmp_name'], $uploadFile)) {
                header('Location: ../config.php?a=config&b=usuarios&flag=erro&tip=Erro no upload da imagem.');
                exit();
            }
        } else {
            header('Location: ../config.php?a=config&b=usuarios&flag=erro&tip=Tipo de imagem inválido.');
            exit();
        }
    }

    // Verifica se os campos obrigatórios estão preenchidos
    if ($email && $tipo_usuario && $nome && $id_empresa) {
        try {
            $sql = "INSERT INTO users (nome, email, user_photo, senha_hash, users_type_id, company_id, telefone_usuario, user_status, criado_em) 
                    VALUES (:nome, :email, :user_photo, :senha_hash, :users_type_id, :company_id, :telefone_usuario, :user_status, NOW())";
            
            $stmt = $PDO->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':user_photo', $newFileName);
            $stmt->bindParam(':senha_hash', $senhaHash);
            $stmt->bindParam(':users_type_id', $tipo_usuario);
            $stmt->bindParam(':company_id', $id_empresa);
            $stmt->bindParam(':telefone_usuario', $telefone);
            $stmt->bindParam(':user_status', $user_status);

            if ($stmt->execute()) {
                header('Location: ../config.php?a=config&b=usuarios&flag=success&tip=Usuário cadastrado com sucesso!');
                exit();
            } else {
                header('Location: ../config.php?a=config&b=usuarios&flag=erro&tip=Erro ao cadastrar usuário.');
                exit();
            }
        } catch (PDOException $e) {
            $errorMsg = urlencode($e->getMessage());
            header('Location: ../config.php?a=config&b=usuarios&flag=erro&tip='.$errorMsg);
            exit();
        }
    } else {
        header('Location: ../config.php?a=config&b=usuarios&flag=erro&tip=Preencha todos os campos obrigatórios!');
        exit();
    }
}