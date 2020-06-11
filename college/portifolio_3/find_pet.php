
<?php

  header('Content-type: text/html; charset=utf-8');

  // Define os parametros de configuração do banco.
  $DB_HOST = "localhost";
  $DB_USERNAME = "root";
  $DB_PASSWORD = "root";
  $DB_NAME = "pet_monitor";
  $erro = "";
  $petSelecionado = "";

  // Efetua a conexão com o Banco com os daodos
  $link = mysqli_connect($DB_HOST, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);

  // Se não conseguir a conexão, para a execução do sistema
  if (!$link) {
    // cho 'falha na conexão';
    die('Não foi possível conectar: ');
  }

  // Verifica se existes um pet selecionado:
  if(isset($_POST['pet'])){
      // Verifica se os dados foram informados:
      if(strlen($_POST['inicio']) <= 0){
        $erro .= "Informe a data.";
      } else {

        // Cria a query SQL:
        $sqlRastreador = "SELECT p.pet_id, p.rastreador_id, p.descricao, ";
        $sqlRastreador .= "mp.data_hora, mp.latitude, mp.longitude ";
        $sqlRastreador .= " FROM `passeio` p, `monitoramento_passeio` mp ";
        $sqlRastreador .= "WHERE mp.passeio_id = p.id AND p.pet_id = ";
        $sqlRastreador .= $_POST['pet'];
        // $sqlRastreador .= " AND p.id = ";
        // $sqlRastreador .= $_POST['passeio'];
        $sqlRastreador .= " AND mp.data_hora LIKE '" . $_POST['inicio'] . "%' " ;
        $sqlRastreador .= ";";

        // limpa a variavel linhas
        $linhas = '';

        // cria um contador apra mostrar linhas com 
        // interposição de cores.
        $i = 1;
        $coords = [];

        // Execut a consulta no banco de dados e cria as linhas com o resultado das consultas.
        // Enquanto houverem linhas no banco, ele executa o while, adicionando os 
        // dados do rastreamento para uma linha da tabela. 
        $result_query_rastreador = mysqli_query($link, $sqlRastreador) or die (' Erro na query:' . $sqlRastreador . ' ' . mysqli_error() );
        while ($resultado = mysqli_fetch_array($result_query_rastreador)){
          $coords[$i] = $resultado;
          // abre uma linha colocando a cor de fundo em linhas pares;
          if($i % 2 == 0){
            $linhas .= "<tr bgcolor='#CCC'>";   
          }else{
            $linhas .= '<tr>';   
          }
          $linhas .= '<td>'.$resultado['data_hora'].'</td>';
          $linhas .= '<td>'.$resultado['data_hora'].'</td>';
          $linhas .= '<td>'.$resultado['latitude'].'</td>';
          $linhas .= '<td>'.$resultado['longitude'].'</td>';
          $linhas .= '</tr>'; // fecha linha
          $i++;
        }
        

        // Se não houver nenhum dado do banco informa que não existem informações:
        if(strlen($linhas) == 0){
          $linhas = "<tr><td colspan='4' align='center' >Nenhuma informação para o pet selecionado.</td></tr>";
        }    

    }

  } else {
      // Se não houver um pet selecionado exibe a mensagem para selecionar um.
      $linhas = "<tr><td colspan='4' align='center' >Selecione um Pet.</td></tr>";
  }
  
  // Monta o combo box com os pets
  $sqlPet ='SELECT * FROM `pet`;';
  $resultPet = mysqli_query($link, $sqlPet) or die(' Erro na query:' . $sqlPet . ' ' . mysqli_error() ); 
  $option = "";
  
  while ($exibe = mysqli_fetch_array( $resultPet )){
    $option .= '<option value="' . $exibe['id'] .'" ' ;
    if(isset($_POST['pet']) && $_POST['pet'] == $exibe['id']){
      $option .= ' selected ';
      $petSelecionado = $exibe;
    }
    $option .= ' >' . $exibe['nome'] . '</option>';
  }

?>
<html lang="pt" >
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>Pet Monitor</title>
  <script src="http://maps.google.com/maps/api/js?sensor=true"></script>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>

  <script type="text/javascript" >
    $(document).ready(function() {
      // If the browser supports the Geolocation API
      if (typeof navigator.geolocation == "undefined") {
        $("#error").text("Your browser doesn't support the Geolocation API");
        return;
      }
      var path = [];

      navigator.geolocation.getCurrentPosition(function(position) {
        
        // Sala a posicao atual
        path.push(new google.maps.LatLng(position.coords.latitude, position.coords.longitude));

        // Cria o mapa
        var myOptions = {
          zoom : 6,
          center : path[0],
          mapTypeId : google.maps.MapTypeId.ROADMAP
        }
        var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);


        var jscoord = [];
        <?php foreach($coords as $k => $v){  ?>
          jscoord.push("<?php echo $coords[$k]["latitude"] .','. $coords[$k]["longitude"]; ?>"); 
        <?php } ?>
        
        for (i = 0; i < jscoord.length; i++) {
            path.push(
              new google.maps.LatLng(
                (jscoord[i].split(",")[0]),
                (jscoord[i].split(",")[1])
            )
          );
        }     

        // Cria o array que será usaro para ver os pontos no mapa
        var latLngBounds = new google.maps.LatLngBounds();
        for(var i = 0; i < path.length; i++) {
          latLngBounds.extend(path[i]);
          // Place the marker
          new google.maps.Marker({
            map: map,
            position: path[i],
            title: "Point " + (i + 1)
          });
        }

        // Cria o objeto polyline
        var polyline = new google.maps.Polyline({
          map: map,
          path: path,
          strokeColor: '#0000FF',
          strokeOpacity: 0.7,
          strokeWeight: 1
        });

        // Coloca os limites nos pontos gerados
        map.fitBounds(latLngBounds);
      },
    function(positionError){
      $("#error").append("Error: " + positionError.message + "<br />");
    },
    {
      enableHighAccuracy: true,
      timeout: 10 * 1000 // 10 segundos
    });
  });
  </script>
  <style>
    #error {
      font-weight: 600;
      color: #993311;

    }

    .form-field{
      padding: 5px;
      text-align: left;
      margin-right: 10px;
      line-height: 15px; 
    }

    .form-label{
      color: darkgray;
      padding: 5px;
      text-align: right;
      margin-right: 5px;
      line-height: 15px; 
    }

    .table-locations{
      font-size: 10px;
      line-height: 15px; 

    }

  </style>
</head>
<body>

  <!-- Apresenta a mensagem de erro Caso exista. -->
  <?php 
    if(isset($error) && strlen($error) >= 0){
      echo '<div align="center"><p id="error">' . $erro . '</p></div>';
    }
  ?>
  <form method="POST" action="petmonitor.php">
    
    <label for="data-inicio" class="form-label" >Data</label><input id="data-inicio" type="date" name="inicio" class="form-field"/>
    <label for="pet-select" class="form-label" >Pet</label>
    <select id="pet-select" name="pet" class="form-field">
      <?php
        // imprime as opções recuperadas da consulta de pets;
        print($option); 
      ?>
    </select>
    <input type="submit" name="consultar" value="Consultar"/>

  </form>
  <hr />
  <?php if(isset($_POST['pet'])) { /* Verifica se existe um pet selecionado.*/ ?>
  <div style="margin:10px; text-align:left;">
    <h2><?php echo $petSelecionado['nome']; ?></h2>
    <h4>
      <?php echo $petSelecionado['tipo'] . ' - ' . $petSelecionado['raca'];  ?>
      <br />
      <?php echo $petSelecionado['idade'] . ' anos. '; ?>
    </h4>
  </div>
  <hr />
<?php } /* fecha o if que verifica se existe um pet selecionado. */ ?>

  <div style="margin:10px; text-align:center;">
    <table class="table-locations" width="50%" border="1px solid black" cellpadding="3px" cellspacing="3px" >
      <thead>
        <tr>
          <td>Início</td>
          <td>Fim</td>
          <td>Latitude</td>
          <td>Longitude</td>
        </tr>
      </thead>
      <tbody>
        <?php
          // Imprime as linhas com os dados recuperados do banco. 
          print($linhas)
        ?>
      </tbody>
    </table>
  </div>

  <!-- Efetua a substituição da div pelo google maps -->
  <div id="map_canvas" style="width:95%; height:95%; margin:10px; padding:5px;"></div>

</body>
</html>
<?php mysqli_close($link); /*Fecha a conexão com o Banco de Dados*/ ?>
