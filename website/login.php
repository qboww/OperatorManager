<?php
include("database.php");
session_start();

$loginStatus = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST["login"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM operators WHERE Login = '$login'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);

            if (password_verify($password, $row['Password'])) {
                $_SESSION['login'] = $row['Login'];
                $_SESSION['role'] = $row['Role'];
                $_SESSION['operatorid'] = $row["OperatorID"];

                header("Location: index.php");
                exit();
            } else {
                $loginStatus = '<div class="alert alert-danger" role="alert">Invalid login or password</div>';
            }
        } else {
            $loginStatus = '<div class="alert alert-danger" role="alert">Invalid login or password</div>';
        }
    } else {
        $loginStatus = '<div class="alert alert-danger" role="alert">Error: ' . mysqli_error($conn) . '</div>';
    }

    mysqli_close($conn);
}
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

<nav class="navbar navbar-dark bg-dark">
    <a href="index.php" class="navbar-brand ml-3">OperatorManager</a>
    <div class="d-flex justify-content-end">
        <?php
        if (isset($_SESSION['login'])) {
            echo '<span class="navbar-text mr-3">Welcome, ' . $_SESSION['login'] . '!</span>';
            echo '<a class="btn btn-primary mr-3" href="logout.php">Logout</a>';
        } else {
            echo '<a class="btn btn-primary mr-3" href="register.php">Register</a>';
        }
        ?>
    </div>
</nav>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Login page
                    </div>
                    <div class="card-body">
                        <?php echo $loginStatus; ?>

                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="form-group">
                                <label for="username">Login</label>
                                <input type="text" class="form-control" id="login" name="login" placeholder="Enter your login" required>
                            </div>
                            <div class="form-group mt-3">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary mt-3">Log in</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>