<?php

	$newname = $_POST['newname'];
	$password = $_POST['password'];
	$confirmpassword = $_COOKIE["wolf_of_siebel_password"];
	$username = $_COOKIE["wolf_of_siebel_username"];
	

    if (strcmp($newname, "") == 0)
        exit("Must enter New Name");

    if (strcmp($password, "") == 0)
        exit("Must enter password");

    if (strcmp($password, $confirmpassword) == 0) {
        $con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
    	if (mysqli_connect_errno($con))
        	echo "Failed to connect to MySQL: " , mysqli_connect_error();
    
    	$stmt = mysqli_prepare($con, "UPDATE Users SET name =? WHERE username =? and password=?");
   	mysqli_stmt_bind_param($stmt, "sss", $newname, $username, $password);
   	mysqli_stmt_execute($stmt);        
        setcookie("wolf_of_siebel_name", $newname, time()+3600, "/");
    } else {
        exit("Invalid password");
    }


?>