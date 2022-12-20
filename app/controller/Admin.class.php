<?php

namespace udm\controller;

use udm\model\Model;

class Admin extends AController {

    private array $data;

    public function __construct(Model $model) {
        parent::__construct($model);
        $this->data = [];
    }

    function process(): void {
        $userTypeId = $this->model->user->getUserTypeId();
        $this->data["userTypeId"] = $userTypeId;
        if (in_array($userTypeId, [ID_USERTYPE_STUDENT, ID_USERTYPE_TEACHER])) {
            $this->data["materialsPublished"] = $this->model->material->getPublished($this->model->user);
        }
        if ($userTypeId == ID_USERTYPE_TEACHER) {
            $this->data["materialsToAuthorize"] = $this->model->material->getToAuthorize($this->model->user);
        }
        if ($userTypeId == ID_USERTYPE_ADMIN) {
            $this->data["users"] = $this->model->user->getUsers();
        }
    }

    function getData(): array {
        return $this->data;
    }
}