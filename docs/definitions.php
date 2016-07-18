<?php
require 'bootstrap.php';

use Plasticbrain\EdmundsApiClient;

$pageId = 'definitions';
$pageTitle = 'Definitions';
$pageSubtitle = 'Helpful Definitions';

$definitions = [];

$definitionsFile = __DIR__ . '/inc/definitions.php';
if (!file_exists($definitionsFile)) {
    $msgs['errors'][] = sprintf('The definitions file was not found at "%s"', $definitionsFile);
} else {
    $definitions = require $definitionsFile;
}
ksort($definitions);


require 'header.php';
?>

<div id="content" class="container-fluid">
    
    <table class="table table-hover definitions">
        <tbody>
            <?php foreach ($definitions as $word => $definition): ?>
            <tr>
                <td class="def-word text-right"><?= ucwords($word); ?>:</td>
                <td class="def-definition"><?= $definition; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
        


    </dl>


</div>
<!-- /#content.container -->


<?php require 'footer.php'; ?>