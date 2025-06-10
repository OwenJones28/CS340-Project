<?php
	ini_set('session.save_path', '/nfs/stak/users/jonesow/tmp_sessions');
	session_start();
	if(isset($_GET["productId"]) && !empty(trim($_GET["productId"]))){
		$_SESSION["productId"] = $_GET["productId"];
	}

    require_once "config.php";
	// Delete an Product's record after confirmation
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if(isset($_SESSION["productId"]) && !empty($_SESSION["productId"])){ 
			$productId = $_SESSION['productId'];
			// Prepare a delete statement
			$sql = "DELETE FROM Product WHERE productId = ?";
   
			if($stmt = mysqli_prepare($link, $sql)){
			// Bind variables to the prepared statement as parameters
				mysqli_stmt_bind_param($stmt, "s", $param_id);
 
				// Set parameters
				$param_id = $productId;
       
				// Attempt to execute the prepared statement
				if(mysqli_stmt_execute($stmt)){
					// Records deleted successfully. Redirect to landing page
					header("location: index.php");
					exit();
				} else{
					echo "Error deleting the product";
				}
			}

			// Close statement
			mysqli_stmt_close($stmt);
		}
		
    
		// Close connection
		mysqli_close($link);
	} else{
		// Check existence of id parameter
		if(empty(trim($_GET["productId"]))){
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
    <title>Delete Product</title>
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
                        <h1>Delete Product</h1>
                    </div>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="alert alert-danger fade in">
                            <input type="hidden" name="productId" value="<?php echo ($_SESSION["productId"]); ?>"/>
                            <p>Are you sure you want to delete the record for <?php echo ($_SESSION["productId"]); ?>?</p><br>
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