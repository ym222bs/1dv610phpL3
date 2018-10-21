<?php

class LoginController {

    private $database;
    private $loginView;
    private $validation;

    public function __construct(Database $db, LoginView $lv, InputValidation $iv) {

        $this->database = $db;
        $this->loginView = $lv;
        $this->validation = $iv;
    }


    public function login() {
        $credentials = $this->loginView->getCredentialsInForm();

        $this->loginView->setMessage($this->validation->validationMessageLogin($credentials));

        $username = $credentials->getUsername();
        $password = $credentials->getPassword();

        if($this->loginView->isTryingToLogin()) {
        if (!$this->database->isCorrectPasswordForUsername($username, $password)) {
            return false;
        } else {
            $_SESSION['username'] = $credentials->getUsername();
            $_SESSION['password'] = $credentials->getPassword();
            $this->loginView->setMessage($this->loginView->welcomeMessage());
            return true;
        }

       } 
    }
}