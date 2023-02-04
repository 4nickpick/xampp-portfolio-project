<?php

class UserRepository extends BaseRepository {
    
    public function getAll() {
        return $this->select("SELECT UserId, FirstName, LastName, Email FROM users"); 
    }

    public function getByUserId($userId) {
        return $this->select("SELECT UserId, FirstName, LastName, Email FROM users WHERE UserId = ?", ['i', $userId]); 
    }

    public function getByEmail($email) {
        return $this->select("SELECT UserId, FirstName, LastName, Email FROM users WHERE Email LIKE ?", ['s', $email]); 
    }

    public function getByForgotPasswordToken($token) {
        return $this->select("SELECT UserId, FirstName, LastName, Email, ForgotPasswordTokenExpirationTime FROM users WHERE ForgotPasswordToken = ?", ['s', $token]); 
    }

    public function create($firstName, $lastName, $email, $password) {

        $salt = $this->generateSalt(8); 

        $passwordHashed = $this->generateSHA256($password, $salt);

        return $this->executeStatement(
            "INSERT INTO users (FirstName, LastName, Email, Password, Salt) 
            VALUE (?, ?, ?, ?, ?)", 
            ['sssss', [$firstName, $lastName, $email, $passwordHashed, $salt]]); 
    }

    public function save($userId, $firstName, $lastName, $email) {
        
        $user = $this->getByUserId($userId);

        if(!$user) {
            // User does not exist, we cannot update them. 
            return false; 
        }

        return $this->executeStatement(
            "UPDATE users 
            SET FirstName = ?, 
            LastName = ?, 
            Email = ?
            WHERE UserId = ?", 
            ['sssi', [$firstName, $lastName, $email, $userId]]); 
    }

    public function login($email, $password) {

        // get a list of all user accounts where the email address matches 
        $users = $this->select("SELECT UserId, FirstName, LastName, Email, Password, Salt FROM users WHERE Email LIKE ?", ['s', $email]); 

        // If zero, or more than one, users have the specified email address we cannot login. 
        if(!$users || count($users) != 1) {
            return false;
        }

        // grab our unique user
        $user = $users[0];

        // Hash our password guess using the same salt stored with our user's current password
        $passwordHashed = $this->generateSHA256($password, $user["Salt"]);

        // if the hashes match, we're logged in. 
        $loginVerified = $passwordHashed == $user["Password"];

        if(!$loginVerified) {
            return false; 
        }

        // Add this User Login to our tracking table 
        $this->executeStatement("INSERT INTO UserLogins (UserId) VALUES (?)", ["i", $user["UserId"]]);


        unset($user["Password"]);
        unset($user["Salt"]);
        return $user; 
    }

    public function forgotPasswordRequest($email) {
        // get a list of all user accounts where the email address matches 
        $users = $this->select("SELECT UserId, FirstName, LastName, Email, Password, Salt FROM users WHERE Email LIKE ?", ['s', $email]); 

        // If zero, or more than one, users have the specified email address we cannot login. 
        if(!$users || count($users) != 1) {
            return true;
        }

        // grab our unique user
        $user = $users[0];

        $forgotPasswordToken = $this->generateForgotPasswordToken(40); 
        $forgotPasswordTokenExpiration = date_add(date_create(), date_interval_create_from_date_string("1 hour"));

        $this->executeStatement(
            "UPDATE users 
            SET ForgotPasswordToken = ?, 
            ForgotPasswordTokenExpirationTime = ?
            WHERE UserId = ?", 
            ['ssi', [$forgotPasswordToken, date_format($forgotPasswordTokenExpiration, "Y-m-d H:i:s"), $user["UserId"]]]); 

        return $forgotPasswordToken;
    }

    public function clearForgotPasswordToken($userId) {
        $user = $this->getByUserId($userId);

        if(!$user) {
            // User does not exist, we cannot update them. 
            return false; 
        }

        return $this->executeStatement(
            "UPDATE users 
            SET ForgotPasswordToken = NULL, 
            ForgotPasswordTokenExpirationTime = NULL
            WHERE UserId = ?", 
            ['i', [$userId]]); 
    }

    public function resetPassword($userId, $password) {
        $user = $this->getByUserId($userId);

        if(!$user) {
            // User does not exist, we cannot update them. 
            return false; 
        }

        // salt and hash our new password. Use a new salt to keep things fresh. 
        $salt = $this->generateSalt(8); 
        $passwordHashed = $this->generateSHA256($password, $salt);

        return $this->executeStatement(
            "UPDATE users 
            SET Password = ?, 
            Salt = ?
            WHERE UserId = ?", 
            ['ssi', [$passwordHashed, $salt, $userId]]); 
    }
    
    private function generateSalt($length) {
        $factory = new RandomLib\Factory;
        $generator = $factory->getGenerator(new SecurityLib\Strength(SecurityLib\Strength::MEDIUM));

        $salt = $generator->generateString($length);

        return $salt;
    }

    private function generateForgotPasswordToken($length) {
        $factory = new RandomLib\Factory;
        $generator = $factory->getGenerator(new SecurityLib\Strength(SecurityLib\Strength::MEDIUM));

        $forgotPasswordToken = $generator->generateString($length, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');

        return $forgotPasswordToken;
    }

    private function generateSHA256($password, $salt) {
        return hash('sha256', $password . $salt, false);
    }
}