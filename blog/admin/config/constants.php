<?php
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

if (!defined('ROOT_URL')) define('ROOT_URL', 'https://1507.csasza.hu/');
if (!defined('DB_HOST')) define('DB_HOST', '127.0.0.1:3306');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '#Csanyekok58+');
if (!defined('DB_NAME')) define('DB_NAME', 'blog');