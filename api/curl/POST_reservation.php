<?php

    $ch = curl_init();
    
    $httpheader[] = "Content-Type:application/json";
    $ressource='{"id_client":1,"date_debut":"2019-06-10", "date_fin":"2019-08-02"}';
    curl_setopt($ch, CURLOPT_URL, "http://localhost/service-api/api/index.php/reservation");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $ressource);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);//indication que la ressource est en json
    $reponse= curl_exec($ch); //récupération  de la réponse
    curl_close($ch);//fermeture du tampon



?>