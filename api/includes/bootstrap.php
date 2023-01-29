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

// instantiate our dependencies
require_once PROJECT_ROOT_PATH . "/vendor/autoload.php";


// include the base controller
require PROJECT_ROOT_PATH . "/controllers/BaseController.php";

// include our repositories
require_once PROJECT_ROOT_PATH . "/repositories/BaseRepository.php";
require_once PROJECT_ROOT_PATH . "/repositories/UserRepository.php";

// $userRepository = new UserRepository(); 

// $userRepository->create('Bobby', 'Tables', 'bobby@bobbytables.com', 'mypassword');

// $login = $userRepository->login('bobby@bobbytables.com', 'pikachu');
// var_dump($login);

// $userRepository->resetPassword(7, 'pikachu');

// $login = $userRepository->login('bobby@bobbytables.com', 'pikachu');
// var_dump($login);

// if($userLogin) {
//     echo "Welcome, {$userLogin['FirstName']}!";
// }
// else {
//     echo "Login failed...";
// }

// $users = $userRepository->getAll(); 

// foreach($users as $user) {
//     echo $user["FirstName"] . "<br />"; 
// }

// phpinfo();
