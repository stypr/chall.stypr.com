<?php

/*
 * lib/controller.php
 *
 * Defines a base class for controllers and loads controllers.
 * You should better check out the controllers subdirectory.
 */

// PHPMailer: should be installed by composer!
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/*
 * Base Class for Controller
 */
class Controller
{
    protected $db;

    public function auth_filter($str): string
    {
        // escapes strings for the authentication
        return $this->db->filter($str, 'auth');
    }

    public function is_auth()
    {
        // check if authenticated
        $session = $_SESSION['username'];
        $session .= $this->auth_filter($_SERVER['REMOTE_ADDR']);
        if (secure_hash($session) == $_SESSION['session']) {
            return true;
        }
        return false;
    }

    public function output($data)
    {
        // return result as json
        header("Content-Type: text/json; charset=utf-8");
        echo @json_encode($data);
        exit;
    }

    public function DefaultAction()
    {
        // return empty output for accessing the default action
        $this->output(false);
    }

    public function __construct()
    {
        // get MySQL query variable
        global $query;
        $this->db = $query;
    }
}

// Load Controller
$controller_dir = "lib/controllers/";
$controller_list = array_diff(scandir($controller_dir), ['.', '..']);

foreach ($controller_list as $controller) {
    if (substr($controller, -strlen('.php')) === '.php') {
        include_once $controller_dir . DIRECTORY_SEPARATOR . $controller;
    }
}
