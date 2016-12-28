<?php
/*
Ukázkový skript pro zjištění počet běžících consumerů rabbita pro případ, že monitoring se nebude moct připojit přímo.
Pouze "přeposílá" výstup z api.
*/

$url = "http://127.0.0.1:15672/api/queues/vhost";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_USERPWD, "guest:guest");
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
$output = curl_exec($ch);
curl_close($ch);
echo $output;