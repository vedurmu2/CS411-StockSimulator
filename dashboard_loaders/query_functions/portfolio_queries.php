<?php

    function get_stocks_portfolio_for_group($username, $GID) {
        $con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
        if (mysqli_connect_errno($con))
            echo "Failed to connect to MySQL: " , mysqli_connect_error();
        
        $result = mysqli_query($con, 'SELECT * FROM Portfolio WHERE username="' . $username . '" AND GID="' . $GID . '"');
        if (!$row = mysqli_fetch_array($result)) {
            return null;
        }
        
        $result = mysqli_query($con, 'SELECT * FROM BoughtStock NATURAL JOIN Stock WHERE PID="' . $row['PID'] . '"');
        $retval = array();
        while ($row = mysqli_fetch_array($result)) {
            $retval[] = $row;
        }
        return $retval;
    }

    function create_portfolio($username, $portfolio_name, $group_object) {
        $con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
        if (mysqli_connect_errno($con))
            echo "Failed to connect to MySQL: " , mysqli_connect_error();

        $stmt = mysqli_prepare($con, "INSERT INTO  Portfolio (GID, moneyLeft, pName, username) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iiss", $group_object['GID'], $group_object['startMoney'], $portfolio_name, $username);
        if (!mysqli_stmt_execute($stmt)) {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        } 
    }

    function buy_stock($ticker, $numshares, $portfolio_object) {
        $param = array();
        $param[] = $ticker;
        $stock_price = get_stock_price_info($param);
        if ($numshares * $stock_price > $portfolio_object['moneyLeft'])
            return;

        $con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
        if (mysqli_connect_errno($con))
            echo "Failed to connect to MySQL: " , mysqli_connect_error();
        
        $result = mysqli_query($con, 'SELECT * FROM Stocks WHERE ticker="' . $ticker . '"');
        $row = mysqli_fetch_array($result);

        $stmt = mysqli_prepare($con, "INSERT INTO  BoughtStock (boughtTime, boughtPrice, numShares, ticker, pName) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "siiss", $time, $stock_price, $numshares, $ticker, $portfolio_object['pName']);
        if (!mysqli_stmt_execute($stmt)) {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        } 
    }

    function sell_stock($ticker, $numshares, $portfolio_object) {

    }

?>