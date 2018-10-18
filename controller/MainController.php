<?php


class MainController
{

    private $layoutView;
    private $loginView;
    private $timeView;
    private $registerView;

    // private $registerController;
    // private $loginController;

    private $database;
    private $credentials;


    public function __construct() {
        $this->layoutView = new LayoutView();
        $this->loginView = new LoginView();
        $this->timeView = new DateTimeView();
        $this->registerView = new RegisterView();
        $this->database = new Database();
        // $this->registerController = new RegisterController();
        // $this->loginController = new LoginController();
    }

    public function runLoginOrRegister() {

        if ($this->loginView->isLoggingOut()) {
            $this->killSession();
            $this->loginView->setMessage($this->loginView->logoutMessage());
            $this->layoutView->render(false, $this->loginView, $this->timeView);
            return;
        } else if($this->loginView->isNavigatingToRegistration()) {

            if($this->registerUser()) {
                return $this->layoutView->render(false, $this->loginView, $this->timeView);
            } else {
                return $this->layoutView->render(false, $this->registerView, $this->timeView);
            }
        } else if ($this->loginView->isTryingToLogin()) {
            $this->loginView->setMessage($this->loginView->validationMessageLogin());
            $this->login();
        }
            // Default
            $this->renderHTML($this->loginView);
    }


    private function renderHTML($view) {
        $this->layoutView->render($this->loginView->isAuthorised(), $view, $this->timeView);
    }


    public function killSession() {
        session_destroy();
    }


    public function login() {
        $credentials = $this->loginView->getCredentialsInForm();
        $username = $credentials->getUsername();
        $password = $credentials->getPassword();

        if($this->loginView->isTryingToLogin()) {
         if (!$this->database->isCorrectPasswordForUsername($username, $password)) {
                return false;
            } else {
                $_SESSION['username'] = $credentials->getUsername();
                $_SESSION['password'] = $credentials->getPassword();
                // loginmessage
                $this->loginView->setMessage($this->loginView->welcomeMessage());

                return true;
            }
         }
    }

     public function registerUser() {
        $credentials = $this->registerView->getCredentialsInForm();
        $username = $credentials->getUsername();
        $password = $credentials->getPassword();

        if($this->registerView->isTryingToSignup()) {
             
            $this->registerView->setMessage($this->registerView->validationMessageRegister());

        if ($this->registerView->isUserValid()) {
            $this->loginView->setMessage($this->registerView->validationMessageRegister());
            $this->database->saveUser($username, $password);
            return true;
           } else {
               return false;
           }

        }
    }
}




