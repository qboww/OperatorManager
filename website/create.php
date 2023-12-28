<?php
session_start();
include('database.php');

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
        <h1>Entity creation</h1>
        <p>Here you can add entities to db.</p>
    </div>

    <div class="container mt-4 mb-5">
        <div class="card">
            <div class="card-header">
                Operator
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
                    $role = filter_input(INPUT_POST, "role", FILTER_SANITIZE_SPECIAL_CHARS);

                    if (empty($login) || empty($firstname) || empty($lastname) || empty($positionID) || empty($password)) {
                        echo '<div class="alert alert-danger" role="alert">Please fill all the inputs</div>';
                    } else {
                        // Hash the password before storing it in the database
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                        $sql = "INSERT INTO operators (Login, FirstName, LastName, PositionID, Password, Role) VALUES ('$login', '$firstname', '$lastname', '$positionID', '$hashedPassword', '$role')";

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

                        <label class="mt-2 mb-2" for="role">Role</label>
                        <select class="form-control" id="role" name="role">
                            <?php foreach ($roles as $role) {
                                echo "<option value=\"$role\">$role</option>";
                            }
                            ?>
                        </select>

                        <?php
                        if ($_SESSION['role'] == 'admin') {
                            echo '<div class="d-flex justify-content-end">';
                            echo '<button type="submit" class="btn btn-primary mt-3" name="createOperator">Create</button>';
                        } else {
                            echo '<label class="font-weight-bold mt-3">Only admin can create operators from here*</button></div>';
                            echo '<div class="d-flex justify-content-end">';
                            echo '<button type="submit" class="btn btn-primary mt-3" name="createOperator" disabled>Create</button>';
                        }
                        ?>
                    </div>
            </div>
            </form>
        </div>
    </div>
    </div>

    <div class="container mt-4 mb-5">
        <div class="card">
            <div class="card-header">
                Software
            </div>
            <div class="card-body">
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["createSoftware"])) {

                    $softwareName = filter_input(INPUT_POST, "softwareName", FILTER_SANITIZE_SPECIAL_CHARS);

                    if (empty($softwareName)) {
                        echo '<div class="alert alert-danger" role="alert">Please fill all the inputs</div>';
                    } else {
                        $sql = "INSERT INTO software (softwareName) VALUES ('$softwareName')";
                        if (mysqli_query($conn, $sql)) {
                            echo '<div class="alert alert-success" role="alert">Data is sent</div>';
                        } else {
                            echo '<div class="alert alert-danger" role="alert">Error sending data</div>';
                        }
                    }
                }
                ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

                    <div class="form-group">
                        <label class="mb-2" for="softwareName">Software Name</label>
                        <input type="text" class="form-control" name="softwareName" placeholder="Enter software name">
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary mt-3" name="createSoftware">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container mt-4 mb-5">
        <div class="card">
            <div class="card-header">
                Workstation
            </div>
            <div class="card-body">
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["createWorkstation"])) {
                    $workstationName = filter_input(INPUT_POST, "workstationName", FILTER_SANITIZE_SPECIAL_CHARS);

                    if (empty($workstationName)) {
                        echo '<div class="alert alert-danger" role="alert">Please fill all the inputs</div>';
                    } else {
                        $sql = "INSERT INTO workstations (workstationName) VALUES ('$workstationName')";
                        if (mysqli_query($conn, $sql)) {
                            echo '<div class="alert alert-success" role="alert">Data is sent</div>';
                        } else {
                            echo '<div class="alert alert-danger" role="alert">Error sending data</div>';
                        }
                    }
                }
                ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label class="mb-2" for="workstationName">Workstation Name</label>
                        <input type="text" class="form-control" name="workstationName" placeholder="Enter workstation name">
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary mt-3" name="createWorkstation">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="container mt-4 mb-5">
        <div class="card">
            <div class="card-header">
                Server
            </div>
            <div class="card-body">
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["createServer"])) {
                    $serverName = filter_input(INPUT_POST, "serverName", FILTER_SANITIZE_SPECIAL_CHARS);

                    if (empty($serverName)) {
                        echo '<div class="alert alert-danger" role="alert">Please fill all the inputs</div>';
                    } else {
                        $sql = "INSERT INTO server (serverName) VALUES ('$serverName')";
                        if (mysqli_query($conn, $sql)) {
                            echo '<div class="alert alert-success" role="alert">Data is sent</div>';
                        } else {
                            echo '<div class="alert alert-danger" role="alert">Error sending data</div>';
                        }
                    }
                }
                ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label class="mb-2" for="serverName">Server Name</label>
                        <input type="text" class="form-control" name="serverName" placeholder="Enter server name">
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary mt-3" name="createServer">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container mt-4 mb-5">
        <div class="card">
            <div class="card-header">
                Position
            </div>
            <div class="card-body">
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["createPosition"])) {
                    $positionName = filter_input(INPUT_POST, "positionName", FILTER_SANITIZE_SPECIAL_CHARS);
                    $salaryUSD = filter_input(INPUT_POST, "salaryUSD", FILTER_SANITIZE_SPECIAL_CHARS);

                    if (empty($positionName) || empty($salaryUSD)) {
                        echo '<div class="alert alert-danger" role="alert">Please fill in both Position Name and Salary (USD)</div>';
                    } else {
                        $sql = "INSERT INTO positions (PositionName, SalaryUSD) VALUES ('$positionName', '$salaryUSD')";

                        if (mysqli_query($conn, $sql)) {
                            echo '<div class="alert alert-success" role="alert">Position created successfully</div>';
                        } else {
                            echo '<div class="alert alert-danger" role="alert">Error creating position</div>';
                        }
                    }
                }
                ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label class="mb-2" for="positionName">Position Name</label>
                        <input type="text" class="form-control" name="positionName" placeholder="Enter position name">
                    </div>
                    <div class="form-group">
                        <label class="mb-2 mt-2" for="salaryUSD">Salary (USD)</label>
                        <input type="text" class="form-control" name="salaryUSD" placeholder="Enter salary in USD">
                        
                        <?php
                        if ($_SESSION['role'] == 'admin') {
                            echo'</div><div class="d-flex justify-content-end">';
                            echo '<button type="submit" class="btn btn-primary mt-3" name="createPosition">Create</button>';
                        } else {
                            echo'<label class="font-weight-bold mt-3">Only admin can create positions*</label>';
                            echo'</div><div class="d-flex justify-content-end">';
                            echo '<button type="submit" class="btn btn-primary mt-3" name="createPosition" disabled>Create</button>';
                        }
                        ?>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="container mt-5">
        <h1>Keys setting</h1>
        <p>Here you can set relation keys to db.</p>
    </div>

    <div class="container mt-4 mb-5">
        <div class="card">
            <div class="card-header">
                Shift
            </div>
            <div class="card-body">
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["createShift"])) {
                    $operatorID = filter_input(INPUT_POST, "operatorID", FILTER_SANITIZE_NUMBER_INT);
                    $workstationID = filter_input(INPUT_POST, "workstationID", FILTER_SANITIZE_NUMBER_INT);
                    $dateStart = filter_input(INPUT_POST, "dateStart");
                    $dateEnd = filter_input(INPUT_POST, "dateEnd");
                    $softwareID = filter_input(INPUT_POST, "softwareID", FILTER_SANITIZE_NUMBER_INT);

                    if (empty($operatorID) || empty($workstationID) || empty($dateStart) || empty($dateEnd) || empty($softwareID)) {
                        echo '<div class="alert alert-danger" role="alert">Please fill all the inputs</div>';
                    } else {
                        $sql = "INSERT INTO shifts (OperatorID, WorkstationID, DateStart, DateEnd, SoftwareID) VALUES ('$operatorID', '$workstationID', '$dateStart', '$dateEnd', '$softwareID')";

                        if (mysqli_query($conn, $sql)) {
                            echo '<div class="alert alert-success" role="alert">Shift created successfully</div>';
                        } else {
                            $errorMessage = mysqli_error($conn);

                            if (strpos($errorMessage, 'Shift overlaps with existing shift for the same operator and workstation') !== false) {
                                echo '<div class="alert alert-danger" role="alert">Shift overlaps with existing shift for the same operator and workstation</div>';
                            } else {
                                echo '<div class="alert alert-danger" role="alert">Error creating shift</div>';
                            }
                        }
                    }
                }
                ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label class="mb-2" for="operatorID">Operator ID</label>
                        <select class="form-control" name="operatorID">
                            <?php
                            // Fetch operator data from the database and populate the dropdown
                            $operatorQuery = "SELECT OperatorID, FirstName, LastName FROM operators";
                            $operatorResult = mysqli_query($conn, $operatorQuery);

                            while ($row = mysqli_fetch_assoc($operatorResult)) {
                                echo '<option value="' . $row['OperatorID'] . '">' . $row['FirstName'] . ' ' . $row['LastName'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="mb-2 mt-2" for="workstationID">Workstation ID</label>
                        <select class="form-control" name="workstationID">
                            <?php
                            // Fetch workstation data from the database and populate the dropdown
                            $workstationQuery = "SELECT WorkstationID, WorkstationName FROM workstations";
                            $workstationResult = mysqli_query($conn, $workstationQuery);

                            while ($row = mysqli_fetch_assoc($workstationResult)) {
                                echo '<option value="' . $row['WorkstationID'] . '">' . $row['WorkstationName'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="mb-2 mt-2" for="dateStart">Start Date</label>
                        <input type="datetime-local" class="form-control" name="dateStart">
                    </div>
                    <div class="form-group">
                        <label class="mb-2 mt-2" for="dateEnd">End Date</label>
                        <input type="datetime-local" class="form-control" name="dateEnd">
                    </div>
                    <div class="form-group">
                        <label class="mb-2 mt-2" for="softwareID">Software ID</label>
                        <select class="form-control" name="softwareID">
                            <?php
                            // Fetch software data from the database and populate the dropdown
                            $softwareQuery = "SELECT SoftwareID, SoftwareName FROM software";
                            $softwareResult = mysqli_query($conn, $softwareQuery);

                            while ($row = mysqli_fetch_assoc($softwareResult)) {
                                echo '<option value="' . $row['SoftwareID'] . '">' . $row['SoftwareName'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary mt-3" name="createShift">Create Shift</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container mt-4 mb-5">
        <div class="card">
            <div class="card-header">
                Operator-Workstation keys
            </div>
            <div class="card-body">
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['setOperatorWorkstationKeys'])) {
                    $workstationId = filter_input(INPUT_POST, "workstationId", FILTER_SANITIZE_SPECIAL_CHARS);
                    $operatorId = filter_input(INPUT_POST, "operatorId", FILTER_SANITIZE_SPECIAL_CHARS);

                    if (!empty($workstationId) && !empty($operatorId)) {

                        $resultWorkstation = $conn->query("SELECT * FROM workstations WHERE WorkstationID = $workstationId");
                        $resultOperator = $conn->query("SELECT * FROM operators WHERE OperatorID = $operatorId");

                        if ($resultWorkstation->num_rows > 0 && $resultOperator->num_rows > 0) {
                            $resultCombination = $conn->query("SELECT * FROM operatorworkstations WHERE WorkstationID = $workstationId AND OperatorID = $operatorId");

                            if ($resultCombination->num_rows == 0) {
                                $conn->query("INSERT INTO operatorworkstations (WorkstationID, OperatorID) VALUES ('$workstationId', '$operatorId')");

                                echo '<div class="alert alert-success" role="alert">Keys set successfully</div>';
                            } else {
                                echo '<div class="alert alert-warning" role="alert">Combination already exists</div>';
                            }
                        } else {
                            echo '<div class="alert alert-danger" role="alert">One or both IDs do not exist</div>';
                        }
                    } else {
                        echo '<div class="alert alert-danger" role="alert">Please enter both Workstation ID and Operator ID</div>';
                    }
                }
                ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="mb-2" for="workstationId">Workstation ID</label>
                                <select class="form-control" name="workstationId">
                                    <?php
                                    // Fetch workstation data from the database and populate the dropdown
                                    $workstationQuery = "SELECT WorkstationID, WorkstationName FROM workstations";
                                    $workstationResult = mysqli_query($conn, $workstationQuery);

                                    while ($row = mysqli_fetch_assoc($workstationResult)) {
                                        echo '<option value="' . $row['WorkstationID'] . '">' . $row['WorkstationName'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="mb-2" for="operatorId">Operator ID</label>
                                <select class="form-control" name="operatorId">
                                    <?php
                                    // Fetch operator data from the database and populate the dropdown
                                    $operatorQuery = "SELECT OperatorID, FirstName, LastName FROM operators";
                                    $operatorResult = mysqli_query($conn, $operatorQuery);

                                    while ($row = mysqli_fetch_assoc($operatorResult)) {
                                        echo '<option value="' . $row['OperatorID'] . '">' . $row['FirstName'] . ' ' . $row['LastName'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <?php
                        if ($_SESSION['role'] == 'admin') {
                            echo '<div class="d-flex justify-content-end">';
                            echo '<button type="submit" class="btn btn-primary mt-3" name="setOperatorWorkstationKeys">Set</button>';
                        } else {
                            echo '<label class="font-weight-bold mt-3">Only admin can set operators relations*</button></div>';
                            echo '<div class="d-flex justify-content-end">';
                            echo '<button type="submit" class="btn btn-primary mt-3" name="setOperatorWorkstationKeys" disabled>Set</button>';
                        }
                        ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>


    <div class="container mt-4 mb-5">
        <div class="card">
            <div class="card-header">
                Workstation-Servers keys
            </div>
            <div class="card-body">
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['setWorkstationServersKeys'])) {
                    $workstationId = filter_input(INPUT_POST, "workstationId", FILTER_SANITIZE_SPECIAL_CHARS);
                    $serverId = filter_input(INPUT_POST, "serverId", FILTER_SANITIZE_SPECIAL_CHARS);

                    if (!empty($workstationId) && !empty($serverId)) {

                        $resultWorkstation = $conn->query("SELECT * FROM workstations WHERE WorkstationID = $workstationId");
                        $resultServer = $conn->query("SELECT * FROM servers WHERE ServerID = $serverId");

                        if ($resultWorkstation->num_rows > 0 && $resultServer->num_rows > 0) {
                            $resultCombination = $conn->query("SELECT * FROM workstationservers WHERE WorkstationID = $workstationId AND ServerID = $serverId");

                            if ($resultCombination->num_rows == 0) {
                                $conn->query("INSERT INTO workstationservers (WorkstationID, ServerID) VALUES ('$workstationId', '$serverId')");

                                echo '<div class="alert alert-success" role="alert">Keys set successfully</div>';
                            } else {
                                echo '<div class="alert alert-warning" role="alert">Combination already exists</div>';
                            }
                        } else {
                            echo '<div class="alert alert-danger" role="alert">One or both IDs do not exist</div>';
                        }
                    } else {
                        echo '<div class="alert alert-danger" role="alert">Please enter both Workstation ID and Server ID</div>';
                    }
                }
                ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="mb-2" for="workstationId">Workstation ID</label>
                                <select class="form-control" name="workstationId">
                                    <?php
                                    // Fetch workstation data from the database and populate the dropdown
                                    $workstationQuery = "SELECT WorkstationID, WorkstationName FROM workstations";
                                    $workstationResult = mysqli_query($conn, $workstationQuery);

                                    while ($row = mysqli_fetch_assoc($workstationResult)) {
                                        echo '<option value="' . $row['WorkstationID'] . '">' . $row['WorkstationName'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="mb-2" for="serverId">Server ID</label>
                                <select class="form-control" name="serverId">
                                    <?php
                                    // Fetch server data from the database and populate the dropdown
                                    $serverQuery = "SELECT ServerID, ServerName FROM servers";
                                    $serverResult = mysqli_query($conn, $serverQuery);

                                    while ($row = mysqli_fetch_assoc($serverResult)) {
                                        echo '<option value="' . $row['ServerID'] . '">' . $row['ServerName'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary mt-3" name="setWorkstationServersKeys">Set</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="container mt-4 mb-5">
        <div class="card">
            <div class="card-header">
                Workstation-Software keys
            </div>
            <div class="card-body">
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['setWorkstationSoftwareKeys'])) {
                    $workstationId = filter_input(INPUT_POST, "workstationId", FILTER_SANITIZE_SPECIAL_CHARS);
                    $softwareId = filter_input(INPUT_POST, "softwareId", FILTER_SANITIZE_SPECIAL_CHARS);

                    if (!empty($workstationId) && !empty($softwareId)) {

                        $resultWorkstation = $conn->query("SELECT * FROM workstations WHERE WorkstationID = $workstationId");
                        $resultSoftware = $conn->query("SELECT * FROM software WHERE SoftwareID = $softwareId");

                        if ($resultWorkstation->num_rows > 0 && $resultSoftware->num_rows > 0) {
                            $resultCombination = $conn->query("SELECT * FROM workstationsoftware WHERE WorkstationID = $workstationId AND SoftwareID = $softwareId");

                            if ($resultCombination->num_rows == 0) {
                                $conn->query("INSERT INTO workstationsoftware (WorkstationID, SoftwareID) VALUES ('$workstationId', '$softwareId')");

                                echo '<div class="alert alert-success" role="alert">Keys set successfully</div>';
                            } else {
                                echo '<div class="alert alert-warning" role="alert">Combination already exists</div>';
                            }
                        } else {
                            echo '<div class="alert alert-danger" role="alert">One or both IDs do not exist</div>';
                        }
                    } else {
                        echo '<div class="alert alert-danger" role="alert">Please enter both Workstation ID and Software ID</div>';
                    }
                }
                ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="mb-2" for="workstationId">Workstation ID</label>
                                <select class="form-control" name="workstationId">
                                    <?php
                                    // Fetch workstation data from the database and populate the dropdown
                                    $workstationQuery = "SELECT WorkstationID, WorkstationName FROM workstations";
                                    $workstationResult = mysqli_query($conn, $workstationQuery);

                                    while ($row = mysqli_fetch_assoc($workstationResult)) {
                                        echo '<option value="' . $row['WorkstationID'] . '">' . $row['WorkstationName'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="mb-2" for="softwareId">Software ID</label>
                                <select class="form-control" name="softwareId">
                                    <?php
                                    // Fetch software data from the database and populate the dropdown
                                    $softwareQuery = "SELECT SoftwareID, SoftwareName FROM software";
                                    $softwareResult = mysqli_query($conn, $softwareQuery);

                                    while ($row = mysqli_fetch_assoc($softwareResult)) {
                                        echo '<option value="' . $row['SoftwareID'] . '">' . $row['SoftwareName'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary mt-3" name="setWorkstationSoftwareKeys">Set</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


</body>

</html>