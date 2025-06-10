<?php
// Include config file
require_once "config.php";

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
$quantity = $batchDate = $productId = "";
$quantity_err = $batchDate_err = $productId_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate quantity
    $quantity = trim($_POST["quantity"]);
    if (empty($quantity)) {
        $quantity_err = "Please enter quantity.";
    } elseif (!filter_var($quantity, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
        $quantity_err = "Please enter a valid quantity (positive integer).";
    }

    // Validate batchDate (optional)
    $batchDate = trim($_POST["batchDate"]);
    if (!empty($batchDate)) {
        $d = DateTime::createFromFormat('Y-m-d\TH:i', $batchDate);
        if (!($d && $d->format('Y-m-d H:i:00') === str_replace('T', ' ', $batchDate).':00')) {
            $batchDate_err = "Please enter a valid batch date/time.";
        }
    } else {
        $batchDate = null;
    }

    // Validate productId
    $productId = trim($_POST["productId"]);
    if (empty($productId)) {
        $productId_err = "Please select a product.";
    }
    // Check input errors before inserting in database
    if (empty($quantity_err) && empty($batchDate_err) && empty($productId_err)) {
        // Prepare SQL depending on whether orderDate is provided
        if (!empty($batchDate)) {
            $sql = "INSERT INTO Batch (quantity, batchDate, productId) VALUES (?, ?, ?)";
        } else {
            $sql = "INSERT INTO Batch (quantity, productId) VALUES (?, ?)";
        }

        if ($stmt = mysqli_prepare($link, $sql)) {
            if (!empty($batchDate)) {
                mysqli_stmt_bind_param($stmt, "sss", $quantity, $batchDate, $productId);
            } else {
                mysqli_stmt_bind_param($stmt, "ss", $quantity, $productId);
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
                        <h2>Create Batch</h2>
                    </div>
                    <p>Please fill this form and submit to add an Batch record to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group <?php echo (!empty($quantity_err)) ? 'has-error' : ''; ?>">
                            <label>Quantity</label>
                            <input type="number" name="quantity" class="form-control" min="0" value="<?php echo htmlspecialchars($quantity); ?>">
                            <span class="help-block"><?php echo $quantity_err; ?></span>
                        </div>

                        <div class="form-group <?php echo (!empty($batchDate_err)) ? 'has-error' : ''; ?>">
                            <label>Batch Date (optional)</label>
                            <input type="datetime-local" name="batchDate" class="form-control" value="<?php echo htmlspecialchars($batchDate); ?>">
                            <span class="help-block"><?php echo $batchDate_err; ?></span>
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