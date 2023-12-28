<?php
session_start();
include('database.php');

$server = array("ServerID" => "", "ServerName" => "");

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["ServerID"])) {
    $serverID = $_GET["ServerID"];

    $query = "SELECT * FROM servers WHERE ServerID = '$serverID'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $server = $result->fetch_assoc();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $serverID = $_POST["serverID"];

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
                        Edit Server
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="form-group">
                                <?php
                                if (isset($_POST["delete"])) {
                                    $checkQuery = "SELECT COUNT(*) as count FROM workstationservers WHERE ServerID = '$serverID'";
                                    $checkResult = $conn->query($checkQuery);
                                    $rowCount = $checkResult->fetch_assoc()["count"];

                                    if ($rowCount > 0) {
                                        $deleteRelatedQuery = "DELETE FROM workstationservers WHERE ServerID = '$serverID'";
                                        if ($conn->query($deleteRelatedQuery) === TRUE) {
                                            $deleteServerQuery = "DELETE FROM servers WHERE ServerID = '$serverID'";
                                            if ($conn->query($deleteServerQuery) === TRUE) {
                                                echo '<div class="alert alert-success" role="alert">Record deleted successfully</div>';
                                            } else {
                                                echo "Error deleting server record: " . $conn->error;
                                            }
                                        } else {
                                            echo "Error deleting related records: " . $conn->error;
                                        }
                                    } else {
                                        $deleteServerQuery = "DELETE FROM servers WHERE ServerID = '$serverID'";
                                        if ($conn->query($deleteServerQuery) === TRUE) {
                                            echo '<div class="alert alert-success" role="alert">Record deleted successfully</div>';
                                        } else {
                                            echo "Error deleting server record: " . $conn->error;
                                        }
                                    }
                                }
                                ?>
                                <input type="hidden" name="serverID" value="<?php echo $server["ServerID"]; ?>">

                                <label for="serverName">Server Name</label>
                                <input type="text" class="form-control" id="serverName" name="serverName" placeholder="Enter server name" value="<?php echo $server["ServerName"]; ?>" disabled>

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