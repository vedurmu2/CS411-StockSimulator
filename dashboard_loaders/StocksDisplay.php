<?php

  require_once("query_objects/Stock.php");
  require_once("query_objects/User.php");
  require_once("query_objects/Portfolio.php");
  function show_all_stocks($start, $interval) {
    $partname = $_GET['partname'];
    $min = $_GET['min'];
    $max = $_GET['max'];

    $stocks = Stock::get_all_stocks($partname, $partname, "", "", "", doubleval($min), doubleval($max));
    $username = $_COOKIE["wolf_of_siebel_username"];
    $user = User::get_user_object($username);
    $all_ports = $user->get_portfolios();
    $port_name="new Array(";
    $port_ids="new Array(";
    $i = 0;
    foreach ($all_ports as $port) {
      if($i!=count($all_ports)-1)
      {
        $port_name=$port_name . "'" . $port->portfolio_name . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Money Left: ".$port->money_left . "',";
        $port_ids=$port_ids . "'" . $port->PID . "',";
        
      }
      else
      {
        $port_name=$port_name . "'" . $port->portfolio_name . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Money Left: ".$port->money_left ."')";
        $port_ids=$port_ids . "'" . $port->PID . "')";
        
      }
      $i ++;
    }
    //echo $port_name;
    //echo $port_ids;
    //echo $money;
      for ($i = $start; $i < count($stocks) && $i < $start + $interval; $i ++) {
        $file="http://www.google.com/finance/?q=" . $stocks[$i]->ticker;
        
        $stock = $stocks[$i];
        echo "<tr>";
        echo "<td> <a href=$file target=\"_blank\">". $stocks[$i]->ticker ."</a></td>";
        echo "  <td>" . $stocks[$i]->full_name . "</td>\n";
        echo "  <td>" . $stocks[$i]->sector . "</td>\n";
        echo "  <td>" . $stocks[$i]->exchange . "</td>\n";
          echo "  <td>" . $stocks[$i]->get_price() . "</td>\n";
      echo '  <td> <a data-toggle="modal" href="#BuyStock" onclick="modalType(\'' . $stock->full_name . '\' , \'' . 
      $stock->ticker . '\', \'' . $stock->get_price() . '\', ' . $port_ids .', ' . $port_name  .')"> Buy </a> </td>';
          echo "  <td>" . "" . "</td>\n";
        echo "</tr>";
      }
  }
function modaljs() {
    echo "<script type=\"text/javascript\">
       function modalType(name, ticker, price, Portfolios, PIDs) { 
          var ddl = $(\"#ports\");   
          for (k = 0; k < Portfolios.length; k++)
            ddl.append(\"<option value='\" + Portfolios[k]+ \"'>\" + PIDs[k] + \"</option>\")
            
          $(\"#modalticker\")[0].value = ticker;
          $(\"#stockname\").html(\"Stock Name:\" + name);
          $(\"#stockticker\").html(\"Stock Ticker:\" + ticker);
          $(\"#stockprice\").html(\"Stock Price:\" + price);
   
       }
       ddl.find('option').remove()
    </script>";

     echo '<script type="text/javascript">
                  $(document).ready(function(){
              $("#BuyStock").submit(function(event){
                $.post($("#BuyStockForm").attr("action"), $("#BuyStockForm").serialize(), function(data){ 
                    if (data != "") {
                      $("#errormessegebuy").html(data);
                    } else {
                      $("#BuyStock").modal("hide");
                      window.location.reload()
                    }

                  }) .fail(function() {
                  });
                  //$("#SignUp").modal("hide");
                  
                  event.preventDefault();
              });
          });      
      </script>';
  }
  
function buy_stock_modal() {
  echo '<div class="modal fade" id="BuyStock" tabindex="-1" role="dialog" aria-labelledby="purchaseLabel" aria-hidden="true">
        <div class="modal-dialog">  
            <form id="BuyStockForm" action="/~wolfofsiebel/form_submisions/buy_stock_form.php" method="post">
              <input type="hidden" id="modalPID" name="PID" value="0">
              <input type="hidden" id="modalticker" name="ticker" value="0">
              <div class="modal-content">
                <div class="modal-header">
                  <h3> Buy Stock! </h3>
                  <h5 id="stockname"> </h3>
                  <h5 id="stockticker"> </h3>
                  <h5 id="stockprice"> </h3>
                  <h5 id="stockchange"> </h3>
                  <h5 id="errormessegebuy"> </h3>
                </div>
                <div class="modal-body" style= "padding-top: 0=px;" >
                  <div class="divDialogElements">
                    <h4>Number of Shares</h4>
                    <input class="xlarge" id="xlInput" name="numShares" value="" type="text">
                    <h4>Portfolio Name</h4>
                    <select name="ports" id="ports" form="BuyStockForm" value="">
                    </select>
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
  
  function display() {
    $start = 0;
    modaljs();
    buy_stock_modal();
    echo '<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            <h2 class="sub-header">All Stocks</h2>
            <div id="tfheader">
              <form id="tfnewsearch" method="get" action="/~wolfofsiebel/dashboard.php">
                      <input type="hidden" id="modalPID" name="page" value="Stocks">
                      <input type="text" class="xlarge" name="min" size="21" maxlength="120" value="Min Price">
                      <input type="text" class="xlarge" name="max" size="21" maxlength="120" value="Max Price">
                      <input type="text" class="xlarge" name="partname" size="21" maxlength="120" value="Part Name">
                      <input type="submit" value="search" class="btn btn-primary">
              </form>
            <div class="tfclear"></div>
            </div>

              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>Ticker</th>
                      <th>Full Name</th>
                      <th>Industry</th>
                      <th>Market</th>
                      <th>Price</th>
                      <th>Buy</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>';
                    show_all_stocks($start,30);
                    //echo '<li><a data-toggle="modal" href="#Buy">Buy</a></li>';
        echo '    </tbody>
                </table>
              </div>
             </div>';
  }

?>