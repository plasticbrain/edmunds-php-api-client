<?php
require 'bootstrap.php';

$api->set('api_key', $_SERVER['EDMUNDS_API_KEY']);


try {

    $vehicle = $api->vehicle();

    // $make = $api->make();
    // $model = $api->model();


/*    
    $res = $vehicle
        // ->year(2014)
        // ->state('new')
        ->getMakes();
*/


    // Get a count of all makes, regardless of year or state
    // $res['counts']['All makes, regardless of year or state'] = $vehicle->countMakes();

    // Get a count of all new make for the current year
    // $res['counts']['New makes in the current year'] = $vehicle->year(date('Y'))->state('new')->countMakes();

    // Get a count of all the models for the make "Tesla"
    // $res['counts']['Tesla models'] = $vehicle->make('Tesla')->countModels();
    // $res = $vehicle->make('Tesla')->model('Model 3')->count();

    // Get a count of all the models for new 2016 BMWs
    // $res['counts']['New 2016 BWMs'] = $vehicle->make('BMW')->year(2016)->state('new')->countModels();

    // $res['makes']['All Makes'] = $vehicle->getMakes();
    // $res['makes']['All 2014 makes'] = $vehicle->year(2014)->getMakes();
    // $res['makes']['All new makes'] = $vehicle->state('new')->getMakes();

    // Get all makes
    // $res = $vehicle->get();
    // $res = $vehicle->make('Tesla')->get();
    // $res =  $vehicle->make('Ford')->year(2016)->get();
    // $res =  $vehicle->make('BMW')->get();
    
    // $res = $vehicle->make('Ford')->model('Fusion')->year(2015)->styles()->get();
    // $res = $vehicle->make('Ford')->model('Fusion')->styles()->count();

    // $res = $vehicle->make('Tesla')->model('Model S')->year(2016)->get();
    
    // $res = $vehicle->make('Tesla')->model('Model 3')->year(2016)->count();
    // $res = $vehicle->make('Tesla')->model('Model 3')->year(2018)->get();
    // $res = $vehicle->make('Ford')->model('Fusion')->year(2016)->get();
    // $res = $vehicle->make('Ford')->styles()->count();
    // $res = $vehicle->year(2015)->getModel('Ford', 'Fusion');
    

    // $res = $vehicle->count();
    // $res = $vehicle->count();
    // $res = $vehicle->make('Ford')->count();
    // $res = $vehicle->make('Ford')->year(2014)->count();
    // $res = $vehicle->year(2016)->count();
    
    // $res = $vehicle->style(200744431)->get();
    // $res = $vehicle->style(200744431)->equipment()->count();
    // $res = $vehicle->style(200744431)->getEquipment();
    // $res = $vehicle->style(200744431)->equipment()->get();
    // $res = $vehicle->equipment(200069329)->get();
    
    // $res = $vehicle->style(200744431)->engines()->get();
    // $res = $vehicle->engine(200744476)->get();

    // $res = $vehicle->style(200744431)->transmissions()->get();
    // $res = $vehicle->transmission(200744478)->get();

    // $res = $vehicle->style(200744431)->colors()->get();
    $res = $vehicle->color(200744689)->get();

} catch (Exception $e) {
    echo $e->getMessage();
}

echo '<h5>Endpoint</h5>';
echo '<p>' . $api->getEndpoint() . '</p>';

echo '<h5>Params</h5>';
echo '<ul>';
$params = $api->getParams();
foreach ($params as $k => $v) {
    echo "<li>$k: $v</li>";
}
echo '</ul>';



if (isset($res)) {
    echo '<h5>Results</h5>';
    echo "<pre>";
    print_r($res);
    echo "</pre>";
}



