<?php
    $host = "127.0.0.1/mm";
    $resource = "/cron/clean-media-word";

    $url = $host.$resource;

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
    $response = curl_exec($ch);
    if($response){
        echo date('d/M/Y H:i:s')." SUCCESS \r\n";
    }else{
        echo date('d/M/Y H:i:s')." ERROR \r\n";
    }
?>