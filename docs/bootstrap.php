<?php
if (!session_id()) @session_start();

require __DIR__ .'/inc/functions.php';

// NB: You don't need to manually include these files, since they're
// loaded via Composer's autoloader

// HTTP Handlers
require __DIR__ .'/../src/HttpClients/HttpClientInterface.php';
require __DIR__ .'/../src/HttpClients/HttpClientsFactory.php';
require __DIR__ .'/../src/HttpClients/CurlHttpClient.php';
require __DIR__ .'/../src/HttpClients/Curl.php';

// Base API Class
require __DIR__ .'/../src/EdmundsApiClient.php';

// Resource - Vehicle
require __DIR__ .'/../src/Vehicle.php';


use Plasticbrain\EdmundsApiClient;

// Array to hold error and messages
$msgs = ['errors' => [], 'success' => []];

// Attempt to instantiate the API class
try {
    $apiConfig = [
        // 'api_key' => '',
        // 'protocol' => 'http', // http|https
        // 'format' => 'json', // json|xml
        // 'http_handler' => 'curl', // curl|guzzle
        // 'ignore_ssl_errors' => false,
    ];

    $api = new EdmundsApiClient($apiConfig);
    
} catch (Exception $e) {
    $msgs['errors'][] = $e->getMessage();
}