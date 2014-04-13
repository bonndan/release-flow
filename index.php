<?php
/**
 * Release flow index file
 */
require_once __DIR__ .'/vendor/autoload.php';

$application = new bonndan\ReleaseFlow\Application('release-flow');
$application->run();