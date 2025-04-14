<?php
session_start();

// inclui o arquivo de inicialização
require '../functions/globals.php';

$PDO = db_connect();

$idEmpresa = isset($_POST['id_empresa']) ? $_POST['id_empresa'] : null;
$company = isset($_POST['company']) ? $_POST['company'] : null;
$vat_number = isset($_POST['vat_number']) ? $_POST['vat_number'] : null;
$address = isset($_POST['address']) ? $_POST['address'] : null;
$post_code = isset($_POST['post_code']) ? $_POST['post_code'] : null;
$city_country = isset($_POST['city_country']) ? $_POST['city_country'] : null;
$name_im_delivery = isset($_POST['name']) ? $_POST['name'] : null;
$email_im_delivery = isset($_POST['email']) ? $_POST['email'] : null;


$stmt = $PDO->prepare("INSERT INTO invoice_maker_delivery (id_empresa, company, vat_number, address, post_code, city_country, name_im_delivery, email_im_delivery) 
                            VALUES (:id_empresa, :company, :vat_number, :address, :post_code, :city_country, :name_im_delivery, :email_im_delivery)");
$stmt->bindParam(':id_empresa', $idEmpresa);
$stmt->bindParam(':company', $company);
$stmt->bindParam(':vat_number', $vat_number);
$stmt->bindParam(':address', $address);
$stmt->bindParam(':post_code', $post_code);
$stmt->bindParam(':city_country', $city_country);
$stmt->bindParam(':name_im_delivery', $name_im_delivery);
$stmt->bindParam(':email_im_delivery', $email_im_delivery);

if ($stmt->execute()) {
    header('Location: ../cliente.php?pg=invoice_maker&flag=success&tip=Delivery cadastrado com sucesso!');
} else {
    header('Location: ../cliente.php?pg=invoice_maker&flag=erro&tip=Não foi possível realizar a operação.');
}
