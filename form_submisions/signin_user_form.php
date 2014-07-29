<?php

    include "../dashboard_loaders/query_functions/general_queries.php";

	$username = $_POST['username'];
	$password = $_POST['password'];

    if (strcmp($username, "") == 0)
        exit("Must enter username");

    if (strcmp($password, "") == 0)
        exit("Must enter password");

	$name = validate_username($username, $password);

    if (!is_null($name)) {
        setcookie("wolf_of_siebel_username", $username, time() + 3600 * 24 * 14, "/");
        setcookie("wolf_of_siebel_password", $password, time() + 3600 * 24 * 14, "/");
        setcookie("wolf_of_siebel_name", $name, time() + 3600 * 24 * 14, "/");
    } else {
        echo "Invalid username/password";
    }


?>