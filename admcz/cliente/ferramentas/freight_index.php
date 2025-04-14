<?php
// Obtém o tipo de dado enviado na URL ou define um valor padrão
$tipo = isset($_GET['tipo']) ? (int)$_GET['tipo'] : 2; // Converte para inteiro

// Conecta ao banco de dados
$PDO = db_connect();

// Processamento dos dados no PHP
$dataG = [];
try {
    // Define as variáveis de cor e label com base no tipo
    if ($tipo === 1) {
        $cor = '#0160ae';
        $label = 'Valor ($) / Container FCL';
    } elseif ($tipo === 2) {
        $cor = '#f99e1d';
        $label = 'Valor ($) / por kg';
    } else {
        $cor = '#3c8dbc';
        $label = 'Valor ($)';
    }

    // Consulta SQL com bind seguro de parâmetros
    $sql = "SELECT * FROM (
                SELECT * FROM frete_index WHERE tipo = :tipo ORDER BY data_situacao DESC LIMIT 10
            ) sub
            ORDER BY data_situacao ASC";
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':tipo', $tipo, PDO::PARAM_INT); // Usa PDO::PARAM_INT para inteiros
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result as $row) {
        // Formata a data para exibição
        $date = new DateTime($row['data_situacao']);
        $formattedDate = $date->format('d/m/Y');
        $dataG[] = [
            'y' => $formattedDate,
            'item1' => $row['valor'] // Usa o valor direto do banco
        ];
    }
} catch (PDOException $e) {
    die("Erro ao buscar dados: " . $e->getMessage());
}

try {
    // Consulta os últimos dois registros
    $sql = "SELECT * FROM frete_index
            WHERE tipo = :tipo
            ORDER BY data_situacao DESC LIMIT 2";
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
    $stmt->execute();
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verifica se existem pelo menos dois registros
    if (count($resultado) == 2) {
        $ultimoRegistro = $resultado[0];
        $penultimoRegistro = $resultado[1];

        // Calcula a porcentagem de aumento ou diminuição
        $valorUltimo = $ultimoRegistro['valor'];
        $valorPenultimo = $penultimoRegistro['valor'];

        $diferenca = $valorUltimo - $valorPenultimo;
        $percentual = ($diferenca / $valorPenultimo) * 100;

        // Define a cor com base no aumento ou diminuição
        if ($percentual > 0) {
            $corPercent = "green";
            $direcao = "up";
        } else {
            $corPercent = "red";
            $direcao = "down";
        }
    } else {
        echo "Não há registros suficientes para calcular a porcentagem.";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Ferramentas</h1>
                <small>Freight Index</small>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item">Ferramentas</li>
                    <li class="breadcrumb-item active">Freight Index</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <div class="row">
            <!-- left column -->
            <div class="col-md-8">
                <!-- AREA CHART -->
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line"></i>
                            Freigth Index
                        </h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" id="ship-button"><i class="fa fa-fw fa-ship"
                                    title="Container FCL"></i></button>
                            <button type="button" class="btn btn-tool" id="plane-button"><i class="fa fa-fw fa-plane"
                                    title="Aéreo (por kg)"></i></button>
                            <button type="button" class="btn btn-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-widget="remove"><i
                                    class="fa fa-times"></i></button>
                        </div>
                    </div>

                    <div class="position-relative mb-4">
                        <canvas id="visitors-chart" height="400"></canvas>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!--/.col (left) -->
            <div class="col-md-4">
                <!-- Map card -->
                <div class="card bg-gradient-info">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            China x Brasil
                        </h3>
                        <!-- card tools -->
                        <div class="card-tools">
                            <button type="button" class="btn btn-default btn-sm" data-card-widget="collapse"
                                title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                        <!-- /.card-tools -->
                    </div>
                    <div class="card-body">
                        <div id="world-map" style="height: 250px; width: 100%;"></div>
                    </div>
                    <!-- /.card-body-->
                </div>
                <!-- /.card -->

                <div class="row">

                    <div class="col-md-12">
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <?php if($tipo==1) : ?>
                                    <i class="fas fa-ship" title="Container FCL"></i>
                                    Valor Atual / container
                                    <?php else : ?>
                                    <i class="fas fa-plane" title="Aéreo (por kg)"></i>
                                    Valor Atual / kg
                                    <?php endif;?>

                                </h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="card-body" style="padding-top: 0px;">
                                <h2 class="text-primary">
                                    <?= '$ ' . number_format($valorUltimo, 2, ',', '.'); ?>
                                    <sub style="font-size: 18px;">
                                        <i class="fa fa-fw fa-arrow-<?= $direcao; ?> text-<?= $corPercent; ?>"></i>
                                    </sub>
                                </h2>
                                <small class="badge pull-left bg-<?= $corPercent; ?>">
                                    <?php echo number_format(abs($percentual), 2, ',', '.'); ?>%
                                </small>
                            </div>
                        </div>
                        <!-- /.box -->
                    </div>
                    <!--/.col (left) -->
                </div>
                <!-- /.row -->
            </div>

        </div>
    </div>
</section>
<!-- /.content -->


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Obtém os botões pelo ID
    var shipButton = document.getElementById('ship-button');
    var planeButton = document.getElementById('plane-button');

    // Adiciona o manipulador de eventos de clique para o botão Ship
    shipButton.addEventListener('click', function() {
        window.location.href = 'cliente.php?a=ferramentas&b=freight_index&tipo=1';
    });

    // Adiciona o manipulador de eventos de clique para o botão Plane
    planeButton.addEventListener('click', function() {
        window.location.href = 'cliente.php?a=ferramentas&b=freight_index&tipo=2';
    });
});

// Dados vindos do PHP
const dataG = <?php echo json_encode($dataG); ?>;
const cor = "<?php echo $cor; ?>";
const label = "<?php echo $label; ?>";

// Processa os dados
const labels = dataG.map(item => item.y); // Datas formatadas
const values = dataG.map(item => item.item1); // Valores

// Configura o gráfico
const ctx = document.getElementById('visitors-chart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            data: values,
            backgroundColor: 'transparent',
            borderColor: cor,
            pointBorderColor: cor,
            pointBackgroundColor: cor,
            label: label,
            fill: true
        }]
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
            display: false
        },
        scales: {
            yAxes: [{
                gridLines: {
                    display: true,
                    lineWidth: '4px',
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