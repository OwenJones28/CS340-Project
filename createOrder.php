<?php
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
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate orderDate
    $orderDate = trim($_POST["orderDate"]);
    if (!empty($orderDate)) {
        $d = DateTime::createFromFormat('Y-m-d', $orderDate);
        if (!($d && $d->format('Y-m-d') === $orderDate)) {
            $orderDate_err = "Please enter a valid fulfilled date (YYYY-MM-DD).";
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
    // Check input errors before inserting in database
    if (empty($quantity_err) && empty($fulfilledDate_err) && empty($distributorId_err) && empty($productId_err)) {
        // Prepare SQL depending on whether orderDate is provided
        if (!empty($orderDate)) {
            $sql = "INSERT INTO ProductOrder (orderDate, quantity, fulfilledDate, distributorId, productId) VALUES (?, ?, ?, ?, ?)";
        } else {
            $sql = "INSERT INTO ProductOrder (quantity, fulfilledDate, distributorId, productId) VALUES (?, ?, ?, ?)";
        }

        if ($stmt = mysqli_prepare($link, $sql)) {
            if (!empty($orderDate)) {
                mysqli_stmt_bind_param($stmt, "sssss", $orderDate, $quantity, $fulfilledDate, $distributorId, $productId);
            } else {
                mysqli_stmt_bind_param($stmt, "ssss", $quantity, $fulfilledDate, $distributorId, $productId);
            }

            // Execute
            if (mysqli_stmt_execute($stmt)) {
                header("location: index.php");
                exit();
            } else {
                echo "<center><h4>Error while creating new order</h4></center>";
            }
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Order</title>
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
                        <h2>Create ORder</h2>
                    </div>
                    <p>Please fill this form and submit to add an Order record to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
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