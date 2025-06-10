<?php
    ini_set('session.save_path', '/nfs/stak/users/jonesow/tmp_sessions');
	session_start();	
// Include config file
	require_once "config.php";
 
// Define variables and initialize with empty values
$name = $role = $email = $phone = "";
$name_err = $role_err = $email_err = $phone_err = "";
// Form default values

if(isset($_GET["staffId"]) && !empty(trim($_GET["staffId"]))){
	$_SESSION["staffId"] = $_GET["staffId"];

    // Prepare a select statement
    $sql1 = "SELECT * FROM Staff WHERE staffId = ?";
  
    if($stmt1 = mysqli_prepare($link, $sql1)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt1, "s", $param_id);      
        // Set parameters
       $param_id = trim($_GET["staffId"]);

        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt1)){
            $result1 = mysqli_stmt_get_result($stmt1);
			if(mysqli_num_rows($result1) > 0){

				$row = mysqli_fetch_array($result1);

				$name = $row['name'];
				$role = $row['role'];
				$email = $row['email'];
				$phone = $row['phone'];
			}
		}
	}
}
 
// Post information about the staff when the form is submitted
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // the id is hidden and can not be changed
    $staffId = $_SESSION["staffId"];
    // Validate form data this is similar to the create staff file
    // Validate name
    $name = trim($_POST["name"]);
    if(empty($name)){
        $name_err = "Please enter a name.";
    } elseif(!filter_var($name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $name_err = "Please enter a valid name.";
    }
    // Validate Role
    $role = trim($_POST["role"]);
    if(empty($role)){
        $role_err = "Please enter a role.";
    } elseif(!filter_var($role, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $role_err = "Please enter a valid role.";
    }
	// Validate Email
    $email = trim($_POST["email"]);
    if(empty($email)){
        $email_err = "Please enter an email.";     
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $email_err = "Please enter a valid email.";
    }
	// Validate Phone
    $phone = trim($_POST["phone"]);
    if(empty($phone)){
        $phone_err = "Please enter a phone number.";     
    } elseif(!filter_var($phone, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[0-9\s]{10}$/")))){
        $phone_err = "Please enter a valid phone number.";
    }

    // Check input errors before inserting into database
    if(empty($name_err) && empty($role_err) && empty($email_err) && empty($phone_err)){
        // Prepare an update statement
        $sql = "UPDATE Staff SET name=?, role=?, email=?, phone = ? WHERE staffId=?";
    
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssss", $param_name, $param_role,$param_email, $param_phone, $param_id);
            
            // Set parameters
            $param_name = $name;
            $param_role = $role;
			$param_email = $email;
			$param_phone = $phone;
            $param_id = $staffId;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records updated successfully. Redirect to landing page
                header("location: index.php");
                exit();
            } else{
                echo "<center><h2>Error when updating</center></h2>";
            }
        }        
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
} else {

    // Check existence of sID parameter before processing further
	// Form default values

	if(isset($_GET["staffId"]) && !empty(trim($_GET["staffId"]))){
		$_SESSION["staffId"] = $_GET["staffId"];

		// Prepare a select statement
		$sql1 = "SELECT * FROM Staff WHERE staffId = ?";
  
		if($stmt1 = mysqli_prepare($link, $sql1)){
			// Bind variables to the prepared statement as parameters
			mysqli_stmt_bind_param($stmt1, "s", $param_id);      
			// Set parameters
			$param_id = trim($_GET["staffId"]);

			// Attempt to execute the prepared statement
			if(mysqli_stmt_execute($stmt1)){
				$result1 = mysqli_stmt_get_result($stmt1);
				if(mysqli_num_rows($result1) == 1){

					$row = mysqli_fetch_array($result1);

					$name = $row['name'];
                    $role = $row['role'];
                    $email = $row['email'];
                    $phone = $row['phone'];
				} else{
					// URL doesn't contain valid id. Redirect to error page
					header("location: error.php");
					exit();
				}                
			} else{
				echo "Error in Staff ID while updating";
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
    <title>Update Staff</title>
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
                        <h3>Update Record for Staff ID =  <?php echo $_GET["staffId"]; ?> </H3>
                    </div>
                    <p>Please edit the input values and submit to update.
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
						<div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo $name; ?>">
                            <span class="help-block"><?php echo $name_err;?></span>
                        </div>
                 
						<div class="form-group <?php echo (!empty($role_err)) ? 'has-error' : ''; ?>">
                            <label>Role</label>
                            <input type="text" name="role" class="form-control" value="<?php echo $role; ?>">
                            <span class="help-block"><?php echo $role_err;?></span>
                        </div>
						<div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                            <label>Email</label>
                            <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                            <span class="help-block"><?php echo $email_err;?></span>
                        </div>
						<div class="form-group <?php echo (!empty($phone_err)) ? 'has-error' : ''; ?>">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo $phone; ?>">
                            <span class="help-block"><?php echo $phone_err;?></span>
                        </div>					
                        <input type="hidden" name="staffId" value="<?php echo $staffId; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>