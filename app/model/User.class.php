<?php

namespace udm\model;

use PDO;

class User extends AModel {

    public function getId(string $login): ?int {
        $stmt = $this->pdo->prepare("SELECT id FROM User WHERE login = :login;");
        if (!$stmt || !$stmt->execute([":login" => $login])) {
            return null;
        }
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return ($row) ? $row["id"] : null;
    }

    public function getPassword(string $login): ?string {
        $stmt = $this->pdo->prepare("SELECT password FROM User WHERE login = :login");
        if (!$stmt || !$stmt->execute([":login" => $login])) {
            return null;
        }
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return ($row) ? $row["password"] : null;
    }

    public function register(): ?int {
        return null; // id
    }

}
