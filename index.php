<?php
/**
 * Release flow index file
 */
require_once dirname(__DIR__) .'/vendor/autoload.php';

$application = new bonndan\ReleaseFlow\Application('release-flow');
$application->run();