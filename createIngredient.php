<?php
ini_set('session.save_path', '/nfs/stak/users/jonesow/tmp_sessions');
session_start();
if(isset($_GET["productId"]) && !empty(trim($_GET["productId"]))){
    $_SESSION["productId"] = $_GET["productId"];
}
$productId = isset($_SESSION["productId"]) ? $_SESSION["productId"] : null;

// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$ingredientId ="" ;
$ingredientId_err = "" ;

$ingredients = [];
$sql = "SELECT ingredientId, name FROM Ingredient";
if ($result = mysqli_query($link, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $ingredients[] = $row;
    }
    mysqli_free_result($result);
} else {
    echo "<center><h4>Error fetching ingredients.</h4></center>";
}
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate Dependent name
    $ingredientId = trim($_POST["ingredientId"]);
    if(empty($ingredientId)){
        $ingredientId_err = "Please select an ingredient.";
    }

    // Check input errors before inserting in database
    if(empty($ingredientId_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO ProductIngredient (productId, ingredientId) 
		        VALUES (?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_productId, $param_ingredientId);
           
            // Set parameters
			$param_productId = $productId;
			$param_ingredientId = $ingredientId;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records created successfully. Redirect to landing page
				    header("location: index.php");
					exit();
            } else{
                echo "<center><h4>Error while adding ingredient to product</h4></center>";
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
    <title>Add Ingredient</title>
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
                        <h2>Add Ingredient</h2>
						<h3> For Product with ID = <?php echo $productId; ?> </h3>
                    </div>
                    
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
			
             
						<div class="form-group <?php echo (!empty($ingredientId_err)) ? 'has-error' : ''; ?>">
                            <label>Select Ingredient</label>
                            <select name="ingredientId" class="form-control">
                                <option value="">-- Select Ingredient --</option>
                                <?php foreach ($ingredients as $ingredient): ?>
                                    <option value="<?php echo $ingredient['ingredientId']; ?>" 
                                        <?php echo ($ingredientId == $ingredient['ingredientId']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($ingredient['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="help-block"><?php echo $ingredientId_err; ?></span>
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