<?php

namespace udm\controller;

class Subject extends AController {

    function process(): void {

    }

    function getData(): array {
        $id = $_GET["id"];
        return [
            "subject" => $this->model->subject->getDescription($id),
            "materials" => $this->model->subject->getMaterials($id)
        ];
    }
}
