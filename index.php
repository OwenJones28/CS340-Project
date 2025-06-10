<?php
ini_set('session.save_path', '/nfs/stak/users/jonesow/tmp_sessions');
session_start();
//$currentpage="View Employees"; 
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Chocolate Factory</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <style type="text/css">
        .wrapper {
            width: 70%;
            margin: 0 auto;
        }

        table tr td:last-child a {
            margin-right: 15px;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
        $('.selectpicker').selectpicker();
    </script>
</head>

<body>
    <?php
    // Include config file
    require_once "config.php";
    //		include "header.php";
    ?>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2> Chocolate Factory </h2>
                        <!-- <p> Project should include CRUD operations. In this website you can:
                        <ol>
                            <li> CREATE new employess and dependents </li>
                            <li> RETRIEVE all dependents and prjects for an employee</li>
                            <li> UPDATE employeee and dependent records</li>
                            <li> DELETE employee and dependent records </li>
                        </ol> -->
                    </div>
                    <div class="page-header clearfix">
                        <h2 class="pull-left">Staff</h2>
                        <a href="createStaff.php" class="btn btn-success pull-right">Add Staff</a>
                    </div>
                    <?php
                    // Include config file
                    require_once "config.php";

                    $sql = "SELECT staffId,name,role,email,phone FROM Staff";
                    if ($result = mysqli_query($link, $sql)) {
                        if (mysqli_num_rows($result) > 0) {
                            echo "<table class='table table-bordered table-striped'>";
                            echo "<thead>";
                            echo "<tr>";
                            echo "<th>ID</th>";
                            echo "<th>Name</th>";
                            echo "<th>Role</th>";
                            echo "<th>Email</th>";
                            echo "<th>Phone</th>";
                            echo "<th>Action</th>";
                            echo "</tr>";
                            echo "</thead>";
                            echo "<tbody>";
                            while ($row = mysqli_fetch_array($result)) {
                                echo "<tr>";
                                echo "<td>" . $row['staffId'] . "</td>";
                                echo "<td>" . $row['name'] . "</td>";
                                echo "<td>" . $row['role'] . "</td>";
                                echo "<td>" . $row['email'] . "</td>";
                                echo "<td>" . $row['phone'] . "</td>";
                                echo "<td>";
                                echo "<a href='viewProducts.php?staffId=" . $row['staffId'] . "&name=" . $row['name'] . "' title='View Products' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
                                echo "<a href='updateStaff.php?staffId=" . $row['staffId'] . "' title='Update Record' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                                echo "<a href='deleteStaff.php?staffId=" . $row['staffId'] . "' title='Delete Record' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                            echo "</tbody>";
                            echo "</table>";
                            // Free result set
                            mysqli_free_result($result);
                        } else {
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                    } else {
                        echo "ERROR: Could not able to execute $sql. <br>" . mysqli_error($link);
                    }
                    ?>
                    <div class="page-header clearfix">
                        <h2 class="pull-left">Products</h2>
                        <a href="createProduct.php" class="btn btn-success pull-right">Add Product</a>
                    </div>
                    <?php
                    $sql = "SELECT productId,type,flavor,weight,inventory,createdBy FROM Product";
                    if ($result = mysqli_query($link, $sql)) {
                        if (mysqli_num_rows($result) > 0) {
                            echo "<table class='table table-bordered table-striped'>";
                            echo "<thead>";
                            echo "<tr>";
                            echo "<th>ID</th>";
                            echo "<th>Type</th>";
                            echo "<th>Flavor</th>";
                            echo "<th>Weight</th>";
                            echo "<th>Inventory</th>";
                            echo "<th>Created By</th>";
                            echo "<th>Action</th>";
                            echo "</tr>";
                            echo "</thead>";
                            echo "<tbody>";
                            while ($row = mysqli_fetch_array($result)) {
                                echo "<tr>";
                                echo "<td>" . $row['productId'] . "</td>";
                                echo "<td>" . $row['type'] . "</td>";
                                echo "<td>" . $row['flavor'] . "</td>";
                                echo "<td>" . $row['weight'] . "</td>";
                                echo "<td>" . $row['inventory'] . "</td>";
                                echo "<td>" . $row['createdBy'] . "</td>";
                                echo "<td>";
                                echo "<a href='viewIngredients.php?productId=" . $row['productId'] . "' title='View Ingredients' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
                                echo "<a href='updateProduct.php?productId=" . $row['productId'] . "' title='Update Record' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                                echo "<a href='deleteProduct.php?productId=" . $row['productId'] . "' title='Delete Record' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                            echo "</tbody>";
                            echo "</table>";
                            // Free result set
                            mysqli_free_result($result);
                        } else {
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                    } else {
                        echo "ERROR: Could not able to execute $sql. <br>" . mysqli_error($link);
                    }
                    ?>
                    <div class="page-header clearfix">
                        <h2 class="pull-left">Orders</h2>
                        <a href="createOrder.php" class="btn btn-success pull-right">Add Order</a>
                    </div>
                    <?php
                    $sql = "SELECT orderId,orderDate,quantity,fulfilledDate,distributorId,productId FROM ProductOrder";
                    if ($result = mysqli_query($link, $sql)) {
                        if (mysqli_num_rows($result) > 0) {
                            echo "<table class='table table-bordered table-striped'>";
                            echo "<thead>";
                            echo "<tr>";
                            echo "<th>ID</th>";
                            echo "<th>Date</th>";
                            echo "<th>Quantity</th>";
                            echo "<th>Fulfilled Date</th>";
                            echo "<th>Distributor</th>";
                            echo "<th>Product</th>";
                            echo "<th>Action</th>";
                            echo "</tr>";
                            echo "</thead>";
                            echo "<tbody>";
                            while ($row = mysqli_fetch_array($result)) {
                                echo "<tr>";
                                echo "<td>" . $row['orderId'] . "</td>";
                                echo "<td>" . $row['orderDate'] . "</td>";
                                echo "<td>" . $row['quantity'] . "</td>";
                                echo "<td>" . $row['fulfilledDate'] . "</td>";
                                echo "<td>" . $row['distributorId'] . "</td>";
                                echo "<td>" . $row['productId'] . "</td>";
                                echo "<td>";
                                    if (empty($row['fulfilledDate'])) {
                                        echo "<a href='updateOrder.php?orderId=" . $row['orderId'] . "' title='Update Record' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                                        echo "<a href='deleteOrder.php?orderId=" . $row['orderId'] . "' title='Delete Record' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                                    }
                                echo "</td>";
                                echo "</tr>";
                            }
                            echo "</tbody>";
                            echo "</table>";
                            // Free result set
                            mysqli_free_result($result);
                        } else {
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                    } else {
                        echo "ERROR: Could not able to execute $sql. <br>" . mysqli_error($link);
                    }
                    ?>
                    <div class="page-header clearfix">
                        <h2 class="pull-left">Batches</h2>
                        <a href="createBatch.php" class="btn btn-success pull-right">Add Batch</a>
                    </div>
                    <?php
                    $sql = "SELECT batchId,quantity,batchDate,productId FROM Batch";
                    if ($result = mysqli_query($link, $sql)) {
                        if (mysqli_num_rows($result) > 0) {
                            echo "<table class='table table-bordered table-striped'>";
                            echo "<thead>";
                            echo "<tr>";
                            echo "<th>ID</th>";
                            echo "<th>Quantity</th>";
                            echo "<th>Date</th>";
                            echo "<th>Product</th>";
                            echo "</tr>";
                            echo "</thead>";
                            echo "<tbody>";
                            while ($row = mysqli_fetch_array($result)) {
                                echo "<tr>";
                                echo "<td>" . $row['batchId'] . "</td>";
                                echo "<td>" . $row['quantity'] . "</td>";
                                echo "<td>" . $row['batchDate'] . "</td>";
                                echo "<td>" . $row['productId'] . "</td>";
                                echo "</tr>";
                            }
                            echo "</tbody>";
                            echo "</table>";
                            // Free result set
                            mysqli_free_result($result);
                        } else {
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                    } else {
                        echo "ERROR: Could not able to execute $sql. <br>" . mysqli_error($link);
                    }
                    ?>
                    <div class="page-header clearfix">
                        <h2 class="pull-left">Most Popular Product per Distributor</h2>
                    </div>

                    <?php
                    $sql = "SELECT distributorId, name FROM Distributor";
                    if ($result = mysqli_query($link, $sql)) {
                        if (mysqli_num_rows($result) > 0) {
                            echo "<table class='table table-bordered table-striped'>";
                            echo "<thead>";
                            echo "<tr>";
                            echo "<th>Distributor ID</th>";
                            echo "<th>Distributor Name</th>";
                            echo "<th>Most Popular Product ID</th>";
                            echo "<th>Product Type</th>";
                            echo "<th>Product Flavor</th>";
                            echo "</tr>";
                            echo "</thead>";
                            echo "<tbody>";

                            while ($dist = mysqli_fetch_assoc($result)) {
                                $distributorId = $dist['distributorId'];
                                $distributorName = $dist['name'];
                                $func_sql = "SELECT cs340_jonesow.GetMostPopularProductByDistributor(?) AS mostPopularProduct";
                                if ($stmt = mysqli_prepare($link, $func_sql)) {
                                    mysqli_stmt_bind_param($stmt, "i", $distributorId);
                                    mysqli_stmt_execute($stmt);
                                    mysqli_stmt_bind_result($stmt, $mostPopularProductId);
                                    mysqli_stmt_fetch($stmt);
                                    mysqli_stmt_close($stmt);
                                } else {
                                    $mostPopularProductId = null;
                                }

                                if ($mostPopularProductId) {
                                    $prod_sql = "SELECT type, flavor FROM Product WHERE productId = ?";
                                    if ($prod_stmt = mysqli_prepare($link, $prod_sql)) {
                                        mysqli_stmt_bind_param($prod_stmt, "i", $mostPopularProductId);
                                        mysqli_stmt_execute($prod_stmt);
                                        mysqli_stmt_bind_result($prod_stmt, $type, $flavor);
                                        mysqli_stmt_fetch($prod_stmt);
                                        mysqli_stmt_close($prod_stmt);
                                    } else {
                                        $type = $flavor = 'N/A';
                                    }
                                } else {
                                    $mostPopularProductId = 'N/A';
                                    $type = 'N/A';
                                    $flavor = 'N/A';
                                }

                                echo "<tr>";
                                echo "<td>" . $distributorId . "</td>";
                                echo "<td>" . $distributorName . "</td>";
                                echo "<td>" . $mostPopularProductId . "</td>";
                                echo "<td>" . $type . "</td>";
                                echo "<td>" . $flavor . "</td>";
                                echo "</tr>";
                            }

                            echo "</tbody></table>";
                            mysqli_free_result($result);
                        } else {
                            echo "<p class='lead'><em>No distributors found.</em></p>";
                        }
                    } else {
                        echo "ERROR: Could not execute query to get distributors. " . mysqli_error($link);
                    }
                    
                    // Close connection
                    mysqli_close($link);
                    ?>
                </div>

</body>

</html>