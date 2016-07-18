<?php
namespace Plasticbrain;

use Plasticbrain\EdmundsApiClient;

class Vehicle extends EdmundsApiClient
{

    /**
     * @var string The name of the resource, as used in the API endpoint
     *
     * @example <protocol>://<api_url>/$resource/<version>/<endpoint>
     * @example http://api.edmunds.com/api/vehicle/v2/makes
     *
     */
    protected $resource = 'vehicle'; // {base}/$resource/{version}/{endpoint}

    /** @var object Existing instance of the current class */
    public static $instance;

    /** @var object Instance of the HTTP Handler Client */
    protected $client;

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

        $this->client = parent::getClient();

    }

    /**
     * Returns the count of a given resource
     *
     * @return int
     */
    public function count()
    {

        $this->view('basic');

        $type = null;

        // Get a specific spec? (eg: styles, equipment)
        if (static::$spec) {
            $method = 'count' . ucwords(static::$spec);
            return $this->$method();

        // If make is set, then we're counting models for the given make
        } elseif (static::$make) {
            return $this->countModels();

        // Otherwise, we're counting all available makes
        } else {
            return $this->countMakes();
        }

    }

    /**
     * Return the specified resource
     *
     * @return mixed
     */
    public function get()
    {
        $type = null;

        // Get a specific spec? (eg: styles)
        if (static::$spec) {
            $method = 'get' . ucwords(static::$spec);
            return $this->$method();

        // If both make and model are set, then we're getting the details for the given model
        } if (static::$model && static::$make) {
            return $this->getModel();

        // If only make is set, then obviously we're getting all the models for the given make
        } elseif (static::$make) {
            return $this->getModels();

        // Otherwise, get the list of all makes and model
        } else {
            return $this->getMakes();
        }

    }

    /**
     * Count all of the available makes for the given parameters
     *
     * @throws Exception
     * @return int
     */
    public function countMakes()
    {
        $expects = 'makesCount';
        $endpoint = $this->endpoint($this->resource, 'makes/count', $this->getParams());

        $res = parent::send($endpoint);
        $res = @json_decode($res);

        if (!$res || !isset($res->$expects)) {
            throw new \Exception("Malformed response from the Edmunds API");
        }
        return $res->$expects;
    }

    /**
     * Get all of the available makes for the given parameters
     *
     * @throws Exception
     * @return object All of the available makes
     */
    public function getMakes()
    {
        $this->view('basic');
        $endpoint = $this->endpoint($this->resource, 'makes', $this->getParams());
        $res = parent::send($endpoint);
        $res = @json_decode($res);

        if (!$res || !isset($res->makes)) {
            throw new \Exception("Malformed response from the Edmunds API");
        }

        $makes = $res->makes;

        // Iterate over the results and unset the "models", so that we're just
        // returning the makes (as the method and API endpoint implies)
        array_walk($makes, function (&$item) {
            unset($item->models);
        });
        return $res->makes;

    }

    /**
     * Count all of the available Models for the given parameters
     *
     * @param  string|null $make The name of a specific Make
     *
     * @throws Exception
     * @return int
     */
    public function countModels(string $make = null)
    {

        if ($make) $this->make($make);

        if (!static::$make) {
            throw new \Exception(sprintf("%s requires a make to be set", __METHOD__));
        }

        $expects = 'modelsCount';
        $endpoint = $this->endpoint($this->resource, sprintf('%s/models/count', static::$make), $this->getParams());

        $res = parent::send($endpoint);
        $res = @json_decode($res);

        if (!$res || !isset($res->$expects)) {
            throw new \Exception("Malformed response from the Edmunds API");
        }
        return $res->$expects;
    }

    /**
     * Get all of the models for the given make and parameters
     *
     * @throws Exception
     * @return object All of the available models
     */
    public function getModels(string $make = null)
    {
        if ($make) $this->make($make);

        if (!static::$make) {
            throw new \Exception(sprintf("%s requires a make to be set", __METHOD__));
        }

        $resourceEndpoint = sprintf("%s/models", static::$make);
        $endpoint = $this->endpoint($this->resource, $resourceEndpoint, $this->getParams());
        $res = parent::send($endpoint);
        $res = @json_decode($res);

        if (!$res) {
            throw new \Exception("Malformed response from the Edmunds API");
        }
        return $res;
    }

    /**
     * Get all of the models for the given make, model, and parameters
     *
     * @throws Exception
     * @return object All of the available models
     */
    public function getModel(string $make = null, string $model = null)
    {
        if ($make) $this->make($make);
        if ($model) $this->model($model);

        if (!static::$make) {
            throw new \Exception(sprintf("%s requires a make to be set", __METHOD__));
        }

        if (!static::$model) {
            throw new \Exception(sprintf("%s requires a model to be set", __METHOD__));
        }

        if (static::$year) {
            // $make/$model/$year
            $resourceEndpoint = sprintf("%s/%s/%s", static::$make, static::$model, static::$year);
        } else {
            // $make/$model
            $resourceEndpoint = sprintf("%s/%s", static::$make, static::$model);
        }

        $endpoint = $this->endpoint($this->resource, $resourceEndpoint, $this->getParams());
        $res = parent::send($endpoint);
        $res = @json_decode($res);

        if (!$res) {
            throw new \Exception("Malformed response from the Edmunds API");
        }

        // No models for found for the given parameters
        if (isset($res->status) && $res->status = 'NOT_FOUND') {
            return 0;
        }
        return $res;
    }

    /**
     * Count all of the styles for the given parameters
     *
     * @param  string|null $make  Name of the Make to filter by
     * @param  string|null $model Name of the Model to filter by
     *
     * @throws Exception
     * @return int
     */
    public function countStyles(string $make = null, string $model = null)
    {

        if ($make) $this->make($make);
        if ($model) $this->model($model);

        $expects = 'stylesCount';

        if (static::$year) {
            if (!static::$model) {
                throw new \Exception(sprintf('%s::%s requires a model to be set when a year is given', __METHOD__, static::$spec));
            }
            $resourceEndpoint = sprintf('%s/%s/%s/styles/count', static::$make, static::$model, static::$year);
        } else {
            $resourceEndpoint = sprintf('%s/styles/count', static::$make);
        }

        $endpoint = $this->endpoint($this->resource, $resourceEndpoint, $this->getParams());

        $res = parent::send($endpoint);
        $res = @json_decode($res);

        if (!$res || !isset($res->$expects)) {
            throw new \Exception("Malformed response from the Edmunds API");
        }
        return $res->$expects;
    }

    /**
     * Get all of the styles for the given parameters
     *
     * @param  string|null $make  Name of the Make to filter by
     * @param  string|null $model Name of the Model to filter by
     *
     * @throws Exception
     * @return Object
     */
    public function getStyles(string $make = null, string $model = null)
    {

        if ($make) $this->make($make);
        if ($model) $this->model($model);

        if (!static::$make) {
            throw new \Exception(sprintf("%s requires a make to be set", __METHOD__));
        }


        if (!static::$model) {
            throw new \Exception(sprintf("%s requires a model to be set", __METHOD__));
        }

        if (!static::$year) {
            throw new \Exception(sprintf("%s requires a year to be set", __METHOD__));
        }

        $resourceEndpoint = sprintf("%s/%s/%s/styles", static::$make, static::$model, static::$year);

        $endpoint = $this->endpoint($this->resource, $resourceEndpoint, $this->getParams());
        $res = parent::send($endpoint);
        $res = @json_decode($res);

        if (!$res) {
            throw new \Exception("Malformed response from the Edmunds API");
        }
        return $res;
    }

    /**
     * Get a specific Style by its ID
     *
     * @param  int|null $style The style's ID
     *
     * @throws Exception
     * @return Object
     */
    public function getStyle(int $style = null)
    {
        if ($style) static::$style = $style;

        if (!static::$style) {
            throw new \Exception(sprintf("%s requires a Style ID to be set", __METHOD__));
        }

        $resourceEndpoint = sprintf("styles/%s", static::$style);

        $endpoint = $this->endpoint($this->resource, $resourceEndpoint, $this->getParams());
        $res = parent::send($endpoint);
        $res = @json_decode($res);

        if (!$res) {
            throw new \Exception("Malformed response from the Edmunds API");
        }
        return $res;

    }

    /**
     * Count all of the available Equipment for the given parameters
     *
     * @return int
     */
    public function countEquipment()
    {
        $expects = 'equipmentCount';

        if (!static::$style && !static::$equipment) {
            throw new \Exception(sprintf('%s requires either a Style ID or an Equipment ID to be set', __METHOD__));
        }

        if (static::$equipment) {
            $resourceEndpoint = sprintf('equipment/%s', static::$equipment);
        } else {
            $resourceEndpoint = sprintf('styles/%s/equipment', static::$style);
        }

        $endpoint = $this->endpoint($this->resource, $resourceEndpoint, $this->getParams());

        $res = parent::send($endpoint);
        $res = @json_decode($res);

        if (!$res || !isset($res->$expects)) {
            throw new \Exception("Malformed response from the Edmunds API");
        }
        return $res->$expects;
    }

    /**
     * Get all of the Equipment for the given parameters
     * @param  int|null $styleId     ID of the Style to filter by
     * @param  int|null $equipmentId ID of the Equipment to filter by
     *
     * @throws Exception
     * @return Object
     */
    public function getEquipment(int $styleId = null, int $equipmentId = null)
    {
        if ($styleId) {
            $this->style($styleId);
        }

        if ($equipmentId) {
            $this->equipment($equipmentId);
        }

        if (!static::$style && !static::$equipment) {
            throw new \Exception(sprintf('%s requires either a Style ID or an Equipment ID to be set', __METHOD__));
        }

        if (static::$equipment) {
            $resourceEndpoint = sprintf("equipment/%s", static::$equipment);
            $expects = null;
        } else {
            $resourceEndpoint = sprintf("styles/%s/equipment", static::$style);
            $expects = 'equipment';
        }

        $endpoint = $this->endpoint($this->resource, $resourceEndpoint, $this->getParams());
        $res = parent::send($endpoint);
        $res = @json_decode($res);

        if (!$res || ($expects && !isset($res->$expects))) {
            throw new \Exception("Malformed response from the Edmunds API");
        }
        return $expects ? $res->$expects : $res;
    }


    public function countEngines()
    {
        throw new \Exception('"count()" is not available for Engines');
    }

    /**
     * Get a list of engines available for the specified Style ID
     *
     * @param  int|null    $styleId The ID of the style to lookup
     *
     * @return object
     */
    public function getEngines(int $styleId = null)
    {
        if ($styleId) {
            $this->style($styleId);
        }

        if (!static::$style) {
            throw new \Exception(sprintf('%s requires a Style ID to be set', __METHOD__));
        }

        $resourceEndpoint = sprintf("styles/%s/engines", static::$style);

        $endpoint = $this->endpoint($this->resource, $resourceEndpoint, $this->getParams());
        $res = parent::send($endpoint);
        $res = @json_decode($res);

        if (!$res || !isset($res->engines)) {
            throw new \Exception("Malformed response from the Edmunds API");
        }
        return $res->engines;
    }

    /**
     * Get a specific engine by its ID
     *
     * @param  int|null $engineId
     * @return object
     */
    public function getEngine(int $engine = null)
    {
        if ($engine) static::$engine = $engine;

        if (!static::$engine) {
            throw new \Exception(sprintf("%s requires an Engine ID to be set", __METHOD__));
        }

        $resourceEndpoint = sprintf("engines/%s", static::$engine);

        $endpoint = $this->endpoint($this->resource, $resourceEndpoint, $this->getParams());
        $res = parent::send($endpoint);
        $res = @json_decode($res);

        if (!$res) {
            throw new \Exception("Malformed response from the Edmunds API");
        }
        return $res;
    }


    public function countTransmissions()
    {
        throw new \Exception('"count()" is not available for Transmissions');
    }

    /**
     * Get a list of transmissions available for the specified Style ID
     *
     * @param  int|null    $styleId The ID of the style to lookup
     *
     * @return object
     */
    public function getTransmissions(int $styleId = null)
    {
        if ($styleId) {
            $this->style($styleId);
        }

        if (!static::$style) {
            throw new \Exception(sprintf('%s requires a Style ID to be set', __METHOD__));
        }

        $resourceEndpoint = sprintf("styles/%s/transmissions", static::$style);

        $endpoint = $this->endpoint($this->resource, $resourceEndpoint, $this->getParams());
        $res = parent::send($endpoint);
        $res = @json_decode($res);

        if (!$res || !isset($res->transmissions)) {
            throw new \Exception("Malformed response from the Edmunds API");
        }
        return $res->transmissions;
    }

    /**
     * Get a specific transmission by its ID
     *
     * @param  int|null $transmissionId
     * @return object
     */
    public function getTransmission(int $transmission = null)
    {
        if ($transmission) static::$transmission = $transmission;

        if (!static::$transmission) {
            throw new \Exception(sprintf("%s requires an Transmission ID to be set", __METHOD__));
        }

        $resourceEndpoint = sprintf("transmissions/%s", static::$transmission);

        $endpoint = $this->endpoint($this->resource, $resourceEndpoint, $this->getParams());
        $res = parent::send($endpoint);
        $res = @json_decode($res);

        if (!$res) {
            throw new \Exception("Malformed response from the Edmunds API");
        }
        return $res;
    }

    public function countColors()
    {
        throw new \Exception('"count()" is not available for Colors');
    }

    /**
     * Get a list of colors available for the specified Style ID
     *
     * @param  int|null    $styleId The ID of the style to lookup
     *
     * @return object
     */
    public function getColors(int $styleId = null)
    {
        if ($styleId) {
            $this->style($styleId);
        }

        if (!static::$style) {
            throw new \Exception(sprintf('%s requires a Style ID to be set', __METHOD__));
        }

        $resourceEndpoint = sprintf("styles/%s/colors", static::$style);

        $endpoint = $this->endpoint($this->resource, $resourceEndpoint, $this->getParams());
        $res = parent::send($endpoint);
        $res = @json_decode($res);

        if (!$res || !isset($res->colors)) {
            throw new \Exception("Malformed response from the Edmunds API");
        }
        return $res->colors;
    }

    /**
     * Get a specific color by its ID
     *
     * @param  int|null $transmissionId
     * @return object
     */
    public function getColor(int $color = null)
    {
        if ($color) static::$color = $color;

        if (!static::$color) {
            throw new \Exception(sprintf("%s requires an Color ID to be set", __METHOD__));
        }

        $resourceEndpoint = sprintf("colors/%s", static::$color);

        $endpoint = $this->endpoint($this->resource, $resourceEndpoint, $this->getParams());
        $res = parent::send($endpoint);
        $res = @json_decode($res);

        if (!$res) {
            throw new \Exception("Malformed response from the Edmunds API");
        }
        return $res;
    }

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

}
