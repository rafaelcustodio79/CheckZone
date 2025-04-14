<?php
session_start();

// inclui o arquivo de inicialização
require '../functions/globals.php';

$PDO = db_connect();

// Captura dos dados do formulário
$idEstudo = isset($_POST['id_estudo']) ? $_POST['id_estudo'] : null;
$estimativa_armazenagem = isset($_POST['estimativa_armazenagem']) ? clean_input($_POST['estimativa_armazenagem']) : null;
$valor_armazenagem = isset($_POST['valor_armazenagem']) ? format_to_number(clean_input($_POST['valor_armazenagem'])) : null;
$valor_frete_nacional = isset($_POST['valor_frete_nacional']) ? format_to_number(clean_input($_POST['valor_frete_nacional'])) : null;
$outras_da = isset($_POST['outras_da']) ? format_to_number(clean_input($_POST['outras_da'])) : null;
$licenca_importacao = isset($_POST['licenca_importacao']) ? format_to_number(clean_input($_POST['licenca_importacao'])) : null;
$anuencia_importacao = isset($_POST['anuencia_importacao']) ? format_to_number(clean_input($_POST['anuencia_importacao'])) : null;
$honorario_despachante = isset($_POST['honorario_despachante']) ? format_to_number(clean_input($_POST['honorario_despachante'])) : null;
$marinha_mercante = isset($_POST['marinha_mercante']) ? format_to_number(clean_input($_POST['marinha_mercante'])) : null;
$capatazia = isset($_POST['capatazia']) ? $_POST['capatazia'] : null;

if ($idEstudo) {
    // Atualiza a tabela com as informações do formulário
    $sql = "UPDATE planilha_viabilidade
            SET estimativa_armazenagem = :estimativa_armazenagem,
                valor_armazenagem = :valor_armazenagem,
                valor_frete_nacional = :valor_frete_nacional,
                outras_da = :outras_da,
                licenca_importacao = :licenca_importacao,
                anuencia_importacao = :anuencia_importacao,
                honorario_despachante = :honorario_despachante,
                marinha_mercante = :marinha_mercante,
                capatazia = :capatazia
            WHERE id_pv = :id_estudo";
    
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':estimativa_armazenagem', $estimativa_armazenagem);
    $stmt->bindParam(':valor_armazenagem', $valor_armazenagem);
    $stmt->bindParam(':valor_frete_nacional', $valor_frete_nacional);
    $stmt->bindParam(':outras_da', $outras_da);
    $stmt->bindParam(':licenca_importacao', $licenca_importacao);
    $stmt->bindParam(':anuencia_importacao', $anuencia_importacao);
    $stmt->bindParam(':honorario_despachante', $honorario_despachante);
    $stmt->bindParam(':marinha_mercante', $marinha_mercante);
    $stmt->bindParam(':capatazia', $capatazia);
    $stmt->bindParam(':id_estudo', $idEstudo);

    if ($stmt->execute()) {
        unset($_SESSION['passo']); // Remove a variável de sessão 'passo'
        $_SESSION['passo'] = 6; // Atribui um novo valor a 'passo'
        unset($_SESSION['editar_5']); // Remove a variável de sessão 'passo'
        header('Location: ../ferramentas.php?a=ferramentas&b=viabilidade_criar&visualizar=1&idEstudo='.$idEstudo.'&flag=success&tip=Informações das Despesas Aduaneiras gravadas com sucesso!');
    } else {
        header('Location: ../ferramentas.php?a=ferramentas&b=viabilidade_criar&flag=erro&tip=Não foi possível realizar a operação.');
    }
} else {
    header('Location: ../ferramentas.php?a=ferramentas&b=viabilidade_criar&flag=erro&tip=Nenhuma informação foi encontrada. Reinicie o processo!');
}