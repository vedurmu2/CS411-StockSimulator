<?php

    function create_group($owner, $group_name, $startMoney, $password) {
        $con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
        if (mysqli_connect_errno($con))
            echo "Failed to connect to MySQL: " , mysqli_connect_error();

        $stmt = mysqli_prepare($con, "INSERT INTO  Groups (groupName, owner, startMoney) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssi", $group_name, $username, $startMoney);
        if (!mysqli_stmt_execute($stmt)) {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        } 

        $result = mysqli_query($con, 'SELECT * FROM Groups WHERE username="' . $username . '" AND groupName="' . $group_name . '" ORDER GID BY ASC');
        if ($row = mysqli_fetch_array($result)) {
            join_group($row['GID'], $username);
        } else {
            //TODO ERROR
        }
    }

    function get_group_object($GID) {
        $con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
        if (mysqli_connect_errno($con))
            echo "Failed to connect to MySQL: " , mysqli_connect_error();

        $result = mysqli_query($con, 'SELECT * FROM Groups WHERE GID="' . $GID . '"');
        if ($row = mysqli_fetch_array($result)) {
            return $row;
        }
    }

    function join_group($GID, $username, $password) { 
        //TODO GET GROUP OBJECT MAKE SURE IT EXISTS
        $con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
        if (mysqli_connect_errno($con)) {
            echo "Failed to connect to MySQL: " , mysqli_connect_error();
            return false;
        }

        $stmt = mysqli_prepare($con, "INSERT INTO  PartOf (GID, username) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "is", $GID, $username);
        if (!mysqli_stmt_execute($stmt)) {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            return false;
        } 
        return true;
    }

    function get_all_joinable_groups($username) {
        $con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
        if (mysqli_connect_errno($con))
            echo "Failed to connect to MySQL: " , mysqli_connect_error();
        
        $result = mysqli_query($con, 'SELECT DISTINCT Groups.GID as GID, owner, startMoney, groupName FROM (Groups LEFT OUTER JOIN (SELECT * FROM PartOf WHERE username="' . $username . '") as temp1 on Groups.GID=temp1.GID) WHERE temp1.GID IS NULL');
        $return_val = array();
        while($row = mysqli_fetch_array($result)) {
            $return_val[] = $row;
        }
        return $return_val;
    }

    function get_group_list_for_user($username) {
        $con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
        if (mysqli_connect_errno($con))
            echo "Failed to connect to MySQL: " , mysqli_connect_error();
        
        $result = mysqli_query($con, 'SELECT * FROM Groups NATURAL JOIN PartOf WHERE username="' . $username . '"');
        $return_val = array();
        while($row = mysqli_fetch_array($result)) {
            $return_val[] = $row;
        }
        return $return_val;
    }

?>