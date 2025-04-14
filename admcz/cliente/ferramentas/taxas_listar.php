<?php
$PDO = db_connect();

$sql = "SELECT * FROM taxa_cambial tc
        INNER JOIN taxa_cambial_aux tca ON tca.moeda = tc.moeda
        ORDER BY tc.cod_moeda ASC";
$stmt = $PDO->prepare($sql);
$stmt->execute();
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Ferramentas</h1>
                <small>Taxas Cambiais</small>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item">Ferramentas</li>
                    <li class="breadcrumb-item active">Taxas Cambiais</li>
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
                        <h3 class="card-title">Listando Taxas</h3>
                    </div>

                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="listaMoedas" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Moeda</th>
                                    <th>ISO</th>
                                    <th>País</th>
                                    <th>Valor R$</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($resultados as $resultado) : ?>
                                <tr>
                                    <td><?= $resultado['nome_moeda']; ?></span>
                                    </td>
                                    <td><?= $resultado['moeda']; ?>
                                    </td>
                                    <td><?= $resultado['pais_moeda']; ?>
                                    </td>
                                    <td><?= $resultado['real_venda']; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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