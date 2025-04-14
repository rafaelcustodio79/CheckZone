<?php
$PDO = db_connect();

$sql = "SELECT * FROM ncm ORDER BY ncm ASC";
$stmt = $PDO->prepare($sql);
$stmt->execute();
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<style>
#listaNcm {
    display: none;
}

.progress-bar {
    background-color: #046868 !important;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
        opacity: 1;
    }

    50% {
        opacity: 0.5;
    }

    100% {
        transform: rotate(360deg);
        opacity: 1;
    }
}

.loading-icon {
    display: inline-block;
    /* Para alinhar corretamente */
    animation: spin 1.5s linear infinite;
    /* Define a animação */
}
</style>
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Ferramentas</h1>
                <small>NCM</small>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item">Ferramentas</li>
                    <li class="breadcrumb-item active">NCM</li>
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
                        <h3 class="card-title">Listando NCM's</h3>
                    </div>

                    <!-- /.card-header -->
                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-12">
                                <div class="box box-solid">
                                    <div class="box-header with-border">
                                        <h3 class="card-title">Carregando dados <i
                                                class="fas fa-spinner loading-icon"></i></h3>
                                        <br>
                                    </div>
                                    <div class="box-body">
                                        <div class="progress">
                                            <div class="progress-bar" id="progress-bar" role="progressbar"
                                                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
                                                style="width: 0%">
                                                <span class="sr-only">0% Complete</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">

                                <table id="listaNcm" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>NCM</th>
                                            <th>Descrição</th>
                                            <th>II</th>
                                            <th>IPI</th>
                                            <th>PIS</th>
                                            <th>COFINS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($resultados as $resultado) : ?>
                                        <tr>
                                            <td><?= $resultado['ncm']; ?></span>
                                            </td>
                                            <td><?= $resultado['descricao']; ?>
                                            </td>
                                            <td><?= $resultado['ii']; ?>
                                            </td>
                                            <td><?= $resultado['ipi']; ?>
                                            </td>
                                            <td><?= $resultado['pis']; ?>
                                            </td>
                                            <td><?= $resultado['cofins']; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>

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

<!-- Inclua jQuery e Bootstrap JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>


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