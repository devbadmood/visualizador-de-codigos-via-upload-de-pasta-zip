<?php
include 'config.php';

if (!isset($_FILES['arquivo'])) {
    echo "<h3>Nenhum arquivo foi enviado.</h3>";
    exit;
}

$arquivo = $_FILES['arquivo'];
$extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));

if ($arquivo['error'] === UPLOAD_ERR_OK && in_array($extensao, ['zip', 'rar'])) {
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $arquivoPath = $uploadDir . basename($arquivo['name']);
    if (move_uploaded_file($arquivo['tmp_name'], $arquivoPath)) {
        $pastaExtraida = $uploadDir . pathinfo($arquivo['name'], PATHINFO_FILENAME);
        if (!is_dir($pastaExtraida)) {
            mkdir($pastaExtraida, 0777, true);
        }

        $extraido = false;

        if ($extensao === 'zip') {
            $zip = new ZipArchive;
            if ($zip->open($arquivoPath) === TRUE) {
                $zip->extractTo($pastaExtraida);
                $zip->close();
                $extraido = true;
            }
        }

        if ($extensao === 'rar') {
            if (class_exists('RarArchive')) {
                $rar = RarArchive::open($arquivoPath);
                if ($rar) {
                    $entries = $rar->getEntries();
                    foreach ($entries as $entry) {
                        $entry->extract($pastaExtraida);
                    }
                    $rar->close();
                    $extraido = true;
                }
            } else {
                echo "<h3>Extensão RAR não habilitada no PHP.</h3>";
                exit;
            }
        }

        unlink($arquivoPath);

        if ($extraido) {
            $nome = $arquivo['name'];
            $stmt = $conn->prepare("INSERT INTO projetos (nome, caminho) VALUES (?, ?)");
            $stmt->bind_param("ss", $nome, $pastaExtraida);
            $stmt->execute();

            echo "<h3>Arquivo extraído com sucesso!</h3>";
            echo "<a href='index.php'>Visualizar projeto</a>";
        } else {
            echo "<h3>Erro ao extrair o arquivo.</h3>";
        }

    } else {
        echo "<h3>Erro ao mover o arquivo.</h3>";
    }

} else {
    echo "<h3>Apenas arquivos ZIP ou RAR são permitidos.</h3>";
}

$projeto_id = $conn->insert_id;
$items = scandir($pastaExtraida);
foreach ($items as $item) {
    $filePath = $pastaExtraida . '/' . $item;
    if (is_file($filePath)) {
        $stmt = $conn->prepare("INSERT INTO arquivos (projeto_id, nome_arquivo, caminho) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $projeto_id, $item, $filePath);
        $stmt->execute();
    }
}


?>
