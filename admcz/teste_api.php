<?php
// Sua chave de API do Google Drive
$apiKey = 'AIzaSyDZT8X3HETmXGPUhXhDpyz977ElQU922wo';

// ID da pasta do Google Drive
$folderId = '1-p_6VOFl_zdykvmVSXIU8Z1YOSjl55u1';

// URL da API para listar arquivos e pastas
$url = "https://www.googleapis.com/drive/v3/files?q='" . $folderId . "'%20in%20parents&key=" . $apiKey . "&fields=files(id,%20name,%20mimeType,%20webViewLink)";

// Faz a requisição para a API e captura o erro se houver
$response = @file_get_contents($url);
if ($response === FALSE) {
    $error = error_get_last();
    echo "Erro na requisição: " . $error['message'];
    exit;
}

$data = json_decode($response, true);

if (isset($data['files'])) {
    echo "<ul>";
    foreach ($data['files'] as $file) {
        $fileName = htmlspecialchars($file['name']);
        $fileLink = htmlspecialchars($file['webViewLink']);
        $fileType = $file['mimeType'] === 'application/vnd.google-apps.folder' ? '[Pasta]' : '[Arquivo]';
        
        echo "<li>$fileType <a href='$fileLink' target='_blank'>$fileName</a></li>";
    }
    echo "</ul>";
} else {
    echo "Nenhum arquivo ou pasta encontrado, ou erro na requisição.";
}
?>