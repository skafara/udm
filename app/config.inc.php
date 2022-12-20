<?php

// -- PAGES --

const P_KEY = "page";

const P_TEMPLATE = "template";
const P_CONTROLLER = "controller";

const DEF_PAGE = "home";

const PAGES = [
    DEF_PAGE => [
        P_TEMPLATE => "home.twig",
        P_CONTROLLER => \udm\controller\Home::class
    ],
    "info" => [
        P_TEMPLATE => "info.twig",
        P_CONTROLLER => \udm\controller\Info::class
    ],
    "subject" => [
        P_TEMPLATE => "subject.twig",
        P_CONTROLLER => \udm\controller\Subject::class
    ],
    "search" => [
        P_TEMPLATE => "search.twig",
        P_CONTROLLER => \udm\controller\Search::class
    ],
    "material" => [
        P_TEMPLATE => "material.twig",
        P_CONTROLLER => \udm\controller\Material::class
    ],
    "admin" => [
        P_TEMPLATE => "admin.twig",
        P_CONTROLLER => \udm\controller\Admin::class
    ],
    "user" => [
        P_TEMPLATE => "user.twig",
        P_CONTROLLER => \udm\controller\User::class
    ]
];

// -- END PAGES --

// -- DATABASE --

const DB_HOST = "localhost";
const DB_USER = "root";
const DB_PASS = "";
const DB_NAME = "udm";

const PDO_DSN = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;

// -- END DATABASE --

// -- DATABASE CONSTANTS --

const ID_USERTYPE_STUDENT = 1;
const ID_USERTYPE_TEACHER = 2;
const ID_USERTYPE_ADMIN = 3;

const ID_LESSONTYPE_LECTURE = 1;
const ID_LESSONTYPE_EXERCISE = 2;
const ID_LESSONTYPE_SEMINAR = 3;

// -- END DATABASE CONSTANTS --

// -- MATERIALS RELATED DESCRIPTIONS --

const MATERIALGROUP_TYPE = [
    ID_LESSONTYPE_LECTURE => [
        ID_USERTYPE_TEACHER => "Přednášky - vyučující",
        ID_USERTYPE_STUDENT => "Přednášky - studentské"
    ],
    ID_LESSONTYPE_EXERCISE => [
        ID_USERTYPE_TEACHER => "Cvičení - vyučující",
        ID_USERTYPE_STUDENT => "Cvičení - studentské"
    ],
    ID_LESSONTYPE_SEMINAR => [
        ID_USERTYPE_TEACHER => "Semináře - vyučující",
        ID_USERTYPE_STUDENT => "Semináře - studentské"
    ]
];

const DEF_MATERIALGROUP_DESCRIPTION = [
    ID_LESSONTYPE_LECTURE => [
        ID_USERTYPE_TEACHER => "Podklady k přednáškám",
        ID_USERTYPE_STUDENT => "Výpisky z přednášek"
    ],
    ID_LESSONTYPE_EXERCISE => [
        ID_USERTYPE_TEACHER => "Podklady k cvičením",
        ID_USERTYPE_STUDENT => "Výpisky z cvičení"
    ],
    ID_LESSONTYPE_SEMINAR => [
        ID_USERTYPE_TEACHER => "Podklady k seminářům",
        ID_USERTYPE_STUDENT => "Výpisky ze seminářů"
    ]
];

// -- END MATERIALS RELATED DESCRIPTIONS --

// -- DATA --

const DATA_FOLDER = "data";

// -- END DATA --
