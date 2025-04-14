<?php
$id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

if (!is_numeric($id)) {
    header('Location: banners.php?a=banners&b=listar&flag=erro&tip=Não foi possível realizar a operação.');
    exit();
}

$PDO = db_connect();

$sql = "SELECT * FROM banner WHERE id_banner = :id";
$stmt = $PDO->prepare($sql);
$stmt->bindValue(':id', $id);
$stmt->execute();
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Banners</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="banners.php?a=banners&b=listar">Banners</a></li>
                    <li class="breadcrumb-item active">Editar</li>
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
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Editando dados do banner</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form name="addMateria" method="post" action="banners/salvar.php" enctype="multipart/form-data">
                        <input type="hidden" name="tipoSalvar" value="editar" />
                        <input type="hidden" name="idBanner" value="<?= $id; ?>" />
                        <input type="hidden" name="ativo" value="1" />
                        <div class="card-body">
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="titulo">Chamada</label>
                                        <input type="text" class="form-control" id="chamada" name="chamada" value="<?= $resultados[0]['chamada']; ?>">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="titulo">Título</label>
                                        <input type="text" class="form-control" id="titulo" name="titulo" value="<?= $resultados[0]['titulo']; ?>" required>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="texto">Texto</label>
                                        <input type="text" class="form-control" id="texto" name="texto" value="<?= $resultados[0]['texto']; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="link">Link (URL)</label>
                                        <input type="text" class="form-control" id="link" name="link" value="<?= $resultados[0]['link']; ?>">
                                    </div>
                                </div>
                                <div class="col-4 form-group">
                                    <label>Tipo do link</label>
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" id="cat-0" name="tplink" value="_self" <?php if ($resultados[0]['tipo_link'] == '_self') {
                                                                                                                                    echo 'checked';
                                                                                                                                } ?>>
                                        <label for="cat-0" class="custom-control-label"><b>Na mesma página</b></label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" id="cat-1" name="tplink" value="_blank" <?php if ($resultados[0]['tipo_link'] == '_blank') {
                                                                                                                                        echo 'checked';
                                                                                                                                    } ?>>
                                        <label for="cat-1" class="custom-control-label">Nova janela do navegador</label>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="imagens">Selecione imagens para upload:</label>
                                        <input type="file" name="arquivo" id="arquivo" accept="image/*">
                                        <br>
                                        <?php if (!empty($resultados[0]['imagem'])) : ?>
                                            <small>Já existe uma imagem para o banner. Para manter a mesma imagem basta deixar o campo em branco.</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer">
                            <button type="button" class="btn btn-default" onclick="voltarPagina()"><i class="fas fa-chevron-circle-left"></i> Voltar</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
                        </div>
                    </form>
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

<div class="modal fade" id="modal-apagar">
    <div class="modal-dialog">
        <div class="modal-content bg-danger">
            <div class="modal-header">
                <h4 class="modal-title">Confirmação de remoção</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja remover a foto de capa da matéria?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark" data-dismiss="modal">Não</button>
                <a href="galerias.php?a=galerias&b=remover-foto&id=<?= $idGaleria; ?>" class="btn btn-outline-dark">Sim</a>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="modal-novaFoto">
    <div class="modal-dialog">
        <div class="modal-content bg-default">
            <div class="modal-header">
                <h4 class="modal-title">Adicionar foto de capa</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form name="addMateria" method="post" action="galerias/salvar-foto.php" enctype="multipart/form-data">
                <div class="modal-body">
                    <!-- form start -->

                    <input type="hidden" name="idGaleria" value="<?= $idGaleria; ?>">
                    <input type="hidden" name="tipoFoto" value="editar" />
                    <div class="card-body">
                        <div class="form-group">
                            <label for="exampleInputFile">UPLOAD DA FOTO</label>
                            <input type="file" id="exampleInputFile" name="arquivo">
                            <small class="help-block">Utilize arquivos de imagem do tipo PNG ou JPG. [Tamanho máximo 10MB]</small>
                        </div>
                    </div>
                    <!-- /.card-body -->

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->