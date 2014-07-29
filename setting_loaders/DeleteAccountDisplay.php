<?php
  function display() {
    echo '
   
      


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
        
        <!-- Latest compiled and minified JavaScript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script> 

    <!-- Custom styles for this template -->
    <!--<link href="starter-template.css" rel="stylesheet">-->

    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

        
        <body>
        
        <script type="text/javascript">
                $(document).ready(function(){
            $("#DeleteAccount").submit(function(event){
              $.post($("#DeleteAccount").attr("action"), $("#DeleteAccount").serialize(), function(data){ 
                  if (data == "")
                    document.location = "/";
                  else 
                    $("#errormessegelog").html(data);

                }) .fail(function() {
                  alert("COULD CREATE POST");
                });
                //$("#DeleteAccount").modal("hide");
                event.preventDefault();
            });
        });      
    	</script>


        <div class="modal-dialog">
            <form id="DeleteAccount" action="form_submisions/delete_account.php" method="post">
              <div class="modal-content">
                <div class="modal-header">
                  <h3>Delete Account</h3>
                </div>
                <div class="modal-body" style= "padding-top: 0px;" >
                  <div class="divDialogElements">
                    <h4>Username</h4>
                    <input class="xlarge" id="xlInput" name="username" value="" type="text">
                    
                    <h4>Password</h4>
                    <input class="xlarge" id="xlInput" name="password" value="" type="password">
                    <h4>Confirm Password</h4>
                    <input class="xlarge" id="xlInput" name="passwordre" value="" type="password"> 
                    <h3 id="errormessegelog"></h3>                   
                  </div>
                </div>
                <div class="modal-footer">
                
                  <input type="submit" id="submit" class="btn btn-primary" value="Delete"><br>
                </div>
              </div>
          </form>
        </div>



      </div>';
  }
?>