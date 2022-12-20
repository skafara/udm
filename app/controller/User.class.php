<?php

namespace udm\controller;

use udm\model\Model;

class User extends AController {

    function process(): void {
        // action create
        // authaction create, delete
        if (isset($_GET["action"])) {
            if (isset($_GET["create"])) {

            }
        }
        elseif (isset($_GET["authaction"])) {
            $authaction = $_GET["authaction"];
            if ($authaction == "create") {
                $this->handleCreateTeacher();
                $this->redirect(".?page=admin");
            }
            elseif ($authaction == "delete") {
                if (isset($_GET["id"])) {
                    if ($this->model->user->getUserTypeId() == ID_USERTYPE_ADMIN) {
                        $this->model->user->delete($_GET["id"], $this->model->material, $this->model->user);
                        $this->redirect(".?page=admin");
                    }
                }
            }
        }
    }

    function getData(): array {
        return [];
    }

    private function handleCreateTeacher(): void {
        if (!isset($_POST["login"], $_POST["password"], $_POST["firstName"], $_POST["lastName"], $_POST["email"])) {
            return;
        }
        $this->model->user->registerTeacher();
    }
}
