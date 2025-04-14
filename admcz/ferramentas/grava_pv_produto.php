<?php
session_start();

// inclui o arquivo de inicialização
require '../functions/globals.php';

$PDO = db_connect();

// Verificar se os dados foram enviados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descricao_produto = isset($_POST['descricao_produto']) ? $_POST['descricao_produto'] : null;
    $qt = isset($_POST['qt_produto']) ? $_POST['qt_produto'] : null;
    //$peso_liquido = isset($_POST['peso_liquido']) ? clean_input($_POST['peso_liquido']) : null;
    $peso_liquido = isset($_POST['peso_liquido']) ? format_to_number(clean_input($_POST['peso_liquido'])) : null;
    $valor_total_item = isset($_POST['valor_total_item']) ? format_to_number(clean_input($_POST['valor_total_item'])) : null;
    $ncm = isset($_POST['ncm']) ? $_POST['ncm'] : null;
    $aliquota_icms = isset($_POST['aliquota_icms']) ? $_POST['aliquota_icms'] : null;
    $id_pv = $_POST['id_pv'];

    try {
        // Inserir o produto no banco de dados
        $sql = "INSERT INTO planilha_viabilidade_adicoes 
                    (id_pv, descricao_produto, qt, peso_liquido, valor_total_item, ncm, aliquota_icms) 
                    VALUES 
                    (:id_pv, :descricao_produto, :qt_produto, :peso_liquido, :valor_total_item, :ncm, :aliquota_icms)";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(':id_pv', $id_pv);
        $stmt->bindParam(':descricao_produto', $descricao_produto);
        $stmt->bindParam(':qt_produto', $qt);
        $stmt->bindParam(':peso_liquido', $peso_liquido);
        $stmt->bindParam(':valor_total_item', $valor_total_item);
        $stmt->bindParam(':ncm', $ncm);
        $stmt->bindParam(':aliquota_icms', $aliquota_icms);

        if ($stmt->execute()) {
            header('Location: ../ferramentas.php?a=ferramentas&b=viabilidade_criar&visualizar=1&idEstudo='.$id_pv.'&flag=success&tip=Produto gravado com sucesso!&tabAtiva=2');
        } else {
            header('Location: ../ferramentas.php?a=ferramentas&b=viabilidade_criar&flag=erro&tip=Nenhuma informação foi encontrada. Reinicie o processo!');
        }
    } catch (PDOException $e) {
        header('Location: ../ferramentas.php?a=ferramentas&b=viabilidade_criar&visualizar=1&idEstudo='.$id_pv.'&flag=erro&tip=Erro: ' . $e->getMessage());
    }
}