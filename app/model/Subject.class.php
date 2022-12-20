<?php

namespace udm\model;

use PDO;

class Subject extends AModel {

    public function getCount(): int {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) AS count FROM Subject;");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return ($row) ? $row["count"] : 0;
    }

    public function getDescription(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT F.short_name AS Faculty_short_name, D.short_name AS Department_short_name, S.short_name AS Subject_short_name, S.full_name AS Subject_full_name, S.content AS Subject_content FROM Subject S JOIN Department D ON S.Department_id = D.id JOIN Faculty F ON D.Faculty_id = F.id WHERE S.id = :id;");
        $stmt->execute([":id" => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return array_merge(
            $row, ["teachers" => $this->getDescriptionTeachers($id)]
        );
    }

    private function getDescriptionTeachers(int $id): array {
        $stmt = $this->pdo->prepare("SELECT T.LessonType_id, U.first_name as User_first_name, U.last_name AS User_last_name FROM Teaches T JOIN User U ON T.User_id = U.id WHERE Subject_id = :id ORDER BY T.LessonType_id;");
        $stmt->execute([":id" => $id]);
        $rows = $stmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);
        $result = [];
        foreach (["Přednášející", "Cvičící", "Vedoucí semináře"] as $key => $value) { // TODO mag const
            if (array_key_exists($key + 1, $rows)) {
                $result[$value] = $rows[$key + 1];
            }
        }

        return $result;
    }

    public function getMaterials(int $id): array {
        $stmt = $this->pdo->prepare("SELECT MG.LessonType_id AS LessonType_id, UT.id AS UserType_id, MG.id AS MaterialGroup_id, M.id AS Material_id, U.id AS User_id, U.first_name as User_first_name, U.last_name AS User_last_name, MG.description AS MaterialGroup_description, M.full_name AS Material_full_name FROM MaterialGroup MG JOIN Material M ON MG.id = M.MaterialGroup_id JOIN User U ON MG.User_id = U.id JOIN UserType UT ON U.UserType_id = UT.id WHERE MG.Subject_id = :id ORDER BY MG.LessonType_id, UT.id, MG.id, M.id, U.id, U.last_name, U.first_name;");
        $stmt->execute([":id" => $id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = [];
        foreach ($rows as $row) {
            $lessonTypeId = $row["LessonType_id"];
            $userTypeId = $row["UserType_id"];
            $group = MATERIALGROUP_TYPE[$lessonTypeId][$userTypeId];
            $materialGroupId = $row["MaterialGroup_id"];
            if (!array_key_exists($group, $result)) {
                $result[$group] = [];
            }
            if (!array_key_exists($materialGroupId, $result[$group])) {
                $result[$group][$materialGroupId] = [
                    "User_id" => $row["User_id"],
                    "User_first_name" => $row["User_first_name"],
                    "User_last_name" => $row["User_last_name"],
                    "MaterialGroup_description" => $row["MaterialGroup_description"] ?:
                                                   DEF_MATERIALGROUP_DESCRIPTION[$lessonTypeId][$userTypeId],
                    "materials" => []
                ];
            }
            $result[$group][$materialGroupId]["materials"][$row["Material_id"]] = [
                "Material_full_name" => $row["Material_full_name"]
            ];
        }

        return $result;
    }

    public function getSubjects(): array {
        $stmt = $this->pdo->prepare("SELECT F.short_name AS Faculty_short_name, D.short_name AS Department_short_name, S.id AS Subject_id, S.short_name AS Subject_short_name FROM Subject S JOIN Department D ON S.Department_id = D.id JOIN Faculty F ON D.Faculty_id = F.id ORDER BY F.short_name, D.short_name, S.short_name;");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = [];
        foreach ($rows as $row) {
            $faculty = $row["Faculty_short_name"];
            $department = $row["Department_short_name"];
            if (!array_key_exists($faculty, $result)) {
                $result[$faculty] = [];
            }
            if (!array_key_exists($department, $result[$faculty])) {
                $result[$faculty][$department] = [];
            }
            $result[$faculty][$department][$row["Subject_id"]] = ["Subject_short_name" => $row["Subject_short_name"]];
        }
        return $result;
    }

    public function getLessonTypes(): array {
        $stmt = $this->pdo->prepare("SELECT id AS LessonType_id, full_name AS LessonType_full_name FROM LessonType;");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
