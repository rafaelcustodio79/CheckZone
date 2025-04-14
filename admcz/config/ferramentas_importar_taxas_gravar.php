<?php
session_start();

require '../functions/globals.php';
require '../functions/verifica-log.php';

if (isset($_POST["submit"])) {
    // Verifique se o arquivo foi enviado
    if ($_FILES["file"]["error"] > 0) {
        echo "Erro ao fazer upload: " . $_FILES["file"]["error"];
    } else {
        // Verifique se o arquivo é um CSV
        $fileType = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
        if ($fileType != 'csv') {
            header('Location: ../config.php?a=config&b=ferramentas_importar_taxas&flag=erro&tip=Por favor, envie um arquivo CSV.');
            exit();
        }

        $filename = $_FILES["file"]["tmp_name"];
        
        // Conexão com o banco de dados usando PDO
        $PDO = db_connect();

        // Abra o arquivo CSV
        if (($handle = fopen($filename, "r")) !== false) {
            // Esvazie a tabela antes de importar novos dados
            $PDO->exec("TRUNCATE TABLE taxa_cambial");
            // Pule a primeira linha se contiver cabeçalhos
            fgetcsv($handle, 1000, ";");

            $stmt = $PDO->prepare("INSERT INTO taxa_cambial (data_upload, cod_moeda, tipo_moeda, moeda, real_compra, real_venda, paridade_compra, paridade_venda) 
                    VALUES (:data_upload, :cod_moeda, :tipo_moeda, :moeda, :real_compra, :real_venda, :paridade_compra, :paridade_venda)");

            while (($data = fgetcsv($handle, 1000, ";")) !== false) {
                $data_upload = $data[0];
                $cod_moeda = $data[1];
                $tipo_moeda = $data[2];
                $moeda = $data[3];
                $real_compra = str_replace(',', '.', $data[4]);
                $real_venda = str_replace(',', '.', $data[5]);
                $paridade_compra = str_replace(',', '.', $data[6]);
                $paridade_venda = str_replace(',', '.', $data[7]);

                $stmt->bindParam(':data_upload', $data_upload);
                $stmt->bindParam(':cod_moeda', $cod_moeda);
                $stmt->bindParam(':tipo_moeda', $tipo_moeda);
                $stmt->bindParam(':moeda', $moeda);
                $stmt->bindParam(':real_compra', $real_compra);
                $stmt->bindParam(':real_venda', $real_venda);
                $stmt->bindParam(':paridade_compra', $paridade_compra);
                $stmt->bindParam(':paridade_venda', $paridade_venda);

                $stmt->execute();
            }
            fclose($handle);
            header('Location: ../config.php?a=config&b=ferramentas_importar_taxas&flag=success&tip=Importação de dados concluída com sucesso.');
        } else {
            header('Location: ../config.php?a=config&b=ferramentas_importar_taxas&flag=erro&tip=Erro ao abrir o arquivo.');
        }

        $PDO = null;
    }
}