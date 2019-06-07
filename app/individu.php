
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>Hello, world!</title>
  </head>
  <body>

  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">API</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
      <div class="navbar-nav">
        <a class="nav-item nav-link active" href="index.php">Accueil</a>
        <a class="nav-item nav-link" href="individu.php">Individus</a>
        <a class="nav-item nav-link" href="reservation.php">Reservations</a>
      </div>
    </div>
  </nav>

<?php
//envoie de la requète
//require_once("cle.php");
$ch = curl_init();
//$apikey=$cle;
//$httpheader = ['DOLAPIKEY: '.$apikey];
$httpheader[] = "Content-Type:application/json";
if( isset($_GET['prenom']) && isset($_GET['nom']) && isset($_GET['email']) ){
    $ressource='{"prenom":"'.$_GET['prenom'].'","nom":"'.$_GET['prenom'].'","email":"'.$_GET['prenom'].'"}';
    //var_dump($ressource);
    curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1/service-api/api/index.php/individu");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $ressource);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);//indication que la ressource est en json
    $reponse= curl_exec($ch); //récupération  de la réponse
}
curl_close($ch);
$ch = curl_init();
// configuration de l'URL GET
curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1/service-api/api/index.php/individu");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//récupération  de la réponse
$reponse= curl_exec($ch);
$reponse = json_decode($reponse);
//var_dump($reponse);
curl_close($ch);

echo "<table class='table' border=1>";
echo "<tr><th>ID</th><th>Prenom </th><th>Nom</th><th>Mail</th></tr>";
foreach ($reponse as $key => $individu){
    echo '<tr><td>'.$individu->id.'</td>';
    echo '<td>'.$individu->prenom.'</td>';
    echo '<td>'.$individu->nom.'</td>';
    echo '<td>'.$individu->email.'</td>';
    echo '</td></tr>';
}
echo "</table>";
echo '<form name="inserer" action="formulaire1.php" method="post">';
echo '</form>';

?>


<form method="GET" action="individu.php">
    Prenom:<input type="text" name="prenom"value="" maxlength="35" required><br>
    Nom:<input type="text" name="nom" value="" maxlength="35" required><br>
    Mail:<input type="mail" name="email"value="" maxlength="100" required><br>
    <input type="submit" value="Ajouter" />
</form>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>
