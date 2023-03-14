<?php

namespace udm\controller;

use udm\model\Model;

class Material extends AController {

    //const POSSIBLE_ACTIONS = ["view"];

    const ACTION_KEY = "action";

    private array $data;

    public function __construct(Model $model) {
        parent::__construct($model);
        $this->data = [];
    }

    function process(): void {
        if (isset($_GET["action"])) {
            $action = $_GET["action"];
            if ($action == "view") {
                if (isset($_GET["groupid"])) {
                    $materialGroupId = $_GET["groupid"];
                    $this->data[SELF::ACTION_KEY] = "viewMaterialGroup";
                    $this->data["description"] = $this->model->material->getDescription($materialGroupId);
                    $this->data["materials"] = $this->model->material->getMaterials($materialGroupId);
                }
                elseif (isset($_GET["id"])) {
                    $this->data[SELF::ACTION_KEY] = "viewMaterial";
                    $this->data["description"] = $this->model->material->getDescription($this->model->material->getMaterialGroupId($_GET["id"]));
                    $this->data["materialUrl"] = DATA_FOLDER . "/" . $this->model->material->getDiskFileName($_GET["id"]);
                }
            }
            elseif ($action == "download") {
                if (isset($_GET["id"])) {
                    $this->handleDownload();
                }
            }
            else if ($action == "create") {
                $this->data[SELF::ACTION_KEY] = "createMaterialGroup";
                $this->data["subjects"] = $this->model->subject->getSubjects();
                $this->data["lessonTypes"] = $this->model->subject->getLessonTypes();
            }
            else if ($action == "edit") {
                if (isset($_GET["groupid"])) {
                    $materialGroupId = $_GET["groupid"];
                    $this->data[SELF::ACTION_KEY] = "editMaterialGroup";
                    $this->data["description"] = $this->model->material->getDescription($materialGroupId);
                    $this->data["materials"] = $this->model->material->getMaterials($materialGroupId);
                }
            }
            else if ($action == "authorize") {
                if (isset($_GET["groupid"])) {
                    $materialGroupId = $_GET["groupid"];
                    $this->data[SELF::ACTION_KEY] = "authorizeMaterialGroup";
                    $this->data["description"] = $this->model->material->getDescription($materialGroupId);
                    $this->data["materials"] = $this->model->material->getMaterials($materialGroupId);
                }
            }
        }
        if (isset($_GET["authaction"])) {
            $authaction = $_GET["authaction"];
            if ($authaction == "create") {
                if (isset($_POST["subjectId"], $_POST["lessonTypeId"], $_POST["description"])) {
                    $this->handleCreateMaterialGroup();
                }
                elseif (isset($_POST["fullName"], $_POST["groupId"], $_FILES["file"]) && !$_FILES["file"]["error"]) {
                    $this->handleCreateMaterial();
                }
            }
            elseif ($authaction == "delete") {
                if (isset($_GET["groupid"])) {
                    $this->model->material->deleteMaterialGroup($_GET["groupid"], $this->model->user);
                    $this->redirect("?page=admin");
                }
                elseif (isset($_GET["id"])) {
                    $materialGroupId = $this->model->material->getMaterialGroupId($_GET["id"]);
                    $this->model->material->deleteMaterial($_GET["id"], $this->model->user);
                    $this->redirect(".?page=material&action=edit&groupid=" . $materialGroupId);
                }
            }
            elseif ($authaction == "edit") {
                if (isset($_POST["groupId"], $_POST["description"])) {
                    $groupId = $_POST["groupId"];
                    if ($this->model->material->isMaterialGroupOwner($groupId)) {
                        $this->model->material->updateMaterialGroupDescription($groupId, $_POST["description"] ?: null);
                        $this->redirect(".?page=material&action=edit&groupid=" . $groupId);
                    }
                }
            }
            elseif ($authaction == "authorize") {
                if (isset($_POST["id"], $_POST["points"])) {
                    $materialGroupId = $this->model->material->getMaterialGroupId($_POST["id"]);
                    $this->model->material->authorizeMaterial($_POST["id"], $_POST["points"] ?: null, $this->model->user);
                    $this->redirect(".?page=material&action=authorize&groupid=" . $materialGroupId);
                }
            }
        }
    }

    function getData(): array {
        return $this->data;
    }

    private function handleCreateMaterialGroup(): void {
        /*if (!isset($_POST["subjectId"], $_POST["lessonTypeId"], $_POST["description"])) {
            header("Location: .?page=admin");
            die();
        }*/
        $subjectId = $_POST["subjectId"];
        $lessonTypeId = $_POST["lessonTypeId"];
        $description = $_POST["description"] ?: null;
        $id = $this->model->material->createMaterialGroup($subjectId, $lessonTypeId, $description);
        header("Location: .?page=material&action=edit&groupid=" . $id);
        die();
    }

    private function handleCreateMaterial(): void {
        /*if (!isset($_POST["fullName"], $_POST["groupId"], $_FILES["file"]) || $_FILES["file"]["error"]) {
            header("Location: .?page=material&action=create");
            die();
        }*/
        $fullName = $_POST["fullName"];
        $fileName = $_FILES["file"]["name"];
        $groupId = $_POST["groupId"];
        $this->model->material->createMaterial($fileName, $fullName, $groupId, $this->model->user);
        header("Location: .?page=material&action=edit&groupid=" . $groupId);
        die();
    }

    private function handleDownload(): void {
        $actualFilePath = DATA_FOLDER . "/" . $this->model->material->getDiskFileName($_GET["id"]);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($this->model->material->getUploadFileName($_GET["id"])) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Content-Length: ' . filesize($actualFilePath));
        flush();
        readfile($actualFilePath);
        die();
    }

}
