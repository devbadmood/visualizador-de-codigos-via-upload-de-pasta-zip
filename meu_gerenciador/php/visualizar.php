<?php
include 'config.php';

if (!isset($_GET['id'])) {
    echo "<h3>Projeto não especificado.</h3>";
    exit;
}

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM projetos WHERE id = $id");

if ($result->num_rows === 0) {
    echo "<h3>Projeto não encontrado.</h3>";
    exit;
}

$row = $result->fetch_assoc();
$projeto = $row['nome'];
$caminho = $row['caminho'];

echo "<!DOCTYPE html><html><head>
<title>$projeto</title>
<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/prismjs@1.30.0/themes/prism.min.css'>
<script src='https://cdn.jsdelivr.net/npm/prismjs@1.30.0/prism.min.js'></script>
<style>
body { font-family: sans-serif; background: #f0f0f0; padding: 20px; }
.dark-mode { background: #1e1e1e; color: #eee; }
button { margin: 5px; }
pre { border: 1px solid #ccc; padding: 10px; overflow: auto; }
</style>
</head><body>
<h2>📁 Projeto: $projeto</h2>
<button onclick='toggleDark()'>🌓 Modo Escuro</button>
<hr><h3>📜 Arquivos:</h3>";

function listarArquivos($dir) {
    $items = scandir($dir);
    foreach ($items as $item) {
        $path = $dir . '/' . $item;
        if ($item === '.' || $item === '..') continue;
        if (is_dir($path)) {
            echo "<h4>📁 " . basename($path) . "</h4>";
            listarArquivos($path);
        } else {
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            $lang = match($ext) {
                'php' => 'php',
                'html' => 'html',
                'css' => 'css',
                'js' => 'javascript',
                'sql' => 'sql',
                'py' => 'python',
                default => 'markup'
            };
            $id = md5($path);
            $conteudo = htmlspecialchars(file_get_contents($path));
            echo "<h4>📄 " . basename($path) . "</h4>";
            echo "<button onclick=\"copiar('$id')\">📋 Copiar</button> ";
            echo "<a href='$path' download>⬇️ Baixar</a>";
            echo "<pre><code id='$id' class='language-$lang'>$conteudo</code></pre><hr>";
        }
    }
}

echo "<h3>🔗 Links diretos para os arquivos:</h3><ul>";
$res = $conn->query("SELECT nome_arquivo FROM arquivos WHERE projeto_id = $id");
while ($arq = $res->fetch_assoc()) {
    $nome = urlencode($arq['nome_arquivo']);
    echo "<li><a href='arquivo.php?nome=$nome' target='_blank'>{$arq['nome_arquivo']}</a></li>";
}
echo "</ul>";


listarArquivos($caminho);
?>

<script>
function copiar(id) {
  const el = document.getElementById(id);
  navigator.clipboard.writeText(el.innerText);
  alert("Código copiado!");
}
function toggleDark() {
  document.body.classList.toggle("dark-mode");
}
</script>
</body></html>
