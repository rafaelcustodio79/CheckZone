<?php
$id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
$arquivo = htmlspecialchars($_GET['arquivo'], ENT_QUOTES, 'UTF-8');

if (!is_numeric($id)) {
    header('Location: banners.php?a=banners&b=listar&flag=erro&tip=Não foi possível realizar a operação.');
    exit();
}

$PDO = db_connect();

// Remove do banco
$sqlDelete = "DELETE FROM banner WHERE id_banner = :id";
$stmtDelete = $PDO->prepare($sqlDelete);
$stmtDelete->bindParam(':id', $id, PDO::PARAM_INT);

try {
    $stmtDelete->execute();

    $arquivoRemover = $arquivo;
    $caminhoArquivo = "../assets/img/" . $arquivoRemover;

    if (file_exists($caminhoArquivo)) {
        unlink($caminhoArquivo);
    }

    header('Location: banners.php?a=banners&b=listar&flag=success&tip=Banner removido com sucesso!');
    exit();
} catch (PDOException $e) {
    // Captura exceções do banco de dados
    header('Location: banners.php?a=banners&b=listar&flag=erro&tip=Erro no banco de dados: ' . $e->getMessage());
    exit();
}
