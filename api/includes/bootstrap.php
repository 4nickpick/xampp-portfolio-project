<?php

// Define the project root so we can navigate to files regardless of deployment environment
define("PROJECT_ROOT_PATH", __DIR__ . "/../");

// Define a constant specifying whether we're in dev or prod context 
define("ENVIRONMENT_DEV", getenv("MYPROJECTCOM_DEV") == 1); 

// include main configuration file 
if(ENVIRONMENT_DEV) {
    require_once PROJECT_ROOT_PATH . "/includes/config.php";
}
else {
    require_once PROJECT_ROOT_PATH . "/includes/config.dev.php";
}

if(ENVIRONMENT_DEV) {
    echo "DEV ENV";
}
else {
    echo "PROD ENV";
}