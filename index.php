<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>WolfofSiebel</title>


    <!-- Bootstrap core CSS -->   
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- Optional theme -->
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script> 

    <!-- Custom styles for this template -->
    <!--<link href="starter-template.css" rel="stylesheet">-->

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
  <style>
      .divDemoBody  {
        width: 60%;
        margin-left: auto;
        margin-right: auto;
        margin-top: 100px;
        }
      .divDemoBody p {
        font-size: 18px;
        line-height: 140%;
        padding-top: 12px;
        }
      .divDialogElements input {
        font-size: 18px;
        padding: 3px; 
        height: 32px; 
        width: 500px; 
        }
      .divButton {
        padding-top: 12px;
        }
      </style>

  </head>
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
	<br>
          <h1 class="page-header">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Welcome to WolfOfSiebel!</h1>
         <p style="font-size:20px"><b> WolfOfSiebel is a real time stock trading simulator!</b><br>
         <br>
         <p style="font-size:20px"> Learn the fundamentals of Stock Trading in a fun and competitive environment!<br>
        <br>
         <p style="font-size:20px"> Discover what type of Investor you are and get recommendations on stock picks!<br>
         <br>
      	 <p style="font-size:20px"> Sign Up, Join a group with your friends, and Get Started!<br>
</p>        
      </div>;
  <body>
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="">WolfofSiebel</a>
        </div>
        <div class="collapse navbar-collapse">
          
          <ul class="nav navbar-nav navbar-right">
            <?php
              include "dashboard_loaders/query_functions/general_queries.php";
              $name = validate_username($_COOKIE["wolf_of_siebel_username"], $_COOKIE["wolf_of_siebel_password"]);
              if (is_null($name)) {
                echo '<li><a data-toggle="modal" href="#SignUp">Sign Up</a></li>';
                echo '<li><a data-toggle="modal" href="#Login">Log In</a></li>';
              } else {
                echo '<li><a href="dashboard.php">Welcome ' . $name . ' </a></li>';
                echo '<li><a href="form_submisions/logout.php">Log out</a></li>';
              }
            ?>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>

    <script type="text/javascript">
                $(document).ready(function(){
            $("#SignUp").submit(function(event){
              $.post($("#SignUpForm").attr("action"), $("#SignUpForm").serialize(), function(data){ 
                  if (data == "")
                    document.location = "dashboard.php";
                  else 
                    $("#errormessegesign").html(data);

                }) .fail(function() {
                  alert("COULD CREATE POST");
                });
                //$("#SignUp").modal("hide");
                
                event.preventDefault();
            });
        });      
    </script>

    <script type="text/javascript">
                $(document).ready(function(){
            $("#Login").submit(function(event){
              $.post($("#LoginForm").attr("action"), $("#LoginForm").serialize(), function(data){ 
                  if (data == "")
                    document.location = "dashboard.php";
                  else 
                    $("#errormessegelog").html(data);

                }) .fail(function() {
                  alert("COULD CREATE POST");
                });
                //$("#Login").modal("hide");
                event.preventDefault();
            });
        });      
    </script>

  
    <div class="modal fade" id="SignUp" tabindex="-1" role="dialog" aria-labelledby="purchaseLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="SignUpForm" action="form_submisions/create_user_form.php" method="post">
              <div class="modal-content">
                <div class="modal-header">
                  <h3>Enter Information</h3>
                  <h3 id="errormessegesign" ></h3>
                </div>
                <div class="modal-body" style= "padding-top: 0px;" >
                  <div class="divDialogElements">
                    <h4>Username</h4>
                    <input class="xlarge" id="xlInput" name="username" value="" type="text">
                    
                    <h4>Password</h4>
                    <input class="xlarge" id="xlInput" name="password" value="" type="password">
                    
                    <h4>Retype Password</h4>
                    <input class="xlarge" id="xlInput" name="repassword" value="" type="password">
                    
                    <hr style="height:1px;border:none;color:#333;background-color:#333;">
                    <h4>Name</h4>
                    <input class="xlarge" id="xlInput" name="name" value="" type="text">
                    <h4>Email</h4>
                    <input class="xlarge" id="xlInput" name="email" value="" type="text">
                  </div>
                </div>
                <div class="modal-footer">
                  <a href="#" class="btn btn-primary" data-dismiss="modal">Cancel</a>
                  <input type="submit" id="submit" class="btn btn-primary" value="Submit"><br>
                </div>
              </div>
          </form>
        </div>
    </div>

    <div class="modal fade" id="Login" tabindex="-1" role="dialog" aria-labelledby="signinLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="LoginForm" action="form_submisions/signin_user_form.php" method="post">
              <div class="modal-content">
                <div class="modal-header">
                  <h3>Welcome Back</h3>
                  <h3 id="errormessegelog" ></h3>
                </div>
                <div class="modal-body" style= "padding-top: 0px;" >
                  <div class="divDialogElements">
                    <h4>Username</h4>
                    <input class="xlarge" id="xlInput" name="username" value="" type="text">
                    
                    <h4>Password</h4>
                    <input class="xlarge" id="xlInput" name="password" value="" type="password">
                  </div>
                </div>
                <div class="modal-footer">
                  <a href="#" class="btn btn-primary" data-dismiss="modal">Cancel</a>
                  <input type="submit" id="submit" class="btn btn-primary" value="Submit"><br>
                </div>
              </div>
          </form>
        </div>
    </div>



    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/bootstrap.min.js"></script>

  </body>
</html>