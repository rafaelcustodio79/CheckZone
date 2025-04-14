<?php
session_start();
require '../functions/globals.php';
require '../functions/verifica-log.php';

$PDO = db_connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Coleta os dados do formulário
    $fields = ['razao_social', 'nome_fantasia', 'cnpj', 'telefone_principal', 'email_principal', 'cep', 
               'endereco', 'numero', 'complemento', 'bairro', 'cidade', 'estado', 'status_empresa', 
               'plano_id', 'master_id'];
    
    foreach ($fields as $field) {
        $$field = isset($_POST[$field]) ? $_POST[$field] : null;
    }

    $plano_id = $plano_id ?: 0;
    $master_id = $master_id ?: 0;
    $data_add = date('Y-m-d');

    // Verifica se já existe um CNPJ cadastrado
    $sqlcnpj = "SELECT 1 FROM company WHERE cnpj = :cnpj LIMIT 1";
    $stmtcnpj = $PDO->prepare($sqlcnpj);
    $stmtcnpj->bindParam(':cnpj', $cnpj, PDO::PARAM_STR);
    $stmtcnpj->execute();

    if ($stmtcnpj->fetchColumn()) { 
        header('Location: ../config.php?a=config&b=empresas&flag=erro&tip=CNPJ já cadastrado no sistema!');
        exit;
    }

    // Upload de logo (se existir)
    $newFileName = null;
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $logo = $_FILES['logo'];
        $uploadDir = '../img/logos/';
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $allowedMimeTypes = ['image/jpeg', 'image/png'];

        $ext = strtolower(pathinfo($logo['name'], PATHINFO_EXTENSION));
        $fileMimeType = mime_content_type($logo['tmp_name']);

        if (in_array($ext, $allowedExtensions) && in_array($fileMimeType, $allowedMimeTypes)) {
            $newFileName = uniqid() . '.' . $ext;
            if (!move_uploaded_file($logo['tmp_name'], $uploadDir . $newFileName)) {
                die('Erro ao fazer o upload do arquivo.');
            }
        } else {
            die('Tipo de arquivo não permitido. Apenas JPG, JPEG e PNG são aceitos.');
        }
    }

    try {
        // Query de inserção
        $sql = "INSERT INTO company 
                (razao_social, nome_fantasia, company_logo, cnpj, telefone_principal, email_principal, 
                cep, endereco, numero, complemento, bairro, cidade, estado, status_empresa, master_id, 
                plano_id, created_at) 
                VALUES 
                (:razao_social, :nome_fantasia, :company_logo, :cnpj, :telefone_principal, :email_principal, 
                :cep, :endereco, :numero, :complemento, :bairro, :cidade, :estado, :status_empresa, 
                :master_id, :plano_id, :created_at)";

        $stmt = $PDO->prepare($sql);

        // Bind dos parâmetros
        $stmt->bindParam(':razao_social', $razao_social, PDO::PARAM_STR);
        $stmt->bindParam(':nome_fantasia', $nome_fantasia, PDO::PARAM_STR);
        $stmt->bindParam(':company_logo', $newFileName, PDO::PARAM_STR);
        $stmt->bindParam(':cnpj', $cnpj, PDO::PARAM_STR);
        $stmt->bindParam(':telefone_principal', $telefone_principal, PDO::PARAM_STR);
        $stmt->bindParam(':email_principal', $email_principal, PDO::PARAM_STR);
        $stmt->bindParam(':cep', $cep, PDO::PARAM_STR);
        $stmt->bindParam(':endereco', $endereco, PDO::PARAM_STR);
        $stmt->bindParam(':numero', $numero, PDO::PARAM_STR);
        $stmt->bindParam(':complemento', $complemento, PDO::PARAM_STR);
        $stmt->bindParam(':bairro', $bairro, PDO::PARAM_STR);
        $stmt->bindParam(':cidade', $cidade, PDO::PARAM_STR);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->bindParam(':status_empresa', $status_empresa, PDO::PARAM_STR);
        $stmt->bindParam(':master_id', $master_id, PDO::PARAM_INT);
        $stmt->bindParam(':plano_id', $plano_id, PDO::PARAM_INT);
        $stmt->bindParam(':created_at', $data_add, PDO::PARAM_STR);

        // Executa a query
        if ($stmt->execute()) {
            header('Location: ../config.php?a=config&b=empresas&flag=success&tip=Empresa cadastrada com sucesso!');
            exit;
        } else {
            header('Location: ../config.php?a=config&b=empresas&flag=erro&tip=Não foi possível cadastrar a empresa.');
            exit;
        }
    } catch (PDOException $e) {
        header('Location: ../config.php?a=config&b=empresas&flag=erro&tip=' . urlencode($e->getMessage()));
        exit();
    }
}
?>