<?php

$pageSection = isset($pageSection) ? $pageSection : null;
$pageId = isset($pageId) ? $pageId : null;
$pageTitle = isset($pageTitle) ? $pageTitle : null;
$pageSubtitle = isset($pageSubtitle) ? $pageSubtitle : null;
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= isset($pageTitle) ? "$pageTitle | " : ''; ?>Edmunds PHP API Client Examples</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/styles/prism.css">
    <link rel="stylesheet" href="assets/styles/font-awesome.min.css">
    <link rel="stylesheet" href="assets/styles/main.css">
</head>
<body>
    

    <div id="nav" class="">
        <nav class="navbar navbar-blue navbar-fixed-top">
            <div class="container-fluid">
                <div class="navbar-header">
                  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                  </button>
                  <a class="navbar-brand" href="index.php">Edmunds PHP API Client</a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li class="<?= $pageId == 'start' ? 'active' : ''; ?>"><a href="start.php">Getting Started</a></li>
                        <li class="<?= $pageId == 'vehicle' ? 'active' : ''; ?>"><a href="vehicle.php">Vehicle API</a></li>
                        <?php /*
                        <?php foreach ($apiResources as $section => $resources) : ?>
                        <?php $sectionSlug = str_slug($section); ?>
                        <li class="dropdown <?= $pageSection == $sectionSlug ? 'active' : ''; ?>">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?= $section; ?> <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <?php foreach ($resources as $resource => $available) : ?>
                                <?php $resourceSlug = str_slug($resource); ?>
                                <li class="<?= $pageId == $resourceSlug ? 'active' : ''; ?>">
                                    <?php if ($available) : ?>
                                        <a href="<?= $resourceSlug; ?>.php"><?= $resource; ?></a>
                                    <?php else: ?>
                                        <a href="#" class="text-muted"><del><?= $resource; ?></del></a>
                                    <?php endif; ?>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                        <?php endforeach; ?>
                        */ ?>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li class="<?= $pageId == 'definitions' ? 'active' : ''; ?>">
                            <a href="definitions.php">Definitions</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
    
        
    <div id="header">
        <div class="container-fluid">
            <div class="page-header">
                <h1><a href="index.php"><?= $pageTitle; ?></a></h1>
            </div>
        </div>
    </div>

    <div id="messages" class="container">
        <?php foreach ($msgs['errors'] as $err) : ?>
            <div class="alert alert-danger"><?= $err; ?></div>
        <?php endforeach; ?>

        <?php foreach ($msgs['success'] as $msg) : ?>
            <div class="alert alert-succcess"><?= $msg; ?></div>
        <?php endforeach; ?>
    </div>
