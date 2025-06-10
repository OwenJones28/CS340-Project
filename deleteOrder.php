<?php
	ini_set('session.save_path', '/nfs/stak/users/jonesow/tmp_sessions');
	session_start();
	if(isset($_GET["orderId"]) && !empty(trim($_GET["orderId"]))){
		$_SESSION["orderId"] = $_GET["orderId"];
	}

    require_once "config.php";
	// Delete an Order's record after confirmation
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if(isset($_SESSION["orderId"]) && !empty($_SESSION["orderId"])){ 
			$orderId = $_SESSION['orderId'];
			// Prepare a delete statement
			$sql = "DELETE FROM ProductOrder WHERE orderId = ?";
   
			if($stmt = mysqli_prepare($link, $sql)){
			// Bind variables to the prepared statement as parameters
				mysqli_stmt_bind_param($stmt, "s", $param_id);
 
				// Set parameters
				$param_id = $orderId;
       
				// Attempt to execute the prepared statement
				if(mysqli_stmt_execute($stmt)){
					// Records deleted successfully. Redirect to landing page
					header("location: index.php");
					exit();
				} else{
					echo "Error deleting the order";
				}
			}

			// Close statement
			mysqli_stmt_close($stmt);
		}
		
    
		// Close connection
		mysqli_close($link);
	} else{
		// Check existence of id parameter
		if(empty(trim($_GET["orderId"]))){
			// URL doesn't contain id parameter. Redirect to error page
			header("location: error.php");
			exit();
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Order</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper{
            width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h1>Delete Order</h1>
                    </div>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="alert alert-danger fade in">
                            <input type="hidden" name="orderId" value="<?php echo ($_SESSION["orderId"]); ?>"/>
                            <p>Are you sure you want to delete the record for <?php echo ($_SESSION["orderId"]); ?>?</p><br>
                                <input type="submit" value="Yes" class="btn btn-danger">
                                <a href="index.php" class="btn btn-default">No</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>