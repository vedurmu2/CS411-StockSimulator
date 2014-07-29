<?php

	function validate_username($username, $password) {
		$con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
	    if (mysqli_connect_errno($con))
	        echo "Failed to connect to MySQL: " , mysqli_connect_error();

	    $stmt = mysqli_prepare($con, "SELECT name FROM Users WHERE username=? AND password=?");
	    /* bind parameters for markers */
	    mysqli_stmt_bind_param($stmt, "ss", $username, $password);

	    /* execute query */
	    if (!mysqli_stmt_execute($stmt))
	        echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;

	    /* bind result variables */
	    mysqli_stmt_bind_result($stmt, $name);

	    /* fetch value */
	    mysqli_stmt_fetch($stmt);
	    mysqli_stmt_close($stmt);
	    return $name;
	}


?>