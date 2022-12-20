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

    public function getUserTypeId(): ?int { // nebo cist ze session
        if (!isset($_SESSION["id"])) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT UserType_id FROM User WHERE id = :id;");
        $stmt->execute([":id" => $_SESSION["id"]]);
        return $stmt->fetch(PDO::FETCH_ASSOC)["UserType_id"];
    }

    public function getUsers(): array {
        $stmt = $this->pdo->prepare("SELECT U.id AS User_id, U.UserType_id AS UserType_id, UT.full_name as UserType_full_name, U.login AS User_login, U.first_name AS User_first_name, U.last_name AS User_last_name, U.email AS User_email FROM User U JOIN UserType UT ON U.UserType_id = UT.id WHERE UT.id = :id1 OR UT.id = :id2 ORDER BY UT.id, U.login;");
        $stmt->execute([":id1" => ID_USERTYPE_STUDENT, ":id2" => ID_USERTYPE_TEACHER]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registerStudent(string $login, string $password, string $firstName, string $lastName, string $email): int {
        return $this->register($login, $password, $firstName, $lastName, $email, ID_USERTYPE_STUDENT);
    }

    public function registerTeacher(string $login, string $password, string $firstName, string $lastName, string $email): int {
        return $this->register($login, $password, $firstName, $lastName, $email, ID_USERTYPE_TEACHER);
    }

    private function register(string $login, string $password, string $firstName, string $lastName, string $email, int $userTypeId): int {
        $stmt = $this->pdo->prepare("INSERT INTO User (first_name, last_name, login, email, password, UserType_id) VALUES (:first_name, :last_name, :login, :email, :password, :userTypeId);");
        $stmt->execute([
            ":first_name" => $firstName,
            ":last_name" => $lastName,
            ":email" => $email,
            ":login" => $login,
            ":password" => password_hash($password, PASSWORD_BCRYPT),
            ":userTypeId" => $userTypeId
        ]);
        return $this->pdo->lastInsertId();
    }

    public function teachesSubject(int $subjectId): bool {
        $stmt = $this->pdo->prepare("SELECT User_id FROM Teaches T WHERE Subject_id = :Subject_id AND User_id = :User_id;");
        $stmt->execute([":Subject_id" => $subjectId, ":User_id" => $_SESSION["id"]]);
        return !!$stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete(int $id, Material $material, User $user) {
        foreach ($material->getMaterialGroupIds($id) as $materialGroupId) {
            echo $materialGroupId.", ";
            $material->deleteMaterialGroup($materialGroupId, $user);
        }
        echo 20;
        $stmt = $this->pdo->prepare("DELETE FROM Teaches WHERE User_id = :User_id;");
        $stmt->execute(["User_id" => $id]);
        echo 30;
        $stmt = $this->pdo->prepare("DELETE FROM User WHERE id = :id;");
        $stmt->execute([":id" => $id]);
        echo 40;
    }

}
