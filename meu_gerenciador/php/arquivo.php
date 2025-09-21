<?php
include 'config.php';

$nome = $_GET['nome'] ?? '';
$nome = basename($nome); // segurança

$stmt = $conn->prepare("SELECT caminho FROM arquivos WHERE nome_arquivo = ?");
$stmt->bind_param("s", $nome);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Arquivo não encontrado.";
    exit;
}

$row = $result->fetch_assoc();
$conteudo = htmlspecialchars(file_get_contents($row['caminho']));

echo "<h2>📄 $nome</h2>";
echo "<pre style='background:#f9f9f9;border:1px solid #ccc;padding:10px;'>$conteudo</pre>";
?>
