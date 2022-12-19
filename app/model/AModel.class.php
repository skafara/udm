<?php

namespace udm\model;

use PDO;

abstract class AModel {

    protected PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

}
