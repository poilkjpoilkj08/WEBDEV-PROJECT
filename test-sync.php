<?php

$client = new GuzzleHttp\Client();
$response = $client->get('http://localhost:9000/api/sync/ping', [
    'headers' => [
        'Authorization' => 'Bearer T9qW4eR2yU7iO1pA6sD8fG3hJ5kL0zX9cV2bN4mM'
    ]
]);
echo $response->getBody();