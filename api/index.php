<?php

require_once "./includes/bootstrap.php";

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

if ((isset($uri[1]) && $uri[1] != 'users')) {
    header("HTTP/1.1 404 Not Found");
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    header("HTTP/1.1 200 OK");
    exit();
}

require PROJECT_ROOT_PATH . "/controllers/UserController.php";
$objFeedController = new UserController();
$strMethodName = isset($uri[2]) && strlen($uri[2]) > 0 ? $uri[2] : "default";
$objFeedController->{$strMethodName}();
