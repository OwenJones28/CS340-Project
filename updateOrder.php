<?php
    ini_set('session.save_path', '/nfs/stak/users/jonesow/tmp_sessions');
	session_start();	
// Include config file
	require_once "config.php";

$distributorOptions = [];
$dist_sql = "SELECT distributorId, name FROM Distributor";
if ($dist_result = mysqli_query($link, $dist_sql)) {
    while ($row = mysqli_fetch_assoc($dist_result)) {
        $distributorOptions[] = $row;
    }
    mysqli_free_result($dist_result);
} else {
    echo "<center><h4>Error fetching distributor records.</h4></center>";
}

$productOptions = [];
$product_sql = "SELECT productId, type, flavor FROM Product";
if ($product_result = mysqli_query($link, $product_sql)) {
    while ($row = mysqli_fetch_assoc($product_result)) {
        $productOptions[] = $row;
    }
    mysqli_free_result($product_result);
} else {
    echo "<center><h4>Error fetching product records.</h4></center>";
}
 
// Define variables and initialize with empty values
$orderDate = $quantity = $fulfilledDate = $distributorId = $productId = "";
$orderDate_err = $quantity_err = $fulfilledDate_err = $distributorId_err = $productId_err = "";
// Form default values

if(isset($_GET["orderId"]) && !empty(trim($_GET["orderId"]))){
	$_SESSION["orderId"] = $_GET["orderId"];

    // Prepare a select statement
    $sql1 = "SELECT * FROM ProductOrder WHERE orderId = ?";
  
    if($stmt1 = mysqli_prepare($link, $sql1)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt1, "s", $param_id);      
        // Set parameters
       $param_id = trim($_GET["orderId"]);

        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt1)){
            $result1 = mysqli_stmt_get_result($stmt1);
			if(mysqli_num_rows($result1) > 0){

				$row = mysqli_fetch_array($result1);

                $orderDate = !empty($row['orderDate'])
                    ? (new DateTime($row['orderDate']))->format('Y-m-d')
                    : '';
                $quantity = $row['quantity'];
                $fulfilledDate = $row['fulfilledDate'];
                $distributorId = $row['distributorId'];
                $productId = $row['productId'];
			}
		}
	}
}
 
// Post information about the product when the form is submitted
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // the id is hidden and can not be changed
    $orderId = $_SESSION["orderId"];
    // Validate form data this is similar to the create
    // Validate orderDate
    $orderDate = trim($_POST["orderDate"]);
    if (!empty($orderDate)) {
        $d = DateTime::createFromFormat('Y-m-d', $orderDate);
        if (!($d && $d->format('Y-m-d') === $orderDate)) {
            $orderDate_err = "Please enter a valid order date (YYYY-MM-DD).";
        }
    } else {
        $orderDate = null;
    }
    // Validate quantity
    $quantity = trim($_POST["quantity"]);
    if (empty($quantity)) {
        $quantity_err = "Please enter quantity.";
    } elseif (!filter_var($quantity, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
        $quantity_err = "Please enter a valid quantity (positive integer).";
    }

    // Validate fulfilledDate (optional)
    $fulfilledDate = trim($_POST["fulfilledDate"]);
    if (!empty($fulfilledDate)) {
        $d = DateTime::createFromFormat('Y-m-d', $fulfilledDate);
        if (!($d && $d->format('Y-m-d') === $fulfilledDate)) {
            $fulfilledDate_err = "Please enter a valid fulfilled date (YYYY-MM-DD).";
        }
    } else {
        $fulfilledDate = null;
    }

    // Validate distributorId
    $distributorId = trim($_POST["distributorId"]);
    if (empty($distributorId)) {
        $distributorId_err = "Please select a distributor.";
    }

    // Validate productId
    $productId = trim($_POST["productId"]);
    if (empty($productId)) {
        $productId_err = "Please select a product.";
    }

    // Check input errors before inserting into database
    if (empty($quantity_err) && empty($fulfilledDate_err) && empty($distributorId_err) && empty($productId_err)) {
        // Prepare an update statement
        $sql = "UPDATE ProductOrder SET orderDate=?, quantity=?, fulfilledDate=?, distributorId=?, productId=? WHERE orderId=?";
        if($stmt = mysqli_prepare($link, $sql)){
            $param_orderDate = !empty($orderDate) ? $orderDate : NULL;
            $param_quantity = $quantity;
            $param_fulfilledDate = !empty($fulfilledDate) ? $fulfilledDate : NULL;
            $param_distributorId = $distributorId;
            $param_productId = $productId;
            $param_orderId = $orderId;
            mysqli_stmt_bind_param($stmt, "ssssss", $param_orderDate, $param_quantity, $param_fulfilledDate, $param_distributorId, $param_productId, $param_orderId);

            // Execute
            if(mysqli_stmt_execute($stmt)){
                header("location: index.php");
                exit();
            } else {
                echo "<center><h2>Error when updating order</center></h2>";
                echo "<center><p style='color: red;'>" . mysqli_stmt_error($stmt) . "</p></center>";
            }
            mysqli_stmt_close($stmt);
        }

    }
    
    // Close connection
    mysqli_close($link);
} else {

    // Check existence of sID parameter before processing further
	// Form default values

	if(isset($_GET["orderId"]) && !empty(trim($_GET["orderId"]))){
		$_SESSION["orderId"] = $_GET["orderId"];

		// Prepare a select statement
		$sql1 = "SELECT * FROM ProductOrder WHERE orderId = ?";
  
		if($stmt1 = mysqli_prepare($link, $sql1)){
			// Bind variables to the prepared statement as parameters
			mysqli_stmt_bind_param($stmt1, "s", $param_id);      
			// Set parameters
			$param_id = trim($_GET["orderId"]);

			// Attempt to execute the prepared statement
			if(mysqli_stmt_execute($stmt1)){
				$result1 = mysqli_stmt_get_result($stmt1);
				if(mysqli_num_rows($result1) == 1){

					$row = mysqli_fetch_array($result1);

					$orderDate = !empty($row['orderDate']) 
                        ? (new DateTime($row['orderDate']))->format('Y-m-d') 
                        : '';
                    $quantity = $row['quantity'];
                    $fulfilledDate = $row['fulfilledDate'];
                    $distributorId = $row['distributorId'];
                    $productId = $row['productId'];
				} else{
					// URL doesn't contain valid id. Redirect to error page
					header("location: error.php");
					exit();
				}                
			} else{
				echo "Error in Order ID while updating";
			}		
		}
			// Close statement
			mysqli_stmt_close($stmt1);
        
			// Close connection
			mysqli_close($link);
	}  else{
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
    <title>Update Order</title>
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
                        <h3>Update Record for Order ID =  <?php echo $_GET["orderId"]; ?> </H3>
                    </div>
                    <p>Please edit the input values and submit to update.
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
						<div class="form-group <?php echo (!empty($orderDate_err)) ? 'has-error' : ''; ?>">
                            <label>Order Date (optional)</label>
                            <input type="date" name="orderDate" class="form-control" value="<?php echo htmlspecialchars($orderDate); ?>">
                            <span class="help-block"><?php echo $orderDate_err; ?></span>
                        </div>
                    
                        <div class="form-group <?php echo (!empty($quantity_err)) ? 'has-error' : ''; ?>">
                            <label>Quantity</label>
                            <input type="number" name="quantity" class="form-control" value="<?php echo htmlspecialchars($quantity); ?>">
                            <span class="help-block"><?php echo $quantity_err; ?></span>
                        </div>

                        <div class="form-group <?php echo (!empty($fulfilledDate_err)) ? 'has-error' : ''; ?>">
                            <label>Fulfilled Date (optional)</label>
                            <input type="date" name="fulfilledDate" class="form-control" value="<?php echo htmlspecialchars($fulfilledDate); ?>">
                            <span class="help-block"><?php echo $fulfilledDate_err; ?></span>
                        </div>

                        <div class="form-group <?php echo (!empty($distributorId_err)) ? 'has-error' : ''; ?>">
                            <label>Distributor</label>
                            <select name="distributorId" class="form-control">
                                <option value="">-- Select Distributor --</option>
                                <?php foreach ($distributorOptions as $dist): ?>
                                    <option value="<?php echo $dist['distributorId']; ?>" <?php echo ($distributorId == $dist['distributorId']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($dist['distributorId'] . ' - ' . $dist['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="help-block"><?php echo $distributorId_err; ?></span>
                        </div>

                        <div class="form-group <?php echo (!empty($productId_err)) ? 'has-error' : ''; ?>">
                            <label>Product</label>
                            <select name="productId" class="form-control">
                                <option value="">-- Select Product --</option>
                                <?php foreach ($productOptions as $prod): ?>
                                    <option value="<?php echo $prod['productId']; ?>" <?php echo ($productId == $prod['productId']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($prod['productId'] . ' - ' . $prod['type'] . ' ' . $prod['flavor']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="help-block"><?php echo $productId_err; ?></span>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>