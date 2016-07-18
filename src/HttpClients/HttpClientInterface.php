<?php

namespace Plasticbrain\HttpClients;


interface HttpClientInterface
{
    public function send($url, $method, $body, array $headers, $timeOut);
}
