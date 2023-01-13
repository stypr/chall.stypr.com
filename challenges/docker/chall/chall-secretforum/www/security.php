<?php
function secureInt($i) {
	if (preg_match('/^([0-9]+)$/', $i))
		return (int) $i;
	else
		return (1);
}

function secureContent($content) {
	return str_replace(array(',', '-', '#'), '', htmlspecialchars(addslashes($content)));
}

function securePassword($pass) {
	return sha1($pass);
}

function validNick($nick) {
	return (preg_match('/^([a-zA-Z0-9\-\._@\!=\[\]]{3,15})$/', $nick));
}

function validPassword($pass) {
	return (preg_match('/^(.{8,64})$/', $pass));
}
?>