<?php

	function isValid($str) {
    		return !preg_match('/[^A-Za-z0-9.#\\-$]/', $str);
	}

	$username = $_POST['username'];
	$password = $_POST['password'];
	$repassword = $_POST['repassword'];
	$email = $_POST['email'];
	$name = $_POST['name'];

    if (strcmp($username, "") == 0 || !isValid($username)) 
        exit("Must enter valid username");

    if (strcmp($password, "") == 0 || !isValid($password))
        exit("Must enter valid password");

    if (strcmp($repassword, "") == 0)
        echo "Must enter password again";

    if (strcmp($email, "") == 0)
        echo "Must enter email";

    if (strcmp($name, "") == 0 || !isValid($username))
        echo "Must enter valid name";

	if (strcmp($password, $repassword) != 0)
		echo "Password Mismatch";

	//TODO check lengh of input make sure its shorter than 20char

	$con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
    if (mysqli_connect_errno($con))
        echo "Failed to connect to MySQL: " , mysqli_connect_error();
    
    $stmt = mysqli_prepare($con, "INSERT INTO  Users (name, userName, password, email) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $name, $username, $password, $email);

    if (!mysqli_stmt_execute($stmt)) {
        exit ("This username is taken");
    } else {
        setcookie("wolf_of_siebel_username", $username, time()+3600, "/");
        setcookie("wolf_of_siebel_password", $password, time()+3600, "/");
        setcookie("wolf_of_siebel_name", $name, time()+3600, "/");
    }
?>