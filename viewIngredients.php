<?php
    ini_set('session.save_path', '/nfs/stak/users/jonesow/tmp_sessions');
	session_start();

    // Check existence of id parameter before processing further
    if(isset($_GET["productId"]) && !empty(trim($_GET["productId"]))){
        $_SESSION["productId"] = $_GET["productId"];
    }
    // Include config file
    require_once "config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Ingredients</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js"></script>
    <style type="text/css">
        .wrapper{
            width: 650px;
            margin: 0 auto;
        }
        .page-header h2{
            margin-top: 0;
        }
        table tr td:last-child a{
            margin-right: 15px;
        }
    </style>
	   <script type="text/javascript">
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();   
        });
    </script>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2 class="pull-left">View Ingredients</h2>
                        <a href="createIngredient.php?productId=<?php echo urlencode($_SESSION["productId"]); ?>" class="btn btn-success pull-right">Add Ingredients</a>
                    </div>
<?php

if(isset($_SESSION["productId"]) ){
	
    // Prepare a select statement
    $sql = "SELECT I.ingredientId, I.name FROM Ingredient I, ProductIngredient PI WHERE PI.productId = ? AND PI.ingredientId = I.ingredientId";

    if($stmt = mysqli_prepare($link, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "s", $param_id);      
        // Set parameters
       $param_id = $_SESSION["productId"];

        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
    
			echo"<h4> Ingredients for Product ID =".$param_id."</h4><p>";
			if(mysqli_num_rows($result) > 0){
				echo "<table class='table table-bordered table-striped'>";
                    echo "<thead>";
                        echo "<tr>";
                            echo "<th width = 20%>ID</th>";
                            echo "<th>Name</th>";
                            echo "<th>Action</th>";
                        echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";							
				// output data of each row
                    while($row = mysqli_fetch_array($result)){
                        echo "<tr>";
                        echo "<td>" . $row['ingredientId'] . "</td>";
                        echo "<td>" . $row['name'] . "</td>";
                        echo "<td>";
                          echo "<a href='deleteIngredient.php?ingredientId=". $row['ingredientId'] . "&productId=" . urlencode($_SESSION['productId']) ."' title='Delete Ingredient' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody>";                            
                echo "</table>";				
				mysqli_free_result($result);
			} else {
				echo "No Ingredients. ";
			}
//				mysqli_free_result($result);
        } else{
			// URL doesn't contain valid id parameter. Redirect to error page
            header("location: error.php");
            exit();
        }
    }     
    // Close statement
    mysqli_stmt_close($stmt);
    
    // Close connection
    mysqli_close($link);
} else{
    // URL doesn't contain id parameter. Redirect to error page
    header("location: error.php");
    exit();
}
?>					                 					
	<p><a href="index.php" class="btn btn-primary">Back</a></p>
    </div>
   </div>        
  </div>
</div>
</body>
</html>