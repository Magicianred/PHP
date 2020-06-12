<?php
  $uploaddir = 'arquivos_denuncia/';
  $uploadfile = $uploaddir . basename($_FILES['arquivo']['name']);
  $status = 'Problema ao efetuar denúncia, tente novamente';
  if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $uploadfile)) {
      $status = "Denúncia cadastrada com sucesso \n";
  }
?>
<html>
  <head>
    <title>Resultados denúncia</title>
  </head>
<body>
    <h2> Resultados da denúncia </h2>
    <h3><?php echo $status ?> </h3>
    <p>Denunciante: <?php echo $_POST['name'] ?></p>
    <p>Endereço ocorrencia: <?php echo $_POST['denounce_adress'] ?></p>
    <p>Descrição denúncia: <?php echo $_POST['description'] ?> </p>
    <p>Foto da denúncia:</p>
    <img width=400 src="<?php echo $uploadfile ?>"/>
  </body>
 </html>
