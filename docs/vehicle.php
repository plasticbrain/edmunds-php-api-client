<?php
require 'bootstrap.php';

use Plasticbrain\EdmundsApiClient;

$pageTitle = "Vehicle API Class";
$pageId = 'vehicle';


require 'header.php';
?>

<div id="content" class="container-fluid">

                
<div class="row">
    <div class="col-md-9">


        <div class="panel panel-custom">
            <a name="intro"></a>
            <div class="panel-body">

                <div class="panel-head">
                    <h3>Introduction</h3>
                </div>
                
                <p>The <code>Vehicle</code> subclass provides access to details about a vehicle's available options and features. Data includes: </p>
                <ul>
                    <li><a href="#toc-makes"><samp>Makes</samp></a></li>
                    <li><a href="#toc-models"><samp>Models</samp></a></li>
                    <li><a href="#toc-styles"><samp>Styles</samp></a></li>
                    <li><a href="#toc-equipment"><samp>Equipment</samp></a></li>
                    <li><a href="#toc-engines"><samp>Engines</samp></a></li>
                    <li><a href="#toc-transmissions"><samp>Transmissions</samp></a></li>
                    <li><a href="#toc-colors"><samp>Colors</samp></a></li>
                    
                </ul>
                
               
            </div>
        </div>
        <!-- /intro -->

        <div class="panel panel-custom">
            <a name="start"></a>
            <div class="panel-body">

                <div class="panel-head">
                    <h3>Getting Started</h3>
                </div>
                
                <div class="alert alert-warning">
                    <p><b>Note:</b> The main API client must be <a class="" href="start.php#start">instantiated</a> before the <samp>Vehicle</samp> class can be used</p>
                </div>

                <p>Use the main API Client's <code>vehicle</code> method to instantiate the <samp>Vehicle</samp> class.</p>
                <pre><code class="language-php line-numbers">$vehicle = $api->vehicle();</code></pre>
               
            </div>
        </div>
        <!-- /start -->

        <div class="panel panel-custom">
            <a name="parameters"></a>
            <div class="panel-body">

                <div class="panel-head">
                    <h3>Parameters</h3>
                </div>

                <h4>Available Parameters</h4>
                <?php
                $params = [
                    'make' => [
                        'required' => false,
                        'description' => 'The make of car',
                        'possible' => ['The make <mark>name</mark> as returned by the API (see <code>get()</code>)'],
                        'default' => '',
                    ],
                    'model' => [
                        'required' => false,
                        'description' => 'The model of car',
                        'possible' => ['The model <mark>name</mark> as returned by the API (see <code>get()</code>)'],
                        'default' => '',
                    ],
                    'state' => [
                        'required' => false,
                        'description' => 'The state of the cars',
                        'possible' => ['new', 'used', 'future'],
                        'default' => '',
                    ],
                    'year' => [
                        'required' => false,
                        'description' => 'The year of the cars',
                        'possible' => ['1990-current year'],
                        'default' => '',
                    ],
                    'view' => [
                        'required' => false,
                        'description' => 'The level of details in the response',
                        'possible' => ['basic', 'full'],
                        'default' => 'basic',
                    ],
                ];

                printParamsTable($params);
                ?>
                
                <h4>Setting Parameters</h4>

                <p>The <code>Vehicle</code> class provides helper methods to set the <mark>Make</mark>, <mark>Model</mark>, <mark>Year</mark>, <mark>State</mark>, and <mark>View</mark> parameters.</p>

                <pre><code class="language-php line-numbers">// Set the Make
$vehicle->make('BMW');

// Set the Model
$vehicle->model('5 Series');

// Set the Year
$vehicle->year(2016);

// Set the State (ie: New, Used, Future)
$vehicle->state('new');

// Set the View (ie: basic, full)
$vehicle->view('full');
                            
// Or, use method chaining
$vehicle->make('Ford')->model('Fusion')->year(2015);
</code></pre>
            
            </div>
        </div>
        <!-- /parameters -->


        <div class="panel panel-custom">
            <a name="makes"></a>
            <div class="panel-body">
                
                <div class="panel-head">
                    <h3>Makes</h3>
                </div>

                <?= definition('Make'); ?>

                <h4>Count All Makes</h4>
                <pre><code class="language-php line-numbers">$count = $vehicle->count();</code></pre>
                
                <h4>Get All Makes</h4>
                <pre><code class="language-php line-numbers">$makes = $vehicle->get();</code></pre>
                
                <h4>Filter by Year and State</h4>
                <pre><code class="language-php line-numbers">// Get all Makes for a given year
$makes = $vehicle->year(2015)->get();

// Get all vehicles for a given year and state
$makes = $vehicle->year(2015)->state('new')->get();
</code></pre>
                
            </div>
        </div>

        <div class="panel panel-custom">
            <a name="models"></a>
            <div class="panel-body">
                
                <div class="panel-head">
                    <h3>Models</h3>
                </div>

                <?= definition('Model'); ?>

                <h4>Count Models for a given Make</h4>
                <pre><code class="language-php line-numbers">$count = $vehicle->make('Ford')->count();</code></pre>
                
                <h4>Get Models for a given Make</h4>
                <pre><code class="language-php line-numbers">$model = $vehicle->make('Ford')->get();</code></pre>
                
                <h4>Filter by Year and/or State</h4>
                <pre><code class="language-php line-numbers">// Get all Makes for a given year
$models = $vehicle->make('BMW')->year(2015)->get();

// Get all vehicles for a given state
$models = $vehicle->make('BMW')->state('new')->get();
</code></pre>
                
                <h4>Get a Specific Model</h4>
                <p>In this example the <mark>2015 Ford Fusion</mark> is used.</p>
                <pre><code class="language-php line-numbers">$model = $vehicle->make('Ford')->model('Fusion')->year(2015)->get();</code></pre>

            </div>
        </div>
        
         <div class="panel panel-custom">
            <a name="styles"></a>
            <div class="panel-body">
                
                <div class="panel-head">
                    <h3>Styles</h3>
                </div>

                <?= definition('Style'); ?>

                <h4>Count Styles for a Make/Model</h4>
                <pre><code class="language-php line-numbers">$count = $vehicle->make('Ford')->model('Fusion')->styles()->count();</code></pre>

                <h4>Get Styles for a Make/Model</h4>
                <pre><code class="language-php line-numbers">$styles = $vehicle->make('Ford')->model('Fusion')->styles()->get();</code></pre>

                <h4>Get Details for a Specific Style</h4>
                <p><i class="fa fa-info-circle"></i> This method requires a <mark>Style ID</mark> (See <em>Get All Styles for a given Make and Model</em>)</p>
                <pre><code class="language-php line-numbers">$styles = $vehicle->style(200744431)->get();</code></pre>

            </div>
        </div>

        <div class="panel panel-custom">
            <a name="equipment"></a>
            <div class="panel-body">
                
                <div class="panel-head">
                    <h3>Equipment</h3>
                </div>

                <?= definition('Equipment'); ?>

                <h4>Count Equipment for a given Style</h4>
                <p><i class="fa fa-info-circle"></i> This method requires a <mark>Style ID</mark></p>
                <pre><code class="language-php line-numbers">$count = $vehicle->style(200744431)->equipment()->count();</code></pre>

                <h4>Get All Equipment for a given Style</h4>
                <p><i class="fa fa-info-circle"></i> This method requires a <mark>Style ID</mark></p>
                <pre><code class="language-php line-numbers">$equipment = $vehicle->style(200744431)->equipment()->get();</code></pre>

                <h4>Get a Specific Piece of Equipment</h4>
                <p><i class="fa fa-info-circle"></i> This method requires an <mark>Equipment ID</mark></p>
                <pre><code class="language-php line-numbers">$details = $vehicle->equipment(200069329)->get();</code></pre>

            </div>
        </div>

        <div class="panel panel-custom">
            <a name="engine"></a>
            <div class="panel-body">
                
                <div class="panel-head">
                    <h3>Engines</h3>
                </div>

                <h4>Get All Engines for a given Style</h4>
                <p><i class="fa fa-info-circle"></i> This method requires a <mark>Style ID</mark></p>
                <pre><code class="language-php line-numbers">$engines = $vehicle->style(200744431)->engines()->get();</code></pre>

                <h4>Get a Specific Engine</h4>
                <p><i class="fa fa-info-circle"></i> This method requires an <mark>Engine ID</mark></p>
                <pre><code class="language-php line-numbers">$details = $vehicle->engine(200744476)->get();</code></pre>

            </div>
        </div>

        <div class="panel panel-custom">
            <a name="transmission"></a>
            <div class="panel-body">
                
                <div class="panel-head">
                    <h3>Transmissions</h3>
                </div>

                <h4>Get All Transmissions for a given Style</h4>
                <p><i class="fa fa-info-circle"></i> This method requires a <mark>Style ID</mark></p>
                <pre><code class="language-php line-numbers">$transmissions = $vehicle->style(200744431)->transmissions()->get();</code></pre>

                <h4>Get a Specific Transmission</h4>
                <p><i class="fa fa-info-circle"></i> This method requires an <mark>Transmission ID</mark></p>
                <pre><code class="language-php line-numbers">$details = $vehicle->transmission(200744478)->get();</code></pre>

            </div>
        </div>

        <div class="panel panel-custom">
            <a name="color"></a>
            <div class="panel-body">
                
                <div class="panel-head">
                    <h3>Colors</h3>
                </div>

                <h4>Get All Colors for a given Style</h4>
                <p><i class="fa fa-info-circle"></i> This method requires a <mark>Style ID</mark></p>
                <pre><code class="language-php line-numbers">$transmissions = $vehicle->style(200744431)->colors()->get();</code></pre>

                <h4>Get a Specific Color</h4>
                <p><i class="fa fa-info-circle"></i> This method requires an <mark>Color ID</mark></p>
                <pre><code class="language-php line-numbers">$details = $vehicle->color(200744689)->get();</code></pre>

            </div>
        </div>

                        
        </div>
        <!-- /left-col -->

        <div class="col-md-3">
            <div id="sidebar" data-spy="affix" data-offset-top="70" data-offset-bottom="20">
                <h5>On this Page</h5>
                <div id="toc">
                    
                </div>
            </div>
        </div>
        <!-- /right-col -->
    </div>
</div>
<!-- /#content.container -->


<?php require 'footer.php'; ?>