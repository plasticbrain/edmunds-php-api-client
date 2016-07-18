<?php

function definition($word, $returnHtml = true)
{
    if (!isset($words)) {
        if (!file_exists(__DIR__ . '/definitions.php')) {
            throw new \Exception(sprintf('The definitions file was not found at "%s"', __DIR__ . '/definitions.php'));
        }

        $words = require __DIR__ . '/definitions.php';
    }

    $searchTerm = trim(strtolower($word));

    if (isset($words[$searchTerm])) {
        $definition = $words[$searchTerm];
        $icon = 'fa fa-lightbulb-o';
    } else {
        // return sprintf('No definition was found for "%s"', htmlspecialchars($word));
        $definition = sprintf('No definition was found for "%s"', htmlspecialchars($word));
        $icon = 'fa fa-warning';
    }

    if (!$returnHtml) {
        return $definition;
    }

    
    if (substr(strtolower($definition), 0, 3) != '<p>') {
        $definition = "<p>$definition</p>";
    }

    $htmlTemplate = '
        <div class="definition">
            <div class="icon"><i class="%s"></i></div>
            <div class="text">%s</div>
        </div>';

    return sprintf(
        trim($htmlTemplate),
        $icon,
        $definition
    );
}

function footnotes(array $footnotes)
{
    $template = '
        <div class="footnotes">
            <h5>Footnotes and Supplemental Info</h5>
            <ol>
                %s
            </ol>
        </div>
    ';

    $lines = [];
    foreach ($footnotes as $footnote) {
        $lines[] = "<li>$footnote</li>";
    }

    return sprintf($template, implode(PHP_EOL, $lines));
        
}

function str_slug($string)
{

    return strtolower(preg_replace('/[^a-z0-9]/i', '-', $string));
}

function stripUrlProtocol($url)
{
    return str_replace(['http:', 'https:'], '', $url);
}

/**
 * Dump and Die
 *
 * @return mixed
 */
function dd()
{
    array_map(function($x) { var_dump($x); }, func_get_args());
    exit;
}

/**
 * Get the current user's IP address
 *
 * @return mixed
 */
function getIp()
{

    // Check for CloudFlare
    if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && !empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        return $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    return @$_SERVER['REMOTE_ADDR'];

}


/**
 * Output a JSON response
 *
 * @param bool   $success whether or not the last action was successful
 * @param string $msg     The message to send
 * @param array  $data    Optional array of data to pass
 *
 * @return JSON
 */
function jsonOut($success = true, $msg = null, $data = null)
{
    $out = ['success' => (bool) $success];
    if ($msg) {
        $out['message'] = $msg;
    }
    if ($data) {
        foreach ($data as $key => $val) {
            $out[$key] = $val;
        }
    }

    header('Content-type:application/json;charset=utf-8');
    print json_encode($out);
    exit;
}

/**
 * Output an error message
 *
 * @param string $msg  The message to send
 * @param array  $data Optional array of data to pass
 *
 * @return JSON
 */
function jsonError($msg = null, $data = null)
{
    return jsonOut(false, $msg, $data);
}

/**
 * Output a success  message
 *
 * @param string $msg  The message to send
 * @param array  $data Optional array of data to pass
 *
 * @return JSON
 */
function jsonSuccess($msg = null, $data = null)
{
    return jsonOut(true, $msg, $data);
}


/**
 * Returns the current URL
 *
 * @param  array   $params               Optional query string parameters to add
 * @param  boolean $includeTrailingSlash Whether or not to include a traling slash
 * @param  boolean $excludeQueryString   Whether or not to exclude the query string altogether
 *
 * @return string
 */
function currentUrl(array $params = [], $includeTrailingSlash = false, $excludeQueryString = false)
{

    // http(s)://domain.com
    $url = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://'. $_SERVER['SERVER_NAME'] : 'http://'.$_SERVER['SERVER_NAME'];

    // add the port number, if there is one
    if ($_SERVER['SERVER_PORT'] != "80" && $_SERVER['SERVER_PORT'] != '443') {
        $url .=  ':'. $_SERVER['SERVER_PORT'];
    }
    $url .= rtrim($_SERVER['PHP_SELF'], '/');

    if ($includeTrailingSlash) {
        $url .= '/';
    }

    // Handle the current query string and any added query string data
    parse_str($_SERVER['QUERY_STRING'], $qs);

    // Add and additional parameters that may have been included
    if (!empty($params)) {
        $qs = array_merge($qs, $params);
    }

    $qs = http_build_query($qs);

    if ($qs && !$excludeQueryString) {
        $url .= "?$qs";
    }

    return $url;
}

function printParamsTable(array $params)
{
?>
<table class="table table-bordered">
    <thead>
        <tr>
            <th class="param-name">Parameter</th>
            <th class="param-desc">Description</th>
            <th class="param-possible">Possible Values</th>
            <th class="param-defaults">Default Value</th>
            <?php /*<th class="param-req">Required</th>*/ ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($params as $param => $info) : ?>
        <tr>
            <td class="param-name"><samp><?= $param; ?></samp></td>
            <td class="param-desc"><?= $info['description']; ?></td>
            <td class="param-possible"><?= implode(', ', $info['possible']); ?></td>
            <td class="param-defaults"><?= $info['default']; ?></td>
            <?php /*<td class="param-req"><?= $info['required'] ? 'Yes' : 'No'; ?></td>*/ ?>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php

}
