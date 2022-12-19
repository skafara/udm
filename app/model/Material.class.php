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

    public function create(): int {
        return -1;
    }

}
