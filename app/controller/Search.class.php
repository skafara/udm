<?php

namespace udm\controller;

class Search extends AController {

    function process(): void {

    }

    function getData(): array {
        $q = $_GET["q"] ?? null;
        return [
            "query" => $q,
            "subjects" => $this->model->search->getSubjects($q),
            "teachers" => $this->model->search->getTeachers($q),
            "materials" => $this->model->search->getMaterials($q, $this->model->subject)
        ];
    }

}