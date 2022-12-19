<?php

namespace udm\model;

use PDO;

class Search extends AModel {

    public function getSubjects(?string $q): array {
        if ($q == null) {
            $stmt = $this->pdo->prepare("SELECT DISTINCT F.short_name AS Faculty_short_name, D.short_name AS Department_short_name, S.id AS Subject_id, S.short_name AS Subject_short_name, S.full_name AS Subject_full_name, U.first_name AS User_first_name, U.last_name AS User_last_name FROM Teaches T JOIN Subject S ON T.Subject_id = S.id JOIN Department D ON S.Department_id = D.id JOIN Faculty F ON D.Faculty_id = F.id JOIN User U ON T.User_id = U.id ORDER BY F.short_name, D.short_name, S.short_name, U.last_name, U.first_name;");
            $stmt->execute();
        }
        else {
            $stmt = $this->pdo->prepare("SELECT DISTINCT F.short_name AS Faculty_short_name, D.short_name AS Department_short_name, S.id AS Subject_id, S.short_name AS Subject_short_name, S.full_name AS Subject_full_name, U.first_name AS User_first_name, U.last_name AS User_last_name FROM Teaches T JOIN Subject S ON T.Subject_id = S.id JOIN Department D ON S.Department_id = D.id JOIN Faculty F ON D.Faculty_id = F.id JOIN User U ON T.User_id = U.id WHERE S.id IN (SELECT S.id FROM Teaches T JOIN Subject S ON T.Subject_id = S.id JOIN Department D ON S.Department_id = D.id JOIN Faculty F ON D.Faculty_id = F.id JOIN User U ON T.User_id = U.id WHERE F.short_name LIKE :q OR D.short_name LIKE :q OR S.short_name LIKE :q OR S.full_name LIKE :q OR U.first_name LIKE :q OR U.last_name LIKE :q OR CONCAT(F.short_name, '/', D.short_name) LIKE :q OR CONCAT(D.short_name, '/', S.short_name) LIKE :q OR CONCAT(U.last_name, ' ', U.first_name) LIKE :q OR CONCAT(U.first_name, ' ', U.last_name) LIKE :q) ORDER BY F.short_name, D.short_name, S.short_name, U.last_name, U.first_name;");
            $stmt->execute([":q" => "%$q%"]);
        }
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = [];
        foreach ($rows as $row) {
            $faculty = $row["Faculty_short_name"];
            $department = $row["Department_short_name"];
            $id = $row["Subject_id"];
            $teacher = [
                "User_first_name" => $row["User_first_name"],
                "User_last_name" => $row["User_last_name"]
            ];
            if (!array_key_exists($faculty, $result)) {
                $result[$faculty] = [];
            }
            if (!array_key_exists($department, $result[$faculty])) {
                $result[$faculty][$department] = [];
            }
            if (!array_key_exists($id, $result[$faculty][$department])) {
                $result[$faculty][$department][$id] = [
                    "Subject_short_name" => $row["Subject_short_name"],
                    "Subject_full_name" => $row["Subject_full_name"],
                    "teachers" => []
                ];
            }
            if (!in_array($teacher, $result[$faculty][$department][$id]["teachers"])) {
                $result[$faculty][$department][$id]["teachers"][] = $teacher;
            }

        }
        return $result;
    }

    public function getTeachers(?string $q): array {
        if ($q == null) {
            $stmt = $this->pdo->prepare("SELECT DISTINCT F.short_name AS Faculty_short_name, D.short_name AS Department_short_name, U.id AS User_id, U.first_name AS User_first_name, U.last_name AS User_last_name FROM Teaches T JOIN Subject S ON T.Subject_id = S.id JOIN Department D ON S.Department_id = D.id JOIN Faculty F ON D.Faculty_id = F.id JOIN User U ON T.User_id = U.id ORDER BY F.short_name, D.short_name, U.last_name, U.first_name;");
            $stmt->execute();
        }
        else {
            $stmt = $this->pdo->prepare("SELECT DISTINCT F.short_name AS Faculty_short_name, D.short_name AS Department_short_name, U.id AS User_id, U.first_name AS User_first_name, U.last_name AS User_last_name FROM Teaches T JOIN Subject S ON T.Subject_id = S.id JOIN Department D ON S.Department_id = D.id JOIN Faculty F ON D.Faculty_id = F.id JOIN User U ON T.User_id = U.id WHERE F.short_name LIKE :q OR D.short_name LIKE :q OR S.short_name LIKE :q OR S.full_name LIKE :q OR U.first_name LIKE :q OR U.last_name LIKE :q OR CONCAT(F.short_name, '/', D.short_name) LIKE :q OR CONCAT(D.short_name, '/', S.short_name) LIKE :q OR CONCAT(U.last_name, ' ', U.first_name) LIKE :q OR CONCAT(U.first_name, ' ', U.last_name) LIKE :q ORDER BY F.short_name, D.short_name, U.last_name, U.first_name;");
            $stmt->execute([":q" => "%$q%"]);
        }
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = [];
        foreach ($rows as $row) {
            $faculty = $row["Faculty_short_name"];
            $department = $row["Department_short_name"];
            $id = $row["User_id"];
            $teacher = [
                "User_first_name" => $row["User_first_name"],
                "User_last_name" => $row["User_last_name"]
            ];
            if (!array_key_exists($faculty, $result)) {
                $result[$faculty] = [];
            }
            if (!array_key_exists($department, $result[$faculty])) {
                $result[$faculty][$department] = [];
            }
            $result[$faculty][$department][$id] = $teacher;

        }
        return $result;
    }

    public function getMaterials(?string $q, Subject $subject): array {
        if ($q == null) {
            $stmt = $this->pdo->prepare("SELECT S.id AS Subject_id, F.short_name AS Faculty_short_name, D.short_name AS Department_short_name, S.short_name AS Subject_short_name FROM Subject S JOIN Department D on S.Department_id = D.id JOIN Faculty F on D.Faculty_id = F.id ORDER BY F.short_name, D.short_name, S.short_name;");
            $stmt->execute();
        }
        else {
            $stmt = $this->pdo->prepare("SELECT DISTINCT S.id AS Subject_id, F.short_name AS Faculty_short_name, D.short_name AS Department_short_name, S.short_name AS Subject_short_name FROM MaterialGroup MG JOIN Subject S ON MG.Subject_id = S.id JOIN Department D ON S.Department_id = D.id JOIN Faculty F ON D.Faculty_id = F.id JOIN User U on MG.User_id = U.id WHERE F.short_name LIKE :q OR D.short_name LIKE :q OR S.short_name LIKE :q OR S.full_name LIKE :q OR U.first_name LIKE :q OR U.last_name LIKE :q OR CONCAT(F.short_name, '/', D.short_name) LIKE :q OR CONCAT(D.short_name, '/', S.short_name) LIKE :q OR CONCAT(U.last_name, ' ', U.first_name) LIKE :q OR CONCAT(U.first_name, ' ', U.last_name) LIKE :q ORDER BY F.short_name, D.short_name, S.short_name;");
            $stmt->execute([":q" => "%$q%"]);
        }
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $materials = [];
        foreach ($rows as $row) {
            $faculty = $row["Faculty_short_name"];
            $department = $row["Department_short_name"];
            $subjectId = $row["Subject_id"];
            if (!array_key_exists($faculty, $materials)) {
                $materials[$faculty] = [];
            }
            if (!array_key_exists($department, $materials[$faculty])) {
                $materials[$faculty][$department] = [];
            }
            $materials[$faculty][$department][$subjectId] = [
                "Subject_short_name" => $row["Subject_short_name"],
                "materials" => $subject->getMaterials($subjectId)
            ];
        }
        return $materials;
    }

}