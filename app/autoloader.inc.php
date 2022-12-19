<?php

const BASE_NAMESPACE = "udm";
//const BASE_APP_DIR = "app";

const FILE_EXTENSIONS = [".class.php", ".interface.php"];

spl_autoload_register(
    function ($className) {
        $className = str_replace(BASE_NAMESPACE . "\\", "", $className);
        $className = str_replace("\\", "/", $className);
        foreach (FILE_EXTENSIONS as $ext) {
            if (is_file($className . $ext)) {
                require_once $className . $ext;
                break;
            }
        }
    }
);
