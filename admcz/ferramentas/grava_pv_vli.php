<?php
session_start();

// inclui o arquivo de inicialização
require '../functions/globals.php';

$PDO = db_connect();

$idEstudo = isset($_POST['id_estudo']) ? $_POST['id_estudo'] : null;
$vfi_resumido = isset($_POST['vfi_resumido']) ? $_POST['vfi_resumido'] : null;

if ($vfi_resumido==1) {
    $vfi = isset($_POST['vfi']) ? $_POST['vfi'] : null;
    $valor_sem_ponto = str_replace(".", "", $vfi);
    $valor_mercadoria = str_replace(",", ".", $valor_sem_ponto);
    $taxas = isset($_POST['taxas']) ? $_POST['taxas'] : null;
    $valor_sem_ponto_taxas = str_replace(".", "", $taxas);
    $taxasEXW = str_replace(",", ".", $valor_sem_ponto_taxas);
    $seguro = isset($_POST['seguro']) ? $_POST['seguro'] : null;
    $valor_sem_ponto_seguro = str_replace(".", "", $seguro);
    $valor_seguro = str_replace(",", ".", $valor_sem_ponto_seguro);
    
    if ($valor_mercadoria == null) {
        $valor_mercadoria = 0.00;
    } else {
        $valor_mercadoria = (float) $valor_mercadoria;
    }
} else {
    $tipo_carga = $_POST['tipo_carga'];
    $qtCont = $_POST['qtCont'];
    $modal = $_POST['modal'];
    $incoterm = $_POST['incoterm'];
    $CBM = (float) str_replace(',', '.', $_POST['cbm']);
    $peso_bruto = (float)$_POST['peso_bruto'];

    if ($tipo_carga=='carga_solta') {
        $tipo_carga = $tipo_carga . '_' . $modal;

    }
    $sql = "SELECT * FROM planilha_viabilidade_frete_int_aux
            WHERE tipo_carga = :tipo_carga";
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':tipo_carga', $tipo_carga);
    $stmt->execute();
    $dados = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($tipo_carga == 'carga_solta_mar') {
        
        if ($incoterm == 'EXW') {
            // Garante que o valor mínimo seja 1
            $taxa_cbm = ($CBM < 1) ? 1 : $CMB;
            $valor_mercadoria = ($dados['frete'] * $taxa_cbm);
            $taxasEXW = $dados['taxa_exw'];
        } else {
            $valor_mercadoria = $dados['frete'] * $CBM;
            $taxasEXW = 0.00;
        }
        $txt_obs = '';
    } elseif ($tipo_carga == 'carga_solta_aer') {
        
        // Calcula os dois valores
        $calculo1 = $CBM * 166.60;
        $calculo2 = $dados['frete'] * $peso_bruto;
        
        // Compara os valores e seleciona o maior
        $valor_mercadoria = max($calculo1, $calculo2);
        
        // Se o Incoterm for EXW, adiciona a taxa EXW
        $taxasEXW = 0;
        if ($incoterm == 'EXW') {
            $taxasEXW += $dados['taxa_exw'];
        }
        $txt_obs = '';
    } else {

        if ($incoterm == 'EXW') {
            $valor_mercadoria = $dados['frete'] * $qtCont;
            $taxasEXW = $dados['taxa_exw'] *$qtCont;
            $txt_obs = '';
        } elseif (in_array($incoterm, ['CPT', 'CIP', 'CFR', 'CIF', 'DAP'])) {
            $valor_mercadoria = 0.00;
            $txt_obs = 'Frete embutido no valor da mercadoria de acordo o Incoterm selecionado.';
        } else {
            $valor_mercadoria = $dados['frete'] * $qtCont;
            $txt_obs = '';
        }
    }
}

if ($idEstudo) {
    $valor_frete_int = $valor_mercadoria;
    if ($vfi_resumido==0 && empty($valor_seguro)) {
        $valor_seguro = max(($valor_mercadoria * 0.75) / 100, 50);
    }
    $valor_total_li = $valor_frete_int + $valor_seguro + $taxasEXW;
    
    // Atualiza com as informações iniciais
    $sql = "UPDATE planilha_viabilidade
            SET vfi_resumido = :vfi_resumido,
                valor_frete_int_li = :valor_frete_int,
                taxa_exw_li = :taxa_exw,
                valor_seguro_li = :valor_seguro, 
                valor_log_int = :valor_total_li,
                txt_obs = :txt_obs
            WHERE id_pv = :id_estudo";
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':vfi_resumido', $vfi_resumido);
    $stmt->bindParam(':valor_frete_int', $valor_frete_int);
    $stmt->bindParam(':taxa_exw', $taxasEXW);
    $stmt->bindParam(':valor_seguro', $valor_seguro);
    $stmt->bindParam(':valor_total_li', $valor_total_li);
    $stmt->bindParam(':txt_obs', $txt_obs);
    $stmt->bindParam(':id_estudo', $idEstudo);

    if ($stmt->execute()) {
        unset($_SESSION['passo']); // Remove a variável de sessão 'passo'
        $_SESSION['passo'] = 5; // Atribui um novo valor a 'passo'
        unset($_SESSION['editar_4']); // Remove a variável de sessão 'passo'
        header('Location: ../ferramentas.php?a=ferramentas&b=viabilidade_criar&visualizar=1&idEstudo='.$idEstudo.'&flag=success&tip=Informações da Logistica Internacional com sucesso!');
    } else {
        header('Location: ../ferramentas.php?a=ferramentas&b=viabilidade_criar&flag=erro&tip=Não foi possível realizar a operação.');
    }
} else {
    header('Location: ../ferramentas.php?a=ferramentas&b=viabilidade_criar&flag=erro&tip=Nenhuma informação foi encontrada. Reinicie o processo!');
}