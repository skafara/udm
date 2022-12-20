<?php

namespace udm\model;

use PDO;

class Material extends AModel {

    public function getCount(): int {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) AS count FROM Material;");
        if (!$stmt || !$stmt->execute()) {
            return 0;
        }
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return ($row) ? $row["count"] : 0;
    }

    public function getDescription(int $materialGroupId): ?array {
        $stmt = $this->pdo->prepare("SELECT MG.id AS MaterialGroup_id, MG.LessonType_id AS LessonType_id, LT.full_name AS LessonType_full_name, U.UserType_id AS UserType_id, F.short_name AS Faculty_short_name, D.short_name AS Department_short_name, S.short_name AS Subject_short_name, MG.description AS MaterialGroup_description, U.first_name AS User_first_name, U.last_name AS User_last_name FROM MaterialGroup MG JOIN LessonType LT on MG.LessonType_id = LT.id JOIN Subject S ON MG.Subject_id = S.id JOIN User U on MG.User_id = U.id JOIN Department D ON S.Department_id = D.id JOIN Faculty F ON D.Faculty_id = F.id WHERE MG.id = :id;");
        $stmt->execute([":id" => $materialGroupId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        if ($row["MaterialGroup_description"] == null) {
            $row["MaterialGroup_description"] = DEF_MATERIALGROUP_DESCRIPTION[$row["LessonType_id"]][$row["UserType_id"]];
        }
        return $row;
    }

    public function getMaterials(int $materialGroupId): ?array {
        $stmt = $this->pdo->prepare("SELECT id AS Material_id, full_name AS Material_full_name, upload_filename AS Material_upload_filename, passed AS Material_passed FROM Material WHERE MaterialGroup_id = :id;");
        $stmt->execute([":id" => $materialGroupId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = [];
        foreach ($rows as $row) {
            $result[$row["Material_id"]] = [
                "Material_full_name" => $row["Material_full_name"],
                "Material_upload_filename" => $row["Material_upload_filename"],
                "Material_passed" => $row["Material_passed"]
            ];
        }
        return $result;
    }

    public function getPublished(User $user): array {
        $stmt = $this->pdo->prepare("SELECT S.id AS Subject_id, MG.LessonType_id AS LessonType_id, D.short_name AS Department_short_name, S.short_name AS Subject_short_name, MG.id AS MaterialGroup_id, MG.description AS MaterialGroup_description FROM MaterialGroup MG JOIN User U ON MG.User_id = U.id JOIN Subject S ON MG.Subject_id = S.id JOIN Department D ON S.Department_id = D.id WHERE U.id = :id ORDER BY D.short_name, S.short_name;");
        $stmt->execute([":id" => $_SESSION["id"]]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $userTypeId = $user->getUserTypeId();
        $materials = [];
        foreach ($rows as $row) {
            $subjectId = $row["Subject_id"];
            $department = $row["Department_short_name"];
            $materialGroupId = $row["MaterialGroup_id"];
            if (!array_key_exists($department, $materials)) {
                $materials[$department] = [];
            }
            if (!array_key_exists($subjectId, $materials[$department])) {
                $materials[$department][$subjectId] = [
                    "Subject_short_name" => $row["Subject_short_name"],
                    "materialGroups" => []
                ];
            }
            $materials[$department][$subjectId]["materials"][$materialGroupId] = [
                "MaterialGroup_description" => $row["MaterialGroup_description"] ?? DEF_MATERIALGROUP_DESCRIPTION[$row["LessonType_id"]][$userTypeId],
                "materials" => $this->getMaterials($materialGroupId)
            ];
        }
        return $materials;
    }

    public function getToAuthorize(User $user): array {
        $stmt = $this->pdo->prepare("SELECT D.short_name AS Department_short_name, S.short_name AS Subject_short_name, MG.id AS MaterialGroup_id, MG.LessonType_id AS LessonType_id, MG.description AS MaterialGroup_description, COUNT(*) AS count FROM MaterialGroup MG JOIN Material M ON MG.id = M.MaterialGroup_id JOIN User U ON MG.User_id = U.id JOIN Subject S ON MG.Subject_id = S.id JOIN Department D ON S.Department_id = D.id WHERE M.passed = 0 AND MG.Subject_id IN (SELECT Subject_id FROM Teaches WHERE User_id = :User_id) GROUP BY MG.id ORDER BY D.short_name, S.short_name;");
        $stmt->execute([":User_id" => $_SESSION["id"]]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $userTypeId = $user->getUserTypeId();
        foreach ($rows as $row) {
            if ($row["MaterialGroup_description"] == null) {
                $row["MaterialGroup_description"] = DEF_MATERIALGROUP_DESCRIPTION[$row["LessonType_id"]][$row[$userTypeId]];
            }
        }
        return $rows;
    }

    public function createMaterialGroup(int $subjectId, int $lessonTypeId, ?string $description): int {
        $stmt = $this->pdo->prepare("INSERT INTO MaterialGroup (description, User_id, Subject_id, LessonType_id) VALUES (:description, :User_id, :Subject_id, :LessonType_id);");
        $stmt->execute([
            ":description" => $description,
            ":User_id" => $_SESSION["id"],
            ":Subject_id" => $subjectId,
            ":LessonType_id" => $lessonTypeId
        ]);
        return $this->pdo->lastInsertId();
    }

    public function createMaterial(string $upload_filename, string $full_name, int $materialGroupId, User $user): int { // na zaklade user passed
        do {
            $disk_filename = uniqid();
        } while (file_exists(DATA_FOLDER . "/" . $disk_filename));
        $stmt = $this->pdo->prepare("INSERT INTO Material (upload_filename, disk_filename, full_name, passed, MaterialGroup_id) VALUES (:upload_filename, :disk_filename, :full_name, 0, :MaterialGroup_id);");
        echo $stmt->execute([
            ":upload_filename" => $upload_filename,
            ":disk_filename" => $disk_filename,
            ":full_name" => $full_name,
            ":MaterialGroup_id" => $materialGroupId
        ]);
        $id = $this->pdo->lastInsertId();
        if ($user->getUserTypeId() == ID_USERTYPE_TEACHER) {
            $this->authorizeMaterial($id, null, $user);
        }
        move_uploaded_file($_FILES["file"]["tmp_name"], DATA_FOLDER . "/" . $disk_filename);
        return $id;
    }

    public function getMaterialGroupId(int $materialId): ?int {
        $stmt = $this->pdo->prepare("SELECT MaterialGroup_id FROM Material WHERE id = :id;");
        $stmt->execute([":id" => $materialId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        return $row["MaterialGroup_id"];
    }

    public function isMaterialGroupOwner(int $id): bool {
        $stmt = $this->pdo->prepare("SELECT id FROM MaterialGroup WHERE id = :id AND User_id = :User_id;");
        $stmt->execute([":id" => $id, ":User_id" => $_SESSION["id"]]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            return true;
        }
        return false;
    }

    public function deleteMaterialGroup(int $id, User $user): void {
        if ($this->isMaterialGroupOwner($id) || $user->getUserTypeId() == ID_USERTYPE_ADMIN || $user->teachesSubject($this->getMaterialGroupSubjectId($id))) {
            $stmt = $this->pdo->prepare("SELECT id FROM Material WHERE MaterialGroup_id = :MaterialGroup_id;");
            $stmt->execute([":MaterialGroup_id" => $id]);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $materialId = $row["id"];
                $this->deleteMaterial($materialId, $user);
            }
            $stmt = $this->pdo->prepare("DELETE FROM MaterialGroup WHERE id = :id;");
            $stmt->execute([":id" => $id]);
        }
    }

    public function deleteMaterial(int $id, User $user): void {
        if ($this->isMaterialGroupOwner($this->getMaterialGroupId($id)) || $user->getUserTypeId() == ID_USERTYPE_ADMIN || $user->teachesSubject($this->getMaterialGroupSubjectId($this->getMaterialGroupId($id)))) {
            unlink(DATA_FOLDER . "/" . $this->getDiskFileName($id));
            $stmt = $this->pdo->prepare("DELETE FROM Material WHERE id = :id;");
            $stmt->execute([":id" => $id]);
        }
    }

    public function authorizeMaterial(int $id, ?int $points, User $user): void {
        if (!$user->teachesSubject($this->getMaterialGroupSubjectId($this->getMaterialGroupId($id)))) {
            return;
        }
        $stmt = $this->pdo->prepare("UPDATE Material SET passed = 1, points = :points WHERE id = :id;");
        $stmt->execute([":points" => $points, ":id" => $id]);
    }

    private function getMaterialGroupSubjectId(int $materialGroupId): int {
        $stmt = $this->pdo->prepare("SELECT Subject_id FROM MaterialGroup WHERE id = :id;");
        $stmt->execute([":id" => $materialGroupId]);
        return $stmt->fetch(PDO::FETCH_ASSOC)["Subject_id"];
    }

    public function getDiskFileName(int $id): string {
        $stmt = $this->pdo->prepare("SELECT disk_filename FROM Material WHERE id = :id;");
        $stmt->execute([":id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC)["disk_filename"];
    }

    public function getUploadFileName(int $id): string {
        $stmt = $this->pdo->prepare("SELECT upload_filename FROM Material WHERE id = :id;");
        $stmt->execute([":id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC)["upload_filename"];
    }

    public function updateMaterialGroupDescription(int $materialGroupId, ?string $description): void {
        $stmt = $this->pdo->prepare("UPDATE MaterialGroup SET description = :description WHERE id = :id;");
        $stmt->execute([":description" => $description, ":id" => $materialGroupId]);
    }

    public function getMaterialGroupIds(int $userId) {
        $stmt = $this->pdo->prepare("SELECT id AS MaterialGroup_id FROM MaterialGroup WHERE User_id = :User_id;");
        $stmt->execute([":User_id" => $userId]);
        $result = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $result[] = $row["MaterialGroup_id"];
        }
        return $result;
    }

    public function exists(int $id): bool {
        $stmt = $this->pdo->prepare("SELECT id FROM Material WHERE id = :id;");
        $stmt->execute([":id" => $id]);
        return !!$stmt->fetch(PDO::FETCH_ASSOC);
    }

}
