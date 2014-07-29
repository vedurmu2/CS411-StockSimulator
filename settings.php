<?php
  include_once "dashboard_loaders/query_functions/general_queries.php";

  $name = validate_username($_COOKIE["wolf_of_siebel_username"], $_COOKIE["wolf_of_siebel_password"]);
  if (is_null($name)) {
    header("Location: /");
  }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>WolfOfSiebel</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/dashboard.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/">WolfOfSiebel</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
	    <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="form_submisions/logout.php">Log Out</a></li>
          </ul>
          
        </div>
      </div>
    </div>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">
            <li class="active"><a href="?page=Overview">Overview</a></li>
	    <li ><a href="?page=ChangeName">Change Name</a></li>
            <li ><a href="?page=ChangeEmail">Change Email</a></li>
			</ul>
          <ul class="nav nav-sidebar">
            <li ><a href="?page=ChangeUsername">Change Username</a></li>
            <li ><a href="?page=ChangePassword">Change Password</a></li>
			</ul>
          <ul class="nav nav-sidebar">
            <li ><a href="?page=DeleteAccount">Delete Account</a></li>
          </ul>
        </div>
        
        <?php
          if (strcmp($_GET['page'], "ChangeName") == 0) {
             include "setting_loaders/ChangeNameDisplay.php";
          } else if (strcmp($_GET['page'], "ChangeEmail") == 0) {
             include "setting_loaders/ChangeEmailDisplay.php";
          } else if (strcmp($_GET['page'], "ChangeUsername") == 0) {
             include "setting_loaders/ChangeUsernameDisplay.php";
          } else if (strcmp($_GET['page'], "ChangePassword") == 0) {
             include "setting_loaders/ChangePasswordDisplay.php";
	  } else if (strcmp($_GET['page'], "DeleteAccount") == 0) {
             include "setting_loaders/DeleteAccountDisplay.php";
	  }  else {
             include "setting_loaders/AccountSettingsDisplay.php";
          }

          display();
        ?>
    </div>

    

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/docs.min.js"></script>
  </body>
</html>