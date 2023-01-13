<?php

/**
 * lib/template.php
 *
 * Templating Engine, No need to add up anything at this stage
 */

class Template
{
    protected $dir;

    public function __construct()
    {
        if (strpos(__TEMPLATE__, __DIR__) !== 0) {
            die("Template Error");
        }
        $this->dir = __TEMPLATE__;
    }

    public function include(string $filename): bool
    {
        // Safely include file
        $filename = preg_replace("/[^a-zA-Z0-9-_]/", "", $filename);
        $template = $this->dir . $filename . ".php";
        if (!file_exists($template)) {
            return false;
        }
        @include $template;
        return true;
    }
}
