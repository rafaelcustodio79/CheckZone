<?php
session_start();

// inclui o arquivo de inicialização
require '../functions/globals.php';

$PDO = db_connect();

$idEstudo = isset($_POST['id_estudo']) ? $_POST['id_estudo'] : null;
$origem = isset($_POST['origem']) ? $_POST['origem'] : null;
$destino = isset($_POST['destino']) ? $_POST['destino'] : null;
$moeda_negociada = isset($_POST['moeda_padrao']) ? $_POST['moeda_padrao'] : null;
$modal = isset($_POST['modal']) ? $_POST['modal'] : null;
$tipo_carga = isset($_POST['tipo_carga']) ? $_POST['tipo_carga'] : null;
$incoterm = isset($_POST['incoterms']) ? $_POST['incoterms'] : null;
$qtCont = isset($_POST['qtConteiner']) ? $_POST['qtConteiner'] : null;

if ($idEstudo) {
    // Atualiza com as informações iniciais
    $sql = "UPDATE planilha_viabilidade
            SET origem = :origem,
                destino = :destino,
                moeda_padrao = :moeda_negociada,
                modal = :modal,
                tipo_carga = :tipo_carga,
                incoterm = :incoterm,
                qtCont = :qtCont
            WHERE id_pv = :id_estudo";
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':origem', $origem);
    $stmt->bindParam(':destino', $destino);
    $stmt->bindParam(':moeda_negociada', $moeda_negociada);
    $stmt->bindParam(':modal', $modal);
    $stmt->bindParam(':tipo_carga', $tipo_carga);
    $stmt->bindParam(':incoterm', $incoterm);
    $stmt->bindParam(':qtCont', $qtCont);
    $stmt->bindParam(':id_estudo', $idEstudo);

    if ($stmt->execute()) {
        unset($_SESSION['passo']); // Remove a variável de sessão 'passo'
        $_SESSION['passo'] = 2; // Atribui um novo valor a 'passo'
        unset($_SESSION['editar_1']); // Remove a variável de sessão 'passo'
        header('Location: ../ferramentas.php?a=ferramentas&b=viabilidade_criar&flag=success&visualizar=1&idEstudo='.$idEstudo.'&tip=Informações iniciais gravadas com sucesso!');
    } else {
        header('Location: ../ferramentas.php?a=ferramentas&b=viabilidade_criar&flag=erro&tip=Não foi possível realizar a operação.');
    }
} else {
    header('Location: ../ferramentas.php?a=ferramentas&b=viabilidade_criar&flag=erro&tip=Nenhuma informação foi encontrada. Reinicie o processo!');
}