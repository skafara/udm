<?php

namespace udm\controller;

use udm\model\Model;

abstract class AController {

    protected Model $model;

    public function __construct(Model $model) {
        $this->model = $model;
    }

    abstract function process(): void;

    abstract function getData(): array;

}
