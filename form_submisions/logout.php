<?php 
	setcookie("wolf_of_siebel_username", "", time() - 3600 * 24 * 14, "/");
    setcookie("wolf_of_siebel_password", "", time() - 3600 * 24 * 14, "/");
    setcookie("wolf_of_siebel_name", "", time() - 3600 * 24 * 14, "/");

    header("Location: /~wolfofsiebel");
	die();
?>