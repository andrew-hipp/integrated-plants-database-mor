<?php
session_start();

function getRootUrl() {
	if ($_SERVER["SERVER_PORT"] == 443) {
		$root_url = 'https://';
	} else {
		$root_url = 'http://';
	}
	$root_url .= $_SERVER['HTTP_HOST'];
	$root_uri = $_SERVER['REQUEST_URI'];
	if (strpos($root_uri, '?') !== false)
		$root_uri = substr($root_uri, 0, strpos($root_uri, '?'));
	if (substr($root_uri, -7, 7) == '/Login/')
		$root_uri = substr($root_uri, 0, strlen($root_uri) - 6);
	else if (strpos($root_uri, '/Login/') !== false)
		$root_uri = substr($root_uri, 0, strpos($root_uri, '/Login/') + 1);
	if (substr($root_uri, -1, 1) == '/')
		$root_url .= $root_uri;
	else
		$root_url .= dirname($root_uri);
	$root_url = rtrim($root_url, '/');

	return $root_url;
}

function isNull($val) {
	if (!isset($val)) {
		return true;
	}
	if (is_object($val) && ($val == NULL)) {
		return true;
	}
	if (is_string($val) && (($val == 'NULL') || ($val == ''))) {
		return true;
	}
	return false;
}
?>
