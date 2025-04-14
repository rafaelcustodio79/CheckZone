<?php
// Inicia sessões
session_start();
require '../functions/globals.php';
require '../functions/verifica-log.php';

$tipoSalvar = $_POST['tipoSalvar'] ?? null;

$idBanner = filter_var($_POST['idBanner'] ?? null, FILTER_VALIDATE_INT);
$chamada = filter_var($_POST['chamada'] ?? null, FILTER_SANITIZE_STRING);
$titulo = filter_var($_POST['titulo'] ?? null, FILTER_SANITIZE_STRING);
$texto = filter_var($_POST['texto'] ?? null, FILTER_SANITIZE_STRING);
$link = filter_var($_POST['link'] ?? null, FILTER_VALIDATE_URL);
$tplink = filter_var($_POST['tplink'] ?? null, FILTER_SANITIZE_STRING);
$ativo = filter_var($_POST['ativo'] ?? null, FILTER_VALIDATE_INT);

$PDO = db_connect();

$sql = ""; // Adicione esta linha

if ($tipoSalvar == 'editar') {

    if (!empty($_FILES['arquivo'])) {
        # Código para UPLOAD
        $uploadDir = '../../assets/img/'; // Substitua pelo caminho do diretório onde você deseja salvar os arquivos

        $allowedExtensions = array('jpg', 'jpeg', 'png');

        $fileName = $_FILES['arquivo']['name'];
        $fileTmp = $_FILES['arquivo']['tmp_name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Verifica a extensão do arquivo
        if (in_array($fileExt, $allowedExtensions)) {
            $newFileName = uniqid() . '.' . $fileExt; // Gera um nome único para o arquivo

            $uploadPath = $uploadDir . $newFileName;

            // Move o arquivo temporário para o diretório de destino com o novo nome
            if (move_uploaded_file($fileTmp, $uploadPath)) {
                echo 'Arquivo enviado com sucesso!';
            } else {
                echo 'Erro ao enviar o arquivo.';
            }
        } else {
            header('Location: ../banners.php?a=banners&b=adicionar&flag=erro&tip=Apenas arquivos PNG e JPG são permitidos.');
        }

        $arquivo = $newFileName;

        $sql = "UPDATE banner SET chamada = :chamada, titulo = :titulo, texto = :texto, link = :link, tipo_link = :tipo_link, imagem = :imagem, ativo = :ativo 
            WHERE  id_banner = :idBanner";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(':imagem', $arquivo);
    } else {
        $sql = "UPDATE banner SET chamada = :chamada, titulo = :titulo, texto = :texto, link = :link, tipo_link = :tipo_link, ativo = :ativo 
            WHERE  id_banner = :idBanner";
        $stmt = $PDO->prepare($sql);
    }
    $stmt->bindParam(':idBanner', $idBanner);
    $stmt->bindParam(':chamada', $chamada);
    $stmt->bindParam(':titulo', $titulo);
    $stmt->bindParam(':texto', $texto);
    $stmt->bindParam(':link', $link);
    $stmt->bindParam(':tipo_link', $tplink);
    $stmt->bindParam(':ativo', $ativo);
} else {
    # Código para UPLOAD
    $uploadDir = '../../assets/img/'; // Substitua pelo caminho do diretório onde você deseja salvar os arquivos

    $allowedExtensions = array('jpg', 'jpeg', 'png');

    $fileName = $_FILES['arquivo']['name'];
    $fileTmp = $_FILES['arquivo']['tmp_name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Verifica a extensão do arquivo
    if (in_array($fileExt, $allowedExtensions)) {
        $newFileName = uniqid() . '.' . $fileExt; // Gera um nome único para o arquivo

        $uploadPath = $uploadDir . $newFileName;

        // Move o arquivo temporário para o diretório de destino com o novo nome
        if (move_uploaded_file($fileTmp, $uploadPath)) {
            echo 'Arquivo enviado com sucesso!';
        } else {
            echo 'Erro ao enviar o arquivo.';
        }
    } else {
        header('Location: ../banners.php?a=banners&b=adicionar&flag=erro&tip=Apenas arquivos PNG e JPG são permitidos.');
    }

    $arquivo = $newFileName;

    $sql = "INSERT INTO banner (chamada, titulo, texto, link, tipo_link, imagem, ativo) 
            VALUES (:chamada, :titulo, :texto, :link, :tipo_link, :imagem, :ativo)";
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':chamada', $chamada);
    $stmt->bindParam(':titulo', $titulo);
    $stmt->bindParam(':texto', $texto);
    $stmt->bindParam(':link', $link);
    $stmt->bindParam(':tipo_link', $tplink);
    $stmt->bindParam(':imagem', $arquivo);
    $stmt->bindParam(':ativo', $ativo);
}

if ($stmt->execute()) {
    if ($tipoSalvar == 'editar') {
        $redirecionarOk = 'Location: ../banners.php?a=banners&b=editar&id=' . $idBanner . '&flag=success&tip=Edição realizada com sucesso!';
    } else {
        $redirecionarOk = 'Location: ../banners.php?a=banners&b=adicionar&flag=success&tip=Banner cadastrado com sucesso!';
    }
    header($redirecionarOk);
} else {
    if ($tipoSalvar == 'editar') {
        $redirecionarErro = 'Location: ../banners.php?a=banners&b=editar&id=' . $idBanner . '&flag=erro&tip=Não foi possível editar.';
    } else {
        $redirecionarErro = 'Location: ../banners.php?a=banners&b=adicionar&flag=erro&tip=Não foi possível cadastrar.';
    }
    header($redirecionarErro);
}
