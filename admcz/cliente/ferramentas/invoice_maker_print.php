<?php
// Inicia sessões
session_start();
require '../functions/globals.php';
require '../functions/verifica-log.php';

$idEmpresa = $_SESSION['empresa_id'];

// Sanitiza o valor de idIM recebido via GET
$idIM = filter_input(INPUT_GET, 'idIM', FILTER_SANITIZE_NUMBER_INT);
$invoice = filter_input(INPUT_GET, 'invoice', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if ($idIM === null || $invoice === null) {
    header('Location: ../cliente.php?pg=listar_invoice_maker&flag=erro&tip=ID da Invoice não fornecida.');
    exit;
}

$PDO = db_connect();

$sql = "SELECT im.*, 
        ims.*, ims.company AS empresaSender, ims.vat_number AS vatSender, ims.address AS addressSender, ims.post_code AS postCodeSender, ims.city_country AS cityCountrySender, 
        imr.*, imr.company AS empresaReceiver, imr.vat_number AS vatReceiver, imr.address AS addressReceiver, imr.post_code AS postCodeReceiver, imr.city_country AS cityCountryReceiver,
        imb.* 
        FROM invoice_maker im
        INNER JOIN invoice_maker_sender ims ON im.id_im_sender = ims.id_im_sender
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

<!DOCTYPE html>
<html>

<head>
    <!-- Google Tag Manager -->
    <script>
    (function(w, d, s, l, i) {
        w[l] = w[l] || [];
        w[l].push({
            'gtm.start': new Date().getTime(),
            event: 'gtm.js'
        });
        var f = d.getElementsByTagName(s)[0],
            j = d.createElement(s),
            dl = l != 'dataLayer' ? '&l=' + l : '';
        j.async = true;
        j.src =
            'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
        f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', 'GTM-T4P63BVB');
    </script>
    <!-- End Google Tag Manager -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>COMMERCIAL INVOICE</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="../framework/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../framework/bower_components/font-awesome/css/font-awesome.min.css">
    <!-- favicon -->
    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon" />
    <!-- Ionicons -->
    <link rel="stylesheet" href="../framework/bower_components/Ionicons/css/ionicons.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="../framework/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../framework/dist/css/AdminLTE.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="../framework/dist/css/skins/_all-skins.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="../framework/bower_components/select2/dist/css/select2.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="../framework/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../framework/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="../framework/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- Morris charts -->
    <link rel="stylesheet" href="../framework/bower_components/morris.js/morris.css">

    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@opencage/geosearch-bundle/dist/css/autocomplete-theme-classic.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/@opencage/geosearch-bundle"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap"
        rel="stylesheet">
    <!--<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">-->

</head>

<body class="hold-transition skin-blue sidebar-mini">

    <!-- Main content -->
    <section class="invoice">
        <div class="row" style="padding: 20px;">
            <div class="col-12 card-invoice">
                <!-- title row -->
                <div class="row">
                    <div class="col-xs-12">
                        <h2 class="page-header">
                            <i class="fa fa-globe"></i> COMMERCIAL INVOICE
                            <small class="pull-right">
                                Date:
                                <?=dateConvert($resultados['date_created']);?>
                            </small>
                        </h2>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- info row -->
                <div class="row invoice-info">
                    <div class="col-sm-3 invoice-col">
                        Sender
                        <address>
                            <strong><?=$resultados['empresaSender'];?></strong><br>
                            <?=$resultados['vatSender'];?><br>
                            <?=$resultados['addressSender'];?><br>
                            <?=$resultados['cityCountrySender'];?>
                            -
                            <?=$resultados['postCodeSender'];?><br>
                            <?=$resultados['name_im_sender'];?><br>
                            <?=$resultados['email_im_sender'];?>
                        </address>
                    </div>
                    <!-- /.col -->
                    <!-- Receiver -->
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

                    <!-- Condicional: SENDER / DELIVERY -->

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
                    <!-- /.col -->
                </div>
                <!-- /.row -->

                <!-- Table row -->
                <div class="row">
                    <div class="col-xs-12">
                        <table class="table table-striped table-condensed" style="font-size: 10px;">
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
                                        <?=number_format($goods['unit_price'], 2, ',', '.');?>
                                    </td>
                                    <td class="text-right">
                                        <?php
                            $subtotalProd = $goods['qty'] * $goods['unit_price'];
                                    echo number_format($subtotalProd, 2, ',', '.');
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
                    <div class="col-xs-6">
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
                    <div class="col-xs-1">
                        <div class="clearfix"></div>
                    </div>
                    <div class="col-xs-5">

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
                                        <?php echo $resultados['currency'] . ' ' . number_format($resultados['shipping_cost'] ?? 0, 2, ',', '.'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-right">Insurance:</th>
                                    <td class="text-right">
                                        <?php echo $resultados['currency'] . ' ' . number_format($resultados['insurance'] ?? 0, 2, ',', '.'); ?>
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
                            <i class="fa fa-check-square-o"></i>
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
    </section>

    <!-- jQuery 3 -->
    <script src="framework/bower_components/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="framework/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- Select2 -->
    <script src="framework/bower_components/select2/dist/js/select2.full.min.js"></script>
    <!-- SlimScroll -->
    <script src="framework/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
    <!-- AdminLTE App -->
    <script src="framework/dist/js/adminlte.min.js"></script>
    <!-- DataTables  & Plugins -->
    <script src="framework/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="framework/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="framework/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="framework/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="framework/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="framework/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="framework/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="framework/plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="framework/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

    <script type="text/javascript">
    window.print();
    </script>

</body>

</html>