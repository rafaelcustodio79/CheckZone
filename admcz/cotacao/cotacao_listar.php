<?php
$PDO = db_connect();

# Selecionando todas as cotações para mostrar em tela
//$sql = "SELECT * FROM cotacao_abertura cot INNER JOIN cotacao_status sta ON cot.id_cotacao_status = sta.id INNER JOIN cotacao_situacao_atual sit ON cot.id_status_situacao_atual_cliente = sit.id WHERE cot.id_empresa = :id_empresa ORDER BY data_envio DESC";
$sql = "SELECT * FROM cotacao_abertura ca
		INNER JOIN cotacao_status cs ON ca.id_status_cotacao = cs.id_status_cotacao
		INNER JOIN cotacao_situacao_atual_cliente csac ON ca.id_status_situacao_atual_cliente = csac.id_status_situacao_atual_cliente
		WHERE ca.finalizada = 1
		ORDER BY ca.data_add DESC";
$stmt = $PDO->prepare($sql);
$stmt->execute();
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

//var_dump($resultados);
//die;

function adicionarDiasUteis(
    $data,
    $dias
) {
    $data = new DateTime($data);

    for ($i = 0; $i < $dias; $i++) {
        $data->modify('+1 day');
        while ($data->format('N') >= 6) { // 6 e 7 representam sábado e domingo
            $data->modify('+1 day');
        }
    }

    return $data->format('Y-m-d');
}

?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Cotação de Frete Internacional</h1>
                <small>Listar</small>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item">Cotação de Frete Internacional</li>
                    <li class="breadcrumb-item active">Listar</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Listando cotações</h3>
                    </div>

                    <!-- /.card-header -->
                    <div class="card-body">
                        <!-- /.row -->
                        <div class="row">
                            <div class="col-lg-12">
                                <?php if (!empty($resultados)) : ?>
                                <table id="listCotacoes" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nº Proc ComexManager</th>
                                            <th>Situação Atual</th>
                                            <th>Status</th>
                                            <th>Nº Proc Interno</th>
                                            <th class="text-center">Data encerramento</th>
                                            <th class="text-center">Hora encerramento</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($resultados as $resultado) : ?>
                                        <tr>
                                            <td><?= $resultado['n_proc_logix']; ?>
                                            </td>
                                            <td>
                                                <span class="label label-<?= $resultado['css_cliente']; ?>">
                                                    <?= $resultado['titulo_sa_cliente']; ?>
                                                </span>
                                            </td>
                                            <td><?= $resultado['titulo_status_cotacao']; ?>
                                            </td>
                                            <td><?= $resultado['n_proc_interno']; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                            $data_sql = $resultado['data_add'];
                                            $diasUteis = 2;
                                            $dataFinal = adicionarDiasUteis($data_sql, $diasUteis);
                                            $data_formatada = date('d/m/Y', strtotime($dataFinal));

                                            echo $data_formatada;
                                            ?>
                                            </td>
                                            <td class="text-center">
                                                <?= $resultado['hora_add']; ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="frete.php?a=frete&b=frete_abrir&idCotacao=<?= $resultado['id']; ?>"
                                                    target="_blank"><i class="fas fa-eye"
                                                        title="Visualizar Cotação"></i></a>
                                                &nbsp;&nbsp;
                                                <a
                                                    href="cotacao.php?a=cotacao&b=cotacao_proposta&idCotacao=<?= $resultado['id']; ?>">
                                                    <i class="fas fa-solid fa-clipboard-list"
                                                        title="Cadastrar Propostas"></i></a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php else : ?>
                                <div class="box-body">
                                    <p>Não existem cotações para serem exibidas</p>
                                </div>
                                <?php endif; ?>
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->


<script>
$(document).ready(function() {
    // Verifique se o parâmetro 'flag' está presente na URL
    var flagParam =
        '<?php echo isset($_GET['flag']) ? $_GET['flag'] : ''; ?>';
    var tipParam =
        '<?php echo isset($_GET['tip']) ? $_GET['tip'] : ''; ?>';

    // Se 'flag' estiver presente e for igual a 'erro', exiba a notificação de erro
    if (flagParam == 'erro') {
        toastr.error(tipParam);
    }
    if (flagParam == 'warning') {
        toastr.warning(tipParam);
    }
    if (flagParam == 'success') {
        toastr.success(tipParam);
    }
});
</script>