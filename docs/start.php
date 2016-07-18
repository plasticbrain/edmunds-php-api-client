<?php

require 'bootstrap.php';

$pageId = 'start';
$pageTitle = 'Getting Started';
$pageSubtitle = $pageTitle;


require 'header.php';
?>
<div id="content" class="container-fluid">
    <div class="panel panel-custom">
        <a name="start"></a>
            <div class="panel-body">
                
                <div class="panel-head">
                    <h3>Configuring and Instantiating the API client</h3>
                </div>

                <p></p>

            <h4>Configuration Parameters</h4>
            <?php
            $configParams = [
                'api_key' => [
                    'required' => true,
                    'description' => 'API Key supplied by Edmunds <sup><a target="_blank" href="http://developer.edmunds.com/api-documentation/overview/index.html">[1]</a></sup>',
                    'possible' => [],
                    'default' => '',
                ],
                'protocol' => [
                    'required' => false,
                    'description' => 'Which protocol to use when making API calls',
                    'possible' => ['http', 'https'],
                    'default' => 'Defaults to whichever protocol the current site is using',
                ],
                'http_handler' => [
                    'required' => false,
                    'description' => 'Which HTTP handler to use for making API calls',
                    'possible' => ['curl', 'guzzle'],
                    'default' => 'curl',
                ],
                'ignore_ssl_errors' => [
                    'required' => false,
                    'description' => 'Whether or not to ignore any SSL errors that may be encountered',
                    'possible' => ['true', 'false'],
                    'default' => 'false',
                ],
            ];
            ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="param-name">Parameter</th>
                        <th class="param-desc">Description</th>
                        <th class="param-possible">Possible Values</th>
                        <th class="param-defaults">Default Value</th>
                        <th class="param-req">Required</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($configParams as $param => $info) : ?>
                    <tr>
                        <td class="param-name"><samp><?= $param; ?></samp></td>
                        <td class="param-desc"><?= $info['description']; ?></td>
                        <td class="param-possible"><?= implode(', ', $info['possible']); ?></td>
                        <td class="param-defaults"><?= $info['default']; ?></td>
                        <td class="param-req"><?= $info['required'] ? 'Yes' : 'No'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h4>Instantiating the API Client</h4>
            <h5>Example 1: Only passing an API key (and using the default values for everything else)</h5>
<pre><code class="language-php line-numbers">&lt;?php
try {
    $api = new EdmundsApiClient(['api_key' => "Your Edmunds API Key"]);
} catch (Exception $e) {
    // Handle any Exceptions
}
</code></pre>
            <h5>Example 2: Using more of the available config parameters</h5>
<pre><code class="language-php line-numbers">&lt;?php
$config = [
    'api_key' => "&lt;Your Edmunds API Key>",
    'protocol' => 'http',
    'http_handler' => 'curl',
    'ignore_ssl_errors' => false,
];

try {
    $api = new EdmundsApiClient($config);
} catch (Exception $e) {
    // Handle any Exceptions
}
</code></pre>
    
     <h4>Errors, and Catching Exceptions</h4>
                <p>In the event that an error occurs, the API will throw a generic <mark>Exception</mark>. Because of this, all API methods should be wrapped in <code>try/catch</code> blocks:</p>
                <pre><code class="language-php line-numbers">try {
    // API Method to call
} catch (Exception $e) {
    // Handle any errors
    // echo "Error: " . $e->getMessage();
}</code></pre>
                <p>For more information on PHP Exceptions, see <a href="http://php.net/manual/en/language.exceptions.php" target="_blank">the manual</a>.</p>


    <?php
    echo footnotes([
        'Get or create an Edmunds API key at the <a target="_blank" href="http://developer.edmunds.com/api-documentation/overview/index.html">Edmunds Developer Portal</a>'
    ]);
    ?>

    </div>
    </div>
</div>
<!-- /#content.container -->

<?php require 'footer.php'; ?>