<?php

    $ch = curl_init();
    
    $httpheader[] = "Content-Type:application/json";
    $ressource='{"id_reservation":"1","id_emplacement":"1", "nb_personnes":"5","prix":10,"date_debut":"2019-05-09","date_fin":"2019-05-12"}';
    curl_setopt($ch, CURLOPT_URL, "http://localhost/service-api/api/index.php/resemplacements");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $ressource);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);//indication que la ressource est en json
    $reponse= curl_exec($ch); //récupération  de la réponse
    curl_close($ch);//fermeture du tampon



?>