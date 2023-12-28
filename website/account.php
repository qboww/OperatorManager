<?php
session_start();
include('database.php');

if (isset($_SESSION['login'])) {
    $login = $_SESSION['login'];
    $sql = "SELECT * FROM operators WHERE Login = '$login'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $operator = $result->fetch_assoc();
    } else {
        echo "Operator data not found for user: $login";
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
            } else if ($_SESSION['role'] == 'guest') {
                echo '<div class="d-flex justify-content-start">';
                echo '<a href="index.php" class="navbar-brand ml-3">OperatorManager</a>';
                echo '<a class="btn btn-secondary mr-2" href="manage.php">Manage</a>';
                echo '<a class="btn btn-info mr-2" href="account.php">Account</a></div>';;
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
                        Account managament
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="form-group">
                                <?php
                                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                    $operatorID = $_POST["operatorID"];

                                    if (isset($_POST["save"])) {
                                        $login = $_POST["login"];
                                        $firstname = $_POST["firstname"];
                                        $lastname = $_POST["lastname"];
                                        $positionID = $_POST["position"];
                                        $password = $_POST["password"];

                                        $sql = "UPDATE operators " .
                                            "SET Login = '$login', FirstName = '$firstname', LastName = '$lastname', PositionID = '$positionID', Password = '$password' " .
                                            "WHERE OperatorID = '$operatorID'";

                                        if ($conn->query($sql) === TRUE) {
                                            echo '<div class="alert alert-success" role="alert">Record updated successfully</div>';
                                        } else {
                                            echo "Error updating record: " . $conn->error;
                                        }
                                    }
                                }
                                ?>

                                <?php
                                $positions = [];
                                $sqlPositions = "SELECT * FROM positions";
                                $resultPositions = $conn->query($sqlPositions);

                                if ($resultPositions->num_rows > 0) {
                                    while ($row = $resultPositions->fetch_assoc()) {
                                        $positions[] = $row;
                                    }
                                } ?>

                                <input type="hidden" name="operatorID" value="<?php echo $operator["OperatorID"]; ?>">

                                <label class="mt-3" for="login">Login</label>
                                <input type="text" class="form-control" id="login" name="login" placeholder="Enter your login" value="<?php echo $operator["Login"]; ?>" required>

                                <label class="mt-3" for="firstname">FirstName</label>
                                <input type="text" class="form-control" id="firstname" name="firstname" placeholder="Enter your firstname" value="<?php echo $operator["FirstName"]; ?>" required>

                                <label class="mt-3" for="lastname">LastName</label>
                                <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Enter your lastname" value="<?php echo $operator["LastName"]; ?>" required>

                                <label class="mt-3" for="position">Position</label>
                                <select class="form-control" id="position" name="position" required>
                                    <?php foreach ($positions as $positionOption) : ?>
                                        <option value="<?php echo $positionOption["PositionID"]; ?>" <?php echo ($operator["PositionID"] == $positionOption["PositionID"]) ? "selected" : ""; ?>>
                                            <?php echo $positionOption["PositionName"]; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <label class="mt-3" for="password">Password</label>
                                <input type="text" class="form-control" id="password" name="password" placeholder="Enter your password" value="<?php echo $operator["Password"]; ?>" required>

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