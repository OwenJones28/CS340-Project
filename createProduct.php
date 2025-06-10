<?php
// Include config file
require_once "config.php";

$staffOptions = [];
$staff_sql = "SELECT staffId, name FROM Staff"; // Assuming there's a 'name' column; adjust if needed
if ($staff_result = mysqli_query($link, $staff_sql)) {
    while ($row = mysqli_fetch_assoc($staff_result)) {
        $staffOptions[] = $row;
    }
    mysqli_free_result($staff_result);
} else {
    echo "<center><h4>Error fetching staff records.</h4></center>";
}

// Define variables and initialize with empty values
$type = $flavor = $weight = $createdBy = "";
$type_err = $flavor_err = $weight_err = $createdBy_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate type
    $type = trim($_POST["type"]);
    if(empty($type)){
        $type_err = "Please enter a type.";
    } elseif(!filter_var($type, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $type_err = "Please enter a valid type.";
    }
    // Validate Flavor
    $flavor = trim($_POST["flavor"]);
    if(empty($flavor)){
        $flavor_err = "Please enter a flavor.";
    } elseif(!filter_var($flavor, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $flavor_err = "Please enter a valid flavor.";
    }
	// Validate Weight
    $weight = trim($_POST["weight"]);
    if(empty($weight)){
        $weight_err = "Please enter an weight.";     
    } elseif(!filter_var($weight, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[0-9\s]+$/")))){
        $weight_err = "Please enter a valid weight.";
    }
	// Validate Created By
    $createdBy = trim($_POST["createdBy"]);
    if(empty($createdBy)){
        $createdBy_err = "Please enter a staff id.";     
    } elseif(!filter_var($weight, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[0-9\s]+$/")))){
        $weight_err = "Please enter a valid staff id.";
    }
    // Check input errors before inserting in database
    if(empty($type_err) && empty($flavor_err) && empty($weight_err) 
				&& empty($createdBy_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO Product (type, flavor, weight, createdBy) 
		        VALUES (?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssss", $param_type, $param_flavor, 
				$param_weight, $param_createdBy);
            
            // Set parameters
			$param_type = $type;
            $param_flavor = $flavor;
			$param_weight = $weight;
			$param_createdBy = $createdBy;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records created successfully. Redirect to landing page
				    header("location: index.php");
					exit();
            } else{
                echo "<center><h4>Error while creating new product</h4></center>";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }

    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Product</title>
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
                        <h2>Create Product</h2>
                    </div>
                    <p>Please fill this form and submit to add an Product record to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
						<div class="form-group <?php echo (!empty($type_err)) ? 'has-error' : ''; ?>">
                            <label>Type</label>
                            <input type="text" name="type" class="form-control" value="<?php echo $type; ?>">
                            <span class="help-block"><?php echo $type_err;?></span>
                        </div>
                 
						<div class="form-group <?php echo (!empty($flavor_err)) ? 'has-error' : ''; ?>">
                            <label>Flavor</label>
                            <input type="text" name="flavor" class="form-control" value="<?php echo $flavor; ?>">
                            <span class="help-block"><?php echo $flavor_err;?></span>
                        </div>
						<div class="form-group <?php echo (!empty($weight_err)) ? 'has-error' : ''; ?>">
                            <label>Weight</label>
                            <input type="text" name="weight" class="form-control" value="<?php echo $weight; ?>">
                            <span class="help-block"><?php echo $weight_err;?></span>
                        </div>
						<div class="form-group <?php echo (!empty($createdBy_err)) ? 'has-error' : ''; ?>">
                            <label>Created By</label>
                            <select name="createdBy" class="form-control">
                                <option value="">-- Select Staff --</option>
                                <?php foreach ($staffOptions as $staff): ?>
                                    <option value="<?php echo $staff['staffId']; ?>" <?php echo ($createdBy == $staff['staffId']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($staff['staffId'] . ' - ' . $staff['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="help-block"><?php echo $createdBy_err;?></span>
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