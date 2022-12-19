<?php

namespace udm\controller;

class Home extends AController {

    function process(): void {

    }

    function getData(): array {
        return [
            "materialsCount" => $this->model->material->getCount(),
            "subjectsCount" => $this->model->subject->getCount()
        ];
    }

}
