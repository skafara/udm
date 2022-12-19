<?php

namespace udm\model;

use PDO;

class Model {

    public User $user;
    public Material $material;
    public Subject $subject;
    public Search $search;

    public function __construct(PDO $pdo) {
        $this->user = new User($pdo);
        $this->material = new Material($pdo);
        $this->subject = new Subject($pdo);
        $this->search = new Search($pdo);
    }

}
