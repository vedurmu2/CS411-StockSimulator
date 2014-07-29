<?php

require_once("query_objects/Portfolio.php");
require_once("query_objects/User.php");

function show_portfolio_list() {
  $portfolios = User::get_user_object($_COOKIE["wolf_of_siebel_username"])->get_portfolios();
  foreach ($portfolios as $portfolio) {
    $quick_view_code = "<a href=\"?page=MyPortfolios&showmore" . $portfolio->PID . "\"> Portfolio Quick View </a>";

    if (isset($_GET["showmore" . $portfolio->PID])) 
     $quick_view_code = "<a href=\"?page=MyPortfolios\" > Hide </a>";

    echo "<tr>";
    echo "  <td> <a href=\"?page=Portfolio&PID=" . $portfolio->PID . "\">" . $portfolio->portfolio_name . "</a> </td>\n";
    echo "  <td> <a href=\"?page=Group&GID=" . $portfolio->get_group_object()->GID . "\" >" . $portfolio->get_group_object()->group_name . "</a> </td>\n";
    echo "  <td>" . count($portfolio->get_all_transactions()) . "</td>\n";
    echo "  <td> ~ " . intval($portfolio->get_networth()) .  "</td>\n";
    echo "  <td> ~ " . intval($portfolio->money_left) . "</td>\n";
    echo "  <td> ~ " . intval($portfolio->get_profit()) . "</td>\n";
    echo "  <td> " . $quick_view_code . " </td>";
    echo "</tr>";

    if (isset($_GET["showmore" . $portfolio->PID])) {
      echo "<tr> <td colspan=\"6\" style=\"
      padding-left: 30px;
      padding-right: 30px;
      padding-bottom: 20px;
      \">";
        show_portfolio($portfolio);
      echo "</td> </tr>";
    }
  }
} 

function modaljs() {
    echo '<script type="text/javascript">
                  $(document).ready(function(){
              $("#SellStock").submit(function(event){
                $.post($("#SellStockForm").attr("action"), $("#SellStockForm").serialize(), function(data){ 
                    if (data != "") {
                      $("#errormessegesell").html(data);
                    } else {
                      $("#SellStock").modal("hide");
                      window.location.reload()
                    }

                  }) .fail(function() {
                  });
                  //$("#SignUp").modal("hide");
                  
                  event.preventDefault();
              });
          });      
      </script>';

    echo "<script type=\"text/javascript\">
       function modalType(name, ticker, price, boughtPrice, PID) { 
          $(\"#modalPID\")[0].value = PID;
          $(\"#modalticker\")[0].value = ticker;
          $(\"#stockname\").html(\"Stock Name:\" + name);
          $(\"#stockticker\").html(\"Stock Ticker:\" + ticker);
          $(\"#stockprice\").html(\"Stock Price:\" + price);
          $(\"#stockchange\").html(\"Stock Change:\" + (price - boughtPrice));
       }
    </script>";
  }

function sell_stock_modal() {
  echo '<div class="modal fade" id="SellStock" tabindex="-1" role="dialog" aria-labelledby="purchaseLabel" aria-hidden="true">
        <div class="modal-dialog">  
            <form id="SellStockForm" action="/~wolfofsiebel/form_submisions/sell_stock_form.php" method="post">
              <input type="hidden" id="modalPID" name="PID" value="0">
              <input type="hidden" id="modalticker" name="ticker" value="0">
              <div class="modal-content">
                <div class="modal-header">
                  <h3> Sell Stock! </h3>
                  <h5 id="stockname"> </h3>
                  <h5 id="stockticker"> </h3>
                  <h5 id="stockprice"> </h3>
                  <h5 id="stockchange"> </h3>
                  <h5 id="errormessegesell"> </h3>
                </div>
                <div class="modal-body" style= "padding-top: 0px;" >
                  <div class="divDialogElements">
                    <h4>Number of Shares</h4>
                    <input class="xlarge" id="xlInput" name="numShares" value="" type="text">
                  </div>
                </div>
                <div class="modal-footer">
                  <a href="#" class="btn btn-primary" data-dismiss="modal">Cancel</a>
                  <input type="submit" id="submit" class="btn btn-primary" value="Submit"><br>
                </div>
              </div>
          </form>
        </div>
      </div>';
}

function show_portfolio($portfolio) {
  modaljs();
  sell_stock_modal();

  echo '<table class="table table-striped">
  <thead>
  <tr>
  <th>Ticker</th>
  <th>Full Name</th>
  <th>Industry</th>
  <th>Market</th>
  <th>Bought Time</th>
  <th>Bought Price</th>
  <th>Current Price</th>
  <th># Shares</th>
  <th>% of Portfolio</th>
  </tr>
  </thead>
  <tbody>';

  $mLeft=$portfolio->money_left;
  $mStart=$portfolio->get_group_object()->start_money;
  $mInvested=0;

  $username = $_COOKIE["wolf_of_siebel_username"];
  $stocks_in_port = $portfolio->get_bought_stocks();
  foreach ($stocks_in_port as $stock) 
    $mInvested += $stock->bought_price * $stock->number_of_shares;

$weightedCap = 0;
  foreach ($stocks_in_port as $stock) {
    echo "<tr>";
    echo "  <td>" . $stock->ticker . "</td>\n";
    echo "  <td>" . $stock->get_stock_object()->full_name . "</td>\n";
    echo "  <td>" . $stock->get_stock_object()->sector . "</td>\n";
    echo "  <td>" . $stock->get_stock_object()->exchange . "</td>\n";
    echo "  <td>" . $stock->bought_time . "</td>\n";
    echo "  <td>" . $stock->bought_price . "</td>\n";
    echo "  <td>" . $stock->get_stock_object()->get_price() . "</td>\n";
    echo "  <td>" . $stock->number_of_shares. "</td>\n";

    $percentInvested=(($stock->bought_price*$stock->number_of_shares)/$mInvested);
    $percentInvested=number_format($percentInvested,2,'.','');
    $val = 0;
    if($stock->get_stock_object()->market_cap >= 10000000000)
    	$val = 1;
    else if ($stock->get_stock_object()->market_cap >= 2000000000)
    	$val = 0.5;
    
    $weightedCap= $weightedCap + ($percentInvested*$val);
    echo " <td>" . $percentInvested * 100 . "</td>\n";

    if (strcmp($portfolio->username, $username) == 0)
      echo '  <td> <a data-toggle="modal" href="#SellStock" onclick="modalType(\'' . $stock->get_stock_object()->full_name . '\' , \'' . 
      $stock->ticker . '\', \'' . $stock->get_stock_object()->get_price() . '\', \'' . $stock->bought_price . '\' , \'' . $portfolio->PID . '\')"> Sell </a> </td>';
    else 
      echo ' <td></td>';

    echo "</tr>";
  }

  echo '</tbody>
  </table>';

  echo "Investor Type: ";
  if($weightedCap>=.7)
  {
    echo "You are a conservative investor. The majority of your stocks are large cap stocks with market caps above $10 billion. You are invested in stable companies that yield dividends throughout the year. The majority of your portfolio growth is attained through dividends. The majority of companies that you are invested in provide essential services that are not heavily influenced by the economic business cycle. This means that they are less likely to be impacted by a bad economy and will not be very volatile.";
  }
  if($weightedCap>=.3 & $weightedCap<.7)
  {
    echo "You are a hybrid investor. Your portfolio contains a combination of small, mid, and large cap stocks. You have a very diversified portfolio that likely has income stocks that grant a dividend such as Apple as well as more volatile stocks such as Solar companies that typically move much faster (in either direction) than the direction of the overall market. ";
  }
  if($weightedCap<.3 & $weightedCap>=0)
  {
    echo "You are an aggressive investor. The majority of your portfolio contains small cap stocks with a market cap under $2 billion. These types of stocks are typically very volatile and move much faster (in either direction) than the direction of the overall market. These stocks are potentially risky investments that can potentially yield high rewards. ";
  }
}


function display() {
  echo '<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
  <h2 class="sub-header"> My Portfolios </h2> <br>
  <div class="table-responsive">
  <table class="table table-striped">
  <thead>
  <tr>
  <th>Portfolio Name</th>
  <th>Group</th>
  <th>Number of Transactions</th>
  <th>Net Worth</th>
  <th>Money Left</th>
  <th>Profit</th>
  <th></th>
  </tr>
  </thead>
  <tbody>';
  show_portfolio_list();
  echo '    </tbody>
  </table>
  </div>
  </div>';
}

?>