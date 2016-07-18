<?php
namespace Plasticbrain;

use Plasticbrain\HttpClients\HttpClientsFactory;
use Plasticbrain\Specification\Vehicle;

class EdmundsApiClient
{

    /** @var int Which version of the Edmunds API to use */
    const API_VERSION = 2;

    /** @var string The base URL (w/o protocol) to the Edmunds API */
    const API_URL = 'api.edmunds.com/api';

    /** @var string Which HTTP handler to use (curl|guzzle) */
    const DEFAULT_HTTT_HANDLER = 'curl';

    /** @var string URL to Edmunds Developer Portal */
    const EDMUNDS_DEVELOPER_URL = 'http://developer.edmunds.com/';

    /** @var string The last error that was encountered (eg: before an exception is thrown) */
    protected static $lastError;

    /** @var object Existing instance of the current class */
    public static $instance;

    /** @var array Default configuration settings */
    protected static $config = [
        'protocol' => null,
        'api_key' => null,
        'format' => 'json',
        'http_handler' => 'curl',
        'ignore_ssl_errors' => false,
    ];

    /**
     * @var array List of API Sections and Resources
     * @see http://developer.edmunds.com/api-documentation/vehicle/#sec-5
     */
    protected $resources = [
        'Specification' => [
            'Make', 'Model', 'Model Year and Trim', 'Style',
            'Engine and Transmission', 'Colors and Options',
            'Configuration', 'Equipment', 'VIN',
        ],
        'Service' => ['Maintenance Schedule', 'Recalls', 'Service Bulletins', 'Local Labor Rates'],
        'Pricing' => [ 'True Market Value &reg;', 'True Cost to Own &reg;', 'Incentives and Rebates'],
        'Review' => ['Edmunds Grade Ratings', 'Consumer Ratings and Grades'],
        'Media' => ['Vehicle Photos'],
    ];


    /** @var string The name of the current API Resource (eg: Vehicle) */
    protected $resource;

    /** @var object Instance of the current API Resource class (eg: Vehicle) */
    protected $resourceClass;

    /** @var object Instance of the HTTP Handler Client */
    protected $client;

    /** @var string The endpoint currently being used (or last used) */
    protected static $endpoint;

    /** @var array Query string parameters */
    protected static $params = [
        'year' => null,
        'state' => null,
        'view' => 'basic',
    ];

    /** @var string The name of the Make currently being used */
    protected static $make;

    /** @var string The name of the Model currently being used */
    protected static $model;

    /** @var int The Year currently being used */
    protected static $year;

    /** @var int The ID of the Style currently being used */
    protected static $style;
    
    /** @var int The ID of the Equipment currently being used */
    protected static $equipment;

    /** @var int The ID of the Engine currently being used */
    protected static $engine;

    /** @var int The ID of the Transmission currently being used */
    protected static $transmission;

    /** @var int The ID of the Color currently being used */
    protected static $color;

    /** @var string The current spec (eg: Styles, Equipment) */
    protected static $spec;

    /**
     * Constructor
     *
     * @param array $config
     *
     * @return $this
     */
    public function __construct(array $config = [])
    {

        if (self::$instance) {
            return self::$instance;
        }
        self::$instance = $this;

        // Set any given config values
        static::$config = array_merge(static::$config, $config);

        // Set the default HTTP handler
        $this->setHttpClient($this->config('http_handler'));

        // Check to see if a protocol was given in the config array, and if
        // not then determine which protocol should be used for API calls
        if (!$this->config('protocol') || !in_array($this->config('protocol'), ['http', 'https'])) {
            $this->set('protocol', $this->isSssl() ? 'https' : 'http');
        }

        // Return the instance when possible/relevant so methods can be chained
        return $this;
    }


    //------------------------------------------------------------------------------
    // Helper methods for API subclasses
    //------------------------------------------------------------------------------

    /**
     * Helper method to instantiate the "Vehicle" subclass
     *
     * @return object Instance of \Plasticbrain\Vehicle
     */
    public function vehicle()
    {
        $this->setResourceClass(\Plasticbrain\Vehicle::getInstance());
        return $this->getResourceClass();
    }

    //------------------------------------------------------------------------------
    // Getters
    //------------------------------------------------------------------------------


    /**
     * Return existing instance of the class or create a new instance
     *
     * @return object
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get the current Section (eg: Specification)
     *
     * @return mixed
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * Get the instance of the HTTP Handler client
     *
     * @return object HttpClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Get the Resource currently being used
     *
     * @return string Name of the Resource
     */
    public function getResource()
    {
        return $this->resource;
    }

     /**
     * Get the current Resource class
     *
     * @return object Instance of the current Resource class
     */
    public function getResourceClass()
    {
        return $this->resourceClass;
    }

    /**
     * Return a query string parameter
     *
     * @param  string $key The key of the query string parameter to retrieve
     *
     * @return mixed
     */
    public function getParam($key)
    {
        return isset(static::$params[$key]) ? static::$params[$key] : null;
    }

    /**
     * Return an array of all the query string parameters
     *
     * @return array
     */
    public function getParams()
    {
        return static::$params;
    }

    /**
     * Return the endpoint that is being used
     *
     * @return string
     */
    public function getEndpoint()
    {   
        return static::$endpoint;
    }

    /**
     * Get the last error that was encountered
     *
     * @return mixed
     */
    public function lastError()
    {
        return static::$lastError;
    }


    //------------------------------------------------------------------------------
    // Setters
    //------------------------------------------------------------------------------


    /**
     * Set a config value
     *
     * @param string $key The key in the $config array
     * @param mixed  $val The value to set
     */
    public function set(string $key, $val)
    {
        static::$config[$key] = $val;
        return $this;
    }

    /**
     * Set the HTTP Client Handler
     *
     * @param string $handler The HTTP handler to use (curl|guzzle)
     *
     * @return void
     */
    public function setHttpClient($handler)
    {

        if (!$handler || !in_array($handler, ['curl', 'guzzle'])) {
            $handler = static::DEFAULT_HTTT_HANDLER;
        }

        $this->client = HttpClientsFactory::createHttpClient($handler);

        if ($this->config('ignore_ssl_errors') === true) {
            $this->client->ignoreSslErrors();
        }
    }

    /**
     * Set the Resource class
     *
     * @param object $classObject Instance of the Resource class to use
     */
    protected function setResourceClass($classObject)
    {
        $this->resourceClass = $classObject;
        return $this;
    }

    /**
     * Set the API Resource to use (eg: Make)
     *
     * @param string $resource Name of the Resource
     */
    protected function setResource($resource)
    {
        $resource = ucwords(trim($resource));
        $this->resource = $resource;
        return $this;
    }

    /**
     * Set a query string parameter in the API endpoint
     *
     * @param string $key The key to use for the parameter
     * @param mixed $val  The value to use for the parameter
     */
    public function setParam($key, $val)
    {
        static::$params[$key] = $val;
        return $this;
    }

    /**
     * Create a url-friendly "slug" for a string
     * 
     * @param  string $string      The string to make into a slug
     * @param  string $replaceWith Character that will replace and non-url-friendly characters
     * 
     * @return string
     */
    public function slug($string, $replaceWith = '-')
    {
        $string = strtolower(trim($string));
        
        // Replace any non-alphanumeric chars
        $string = preg_replace('/[^a-z0-9]/i', $replaceWith, $string);

        return $string;
    }

    /**
     * Set the "make" parameter
     *
     * @param  string $make The name of the make to query
     *
     * @return $this
     */
    public function make(string $make)
    {
        static::$make = $this->slug($make);
        return $this;
    }

    /**
     * Set the "model" parameter
     *
     * @param  string $model The name of the model to query
     *
     * @return $this
     */
    public function model(string $model)
    {
        static::$model = $this->slug($model);
        return $this;
    }

    /**
     * Set the "year" parameter
     *
     * @param int $year The year to set
     *
     * @return $this
     */
    public function year(int $year = null)
    {

        if (!$year) {
            static::$year = null;
            return $this;
        }

        $year = trim($year);
        // if (!ctype_digit($year) || $year < 1990 || $year > date('Y')) {
        if (!ctype_digit($year) || $year < 1990) {
            throw new \Exception(sprintf('"year" must be a 4 digit number greater than 1990'));
        }
        $this->setParam('year', $year);
        static::$year = $year;
        return $this;
    }

    /**
     * Set the "state" parameter
     *
     * @param  string $state The state to use (new|used|future)
     *
     * @return $this
     */
    public function state(string $state = null)
    {

        if (!$state) {
            $this->setParam('state', null);
            return $this;
        }

        $states = ['new', 'used', 'future'];
        $state = strtolower(trim($state));
        if (!in_array($state, $states)) {
            throw new \Exception(sprintf('"%s" is not a valid state. Please use one of: %s', htmlspecialchars($state), implode(', ', $states)));
        }

        $this->setParam('state', $state);
        return $this;
    }

    /**
     * Set the "view" parameter
     *
     * @param  string $view The view to use (basic|full)
     *
     * @return $this
     */
    public function view(string $view = 'basic')
    {
        $view = trim($view);
        if (!in_array($view, ['basic', 'full'])) {
            throw new \Exception(sprintf('"%s" is not a valid view. Please use one of: basic, full', htmlspecialchars($view)));
        }
        $this->setParam('view', $view);
        return $this;
    }

    /**
     * Set the spec to "Styles"
     * 
     * @return $this
     */
    public function styles()
    {
        static::$spec = 'styles';
        return $this;
    }

    /**
     * Set the spec to "Styles" and set a Style ID to interact with
     * 
     * @param  int    $styleId The ID of a Style to interact with
     * 
     * @return $this
     */
    public function style(int $styleId)
    {
        static::$spec = 'style';
        static::$style = $styleId;
        return $this;
    }

    /**
     * Set the spec to "Equipment", and optionally set the ID of a piece of Equipment to interact with
     * 
     * @param  int|null $equipmentId
     * @return $thsi
     */
    public function equipment(int $equipmentId = null)
    {
        static::$spec = 'equipment';
        if ($equipmentId) {
            static::$equipment = $equipmentId;
            return $this;
        }
        return $this;
    }


    /**
     * Set the spec to "Engines"
     * 
     * @return $this
     */
    public function engines()
    {
        static::$spec = 'engines';
        return $this;
    }

    /**
     * Set the spec to "Engine", and optionally set the ID of a specific Engine to interact with
     * 
     * @param  int|null $engineId
     * @return $thsi
     */
    public function engine(int $engineId = null)
    {
        static::$spec = 'engine';
        if ($engineId) {
            static::$engine = $engineId;
            return $this;
        }
        return $this;
    }

     /**
     * Set the spec to "Transmissions"
     * 
     * @return $this
     */
    public function transmissions()
    {
        static::$spec = 'transmissions';
        return $this;
    }

    /**
     * Set the spec to "Transmission", and optionally set the ID of a transmission to interact with
     * 
     * @param  int|null $transmissionId
     * @return $thsi
     */
    public function transmission(int $transmissionId = null)
    {
        static::$spec = 'transmission';
        if ($transmissionId) {
            static::$transmission = $transmissionId;
            return $this;
        }
        return $this;
    }

    /**
     * Set the spec to "Colors"
     * 
     * @return $this
     */
    public function colors()
    {
        static::$spec = 'colors';
        return $this;
    }

    /**
     * Set the spec to "Color", and optionally set the ID of a specific color
     * 
     * @param  int|null $colorId
     * @return $thsi
     */
    public function color(int $colorId = null)
    {
        static::$spec = 'color';
        if ($colorId) {
            static::$color = $colorId;
            return $this;
        }
        return $this;
    }



    /**
     * Set the last error
     *
     * @param string Error message
     *
     * @return $this
     */
    protected function setLastError($error)
    {
        static::$lastError = $error;
        return $this;
    }

    //------------------------------------------------------------------------------
    // Misc. Methods
    //------------------------------------------------------------------------------

    /**
     * Get or Set a config value
     *
     * @param string $key The key in the $config array
     * @param mixed  $val The value to set
     *
     * @return mixed
     */
    public function config(string $key = null, $val = null)
    {
        if (!$key) {
            return static::$config;
        }

        if ($key && $val) {
            static::$config[$key] = $val;
        }

        return isset(static::$config[$key]) ? static::$config[$key] : null;
    }

    /**
     * Build the API endpoint that will be called
     *
     * @param  string $resource The name of the API resource to use
     * @param  string $endpoint The API endpoint for the given resource
     * @param  array  $params   Optional query string parameters to append to the endpoint
     *
     * @throws Exception
     * @return mixed
     */
    public function endpoint($resource, $endpoint = null, array $params = [])
    {
        $this->checkForApiKey();

        $this->setParam('api_key', $this->config('api_key'));
        $this->setParam('fmt', 'json');

        // Append the API Key and format to the endpoint
        $endpoint .= strpos($endpoint, '?') === false ? '?' : '&';
        $endpoint .= http_build_query($this->getParams());

        // eg: http://api.edmunds.com/api/vehicle/v2/makes/count?fmt=json&api_key={api key}
        $url = sprintf(
            "%s://%s/%s/v%s/%s",
            $this->config('protocol'),
            static::API_URL,
            $resource,
            static::API_VERSION,
            $endpoint
        );

        static::$endpoint = $url;
        return $url;
    }

    /**
     * Make sure that an API key was given
     *
     * @throws Exception
     * @return $this
     */
    protected function checkForApiKey()
    {
        if (!$this->config('api_key')) {
            $this->lastError = "The Edmunds API requires an API Key. Learn more at " . static::EDMUNDS_DEVELOPER_URL;
            throw new \Exception($this->lastError);
        }

        return $this;
    }

    /**
     * Send the built request to the API
     *
     * @param  string  $url
     * @param  string  $method  The method to use (GET|POST|PUT|PATCH|...)
     * @param  mixed   $body    Array of POST parameters
     * @param  array   $headers Optional array of headers to send along with the request
     * @param  integer $timeOut Number of seconds to wait until the connection times out
     *
     * @throws Exception
     * @return mixed
     */
    public function send($url, $method = 'GET', $body = null, array $headers = [], $timeOut = 30)
    {
        try {
            $results = self::$instance->client->send($url, $method, $body, $headers, $timeOut);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return $results;
    }

    /**
     * Determine if SSL/TLS (https) is being used or not
     *
     * @return boolean
     */
    protected function isSssl()
    {
        if (isset($_SERVER['HTTPS'])) {
            if (strtolower($_SERVER['HTTPS']) == 'on' || $_SERVER['HTTPS'] == '1') {
                return true;
            }
        } elseif (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') {
            return true;
        }
        return false;
    }

}
