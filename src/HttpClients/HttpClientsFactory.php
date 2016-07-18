<?php

namespace Plasticbrain\HttpClients;

use GuzzleHttp\Client;

class HttpClientsFactory
{
    private function __construct()
    {
        // a factory constructor should never be invoked
    }

    public static function createHttpClient($handler)
    {
        if (!$handler) {
            return self::detectDefaultClient();
        }

        if ($handler === 'curl') {
            if (!extension_loaded('curl')) {
                throw new \Exception('The cURL extension must be loaded in order to use the "curl" handler.');
            }

            return new CurlHttpClient();
        }

        if ($handler === 'guzzle') {
            
            throw new \Exception('The Guzzle HTTP client has not been implemented yet. Please use "curl".');

            if (!class_exists('GuzzleHttp\Client')) {
                throw new \Exception('The Guzzle HTTP client must be included in order to use the "guzzle" handler.');
            }
            return new GuzzleHttpClient();
        }

        throw new \Exception('The http client handler must be set to "curl" or "guzzle"');
    }

    /**
     * Detect default HTTP client.
     *
     * @return HttpClientInterface
     */
    private static function detectDefaultClient()
    {

        if (class_exists('GuzzleHttp\Client')) {
            return new GuzzleHttpClient();
        }

        if (extension_loaded('curl')) {
            return new CurlHttpClient();
        }

        throw new \Exception('Neither curl nor Guzzle are installed');


    }
}
