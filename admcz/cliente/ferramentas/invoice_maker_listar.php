<?php
unset($_SESSION['etapa_atual']); // Remove a variável de sessão
unset($_SESSION['active_step']); // Remove a variável de sessão
$idEmpresa = $_SESSION['empresa_id'];

$PDO = db_connect();

$sql = "SELECT im.*, 
        ims.*, ims.company AS empresaSender, ims.vat_number AS vatSender, ims.address AS addressSender, ims.post_code AS postCodeSender, ims.city_country AS cityCountrySender, 
        imr.*, imr.company AS empresaReceiver, imr.vat_number AS vatReceiver, imr.address AS addressReceiver, imr.post_code AS postCodeReceiver, imr.city_country AS cityCountryReceiver
        FROM invoice_maker im
        INNER JOIN invoice_maker_sender ims ON im.id_im_sender = ims.id_im_sender
        INNER JOIN invoice_maker_receiver imr ON im.id_im_receiver = imr.id_im_receiver
        WHERE im.id_empresa = :id_empresa
        ORDER BY im.id_im DESC";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':id_empresa', $idEmpresa);
$stmt->execute();
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Ferramentas</h1>
                <small>Invoice Maker</small>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item">Ferramentas</li>
                    <li class="breadcrumb-item active">Invoice Maker</li>
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
                        <h3 class="card-title">Listando Invoices criadas</h3>
                    </div>

                    <!-- /.card-header -->
                    <div class="card-body">
                        <!-- /.row -->
                        <div class="row">
                            <div class="col-lg-12">
                                <p>
                                    <a href="cliente.php?a=ferramentas&b=invoice_maker_criar" class="btn btn-info">
                                        <i class="fa fa-plus"></i> Gerar Nova Invoice
                                    </a>
                                </p>
                                <table id="listaInvoice" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 20px;">#</th>
                                            <th>Nº Invoice</th>
                                            <th>País de origem</th>
                                            <th>Exportador</th>
                                            <th>Importador</th>
                                            <th>Criado em</th>
                                            <th class="text-center" style="width: 200px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($resultados as $resultado) : ?>
                                        <tr>
                                            <td>
                                                <?= $resultado['id_im']; ?>
                                            </td>
                                            <td>
                                                <?= $resultado['invoice_number']; ?>
                                            </td>
                                            <td>
                                                <?= $resultado['country_origin']; ?>
                                            </td>
                                            <td>
                                                <?= $resultado['empresaSender']; ?>
                                            </td>
                                            <td>
                                                <?= $resultado['empresaReceiver']; ?>
                                            </td>
                                            <td>
                                                <?= dateConvert($resultado['date_created']);
                                        ?>
                                            </td>
                                            <td class="text-center" style="width: 200px;">
                                                <a href="cliente.php?a=ferramentas&b=invoice_maker_gerar&idIM=<?=$resultado['id_im'];?>&invoice=<?=$resultado['invoice_number'];?>"
                                                    class="btn btn-xs btn-success"><i class="fas fa-eye"></i>
                                                    Visualizar</a>

                                                <a href="cliente.php?a=ferramentas&b=invoice_maker_editar&idIM=<?=$resultado['id_im'];?>&invoice=<?=$resultado['invoice_number'];?>"
                                                    class="btn btn-xs btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                    Editar
                                                </a>

                                                <a href="cliente/ferramentas/invoice_maker_deletar.php?idIM=<?=$resultado['id_im'];?>&invoice=<?=$resultado['invoice_number'];?>"
                                                    class="btn btn-xs btn-danger"
                                                    onclick="return confirm('Você tem certeza que deseja excluir esta Invoice?');"><i
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