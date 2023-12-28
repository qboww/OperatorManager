<?php
session_start();
include('database.php');

$server = array("OperatorID" => "");

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["OperatorID"])) {
    $operatorID = $_GET["OperatorID"];

    $query = "SELECT * FROM shifts WHERE OperatorID = '$operatorID'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $server = $result->fetch_assoc();
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
                        Delete Shift
                    </div>
                    <div class="card-body">
                        <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="form-group">
                                <?php
                                if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["delete"])) {
                                    $operatorID = $_GET["operatorid"];
                                    $workstationID = $_GET["workstationid"];
                                    $softwareID = $_GET["softwareid"];
                                    $dateStart = urldecode($_GET["datestart"]);
                                    $dateEnd = urldecode($_GET["dateend"]);
                                
                                    $formattedDateStart = date("Y-m-d H:i:s", strtotime($dateStart));
                                    $formattedDateEnd = date("Y-m-d H:i:s", strtotime($dateEnd));
                                
                                    $sql = "DELETE FROM shifts WHERE OperatorID = '$operatorID' AND WorkstationID = '$workstationID' AND SoftwareID = '$softwareID' AND DateStart = '$formattedDateStart' AND DateEnd = '$formattedDateEnd'";
                                
                                    if ($conn->query($sql) === TRUE) {
                                        echo '<div class="alert alert-success" role="alert">Record deleted successfully</div>';
                                    } else {
                                        echo "Error deleting record: " . $conn->error;
                                    }
                                }
                                ?>

                                <label for="operatorid">OperatorID</label>
                                <input type="text" class="form-control" id="operatorid" name="operatorid" placeholder="Enter operator id" value="<?php echo $server["OperatorID"]; ?>" readonly>

                                <label class="mt-2" for="workstationid">Workstation ID</label>
                                <input type="text" class="form-control" id="workstationid" name="workstationid" placeholder="Enter workstation id" value="<?php echo $server["WorkstationID"]; ?>" readonly>

                                <label class="mt-2" for="datestart">Date Start</label>
                                <?php
                                $valueStart = date("Y-m-d\TH:i", strtotime($server["DateStart"]));
                                ?>
                                <input type="datetime-local" class="form-control" id="datestart" name="datestart" value="<?php echo $valueStart; ?>" readonly>

                                <label class="mt-2" for="dateend">Date End</label>
                                <?php
                                $valueEnd = date("Y-m-d\TH:i", strtotime($server["DateEnd"]));
                                ?>
                                <input type="datetime-local" class="form-control" id="dateend" name="dateend" value="<?php echo $valueEnd; ?>" readonly>

                                <label class="mt-2" for="softwareid">Software ID</label>
                                <input type="text" class="form-control" id="softwareid" name="softwareid" placeholder="Enter software id" value="<?php echo $server["SoftwareID"]; ?>" readonly>

                                <div class="d-flex justify-content-center">
                                    <button type="submit" class="btn btn-danger mt-3" name="delete" id="delete">Delete</button>
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