<?php
session_start();

// inclui o arquivo de inicialização
require '../functions/globals.php';

$PDO = db_connect();

$id_terminal = isset($_POST['id_terminal']) ? $_POST['id_terminal'] : null;
$cif = isset($_POST['cif']) ? $_POST['cif'] : null;
$periodo = isset($_POST['periodo']) ? $_POST['periodo'] : null;
$tipo_carga = isset($_POST['tipo_carga']) ? $_POST['tipo_carga'] : null;
$qtde_container = isset($_POST['qtde_container']) ? $_POST['qtde_container'] : null;
$peso_bruto = isset($_POST['peso_bruto']) ? $_POST['peso_bruto'] : null;
$cp = isset($_POST['cp']) ? $_POST['cp'] : null;

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

$sql = "SELECT * FROM armazenagem_mar_aux
        WHERE id_terminal = :id_terminal";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':id_terminal', $id_terminal, PDO::PARAM_INT);
$stmt->execute();
$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verificar o terminal de Santos (13)
if ($id_terminal == 13) {
    $primeiroPeriodo = 7; // Primeiro período de 7 dias
    if ($numDias <= $primeiroPeriodo) {
        $periodo = 1; // Se o período for até 7 dias, conta como 1 período
    } else {
        // Se for mais que 7 dias, conta o primeiro período e adiciona os subsequentes de 1 dia cada
        $numDiasRestantes = $numDias - $primeiroPeriodo;
        $periodo = 1 + ceil($numDiasRestantes / 1); // Primeiro período + subsequentes de 1 dia
    }
} else {
    $primeiroPeriodo = $resultado[0]['dias_periodo'];
    if ($numDias <= $primeiroPeriodo) {
        $periodo = 1; // Se o período for até o primeiro período definido na tabela, conta como 1 período
    } else {
        // Se for mais que o primeiro período, divide o número de dias pelo período e arredonda para cima
        $periodo = ceil($numDias / $primeiroPeriodo);
    }
}

try {
    
    $PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Preparar e executar a consulta SQL
    $sql = "SELECT * FROM armazenagem_mar arm
            INNER JOIN armazenagem_mar_aux armaux ON arm.id_terminal = armaux.id_terminal
            WHERE arm.id_terminal = :id_terminal 
            AND arm.tipo_carga = :tipo_carga";

    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':id_terminal', $id_terminal, PDO::PARAM_INT); // Definir tipo de dado para terminal como string
    $stmt->bindParam(':tipo_carga', $tipo_carga, PDO::PARAM_STR); // Definir tipo de dado para tipo_carga como string
    //$stmt->bindParam(':periodo', $periodo, PDO::PARAM_INT); // Definir tipo de dado para dias_periodo como inteiro
    $stmt->execute();

    // Verificar erros
    if (!$stmt) {
        echo "\nPDO::errorInfo():\n";
        print_r($PDO->errorInfo());
    }

    // Obter resultados
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Variável para armazenar as informações
    $dados = [];

    if($tipo_carga === "carga_solta") {
        $pesoEmKg = $peso_bruto; // Substitua com o valor real
        $pesoEmToneladas = $pesoEmKg / 1000;
        $quantidadeContainer = ceil($pesoEmToneladas);
        $txtQtde = "calculado por peso (ton) | Peso real: " . $pesoEmKg . " kg";
        
    } else {
        $quantidadeContainer = $qtde_container;
        $txtQtde = "calculado por quantidade (container)";
    }

    $totalFinal = 0;

    $dados[] = $resultados[0]['terminal'];
    $dados[] = "de " . $dataInicial . " até " . $dataFinal;
    $dados[] = 'Qt. Períodos: ' . $periodo . ' | Nº de dias: ' . $numDias;
    $dados[] = "Qtde. de containeres/Peso: " . $quantidadeContainer . " (para base de cálculo)";
    
    $dados[] = $txtQtde;

    // Calcular e exibir valores por período
    foreach ($resultados as $indice => $resultado) {
        $indiceAjustado = $indice + 1;

        if ($indiceAjustado <= $periodo) {
            $valorCIF = str_replace('.', '', $cif);
            $valorCIF = str_replace(',', '.', $valorCIF);
            $valorCIF = floatval($valorCIF);
            $valorPorcentagem = ($valorCIF * $resultado['porcentagem']) / 100;

            $valorTaxas = $resultado['taxas_c'] + $resultado['taxas_t'];

            if (in_array($resultado['tipo_carga'], ['container_20', 'container_40'])) {
                if ($valorPorcentagem < $resultado['minimo_container']) {
                    $valorBase = $resultado['minimo_container'];
                } else {
                    $valorBase = $valorPorcentagem;
                }
            } else {
                if ($valorPorcentagem < $resultado['minimo_embarque']) {
                    $valorBase = $resultado['minimo_embarque'];
                } else {
                    $valorBase = $valorPorcentagem;
                }
            }

            if($cp==1) {
                $valorCP = $valorBase;
                $txtCP = ' + <b>carga perigosa</b>';
            } else {
                $valorCP = 0;
                $txtCP = '';
            }

            $valorTotal = ($valorBase + $valorTaxas + $valorCP) * $quantidadeContainer;
            $totalFinal += $valorTotal;

            $dados[] = $indiceAjustado;
            $dados[] = 'R$ ' . number_format($valorBase, 2, ',', '.');
            $dados[] = 'R$ ' . number_format($valorTaxas, 2, ',', '.');
            $dados[] = 'R$ ' . number_format($valorTotal, 2, ',', '.') . " (para $quantidadeContainer containers/ton ".$txtCP.")";
        }
    }
    $dados[] = $totalFinal;

    // Armazenar os dados na sessão
    $_SESSION['dados'] = $dados;

    // Redirecionar para outra página
    header("Location: ../ferramentas.php?a=ferramentas&b=formulario_armazenagem_m");
    exit();

} catch (PDOException $e) {
    echo "Erro ao executar a consulta SQL: " . $e->getMessage();
}