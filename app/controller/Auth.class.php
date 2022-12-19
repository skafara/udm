<?php

namespace udm\controller;

use udm\model\Model;

class Auth extends AController {

    const AUTH_FORM = "authform";
    const AUTH_ACTION = "authaction";

    const LOGIN = "login";
    const LOGOUT = "logout";
    const REGISTER = "register";

    private array $data;

    public function __construct(Model $model) {
        parent::__construct($model);
        $this->data = [];
    }

    function process(): void {
        if (isset($_GET[self::AUTH_FORM])) { // form is requested to be displayed
            if (in_array($_GET[self::AUTH_FORM], [self::LOGIN, self::REGISTER])) {
                $this->data[self::AUTH_FORM] = $_GET[self::AUTH_FORM];
            }
        }
        if (isset($_GET[self::AUTH_ACTION])) { // authentication is requested
            $authAction = $_GET[self::AUTH_ACTION];
            switch ($authAction) {
                case self::LOGIN:
                    $this->login();
                    break;
                case self::LOGOUT:
                    $this->logout();
                    break;
                case self::REGISTER:
                    $this->register();
                    break;
            }
        }
        $this->data["loggedIn"] = isset($_SESSION["id"]); // determine whether user is logged in
    }

    function getData(): array {
        return $this->data;
    }

    private function login(): void {
        if (!isset($_POST["login"], $_POST["password"])) { // login or password was not sent
            return;
        }

        $id = $this->verify($_POST["login"], $_POST["password"]);
        if ($id == null) { // invalid login attempt
            return;
        }

        $_SESSION["id"] = $id;
    }

    private function verify(string $login, string $password): ?int {
        $storedPassword = $this->model->user->getPassword($login);
        if (!$storedPassword) { // there is no password for login
            return null;
        }
        if (!password_verify($password, $storedPassword)) { // passwords do not match
            return null;
        }
        return $this->model->user->getId($login);
    }

    private function register(): void {
        return;
    }

    private function logout(): void {
        unset($_SESSION["id"]);
    }

}
