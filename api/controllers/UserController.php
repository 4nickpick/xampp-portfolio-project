<?php
class UserController extends BaseController
{

    /**
     * "/users/" Endpoint 
     * GET - Get User By Id
     * POST - Create User 
     */
    public function default() {
        $strErrorDesc = '';
        $queryParams = $this->getQueryStringParams();
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        if (strtoupper($requestMethod) == 'GET') {
            try {
                $userId = $queryParams["id"];
                if($userId && intval($userId) > 0) {
                    $userRepository = new UserRepository();
                    $arrUsers = $userRepository->getByUserId($userId);

                    if(count($arrUsers) == 1) {
                        $responseData = json_encode($arrUsers[0]);
                    }
                    else {
                        $strErrorDesc = 'User Not Found';
                        $strErrorHeader = 'HTTP/1.1 404 Not Found';
                    }
                }
                else {
                    $strErrorDesc = 'Invalid User ID';
                    $strErrorHeader = 'HTTP/1.1 400 Bad Request';
                }
            } catch (Exception|Error $e) {
                $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } 
        else if(strtoupper($requestMethod) == 'POST'){
            try {
                $body = $this->getRequestBodyContent(); 

                $firstName = $body["firstName"];
                $lastName = $body["lastName"];
                $email = $body["email"];
                $password = $body["password"];

                $userRepository = new UserRepository();
                $user = $userRepository->create($firstName, $lastName, $email, $password);

                $responseData = json_encode($user);
            } 
            catch (Exception|Error $e) {
                if(str_contains($e->getMessage(), "Duplicate entry")) {
                    $strErrorDesc = "Account with email address '{$email}' already exists, please log in.";
                    $strErrorHeader = 'HTTP/1.1 400 Bad Request';
                }
                else {
                    $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
                    $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
                }
            }
        } 
        else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }

        // send output 
        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)), 
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }

    /** 
    * GET"/users/list" Endpoint - Get list of users 
    */
    public function list()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        if (strtoupper($requestMethod) == 'GET') {
            try {
                $userRepository = new UserRepository();
                $arrUsers = $userRepository->getAll();
                $responseData = json_encode($arrUsers);
            } catch (Exception|Error $e) {
                $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }

        // send output 
        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)), 
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }


    /** 
    * "/users/login" Endpoint - Log in a user 
    */
    public function login()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $body = $this->getRequestBodyContent();

        if (strtoupper($requestMethod) == 'POST') {
            try {
                $userRepository = new UserRepository();
                $user = $userRepository->login($body["email"], $body["password"]);
                $responseData = json_encode($user);
            } catch (Exception|Error $e) {
                $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }

        // send output 
        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)), 
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }

    /** 
    * "/users/forgotpassword" Endpoint - Request a Forgot Password Email 
    */
    public function forgotPassword()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $queryParams = $this->getQueryStringParams();
        if (strtoupper($requestMethod) == 'GET') {
            try {

                $token = $queryParams["t"];

                $userRepository = new UserRepository();
                $arrUsers = $userRepository->getByForgotPasswordToken($token);

                $user = count($arrUsers) == 1 ? $arrUsers[0] : null;
                
                if($user && strtotime($user["ForgotPasswordTokenExpirationTime"]) > strtotime("now")) {
                    
                    // we don't want to pass these secret fields up to the API user, let's remove them from the response
                    unset($user["ForgotPasswordTokenExpirationTime"]);

                    $responseData = json_encode($user);
                }
                else {
                    $strErrorDesc = 'Password Reset Token Expired';
                    $strErrorHeader = 'HTTP/1.1 400 Bad Request';
                }
                
                if (!$strErrorDesc) {
                    $this->sendOutput(
                        $responseData,
                        array('Content-Type: application/json', 'HTTP/1.1 200 OK')
                    );
                } else {
                    $this->sendOutput(json_encode(array('error' => $strErrorDesc)), 
                        array('Content-Type: application/json', $strErrorHeader)
                    );
                }
            } catch (Exception|Error $e) {
                $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }

        }
        else if (strtoupper($requestMethod) == 'POST') {
            try {
                $body = $this->getRequestBodyContent();
                $userRepository = new UserRepository();
                $email = $body["email"];

                $forgotPasswordToken = $userRepository->forgotPasswordRequest($email);

                // always return true, regardless of whether the email address was on file
                $responseData = json_encode(true);
                
                if(strlen($forgotPasswordToken) > 0) {
                    // send the forgot email 
                    mail(
                        "4nickpick@gmail.com", 
                        "My Project Forgot Password Request", 
                        "Here's your password reset link: http://myproject.com/?t={$forgotPasswordToken} - If you did not request this, please ignore this email."
                    );
                }
                
                if (!$strErrorDesc) {
                    $this->sendOutput(
                        $responseData,
                        array('Content-Type: application/json', 'HTTP/1.1 200 OK')
                    );
                }
            } catch (Exception|Error $e) {
                $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
    }

    /** 
    * "/users/passwordreset" Endpoint - Log in a user 
    */
    public function resetPassword()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        if (strtoupper($requestMethod) == 'POST') {
            try {
                $body = $this->getRequestBodyContent();

                $userRepository = new UserRepository();

                $arrUsers = $userRepository->getByForgotPasswordToken($body["t"]);
                $user = count($arrUsers) == 1 ? $arrUsers[0] : null;

                if($body["password"] == $body["password_again"]) {
                    $userRepository->resetPassword($user["UserId"], $body["password"]);

                    // Let's clear the forgot password token information from the database while we're at it
                    $userRepository->clearForgotPasswordToken($user["UserId"]);

                    $responseData = json_encode(true);
                }
                else {
                    $strErrorDesc = $e->getMessage().'Passwords do not match. Please try again.';
                    $strErrorHeader = 'HTTP/1.1 400 Bad Request';
                }
            } catch (Exception|Error $e) {
                $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }

        // send output 
        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)), 
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }
}