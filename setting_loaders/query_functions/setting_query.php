<?php

    function get_email($username, $password) {
        $con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
        if (mysqli_connect_errno($con))
            echo "Failed to connect to MySQL: " , mysqli_connect_error();

        $result = mysqli_query($con, 'SELECT email FROM Users WHERE username="' . $username . '" and password = "' . $password .'"');
        if ($row = mysqli_fetch_array($result)) {
            return $row[0];
       } }
?>