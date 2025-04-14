<?php
$idEmpresa = $_SESSION['empresa_id'];

// Sanitiza o valor de idIM recebido via GET
$idIM = filter_input(INPUT_GET, 'idIM', FILTER_SANITIZE_NUMBER_INT);
$invoice = filter_input(INPUT_GET, 'invoice', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if ($idIM === null || $invoice === null) {
    header('Location: ../ferramentas.php?a=ferramentas&b=invoice_maker_listar&flag=erro&tip=ID da Invoice não fornecida.');
    exit;
}

$PDO = db_connect();

$sql = "SELECT im.*, 
        ims.*, ims.company AS empresaSender, ims.vat_number AS vatSender, ims.address AS addressSender, ims.post_code AS postCodeSender, ims.city_country AS cityCountrySender, ims.name_im_sender AS nomeCttSender, ims.email_im_sender AS emailCttSender,
        imf.*, imf.company AS empresaManufacture, imf.vat_number AS vatManufacture, imf.address AS addressManufacture, imf.post_code AS postCodeManufacture, imf.city_country AS cityCountryManufacture, imf.name_im_sender AS nomeCttManufacture, imf.email_im_sender AS emailCttManufacture,
        imr.*, imr.company AS empresaReceiver, imr.vat_number AS vatReceiver, imr.address AS addressReceiver, imr.post_code AS postCodeReceiver, imr.city_country AS cityCountryReceiver,
        imb.* 
        FROM invoice_maker im
        INNER JOIN invoice_maker_sender ims ON im.id_im_sender = ims.id_im_sender
        LEFT JOIN invoice_maker_sender imf ON im.id_im_manufacture = imf.id_im_sender
        INNER JOIN invoice_maker_receiver imr ON im.id_im_receiver = imr.id_im_receiver
        LEFT JOIN  invoice_maker_bank imb ON im.id_im = imb.id_im AND im.invoice_number = imb.invoice_number
        WHERE im.id_empresa = :id_empresa AND im.id_im = :id_im AND im.invoice_number = :invoice_number";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':id_empresa', $idEmpresa);
$stmt->bindParam(':id_im', $idIM);
$stmt->bindParam(':invoice_number', $invoice);
$stmt->execute();
$resultados = $stmt->fetch(PDO::FETCH_ASSOC);

// Selecionando GOODS
$sqlGoods = "SELECT * FROM invoice_maker_goods
            WHERE id_empresa = :idEmpresa AND invoice_number = :invoice_number AND id_im = :id_im
            ORDER BY id_im_goods ASC";
$stmtGoods = $PDO->prepare($sqlGoods);
$stmtGoods->bindValue(':idEmpresa', $idEmpresa);
$stmtGoods->bindParam(':id_im', $idIM);
$stmtGoods->bindParam(':invoice_number', $invoice);
$stmtGoods->execute();
$resultadosGoods = $stmtGoods->fetchAll(PDO::FETCH_ASSOC);

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

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 card-invoice">

                <!-- Main content -->
                <div class="invoice p-3 mb-3">
                    <!-- title row -->
                    <div class="row">
                        <div class="col-12">
                            <h4>
                                <i class="fas fa-globe"></i> COMMERCIAL INVOICE
                                <small class="float-right">
                                    Date:
                                    <?=dateConvert($resultados['date_created']);?>
                                </small>
                            </h4>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- info row -->
                    <div class="row invoice-info">
                        <div class="col-sm-3 invoice-col">
                            <?= !empty($resultados['empresaManufacture']) ? 'Sender' : 'Sender'; ?>
                            <address>
                                <strong><?=$resultados['empresaSender'];?></strong><br>
                                Vat Number:
                                <?=$resultados['vatSender'];?><br>
                                Address:
                                <?=$resultados['addressSender'];?><br>
                                City / Country:
                                <?=$resultados['cityCountrySender'];?>
                                -
                                <?=$resultados['postCodeSender'];?><br>
                                Name:
                                <?=$resultados['nomeCttSender'];?><br>
                                Email:
                                <?=$resultados['emailCttSender'];?>
                            </address>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-3 invoice-col">
                            Receiver
                            <address>
                                <strong><?=$resultados['empresaReceiver'];?></strong><br>
                                CNPJ:
                                <?=$resultados['vatReceiver'];?><br>
                                Address:
                                <?=$resultados['addressReceiver'];?><br>
                                City / Country:
                                <?=$resultados['cityCountryReceiver'];?>
                                -
                                <?=$resultados['postCodeReceiver'];?><br>
                                Name:
                                <?=$resultados['name_im_receiver'];?><br>
                                Email:
                                <?=$resultados['email_im_receiver'];?>
                            </address>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-3 invoice-col">
                            Delivery
                            <address>
                                <strong><?=$resultados['empresaReceiver'];?></strong><br>
                                CNPJ:
                                <?=$resultados['vatReceiver'];?><br>
                                <?php if(!is_null($resultados['address_delivery'])) : ?>
                                Address:
                                <?=$resultados['address_delivery'];?>
                                <?php else : ?>
                                Address:
                                <?=$resultados['addressReceiver'];?>
                                <?php endif;?><br>
                                City / Country:
                                <?=$resultados['cityCountryReceiver'];?>
                                -
                                <?=$resultados['postCodeReceiver'];?><br>
                                Name:
                                <?=$resultados['name_im_receiver'];?><br>
                                Email:
                                <?=$resultados['email_im_receiver'];?>
                            </address>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-3 invoice-col">
                            <b>Invoice
                                #<?=$resultados['invoice_number'];?></b><br>
                            <br>
                            <b>Currency:</b>
                            <?=$resultados['currency'];?><br>
                            <b>Reason for Export:</b>
                            <?=$resultados['reason_for_export'];?><br>
                            <b>Incoterms:</b>
                            <?=$resultados['incoterms'];?><br>
                            <b>Terms of Payment :</b>
                            <?=$resultados['terms_payment'];?><br>
                            <b>Country of Origin:</b>
                            <?=$resultados['country_origin'];?><br>
                        </div>
                    </div>
                    <!-- /.row -->

                    <!-- Table row -->
                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>NAME</th>
                                        <th>DETAILED DESCRIPTION</th>
                                        <th class="text-center">NCM</th>
                                        <th>SPECIFICATION</th>
                                        <th class="text-center">UNIT WEIGHT (KG)</th>
                                        <th class="text-center">NW KG</th>
                                        <th class="text-center">GW KG</th>
                                        <th class="text-center">No. BOXES</th>
                                        <th class="text-center">QTY</th>
                                        <th class="text-right">UNIT PRICE</th>
                                        <th class="text-right">TOTAL PRODUCT COST</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $subTotal = 0; ?>
                                    <?php foreach ($resultadosGoods as $goods) :?>
                                    <tr>
                                        <td>
                                            <?=$goods['name'];?>
                                        </td>
                                        <td>
                                            <?=$goods['description'];?>
                                        </td>
                                        <td class="text-center">
                                            <?=$goods['hs_code'];?>
                                        </td>
                                        <td>
                                            <?=$goods['color'];?>
                                        </td>
                                        <td class="text-center">
                                            <?=$goods['unit_weight'];?>
                                        </td>
                                        <td class="text-center">
                                            <?=$goods['nw_kg'];?>
                                        </td>
                                        <td class="text-center">
                                            <?=$goods['gw_kg'];?>
                                        </td>
                                        <td class="text-center">
                                            <?=$goods['no_boxes'];?>
                                        </td>
                                        <td class="text-center">
                                            <?=$goods['qty'];?>
                                        </td>
                                        <td class="text-right">
                                            <?=number_format($goods['unit_price'], 4, ',', '.');?>
                                        </td>
                                        <td class="text-right">
                                            <?php
                            $subtotalProd = $goods['qty'] * $goods['unit_price'];
                                echo number_format($subtotalProd, 4, ',', '.');
                                $subTotal += $subtotalProd;
                                ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <div class="row">
                        <!-- accepted payments column -->
                        <div class="col-6">
                            <p class="lead">Details of Bank:</p>

                            <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                                <strong>Beneficiary Name:</strong>&nbsp;&nbsp;
                                <?=$resultados['beneficiary_name'];?><br>
                                <strong>Beneficiary Account Number:</strong>&nbsp;&nbsp;
                                <?=$resultados['beneficiary_an'];?><br>
                                <strong>Beneficiary Address:</strong>&nbsp;&nbsp;
                                <?=$resultados['beneficiary_address'];?><br>
                                <strong>Swift Code:</strong>&nbsp;&nbsp;
                                <?=$resultados['swift_code'];?><br>
                                <strong>Beneficiary Bank:</strong>&nbsp;&nbsp;
                                <?=$resultados['beneficiary_bank'];?><br>
                                <strong>Remark:</strong>&nbsp;&nbsp;
                                <?=$resultados['remark'];?><br>
                            </p>
                        </div>
                        <!-- /.col -->
                        <div class="col-6">

                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <th class="text-right" style="width:40%">Subtotal:</th>
                                        <td class="text-right">
                                            <?php echo $resultados['currency'] . ' ' .number_format($subTotal, 2, ',', '.');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-right">Shipping Cost:</th>
                                        <td class="text-right">
                                            <?php
                                        // Verifica se o valor não é nulo antes de formatar
                                        $shipping_cost = isset($resultados['shipping_cost']) && !is_null($resultados['shipping_cost']) ? $resultados['shipping_cost'] : 0;

// Exibe o valor formatado com a moeda
echo $resultados['currency'] . ' ' . number_format($shipping_cost, 2, ',', '.');
?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-right">Insurance:</th>
                                        <td class="text-right">
                                            <?php
                                        // Verifica se o valor não é nulo antes de formatar
                                        $insurance = isset($resultados['insurance']) && !is_null($resultados['insurance']) ? $resultados['insurance'] : 0;

// Exibe o valor formatado com a moeda
echo $resultados['currency'] . ' ' . number_format($insurance, 2, ',', '.');
?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-right">Total:</th>
                                        <td class="text-right">
                                            <?php
                                $totalGeral = $subTotal + $resultados['shipping_cost'] + $resultados['insurance'];
echo $resultados['currency'] . ' ' .number_format($totalGeral, 2, ',', '.');?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <div class="row">
                        <div class="col-md-12" style="padding-top: 20px;padding-right:20px;">
                            <p>
                                <i class="far fa-check-square"></i>
                                I declare that the content of this invoice is true and correct
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center" style="padding-top: 70px;">
                            <p>
                                ______________________________________________________________________
                            </p>
                            <p>
                                NAME AND SIGNATURE &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;PLACE
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.row -->
    </div>
    <!-- this row will not appear when printing -->
    <div class="row">
        <div class="col-md-12 text-center">
            <p class="text-center">
                <a href="ferramentas/invoice_maker_print.php?idIM=<?=$idIM;?>&invoice=<?=$invoice;?>" target="_blank"
                    class="btn btn-default"><i class="fas fa-print"></i>
                    Imprimir</a>
                <a href="#" class="btn btn-info btnImprimir">
                    <i class="fas fa-file-pdf"></i> Gerar PDF
                </a>
            </p>
        </div>
    </div><!-- /.container-fluid -->
</section>

<div class="clearfix"></div>

<div class="modal modal-success fade" id="modal-sucesso">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><b><i class="fa fa-fw fa-check"></i> Sucesso!</b></h4>
            </div>
            <div class="modal-body">
                <p><?= $_GET['tip']; ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Fechar</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal modal-danger fade" id="modal-erro">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><b><i class="fa fa-fw fa-warning"></i> Erro</b></h4>
            </div>
            <div class="modal-body">
                <p><?= $_GET['tip']; ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Fechar</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<!-- Inclua jQuery e Bootstrap JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<script type="text/javascript">
window.addEventListener('DOMContentLoaded', function() {
    $('#modal-informacao').modal('show');
});

var printButtons = document.querySelectorAll('.btnImprimir');
var num_invoice = <?php echo json_encode($invoice); ?>;

printButtons.forEach(function(button) {
    button.addEventListener('click', function(event) {
        // Impede que o link navegue para outra página
        event.preventDefault();

        // Seleciona a carteirinha mais próxima do botão clicado
        var card = button.parentElement.previousElementSibling.querySelector('.card-invoice');

        // Ajustar o estilo da carteirinha para o PDF
        card.style.fontSize = '10px'; // Diminui o tamanho da fonte para o PDF
        card.style.margin = '10px'; // Aumenta as margens no PDF

        // Configurações para o PDF
        var opt = {
            margin: [0.5, 0.5, 0.5, 0.5], // Definir margens maiores
            filename: 'invoice_maker_' + num_invoice + '.pdf',
            image: {
                type: 'jpeg',
                quality: 0.98
            },
            html2canvas: {
                scale: 1.3, // Reduzir o scale para melhorar a renderização
                useCORS: true // Usar CORS se houver imagens externas
            },
            jsPDF: {
                unit: 'in',
                format: 'A4',
                orientation: 'portrait'
            },
            pagebreak: {
                mode: ['avoid-all', 'css', 'legacy'] // Evitar cortes no conteúdo
            }
        };

        // Gera o PDF
        html2pdf().from(card).set(opt).save().then(function() {
            // Reverte os ajustes de estilo após a geração do PDF
            card.style.fontSize = '';
            card.style.margin = '';
        });
    });
});
</script>