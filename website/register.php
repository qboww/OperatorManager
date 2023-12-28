<?php
include("database.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OperatorManager</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js" integrity="sha384-oesi62hOLfzrys4LxRF63OJCXdXDipiYWBnvTl9Y9/TRlw5xlKIEHpNyvvDShgf/" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styles.css">
</head>

</html>

<nav class="navbar navbar-dark bg-dark">
    <a href="index.php" class="navbar-brand  ml-3">OperatorManager</a>
    <div class="d-flex justify-content-end">
        <a class="btn btn-primary mr-3" href="login.php">Login</a>
    </div>
</nav>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Register page
                    </div>
                    <div class="card-body">
                        <?php
                        $result = mysqli_query($conn, "SELECT DISTINCT Role FROM operators UNION SELECT 'admin' AS Role UNION SELECT 'operator' AS Role UNION SELECT 'guest' AS Role");
                        $roles = [];
                        while ($row = mysqli_fetch_assoc($result)) {
                            $roles[] = $row['Role'];
                        }
                        ?>

                        <?php
                        include("database.php");
                        session_start();

                        $loginStatus = "";

                        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["createOperator"])) {
                            $login = filter_input(INPUT_POST, "login", FILTER_SANITIZE_SPECIAL_CHARS);
                            $firstname = filter_input(INPUT_POST, "firstname", FILTER_SANITIZE_SPECIAL_CHARS);
                            $lastname = filter_input(INPUT_POST, "lastname", FILTER_SANITIZE_SPECIAL_CHARS);
                            $positionID = filter_input(INPUT_POST, "position", FILTER_SANITIZE_SPECIAL_CHARS);
                            $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);

                            if (empty($login) || empty($firstname) || empty($lastname) || empty($positionID) || empty($password)) {
                                echo '<div class="alert alert-danger" role="alert">Please fill all the inputs</div>';
                            } else {
                                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                                $sql = "INSERT INTO operators (Login, FirstName, LastName, PositionID, Password) VALUES ('$login', '$firstname', '$lastname', '$positionID', '$hashedPassword')";

                                if (mysqli_query($conn, $sql)) {
                                    echo '<div class="alert alert-success" role="alert">Data is sent</div>';
                                } else {
                                    $errorMessage = mysqli_error($conn);

                                    if (strpos($errorMessage, 'Duplicate login is not allowed') !== false) {
                                        echo '<div class="alert alert-danger" role="alert">Duplicate login is not allowed</div>';
                                    } elseif (strpos($errorMessage, 'Duplicate first name, and last name combination is not allowed') !== false) {
                                        echo '<div class="alert alert-danger" role="alert">Duplicate first name, and last name combination is not allowed</div>';
                                    } elseif (strpos($errorMessage, 'Password length must be between 5 and 20 characters') !== false) {
                                        echo '<div class="alert alert-danger" role="alert">Password length must be between 5 and 20 characters</div>';
                                    } elseif (strpos($errorMessage, 'Login length must be between 5 and 60 characters') !== false) {
                                        echo '<div class="alert alert-danger" role="alert">Login length must be between 5 and 60 characters</div>';
                                    } else {
                                        echo '<div class="alert alert-danger" role="alert">Error sending data</div>';
                                    }
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

                        <?php
                        $positions = [];
                        $sqlPositions = "SELECT * FROM positions";
                        $resultPositions = $conn->query($sqlPositions);

                        if ($resultPositions->num_rows > 0) {
                            while ($row = $resultPositions->fetch_assoc()) {
                                $positions[] = $row;
                            }
                        } ?>


                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

                            <div class="form-group">
                                <input type="hidden" name="operatorID" value="<?php echo $operator["OperatorID"]; ?>">

                                <label class="" for="login">Login</label>
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
                                    <button type="submit" class="btn btn-primary mt-3" name="createOperator">Register</button>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php
        mysqli_close($conn);
        ?>
</body>

</html>