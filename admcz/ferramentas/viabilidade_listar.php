<?php
$idEmpresa = $_SESSION['empresa_id'];

//var_dump($idEmpresa);die();

$PDO = db_connect();

$sql = "SELECT pv.*, tc.real_venda FROM planilha_viabilidade pv
        LEFT JOIN taxa_cambial tc ON pv.moeda_padrao = tc.moeda 
        ORDER BY pv.id_pv DESC";
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
                <small>Viabilidade</small>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item">Ferramentas</li>
                    <li class="breadcrumb-item active">Viabilidade</li>
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
                        <h3 class="card-title">Listando estudos de viabilidade</h3>
                    </div>

                    <!-- /.card-header -->
                    <div class="card-body">
                        <!-- /.row -->
                        <div class="row">
                            <div class="col-lg-12">
                                <p>
                                    <a href="ferramentas.php?a=ferramentas&b=viabilidade_criar" class="btn btn-info">
                                        <i class="fa fa-plus"></i> Incluir Novo Estudo
                                    </a>
                                </p>
                                <table id="listaEstudo" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 10px;">ID</th>
                                            <th>Data do Estudo</th>
                                            <th>Nome do Estudo</th>
                                            <th>Origem</th>
                                            <th>Destino</th>
                                            <th class="text-center" style="width: 160px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($resultados as $resultado) : ?>
                                        <tr>
                                            <td style="width: 10px;">
                                                <?= $resultado['id_pv']; ?>
                                            </td>
                                            <td>
                                                <?= dateConvert($resultado['data_add']); ?>
                                            </td>
                                            <td><?= $resultado['nome_estudo']; ?>
                                            </td>
                                            <td>
                                                <?= $resultado['origem']; ?>
                                            </td>
                                            <td>
                                                <?= $resultado['destino']; ?>
                                            </td>
                                            <td class="text-center" style="width: 150px;">
                                                <a href="ferramentas.php?a=ferramentas&b=viabilidade_criar&visualizar=1&idEstudo=<?=$resultado['id_pv'];?>"
                                                    class="btn btn-xs btn-success"><i class="fa fa-eye"></i>
                                                    Visualizar</a>

                                                <a href="ferramentas/viabilidade_deletar.php?idEstudo=<?=$resultado['id_pv'];?>"
                                                    class="btn btn-xs btn-danger"
                                                    onclick="return confirm('Você tem certeza que deseja excluir este estudo?');"><i
                                                        class="fas fa-trash"></i>
                                                    Excluir</a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
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