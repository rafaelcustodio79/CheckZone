<?php
session_start();

// inclui o arquivo de inicialização
require '../functions/globals.php';

$PDO = db_connect();

$id_aeroporto = isset($_POST['id_aeroporto']) ? $_POST['id_aeroporto'] : null;
$cif = isset($_POST['cif']) ? $_POST['cif'] : null;
$valorCIF = str_replace('.', '', $cif);
$valorCIF = str_replace(',', '.', $valorCIF);
$valorCIF = floatval($valorCIF);
$periodo = isset($_POST['periodo']) ? $_POST['periodo'] : null;
$tipo_carga = isset($_POST['tipo_carga']) ? $_POST['tipo_carga'] : null;
$peso_bruto = isset($_POST['peso_bruto']) ? $_POST['peso_bruto'] : null;
$idEstudo = isset($_POST['idEstudo']) ? $_POST['idEstudo'] : null;

# Calcula o período
// Extrair as datas inicial e final do período
$datas = explode(" - ", $periodo);
$dataInicial = $datas[0];
$dataFinal = $datas[1];

// Converter para formato de data do PHP (ano-mês-dia)
$dataInicialFormatada = DateTime::createFromFormat('d/m/Y', $dataInicial)->format('Y-m-d');
$dataFinalFormatada = DateTime::createFromFormat('d/m/Y', $dataFinal)->format('Y-m-d');

// Calcular a diferença em dias
$dataInicialObj = new DateTime($dataInicialFormatada);
$dataFinalObj = new DateTime($dataFinalFormatada);
$diferenca = $dataInicialObj->diff($dataFinalObj);

// Obter o número de dias da diferença
$numDias = $diferenca->days + 1;

// Variável para armazenar as informações
$dados = [];

try {

    // Obter os valores dos períodos da tabela auxiliar
    $sql_aux = "SELECT * FROM armazenagem_aer_aux WHERE id_terminal_aer = :id_aeroporto";
    $stmt_aux = $PDO->prepare($sql_aux);
    $stmt_aux->bindParam(':id_aeroporto', $id_aeroporto, PDO::PARAM_INT);
    $stmt_aux->execute();
    $aux_result = $stmt_aux->fetch(PDO::FETCH_ASSOC);

    $periodos = [
        1 => $aux_result['dias_per_1'],
        2 => $aux_result['dias_per_2'],
        3 => $aux_result['dias_per_3'],
        4 => $aux_result['dias_per_4'],
        5 => $aux_result['dias_per_5']
    ];

    $nomeAeroporto = $aux_result['aeroporto'];

    // Obter as tarifas de armazenagem
    $sql = "SELECT * FROM armazenagem_aer
            WHERE id_aeroporto = :id_aeroporto AND tipo_de_carga = :tipo_carga
            ORDER BY periodos";
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':id_aeroporto', $id_aeroporto, PDO::PARAM_INT);
    $stmt->bindParam(':tipo_carga', $tipo_carga, PDO::PARAM_STR);
    $stmt->execute();
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $valorTotalArmazenagem = 0;
    $ultimaPorcentagem = 0;

    foreach ($resultado as $tarifa) {
        $diasPeriodo = $periodos[$tarifa['periodos']];
        $armazenagemPercentual = $tarifa['armazenagem_percentual'];

        if ($numDias > $diasPeriodo) {
            $ultimaPorcentagem = $armazenagemPercentual;
        } else {
            $ultimaPorcentagem = $armazenagemPercentual;
            break;
        }
    }

    $valorArmazenagem = $valorCIF * ($ultimaPorcentagem / 100);
    $valorTotalArmazenagem = $valorArmazenagem;

    // Calcular a capatazia
    $sql_capatazia = "SELECT * FROM armazenagem_aer WHERE id_aeroporto = :id_aeroporto AND tipo_de_carga = :tipo_carga AND periodos = 1";
    $stmt_capatazia = $PDO->prepare($sql_capatazia);
    $stmt_capatazia->bindParam(':id_aeroporto', $id_aeroporto, PDO::PARAM_INT);
    $stmt_capatazia->bindParam(':tipo_carga', $tipo_carga, PDO::PARAM_STR);
    $stmt_capatazia->execute();
    $capatazia_result = $stmt_capatazia->fetch(PDO::FETCH_ASSOC);

    $capataziaKgBruto = $capatazia_result['capatazia_kg_bruto'];
    $capatazia_valor_minimo = $capatazia_result['capatazia_valor_minimo'];
    $valorCapatazia = $peso_bruto * $capataziaKgBruto;
    if($valorCapatazia < $capatazia_valor_minimo) {
        $valorCapatazia = $capatazia_valor_minimo;
    }

    // Exibir o valor total de armazenagem
    $valorArmazenagemTotal = $valorTotalArmazenagem + $valorCapatazia;

    // Armazenar os dados na sessão
    $_SESSION['valorArmazenagemTotal'] = $valorArmazenagemTotal;

    // Redirecionar para outra página
    header('Location: ../ferramentas.php?a=ferramentas&b=viabilidade_criar&visualizar=1&idEstudo='.$idEstudo.'&flag=success&tip=Cálculo da Armazenagem realizado com sucesso!');
    exit();

} catch (PDOException $e) {
    echo 'Erro: ' . $e->getMessage();
}