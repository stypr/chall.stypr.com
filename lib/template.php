<?php

	/* Template class
	This should be very much identical to view.. */

	class Template {
		protected $dir;
		// not implemented yet, no time to develop this one..
		//private function parse_template(string $data): string{}

		public function __construct(){
			if(strpos(__TEMPLATE__, __DIR__) !== 0){
				die("Template error, contact administrator.");
			}
			$this->dir = __TEMPLATE__;
		}
		public function include(string $filename): bool{
			$filename = preg_replace("/[^a-zA-Z0-9-_]/", "", $filename);
			$template = $this->dir . $filename . ".php";
			if(!file_exists($template)) return false;
			@include($template);
			return true;
		}
	}
?>