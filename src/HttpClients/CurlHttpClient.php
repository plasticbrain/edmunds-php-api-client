<?php

namespace Plasticbrain\HttpClients;

class CurlHttpClient implements HttpClientInterface {

    protected $curl;
    protected $rawResponse;
    protected $rawHeaders;
    protected $rawBody;

    protected $curlErrorMessage = '';

    protected $curlErrorCode = 0;

    protected $ignoreSslErrors = false;

    const CURL_PROXY_QUIRK_VER = 0x071E00;

    /**
     * @const "Connection Established" header text
     */
    const CONNECTION_ESTABLISHED = "HTTP/1.0 200 Connection established\r\n\r\n";

    public function __construct(Curl $curl = null)
    {
        $this->curl = $curl ?: new Curl();
    }

    public function send($url, $method = 'GET', $body = null, array $headers = [], $timeOut = 30)
    {

        $this->openConnection($url, $method, $body, $headers, $timeOut);
        $this->sendRequest();

        if ($curlErrorCode = $this->curl->errno()) {
            throw new \Exception($this->curl->error(), $curlErrorCode);
        }

        // Separate the raw headers from the raw body
        list($this->rawHeaders, $this->rawBody) = $this->extractResponseHeadersAndBody();

        $this->closeConnection();

        return $this->rawBody;
    }

    public function openConnection($url, $method, $body = null, array $headers = [], $timeOut = 30)
    {

        $options = [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $this->compileRequestHeaders($headers),
            CURLOPT_URL => $url,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => $timeOut,
            CURLOPT_RETURNTRANSFER => true, // Follow 301 redirects
            CURLOPT_HEADER => true, // Enable header processing
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => true,
            // CURLOPT_CAINFO => '',
        ];

        if ($this->ignoreSslErrors) {
            $options[CURLOPT_SSL_VERIFYHOST] = 0;
            $options[CURLOPT_SSL_VERIFYPEER] = 0;
        }

        if ($method !== "GET") {
            $options[CURLOPT_POSTFIELDS] = http_build_query($body);
        }

        $this->curl->init();
        $this->curl->setoptArray($options);
    }

    public function closeConnection()
    {
        $this->curl->close();
    }

    public function sendRequest()
    {
        $this->rawResponse = $this->curl->exec();
    }

    public function compileRequestHeaders(array $headers)
    {
        $return = [];
        if (empty($headers)) {
            return [];
        }
        foreach ($headers as $key => $value) {
            $return[] = $key . ': ' . $value;
        }

        return $return;
    }

    public function ignoreSslErrors()
    {
        $this->ignoreSslErrors = true;
    }

    public function extractResponseHeadersAndBody()
    {
        $headerSize = $this->getHeaderSize();

        $rawHeaders = mb_substr($this->rawResponse, 0, $headerSize);
        $rawBody = mb_substr($this->rawResponse, $headerSize);

        return [trim($rawHeaders), trim($rawBody)];
    }

    private function getHeaderSize()
    {
        $headerSize = $this->curl->getinfo(CURLINFO_HEADER_SIZE);
        // This corrects a Curl bug where header size does not account
        // for additional Proxy headers.
        if ($this->needsCurlProxyFix()) {
            // Additional way to calculate the request body size.
            if (preg_match('/Content-Length: (\d+)/', $this->rawResponse, $m)) {
                $headerSize = mb_strlen($this->rawResponse) - $m[1];
            } elseif (stripos($this->rawResponse, self::CONNECTION_ESTABLISHED) !== false) {
                $headerSize += mb_strlen(self::CONNECTION_ESTABLISHED);
            }
        }

        return $headerSize;
    }

    private function needsCurlProxyFix()
    {
        $ver = $this->curl->version();
        $version = $ver['version_number'];

        return $version < self::CURL_PROXY_QUIRK_VER;
    }

    public function getHeaders()
    {
        return $this->rawHeaders;
    }

    public function getBody()
    {
        return $this->rawBody;
    }
}
