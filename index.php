<?php
include('./classes/dbh.php');
include('./classes/login.inc.php');

if (Login::isLoggedin()) {
	echo "Logged in";
	echo Login::isLoggedin();
} else {
	echo "Not Logged in";
}
?>