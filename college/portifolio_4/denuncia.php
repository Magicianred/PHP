<html>
<title>Denuncia - Programa Zer@ Dengue</title>
<head><h3>Denúncia - Programa Zer@ Dengue</h3></head>
<style>
</style>
<body>
<h3>Dados da denúncia</h3>
<form action="relatorio_denuncia.php" method="post" enctype="multipart/form-data">
  <input type="hidden" name="name" value="<?php echo $_POST['name'] ?>" />
  <p>Endereço ocorrência: <input type="text" name="denounce_adress" /></p>
  <p>Foto denúncia: <input type="file" name="arquivo" id="filetoupload" /></p>
  <p>Descrição denúncia: <textarea id="descricao" name="description" rows="4" cols="50"></textarea></p>
  <p><input type="submit" /></p>
</form>
</body>
</html>
