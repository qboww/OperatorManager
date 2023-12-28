<?php
session_start();
include('database.php');
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
                        Delete Workstation-Software relation
                    </div>
                    <div class="card-body">
                        <?php
                        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete"])) {
                            $workstationID = $_POST["workstationID"];
                            $softwareID = $_POST["softwareID"];

                            if (!empty($workstationID) && !empty($softwareID)) {
                                if ($conn) {
                                    $checkQuery = "SELECT * FROM workstationsoftware WHERE WorkstationID = $workstationID AND SoftwareID = $softwareID";
                                    $result = $conn->query($checkQuery);

                                    if ($result->num_rows > 0) {
                                        $deleteQuery = "DELETE FROM workstationsoftware WHERE WorkstationID = $workstationID AND SoftwareID = $softwareID";

                                        if ($conn->query($deleteQuery) === TRUE) {
                                            echo '<div class="alert alert-success" role="alert">Record deleted successfully.</div>';
                                        } else {
                                            echo '<div class="alert alert-danger" role="alert">Error deleting record: ' . $conn->error . '</div>';
                                        }
                                    } else {
                                        echo '<div class="alert alert-danger" role="alert">Record with given Workstation ID and Software ID does not exist.</div>';
                                    }
                                }
                            } else {
                                echo '<div class="alert alert-danger" role="alert">Both Workstation ID and Software ID are required.</div>';
                            }
                        }
                        ?>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="form-group">
                                <label>Workstation ID</label>
                                <input type="text" class="form-control" id="workstationID" name="workstationID" placeholder="Enter workstation id">

                                <label class="mt-2">Software ID</label>
                                <input type="text" class="form-control" id="softwareID" name="softwareID" placeholder="Enter software id">

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