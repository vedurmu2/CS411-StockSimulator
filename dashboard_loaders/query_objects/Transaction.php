<?php

require_once("Stock.php");
require_once("Portfolio.php");

class Transaction {

	public $date;
	public $action;
	public $profit;
	public $number_of_shares;

	public $ticker;
	private $stock_object;

	private $PID;
	private $portfolio_object;

	public static function get_all_transactions($PID) {
		$con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
        if (mysqli_connect_errno($con))
            echo "Failed to connect to MySQL: " , mysqli_connect_error();
        
        $result = mysqli_query($con, 'SELECT * FROM Transaction WHERE PID="' . $PID . '" ORDER BY date ASC');
        $ret = array();
        while  ($row = mysqli_fetch_array($result)) {
            $new_trans = new Transaction(null, null, null, null);
        
	        $new_trans->date = $row['date'];
	        $new_trans->action = $row['action'];
	        $new_trans->profit = $row['profit'];
	        $new_trans->PID = $row['PID'];
	        $new_trans->PID = $row['ticker'];
	        $new_trans->number_of_shares = $row['numShares'];
	        $ret[] = $new_trans;
		}

        return $ret;
	}

	public function __construct($PID, $action, $ticker, $number_of_shares) {
		if (is_null($PID) || is_null($action) || is_null($ticker) || is_null($number_of_shares))
			return;

		$con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
        if (mysqli_connect_errno($con))
            echo "Failed to connect to MySQL: " , mysqli_connect_error();

        $this->PID = $PID;
        $profit = $this->get_portfolio_object()->get_profit();
        $stmt = mysqli_prepare($con, "INSERT INTO  Transaction (PID, action, profit, ticker, numShares) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "isdsi", $PID, $action, $profit, $ticker, $number_of_shares);
        if (!mysqli_stmt_execute($stmt)) {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        	return null;
        }

        $this->action = $action;
        $this->profit = $profit;
        $this->number_of_shares = $number_of_shares;
        $this->ticker = $ticker;
	}
	
	public function get_portfolio_object() {
		if (is_null($this->portfolio_object)) {
			$this->portfolio_object = Portfolio::get_portfolio_object($this->PID, null, null);
		}
		return $this->portfolio_object;
	}

	public function get_stock_object() { 
		if (is_null($this->stock_object)) {
			$this->stock_object = Stock::get_stock_object($this->ticker);
		}
		return $this->stock_object;
	}

}

?>