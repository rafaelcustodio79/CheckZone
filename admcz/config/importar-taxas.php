<style>
    .dataTables_filter {
        text-align: right;
    }
</style>

<?php


?>
<?php if (isset($_GET['flag']) && $_GET['flag'] == 'success') : ?>
<script type="text/javascript">
    window.addEventListener('DOMContentLoaded', function() {
        $('#modal-sucesso').modal('show');
    });
</script>
<?php endif; ?>

<?php if (isset($_GET['flag']) && $_GET['flag'] == 'erro') : ?>
<script type="text/javascript">
    window.addEventListener('DOMContentLoaded', function() {
        $('#modal-erro').modal('show');
    });
</script>
<?php endif; ?>

<!--

<div class="overlay">
    <div class="modal-aviso">
        <span class="close-button" onclick="window.location.href='index.php'">&#10006;</span>
        <br>
        <span class="label label-warning"><i class="fa fa-exclamation" aria-hidden="true"></i> Atenção</span>
        <h3>Conteúdo Exclusivo</h3>
        <p>Para ter acesso às informações dessa página, entre em contato com a equipe da Logixflash.</p>
    </div>
</div>
    -->
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Importação de Dados
        <small>Taxas Cambiais</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="#"><i class="fa fa-upload"></i> Importação de dados</a></li>
        <li><a href="#"><i class="fa fa-money"></i> Taxas Cambiais</a></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">


    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Taxas Cambiais</h3><br><small>Faça o upload do arquivo</small>
                </div>
                <!-- /.box-header -->

                <!-- form start -->
                <form role="form" name="add-fin" method="post" action="adm/importacao/gravar_taxas.php"
                    enctype="multipart/form-data">

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
            <!-- /.box -->
        </div>
        <!--/.col (left) -->
    </div>
    <!-- /.row -->
</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->
</section>


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

<script type="text/javascript">
    window.addEventListener('DOMContentLoaded', function() {
        $('#modal-informacao').modal('show');
    });
</script>