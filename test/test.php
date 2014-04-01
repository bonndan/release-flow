<?php
/**
 * Manual test
 */
require_once dirname(__DIR__) .'/vendor/autoload.php';

$application = new bonndan\ReleaseFlow\Application('release-flow TEST');
$application->run();