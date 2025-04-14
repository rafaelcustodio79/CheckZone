<?php
$PDO = db_connect();

$sql = "SELECT * FROM frete_index ORDER BY id DESC LIMIT 10";
$stmt = $PDO->prepare($sql);
$stmt->execute();
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Configurações</h1>
                <small>Ferramentas <i class="fas fa-long-arrow-alt-right"></i> Importando Taxas Cambiais</small>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item">Configurações</li>
                    <li class="breadcrumb-item">Ferramentas</li>
                    <li class="breadcrumb-item active">Importando Taxas Cambiais</li>
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
                    <div class="card-header align-items-center">
                        <h3 class="card-title">Taxas Cambiais</h3><br><small>Faça o upload do arquivo</small></h3>
                    </div>

                    <!-- /.card-header -->
                    <div class="card-body">

                        <!-- form start -->
                        <form role="form" name="add-fin" method="post"
                            action="config/ferramentas_importar_taxas_gravar.php" enctype="multipart/form-data">

                            <div class="row">

                                <div class="col-lg-8">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="InputFile">Arquivo</label>
                                            <input type="file" name="file" id="file" accept=".csv">
                                            <p class="help-block">Somente arquivo em CSV.</p>
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                </div>

                                <div class="col-lg-4">
                                    <div class="box-body">
                                        <div class="form-group">
                                            Pode baixar o arquivo CSV <a
                                                href="https://www.bcb.gov.br/estabilidadefinanceira/historicocotacoes"
                                                target="_blank">clicando aqui</a>.
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                </div>

                            </div>

                            <div class="box-footer text-left">
                                <input type="submit" class="btn btn-primary" name="submit" value="Importar">
                            </div>
                        </form>

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