<?php
include 'config.php';
$result = $conn->query("SELECT * FROM projetos ORDER BY data_upload DESC");
?>

<h2>Enviar novo projeto (.zip ou .rar)</h2>
<form action="upload.php" method="post" enctype="multipart/form-data">
  <input type="file" name="arquivo" required>
  <button type="submit">Enviar</button>
</form>

<hr>
<h2>📁 Projetos enviados</h2>
<ul>
<?php while ($row = $result->fetch_assoc()): ?>
  <li>
    <a href="visualizar.php?id=<?= $row['id'] ?>"><?= $row['nome'] ?></a>
    <small>(<?= $row['data_upload'] ?>)</small>
  </li>
<?php endwhile; ?>
</ul>
