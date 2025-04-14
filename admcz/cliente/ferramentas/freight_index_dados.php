<?php
// Inicia sessões
session_start();
require '../../functions/globals.php';

$PDO = db_connect();

// Processamento dos dados no PHP
$dataG1 = [];
$dataG2 = [];
try {
    $tipo1 = 1; // Primeiro tipo de linha
    $tipo2 = 2; // Segundo tipo de linha

    // Consulta para o primeiro tipo
    $sql1 = "SELECT * FROM (
                SELECT * FROM frete_index WHERE tipo = :tipo ORDER BY data_situacao DESC LIMIT 10
            ) sub
            ORDER BY data_situacao ASC";
    $stmt1 = $PDO->prepare($sql1);
    $stmt1->bindParam(':tipo', $tipo1, PDO::PARAM_INT);
    $stmt1->execute();
    $result1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result1 as $row) {
        $date = new DateTime($row['data_situacao']);
        $formattedDate = $date->format('d/m/Y');
        $dataG1[] = [
            'y' => $formattedDate,
            'item1' => $row['valor']
        ];
    }

    // Consulta para o segundo tipo
    $stmt2 = $PDO->prepare($sql1); // Reutiliza a mesma SQL
    $stmt2->bindParam(':tipo', $tipo2, PDO::PARAM_INT);
    $stmt2->execute();
    $result2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result2 as $row) {
        $date = new DateTime($row['data_situacao']);
        $formattedDate = $date->format('d/m/Y');
        $dataG2[] = [
            'y' => $formattedDate,
            'item1' => $row['valor']
        ];
    }
} catch (PDOException $e) {
    die("Erro ao buscar dados: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráfico Dinâmico</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <canvas id="visitors-chart" style="width: 100%; max-width: 600px; height: 400px;"></canvas>

    <script>
    // Dados vindos do PHP
    const dataG1 = <?php echo json_encode($dataG1); ?>;
    const dataG2 = <?php echo json_encode($dataG2); ?>;
    const label1 = 'Valor ($) / Container FCL';
    const label2 = 'Valor ($) / por kg';

    // Processa os dados
    const labels = dataG1.map(item => item.y); // Assume que as datas são as mesmas para ambos os conjuntos
    const values1 = dataG1.map(item => item.item1); // Valores do primeiro tipo
    const values2 = dataG2.map(item => item.item1); // Valores do segundo tipo

    console.log(dataG1, dataG2);

    // Configura o gráfico
    const ctx = document.getElementById('visitors-chart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                    data: values1,
                    backgroundColor: 'transparent',
                    borderColor: '#0160ae',
                    pointBorderColor: '#0160ae',
                    pointBackgroundColor: '#0160ae',
                    fill: false,
                    label: label1
                },
                {
                    data: values2,
                    backgroundColor: 'transparent',
                    borderColor: '#f99e1d',
                    pointBorderColor: '#f99e1d',
                    pointBackgroundColor: '#f99e1d',
                    fill: false,
                    label: label2
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                mode: 'index',
                intersect: false
            },
            hover: {
                mode: 'index',
                intersect: false
            },
            legend: {
                display: true // Mostra a legenda para identificar cada linha
            },
            scales: {
                yAxes: [{
                    gridLines: {
                        display: true,
                        lineWidth: '1px',
                        color: 'rgba(0, 0, 0, .2)',
                        zeroLineColor: 'transparent'
                    },
                    ticks: {
                        beginAtZero: true,
                        suggestedMax: 200
                    }
                }],
                xAxes: [{
                    gridLines: {
                        display: false
                    }
                }]
            }
        }
    });
    </script>
</body>

</html>