<?php

namespace udm;

use Exception;
use PDO;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use udm\controller\Auth;
use udm\model\Model;

class Udm {

    private PDO $pdo;
    private Model $model;
    private Auth $auth;

    private Environment $twig;

    public function __construct() {
        $this->pdo = new PDO(PDO_DSN, DB_USER, DB_PASS);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->model = new Model($this->pdo);
        $this->auth = new Auth($this->model);

        $this->twig = new Environment(new FilesystemLoader("view")); // mag const

        session_start();
    }

    public function run(): void {
        $page = $this->determinePage();

        $this->auth->process();

        $template = $page[P_TEMPLATE];
        $controller = new $page[P_CONTROLLER]($this->model);

        try {
            $controller->process();
            echo $this->twig->render(
                $template,
                array_merge($this->auth->getData(), $controller->getData())
            );
        } catch (Exception) {
            http_response_code(500);
            exit();
        }
    }

    private function determinePage(): array {
        if (array_key_exists(P_KEY, $_GET)) {
            if (array_key_exists($_GET[P_KEY], PAGES)) {
                return PAGES[$_GET[P_KEY]];
            }
        }
        return PAGES[DEF_PAGE];
    }

}
