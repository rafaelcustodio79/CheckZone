<?php
// Inicia sessões
session_start();
require '../functions/globals.php';
require '../functions/verifica-log.php';

$PDO = db_connect();

$PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obter dados do formulário e proteger contra injeção SQL
    $idUsuario = $_POST['idUsuario'] ?? null;
    $idProcesso = $_POST['idProcesso'] ?? null;
    $idEmpresa = $_POST['idEmpresa'] ?? null;
    $incoterms = $_POST['incoterms'] ?? null;
    $pais_origem = $_POST['pais_origem'] ?? null;
    $pais_destino = $_POST['pais_destino'] ?? null;
    $porto_descarga = $_POST['porto_descarga'] ?? null;
    $porto_destino = $_POST['porto_destino'] ?? null;
    $terminal_descarga = $_POST['terminal_descarga'] ?? null;
    $n_li = $_POST['n_li'] ?? null;
    $status_li = $_POST['status_li'] ?? null;
    $n_di = $_POST['n_di'] ?? null;
    $registro_di = $_POST['registro_di'] ?? null;
    $parametrizacao = $_POST['parametrizacao'] ?? null;
    $observacoes = $_POST['observacoes'] ?? null;
    $descricao_produto = $_POST['descricao_produto'] ?? null;
    $exportador = $_POST['exportador'] ?? null;
    $n_invoice = $_POST['n_invoice'] ?? null;
    $carga = $_POST['carga'] ?? null;
    $bl = $_POST['bl'] ?? null;
    $eta = $_POST['eta'] ?? null;
    $confirmacao_chegada = $_POST['confirmacao_chegada'] ?? null;
    $quantidade = $_POST['quantidade'] ?? null;
    $nome_navio = $_POST['nome_navio'] ?? null;
    $vencimento_armazenagem = $_POST['vencimento_armazenagem'] ?? null;

    // Verifica se o idProcesso existe
    $stmt = $PDO->prepare("SELECT COUNT(*) FROM processos_informacoes WHERE id_processo = :idProcesso");
    $stmt->bindParam(':idProcesso', $idProcesso, PDO::PARAM_INT);
    $stmt->execute();
    $existe = $stmt->fetchColumn();

    if ($existe) {
        // Atualizar o registro existente
        $stmt = $PDO->prepare("UPDATE processos_informacoes SET
            id_empresa = :idEmpresa,
            incoterms = :incoterms,
            pais_origem = :pais_origem,
            pais_destino = :pais_destino,
            porto_descarga = :porto_descarga,
            porto_destino = :porto_destino,
            terminal_descarga = :terminal_descarga,
            n_li = :n_li,
            status_li = :status_li,
            n_di = :n_di,
            registro_di = :registro_di,
            parametrizacao = :parametrizacao,
            observacoes = :observacoes,
            descricao_produto = :descricao_produto,
            exportador = :exportador,
            n_invoice = :n_invoice,
            carga = :carga,
            bl = :bl,
            eta = :eta,
            confirmacao_chegada = :confirmacao_chegada,
            quantidade = :quantidade,
            nome_navio = :nome_navio,
            vencimento_armazenagem = :vencimento_armazenagem
            WHERE id_processo = :idProcesso");

    } else {
        // Inserir um novo registro
        $stmt = $PDO->prepare("INSERT INTO processos_informacoes (
            id_processo, id_empresa, incoterms, pais_origem, pais_destino, porto_descarga, porto_destino,
            terminal_descarga, n_li, status_li, n_di, parametrizacao, descricao_produto, carga, bl, eta, confirmacao_chegada, quantidade, 
            nome_navio, registro_di, observacoes, exportador, n_invoice, vencimento_armazenagem
        ) VALUES (
            :idProcesso, :idEmpresa, :incoterms, :pais_origem, :pais_destino, :porto_descarga, :porto_destino,
            :terminal_descarga, :n_li, :status_li, :n_di, :parametrizacao, :descricao_produto, :carga, :bl, :eta, :confirmacao_chegada, :quantidade, 
            :nome_navio, :registro_di, :observacoes, :exportador, :n_invoice, :vencimento_armazenagem
        )");
    }


    try {
        // Executar a consulta com os parâmetros
    $stmt->bindParam(':idProcesso', $idProcesso);
    $stmt->bindParam(':idEmpresa', $idEmpresa);
    $stmt->bindParam(':incoterms', $incoterms);
    $stmt->bindParam(':pais_origem', $pais_origem);
    $stmt->bindParam(':pais_destino', $pais_destino);
    $stmt->bindParam(':porto_descarga', $porto_descarga);
    $stmt->bindParam(':porto_destino', $porto_destino);
    $stmt->bindParam(':terminal_descarga', $terminal_descarga);
    $stmt->bindParam(':n_li', $n_li);
    $stmt->bindParam(':status_li', $status_li);
    $stmt->bindParam(':n_di', $n_di);
    $stmt->bindParam(':registro_di', $registro_di);
    $stmt->bindParam(':parametrizacao', $parametrizacao);
    $stmt->bindParam(':observacoes', $observacoes);
    $stmt->bindParam(':descricao_produto', $descricao_produto);
    $stmt->bindParam(':carga', $carga);
    $stmt->bindParam(':bl', $bl);
    $stmt->bindParam(':eta', $eta);
    $stmt->bindParam(':confirmacao_chegada', $confirmacao_chegada);
    $stmt->bindParam(':quantidade', $quantidade);
    $stmt->bindParam(':nome_navio', $nome_navio);
    $stmt->bindParam(':vencimento_armazenagem', $vencimento_armazenagem);
    $stmt->bindParam(':exportador', $exportador);
    $stmt->bindParam(':n_invoice', $n_invoice);


        // Execute the statement
        if ($stmt->execute()) {
            # Salvar a ratreabilidade
            $local = 'Informações do processo';
            $sqlR = "INSERT INTO processos_rastreabilidade(id_processo, id_usuario, id_empresa, local) 
                VALUES (:id_processo, :id_usuario, :id_empresa, :local)";
            // Preparar o statement
            $stmtR = $PDO->prepare($sqlR);

            // Bind dos parâmetros obrigatórios
            $stmtR->bindParam(':id_usuario', $idUsuario);
            $stmtR->bindParam(':id_empresa', $idEmpresa);
            $stmtR->bindParam(':id_processo', $idProcesso);
            $stmtR->bindParam(':local', $local);

            // Executa a gravação da rastreabilidade
            $stmtR->execute();


            // Redirecionar para a página do processo
            header('Location: ../aduaneiro.php?a=aduaneiro&b=processos_acoes&idProcesso='.$idProcesso.'&idEmpresa='.$idEmpresa.'&flag=success&tip=Dados do processo preenchido com sucesso!');
            exit;
        } else {
            header('Location: ../aduaneiro.php?a=aduaneiro&b=processos_acoes&idProcesso='.$idProcesso.'&idEmpresa='.$idEmpresa.'&flag=erro&tip=Não foi possível realizar o cadastro das informações');
            exit;
        }
        
    } catch (PDOException $e) {
        // Error handling
        $errorMsg = urlencode($e->getMessage());
        header('Location: ../aduaneiro.php?a=aduaneiro&b=processos&flag=erro&tip='.$errorMsg);
        exit();
    }

}