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
                <small>Ferramentas -> Freight Index</small>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item">Configurações</li>
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
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Listando dados cadastrados</h3>
                    </div>

                    <!-- /.card-header -->
                    <div class="card-body">

                        <div class="row">
                            <div class="col-lg-8">
                                <table class="table table-striped table-hover">
                                    <tbody>
                                        <tr>
                                            <th class="text-center">ID</th>
                                            <th class="text-center">Tipo</th>
                                            <th class="text-center">País</th>
                                            <th class="text-center">Data situação</th>
                                            <th class="text-center">Valor ($)</th>
                                            <th></th>
                                        </tr>
                                        <?php foreach ($resultados as $resultado) : ?>
                                        <tr>
                                            <td class="text-center">
                                                <?= $resultado['id']; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if($resultado['tipo']==1) : ?>
                                                Container (FCL)
                                                <?php else : ?>
                                                Aéreo por kg
                                                <?php endif;?>
                                            </td>
                                            <td class="text-center">
                                                <?= $resultado['pais']; ?>
                                            </td>
                                            <td class="text-center">
                                                <?= dateConvert($resultado['data_situacao']); ?>
                                            </td>
                                            <td class="text-center">
                                                <?= number_format($resultado['valor'], 2, ',', '.'); ?>
                                            </td>
                                            <td>
                                                <a href="config/ferramentas_freight_index_excluir.php?id=<?= $resultado['id']; ?>"
                                                    onclick="return confirm('Deseja realmente excluir?')"><i
                                                        class="fa fa-fw fa-trash" title="Excluir"></i></a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-lg-4"
                                style="background-color: #046868; padding: 10px;color:#FFF; border-radius: 5px;">
                                <h4 style="text-align:center;"><strong>Cadastrar
                                        Dados</strong></h4>
                                <hr style="border: 1px solid #cacaca; text-align:center;">
                                <!-- form start -->
                                <form role="form" name="add-frete" method="post"
                                    action="config/ferramentas_freight_index_gravar.php">

                                    <div class="form-group">
                                        <label for="InputFile">Tipo</label>
                                        <select name="tipo" class="form-control">
                                            <option value="">Selecione um tipo</option>
                                            <option value="1">Container (FCL)</option>
                                            <option value="2">Aéreo por kg</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="InputFile">País</label>
                                        <input type="text" name="pais" class="form-control" value="China">
                                    </div>

                                    <div class="form-group">
                                        <label for="InputFile">Data</label>
                                        <input type="date" name="data_situacao" class="form-control"
                                            value="<?=date('Y-m-d');?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="valor_fi">Valor ($)</label>
                                        <input type="text" id="valor_fi" name="valor_fi" class="form-control"
                                            placeholder="0,00">
                                    </div>

                                    <div class="box-footer text-center">
                                        <input type="submit" class="btn btn-info" name="submit" value="Cadastrar">
                                    </div>
                                </form>
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