<?php
session_start();
include('database.php');

$server = array("OperatorID" => "", "WorkstationID" => "", "DateStart" => "", "DateEnd" => "", "SoftwareName" => "");

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["OperatorID"])) {
    $operatorID = $_GET["OperatorID"];

    $query = "SELECT * FROM shifts WHERE OperatorID = '$operatorID'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $server = $result->fetch_assoc();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $operatorID = $_POST["operatorID"];

    if (isset($_POST["cancel"])) {
        header("Location: index.php");
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OperatorManager</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <nav class="navbar navbar-dark bg-dark">
        <?php
        if (isset($_SESSION['role'])) {
            if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'operator') {
                echo '<div class="d-flex justify-content-start">';
                echo '<a href="index.php" class="navbar-brand ml-3">OperatorManager</a>';
                echo '<a class="btn btn-secondary mr-2" href="create.php">Create</a>';
                echo '<a class="btn btn-secondary mr-2" href="manage.php">Manage</a>';
                echo '<a class="btn btn-info mr-2" href="account.php">Account</a></div>';
                echo '<div class="d-flex justify-content-end">';
                echo '<span class="navbar-text mr-3">Logged as: ' . $_SESSION['login'] . '</span>';
                echo '<span class="navbar-text mr-3">Role: ' . $_SESSION['role'] . '</span>';
                echo '<a class="btn btn-primary mr-3" href="logout.php">Logout</a></div>';
            }
        } else {
            echo '<div class="d-flex justify-content-start">';
            echo '<a href="index.php" class="navbar-brand ml-3">OperatorManager</a>';
            echo '<a class="btn btn-secondary mr-3" href="manage.php">Manage</a></div>';
            echo '<div class="d-flex justify-content-end">';
            echo '<a class="btn btn-primary mr-3" href="register.php">Register</a>';
            echo '<a class="btn btn-primary mr-3" href="login.php">Login</a></div>';
        }
        ?>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Edit Shift
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="form-group">
                                <?php
                                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                    $operatorID = $_POST["operatorid"];

                                    if (isset($_POST["save"])) {
                                        $workstationID = $_POST["workstationid"];
                                        $dateStart = $_POST["datestart"];
                                        $dateEnd = $_POST["dateend"];
                                        $softwareID = $_POST["softwareid"];

                                        $sql = "UPDATE shifts " .
                                            "SET WorkstationID = '$workstationID', " .
                                            "DateStart = '$dateStart', " .
                                            "DateEnd = '$dateEnd', " .
                                            "SoftwareID = '$softwareID' " .
                                            "WHERE OperatorID = '$operatorID'";

                                        if ($conn->query($sql) === TRUE) {
                                            echo '<div class="alert alert-success" role="alert">Record updated successfully</div>';
                                        } else {
                                            $errorMessage = mysqli_error($conn);
                
                                            if (strpos($errorMessage, 'DateStart must be before DateEnd') !== false) {
                                                echo '<div class="alert alert-danger" role="alert">DateStart must be before DateEnd</div>';
                                            } else {
                                                echo '<div class="alert alert-danger" role="alert">Error editing shift</div>';
                                            }
                                        }
                                    }
                                }
                                ?>
                                <label for="operatorid">OperatorID</label>
                                <input type="text" class="form-control" id="operatorid" name="operatorid" placeholder="Enter operator id" value="<?php echo $server["OperatorID"]; ?>" readonly>

                                <label class="mt-2" for="workstationid">Workstation ID</label>
                                <input type="text" class="form-control" id="workstationid" name="workstationid" placeholder="Enter workstation id" value="<?php echo $server["WorkstationID"]; ?>" required>

                                <label class="mt-2" for="datestart">Date Start</label>
                                <?php
                                $valueStart = date("Y-m-d\TH:i", strtotime($server["DateStart"]));
                                ?>
                                <input type="datetime-local" class="form-control" id="datestart" name="datestart" value="<?php echo $valueStart; ?>" required>

                                <label class="mt-2" for="dateend">Date End</label>
                                <?php
                                $valueEnd = date("Y-m-d\TH:i", strtotime($server["DateEnd"]));
                                ?>
                                <input type="datetime-local" class="form-control" id="dateend" name="dateend" value="<?php echo $valueEnd; ?>" required>

                                <label class="mt-2" for="softwareid">Software ID</label>
                                <input type="text" class="form-control" id="softwareid" name="softwareid" placeholder="Enter software id" value="<?php echo $server["SoftwareID"]; ?>" required>

                                <div class="d-flex justify-content-center">
                                    <button type="submit" class="btn btn-danger mt-3 mr-3" name="cancel">Cancel</button>
                                    <button type="submit" class="btn btn-primary mt-3" name="save" id="save">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>