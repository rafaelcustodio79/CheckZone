<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Banners</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item">Banners</li>
                    <li class="breadcrumb-item active">Adicionar</li>
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
                        <h3 class="card-title">Adicionando banner</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form name="addMateria" method="post" action="banners/salvar.php" enctype="multipart/form-data">
                        <input type="hidden" name="ativo" value="1" />
                        <div class="card-body">

                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="titulo">Chamada</label>
                                        <input type="text" class="form-control" id="chamada" name="chamada" placeholder="Digite a chamada aqui">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="titulo">Título</label>
                                        <input type="text" class="form-control" id="titulo" name="titulo" placeholder="Digite o titulo aqui" required>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="texto">Texto</label>
                                        <input type="text" class="form-control" id="texto" name="texto" placeholder="Digite o texto aqui">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="link">Link (URL)</label>
                                        <input type="text" class="form-control" id="link" name="link" placeholder="Digite a URL aqui">
                                    </div>
                                </div>
                                <div class="col-4 form-group">
                                    <label>Tipo do link</label>
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" id="cat-0" name="tplink" value="_self" checked>
                                        <label for="cat-0" class="custom-control-label"><b>Na mesma página</b></label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" id="cat-1" name="tplink" value="_blank">
                                        <label for="cat-1" class="custom-control-label">Nova janela do navegador</label>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="imagens">Selecione imagem para upload:</label>
                                        <input type="file" name="arquivo" id="arquivo" accept="image/*" required>
                                        <small>Utilize imagens com pelo menos 1920 pixels de largura por 715 pixels de altura.</small>
                                    </div>
                                </div>
                            </div>


                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer">
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