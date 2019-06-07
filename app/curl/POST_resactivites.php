<?php

    $ch = curl_init();
    
    $httpheader[] = "Content-Type:application/json";
    $ressource='{"id_reservation":"1","date":"2019-05-09", "id_sport":"1","nb_unit":2,"prix_unit":"2019-05-09","date_fin":"12"}';
    curl_setopt($ch, CURLOPT_URL, "http://localhost/service-api/api/index.php/resactivites");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $ressource);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);//indication que la ressource est en json
    $reponse= curl_exec($ch); //récupération  de la réponse
    curl_close($ch);//fermeture du tampon



?>