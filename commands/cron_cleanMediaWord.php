<?php
    $host = "mm.test";
    $resource = "/cron/clean-media-word";

    $url = $host.$resource;

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
    $response = curl_exec($ch);
    if($response){
        echo "SUCCESS";
    }else{
        echo "ERROR";
    }
?>