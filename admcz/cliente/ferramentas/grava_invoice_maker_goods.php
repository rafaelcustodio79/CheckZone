<?php

session_start();

// inclui o arquivo de inicialização
require '../../functions/globals.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recebe os dados do formulário
    $pagina = isset($_POST['pagina']) ? $_POST['pagina'] : null;
    $idEmpresa = $_POST['id_empresa'];
    $invoice_number = $_POST['invoice_number'];
    $idIM = $_POST['idIM'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $hs_code = $_POST['hs_code'];
    $qty = $_POST['qty'];
    $color = $_POST['color'];
    $unit_weight = $_POST['unit_weight'];
    $unit_price = format_to_number(clean_input($_POST['unit_price']));
    $nw_kg = $_POST['nw_kg'];
    $gw_kg = $_POST['gw_kg'];
    $no_boxes = $_POST['no_boxes'];

    ob_start();

    try {
        // Conexão com o banco de dados
        $PDO = db_connect();
        $PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepara a query de inserção
        $sql = "INSERT INTO invoice_maker_goods (id_im, id_empresa, invoice_number, name, description, hs_code, qty, color, unit_weight, unit_price, nw_kg, gw_kg, no_boxes) 
            VALUES (:id_im, :id_empresa, :invoice_number, :name, :description, :hs_code, :qty, :color, :unit_weight, :unit_price, :nw_kg, :gw_kg, :no_boxes)";
    
        $stmt = $PDO->prepare($sql);
    
        // Vincula os parâmetros
        $stmt->bindParam(':id_im', $idIM);
        $stmt->bindParam(':id_empresa', $idEmpresa);
        $stmt->bindParam(':invoice_number', $invoice_number);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':hs_code', $hs_code);
        $stmt->bindParam(':qty', $qty);
        $stmt->bindParam(':color', $color);
        $stmt->bindParam(':unit_weight', $unit_weight);
        $stmt->bindParam(':unit_price', $unit_price);
        $stmt->bindParam(':nw_kg', $nw_kg);
        $stmt->bindParam(':gw_kg', $gw_kg);
        $stmt->bindParam(':no_boxes', $no_boxes);
    
        // Executa a query
        if ($stmt->execute()) {
            if ($pagina == 'editar') {
                $_SESSION['active_step'] = 4; // Define o passo ativo como 4
            }

            // Determina a página de redirecionamento e usa urlencode para codificar os parâmetros da URL
            $redirect_page = ($pagina == 'editar') ? 'invoice_maker_editar' : 'invoice_maker_criar';
            $redirect_url = '../../cliente.php?a=ferramentas&b=' . urlencode($redirect_page)
                            . '&idIM=' . urlencode($idIM)
                            . '&invoice=' . urlencode($invoice_number)
                            . '&flag=' . urlencode('success')
                            . '&tip=' . urlencode('Produto cadastrado com sucesso!');
        
            // Redireciona para a página correta com sucesso
            header('Location: ' . $redirect_url);
            exit();
        } else {
            // Redireciona para a página com erro
            $error_url = '../../cliente.php?a=ferramentas&b=' . urlencode('invoice_maker_criar')
                         . '&flag=' . urlencode('erro')
                         . '&tip=' . urlencode('Não foi possível realizar a operação.');
            header('Location: ' . $error_url);
            exit();
        }

    } catch (PDOException $e) {
        // Em caso de exceção, exibe o erro
        echo "Erro: " . $e->getMessage();
    }

    // Limpa o buffer de saída e envia os headers corretamente
    ob_end_flush();

}