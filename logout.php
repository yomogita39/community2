<?php
require_once('./include/line_define.php');
session_start();
$_SESSION = array();

if (isset($_COOKIE["PHPSESSID"])) {
  setcookie("PHPSESSID", '', time() - 1800, '/');
}

session_destroy();
header('location:'.LOGIN_PAGE);
?>