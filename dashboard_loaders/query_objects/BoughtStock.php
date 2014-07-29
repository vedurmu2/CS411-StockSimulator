<?php

require_once("Stock.php");
require_once("Portfolio.php");
require_once("Transaction.php");

class BoughtStock {

	private static $loaded_bought_stocks;

	public $brought_time;
	public $bought_price;
	public $number_of_shares;

	public $ticker;
	private $stock_object;

	private $PID;
	private $portfolio_object;

	private $all_transactions;

	public static function get_bought_stock_object($ticker, $PID) {
		if (is_null(BoughtStock::$loaded_bought_stocks))
			BoughtStock::$loaded_bought_stocks = array();

		$key_string = ((string)($ticker)) . ((string)($PID));
		if (isset(BoughtStock::$loaded_bought_stocks[$key_string]))
			return BoughtStock::$loaded_bought_stocks[$key_string];

		$new_bought = new BoughtStock(null, null, null);
		$new_bought->PID = $PID;
		$new_bought->ticker = $ticker;
		if (isset($_SESSION['con'])) $con = $_SESSION['con']; else $con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
        if (mysqli_connect_errno($con))
            echo "Failed to connect to MySQL: " , mysqli_connect_error();
        
        $result = mysqli_query($con, 'SELECT * FROM BoughtStock WHERE PID="' . $PID . '" AND ticker="' . $ticker . '" ORDER BY boughtTime ASC LIMIT 1');
        if (!$row = mysqli_fetch_array($result)) 
            return null;
        
        $new_bought->bought_time = $row['boughtTime'];
        $new_bought->bought_price = $row['boughtPrice'];
        $new_bought->number_of_shares = $row['numShares'];

        BoughtStock::$loaded_bought_stocks[$key_string] = $new_bought;

        return $new_bought;
	}

	public function __construct($number_of_shares, $ticker, $PID) {
		if (is_null($number_of_shares) || is_null($ticker) || is_null($PID))
			return;

		$this->stock_object = Stock::get_stock_object($ticker);
		$this->portfolio_object = Portfolio::get_portfolio_object($PID, null, null);
		$total = $number_of_shares * $this->stock_object->get_price();
		if ($total > $this->portfolio_object->money_left) {
			echo "Not enough funds";
			return;
		}

		if (isset($_SESSION['con'])) $con = $_SESSION['con']; else $con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
        if (mysqli_connect_errno($con))
            echo "Failed to connect to MySQL: " , mysqli_connect_error();

		$new_money = $this->portfolio_object->money_left - $total;
		mysqli_query($con, "UPDATE Portfolio SET moneyLeft=\"" . $new_money . "\" WHERE PID=\"" . $PID . "\"");

		$this->ticker = $ticker;
		$this->PID = $PID;

        $stmt = mysqli_prepare($con, "INSERT INTO  BoughtStock (boughtPrice, numShares, ticker, PID) VALUES (?, ?, ?, ?)");
        $price = $this->stock_object->get_price();
        mysqli_stmt_bind_param($stmt, "disi", $price, $number_of_shares, $ticker, $PID);
        if (!mysqli_stmt_execute($stmt)) {
            echo "TGIS Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        	return null;
        }

        new Transaction($this->PID, "BUY", $this->ticker, $number_of_shares);
        $this->revalidate($con);
        $key_string = ((string)($ticker)) . ((string)($PID));
        BoughtStock::$loaded_bought_stocks[$key_string] = $this;
	}

	public function revalidate($con) {
		$result = mysqli_query($con, 'SELECT * FROM BoughtStock WHERE PID="' . $this->PID . '" AND ticker="' . $this->ticker . '" ORDER BY boughtTime ASC LIMIT 1');
        if (!$row = mysqli_fetch_array($result)) 
            return null;
        
        $this->bought_time = $row['boughtTime'];
        $this->bought_price = $row['boughtPrice'];
        $this->number_of_shares = $row['numShares'];
        return true;
	}

	public function sell_stock($number_of_shares) {
		$this->get_portfolio_object();
		if (isset($_SESSION['con'])) $con = $_SESSION['con']; else $con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
        if (mysqli_connect_errno($con))
            echo "Failed to connect to MySQL: " , mysqli_connect_error();

		mysqli_query($con, "START TRANSACTION");

		$r1 = (!is_null($this->revalidate($con)) && $number_of_shares <= $this->number_of_shares);

		if (!$r1)
			echo "Transaction error: You only own " . $this->number_of_shares . " shares";
		
		$new_num = $this->number_of_shares - $number_of_shares;
		$r2 = false;
		if ($new_num == 0)
			$r2 = mysqli_query($con, "DELETE FROM BoughtStock WHERE ticker=\"" . $this->ticker . "\" AND PID=\"" . $this->PID . "\"");
		else 
			$r2 = mysqli_query($con, "UPDATE BoughtStock SET numShares=\"" . $new_num . "\" WHERE ticker=\"" . $this->ticker . "\" AND PID=\"" . $this->PID . "\"");

    	$new_money = $this->get_portfolio_object()->money_left + $number_of_shares * $this->bought_price;
    	$r3 = mysqli_query($con, "UPDATE Portfolio SET moneyLeft=\"" . $new_money . "\" WHERE PID=\"" . $this->PID . "\"");
    	if ($r1 and $r2 and $r3) {
    		if ($new_money == 0)
    			$this->get_portfolio_object()->remove_bought_stock($this->ticker);

    		$this->get_portfolio_object()->money_left = $new_money;
    		$this->number_of_shares = $new_num;
    		mysqli_query($con, "COMMIT");
    		new Transaction($this->PID, "SELL", $this->ticker, $number_of_shares);
    	} else {
    		mysqli_query($con, "ROLLBACK");
    	}

	}

	public function get_transactions() { 
		if (is_null($this->all_transactions)) {
			$this->all_transactions = Transaction::get_all_transactions($this->PID);
		}
		return $this->all_transactions;
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