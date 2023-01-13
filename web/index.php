<?php

/**
 * index.php
 *
 * Do not change the code unless required.
 */

// Initializing classes
require_once "lib/init.php";
require_once "lib/function.php";
require_once "lib/debug.php";
require_once "lib/model.php";
require_once "lib/controller.php";
require_once "lib/template.php";
require_once "vendor/autoload.php";
require_once "lib/mail.php";

// Limits controller access
$allowed_controller = ["user", "challenge", "status", "wechall", "default", "badge", "board"];

try {
    $controller = isset($_GET["controller"]) ? $_GET["controller"] : "default";
    $action = isset($_GET["action"]) ? $_GET["action"] : "default";

    if (in_array($controller, $allowed_controller, true)) {
        $controller = (string)ucfirst($controller) . "Controller";
        $action = (string)ucfirst($action) . "Action";
        // load action from the controller
        $controller = new $controller;
        $controller->$action();
        exit;
    }
}catch(Exception $e){
}

// returns error if the script gets invalid inputs
return_error();
