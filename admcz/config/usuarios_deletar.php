<?php
$id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
$arquivo = htmlspecialchars($_GET['arquivo'], ENT_QUOTES, 'UTF-8');

if (!is_numeric($id)) {
    header('Location: config.php?a=config&b=usuarios&flag=erro&tip=Não foi possível realizar a operação.');
    exit();
}

$PDO = db_connect();

// Remove do banco
$sqlDelete = "DELETE FROM users WHERE id = :id";
$stmtDelete = $PDO->prepare($sqlDelete);
$stmtDelete->bindParam(':id', $id, PDO::PARAM_INT);

try {
    $stmtDelete->execute();

    $arquivoRemover = $arquivo;
    $caminhoArquivo = "../img/users/" . $arquivoRemover;

    if (file_exists($caminhoArquivo)) {
        unlink($caminhoArquivo);
    }

    header('Location: config.php?a=config&b=usuarios&flag=success&tip=Usuário removido com sucesso!');
    exit();
} catch (PDOException $e) {
    // Captura exceções do banco de dados
    header('Location: config.php?a=config&b=usuarios&flag=erro&tip=Erro no banco de dados: ' . $e->getMessage());
    exit();
}