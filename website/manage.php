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
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js" integrity="sha384-oesi62hOLfzrys4LxRF63OJCXdXDipiYWBnvTl9Y9/TRlw5xlKIEHpNyvvDShgf/" crossorigin="anonymous"></script>
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
                echo '<a class="btn btn-info mr-2" href="account.php">Account</a></div>';
                echo '<div class="d-flex justify-content-end">';
                echo '<span class="navbar-text mr-3">Logged as: ' . $_SESSION['login'] . '</span>';
                echo '<span class="navbar-text mr-3">Role: ' . $_SESSION['role'] . '</span>';
                echo '<a class="btn btn-primary mr-3" href="logout.php">Logout</a></div>';
            } else if ($_SESSION['role'] == 'guest') {
                echo '<div class="d-flex justify-content-start">';
                echo '<a href="index.php" class="navbar-brand ml-3">OperatorManager</a>';
                echo '<a class="btn btn-info mr-2" href="account.php">Account</a></div>';;
                echo '<div class="d-flex justify-content-end">';
                echo '<span class="navbar-text mr-3">Logged as: ' . $_SESSION['login'] . '</span>';
                echo '<span class="navbar-text mr-3">Role: ' . $_SESSION['role'] . '</span>';
                echo '<a class="btn btn-primary mr-3" href="logout.php">Logout</a></div>';
            }
        } else {
            echo '<div class="d-flex justify-content-start">';
            echo '<a href="index.php" class="navbar-brand ml-3">OperatorManager</a>';
            echo '<div class="d-flex justify-content-end">';
            echo '<a class="btn btn-primary mr-3" href="register.php">Register</a>';
            echo '<a class="btn btn-primary mr-3" href="login.php">Login</a></div>';
        }
        ?>
        </div>
    </nav>

    <div class="container mt-5">
        <h1>Entity management</h1>
        <p>Here you can manage entities and relations from the database.</p>
    </div>

    <div class="container mt-4 mb-5">
        <div class="btn-group d-flex justify-content-start">
            <?php
            if (isset($_SESSION['role'])) {
                if ($_SESSION['role'] == 'operator') {
                }
            }
            ?>
            <button type="button" class="btn btn-secondary" onclick="loadTable('servers')">Servers</button>
            <button type="button" class="btn btn-secondary" onclick="loadTable('operators')">Operators</button>
            <button type="button" class="btn btn-secondary" onclick="loadTable('workstations')">Workstations</button>
            <button type="button" class="btn btn-secondary" onclick="loadTable('software')">Software</button>
            <button type="button" class="btn btn-secondary" onclick="loadTable('positions')">Positions</button>
            <button type="button" class="btn btn-secondary" onclick="loadTable('shifts')">Shifts</button>
            <button type="button" class="btn btn-secondary" onclick="loadTable('operatorworkstations')">Operator-Workstations</button>
            <button type="button" class="btn btn-secondary" onclick="loadTable('workstationservers')">Workstation-Servers</button>
            <button type="button" class="btn btn-secondary" onclick="loadTable('workstationsoftware')">Workstation-Software</button>
        </div>
    </div>
    </div>

    <div class="container mt-5" id="operatorTableContainer" style="display: block;">
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>FirstName</th>
                    <th>LastName</th>
                    <th>Position ID</th>
                    <th>Role</th>
                    <?php
                    if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
                        echo '<th>Login</th>';
                        echo '<th>Operation</th>';
                    }
                    ?>
                </tr>
            </thead>

            <tbody>
                <?php
                $sql = "SELECT * FROM operators";
                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                <td>" . $row["OperatorID"] . "</td>
                <td>" . $row["FirstName"] . "</td>
                <td>" . $row["LastName"] . "</td>
                <td>" . $row["PositionID"] . "</td>
                <td>" . $row["Role"] . "</td>";

                    if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
                        echo '<td>' . $row["Login"] . '</td>';
                        echo "<td><a class='btn btn-primary mr-2' href='edit-operator.php?OperatorID=" . $row["OperatorID"] . "'>Edit</a>";
                        echo "<a class='btn btn-danger' href='delete-operator.php?OperatorID=" . $row["OperatorID"] . "'>Delete</a></td>";
                    }

                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="container mt-5" id="serverTableContainer" style="display: none;">
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>ServerName</th>
                    <th>Inventory Number</th>
                    <?php
                    if (isset($_SESSION['role'])) {
                        if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'operator') {
                            echo "<th>Operation</th>";
                        }
                    }
                    ?>
                </tr>
            </thead>

            <tbody>
                <?php
                $sql = "SELECT * FROM servers";
                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                    <td>" . $row["ServerID"] . "</td>
                    <td>" . $row["ServerName"] . "</td>
                    <td>" . $row["inv_num"] . "</td>";

                    if (isset($_SESSION['role'])) {
                        if ($_SESSION['role'] == 'operator') {
                            echo "<td><a class='btn btn-primary mr-2' href='edit-server.php?ServerID=" . $row["ServerID"] . "'>Edit</a>";
                        }
                        if ($_SESSION['role'] == 'admin') {
                            echo "<td><a class='btn btn-primary mr-2' href='edit-server.php?ServerID=" . $row["ServerID"] . "'>Edit</a>";
                            echo "<a class='btn btn-danger' href='delete-server.php?ServerID=" . $row["ServerID"] . "'>Delete</a></td>";
                        }
                    }

                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="container mt-5" id="workstationTableContainer" style="display: none;">
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>WorkstationName</th>
                    <th>Inventory Number</th>
                    <?php
                    if (isset($_SESSION['role'])) {
                        if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'operator') {
                            echo "<th>Operation</th>";
                        }
                    }
                    ?>
                </tr>
            </thead>

            <tbody>
                <?php
                $sql = "SELECT * FROM workstations";
                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()) {

                    echo "<tr><td>" . $row["WorkstationID"] . "</td>
                    <td>" . $row["WorkstationName"] . "</td>
                    <td>" . $row["inv_num"] . "</td>";

                    if (isset($_SESSION['role'])) {
                        if ($_SESSION['role'] == 'operator') {
                            echo "<td><a class='btn btn-primary mr-2' href='edit-workstation.php?WorkstationID=" . $row["WorkstationID"] . "'>Edit</a>";
                        }
                        if ($_SESSION['role'] == 'admin') {
                            echo "<td><a class='btn btn-primary mr-2' href='edit-workstation.php?WorkstationID=" . $row["WorkstationID"] . "'>Edit</a>";
                            echo "<a class='btn btn-danger' href='delete-workstation.php?WorkstationID=" . $row["WorkstationID"] . "'>Delete</a></td>";
                        }
                    }

                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>


    <div class="container mt-5" id="softwareTableContainer" style="display: none;">
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Software Name</th>
                    <?php
                    if (isset($_SESSION['role'])) {
                        if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'operator') {
                            echo "<th>Operation</th>";
                        }
                    }
                    ?>
                </tr>
            </thead>

            <tbody>
                <?php
                $sql = "SELECT * FROM software";
                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                    <td>" . $row["SoftwareID"] . "</td>
                    <td>" . $row["SoftwareName"] . "</td>";

                    if (isset($_SESSION['role'])) {
                        if ($_SESSION['role'] == 'operator') {
                            echo "<td><a class='btn btn-primary mr-2' href='edit-software.php?SoftwareID=" . $row["SoftwareID"] . "'>Edit</a>";
                        }
                        if ($_SESSION['role'] == 'admin') {
                            echo "<td><a class='btn btn-primary mr-2' href='edit-software.php?SoftwareID=" . $row["SoftwareID"] . "'>Edit</a>";
                            echo "<a class='btn btn-danger' href='delete-software.php?SoftwareID=" . $row["SoftwareID"] . "'>Delete</a></td>";
                        }
                    }
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="container mt-5" id="positionTableContainer" style="display: none;">
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Position Name</th>
                    <th>Salary USD</th>
                    <?php
                    if (isset($_SESSION['role'])) {
                        if ($_SESSION['role'] == 'admin') {
                            echo "<th>Operation</th>";
                        }
                    }
                    ?>
                </tr>
            </thead>

            <tbody>
                <?php
                $sql = "SELECT * FROM positions";
                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                    <td>" . $row["PositionID"] . "</td>
                    <td>" . $row["PositionName"] . "</td>
                    <td>" . $row["SalaryUSD"] . "</td>";

                    if (isset($_SESSION['role'])) {
                        if ($_SESSION['role'] == 'admin') {
                            echo "<td><a class='btn btn-primary mr-2' href='edit-position.php?PositionID=" . $row["PositionID"] . "'>Edit</a>";
                            echo "<a class='btn btn-danger' href='delete-position.php?PositionID=" . $row["PositionID"] . "'>Delete</a></td>";
                        }
                    }
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="container mt-5" id="shiftsTableContainer" style="display: none;">
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th>OperatorID</th>
                    <th>WorkstationID</th>
                    <th>DateStart</th>
                    <th>DateEnd</th>
                    <th>SoftwareID</th>
                    <?php
                    if (isset($_SESSION['role'])) {
                        if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'operator') {
                            echo "<th>Operation</th>";
                        }
                    }
                    ?>
                </tr>
            </thead>

            <tbody>
                <?php
                $sql = "SELECT * FROM shifts";
                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>" . $row["OperatorID"] . "</td>
                        <td>" . $row["WorkstationID"] . "</td>
                        <td>" . $row["DateStart"] . "</td>
                        <td>" . $row["DateEnd"] . "</td>
                        <td>" . $row["SoftwareID"] . "</td>";
                
                    if (isset($_SESSION['role'])) {
                        if ($_SESSION['role'] == 'operator') {
                            echo "<td><a class='btn btn-primary mr-2' href='edit-shifts.php?OperatorID=" . $row["OperatorID"] . "'>Edit</a></td>";
                        }
                        if ($_SESSION['role'] == 'admin') {
                            echo "<td><a class='btn btn-primary mr-2' href='edit-shifts.php?OperatorID=" . $row["OperatorID"] . "'>Edit</a>";
                            echo "<a class='btn btn-danger' href='delete-shifts.php?OperatorID=" . $row["OperatorID"] . "&WorkstationID=" . $row["WorkstationID"] . "&SoftwareID=" . $row["SoftwareID"] . "&DateStart=" . urlencode($row["DateStart"]) . "&DateEnd=" . urlencode($row["DateEnd"]) . "'>Delete</a></td>";
                        }
                    }
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>


    <div class="container mt-5" id="operatorworkstationsTableContainer" style="display: none;">
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th>OperatorID</th>
                    <th>WorkstationID</th>
                    <th>
                        <?php
                        if (isset($_SESSION['role'])) {
                            if ($_SESSION['role'] == 'admin') {
                                echo "Operation";
                            }
                        }
                        ?>
                    </th>
                </tr>
            </thead>

            <tbody>
                <?php
                $sql = "SELECT * FROM operatorworkstations";
                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>" . $row["OperatorID"] . "</td>
                        <td>" . $row["WorkstationID"] . "</td><td>";
                    if (isset($_SESSION['role'])) {
                        if ($_SESSION['role'] == 'admin') {
                            echo "<a class='btn btn-danger' href='delete-operatorworkstations.php'>Delete</a></td></tr>";
                        }
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="container mt-5" id="workstationserversTableContainer" style="display: none;">
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th>WorkstationID</th>
                    <th>ServerID</th>
                    <th>
                        <?php
                        if (isset($_SESSION['role'])) {
                            if ($_SESSION['role'] == 'admin') {
                                echo "Operation";
                            }
                        }
                        ?>
                    </th>
                </tr>
            </thead>

            <tbody>
                <?php
                $sql = "SELECT * FROM workstationservers";
                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>" . $row["WorkstationID"] . "</td>
                        <td>" . $row["ServerID"] . "</td><td>";
                    if (isset($_SESSION['role'])) {
                        if ($_SESSION['role'] == 'admin') {
                            echo "<a class='btn btn-danger' href='delete-workstationservers.php'>Delete</a></td></tr>";
                        }
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="container mt-5" id="workstationsoftwareTableContainer" style="display: none;">
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th>WorkstationID</th>
                    <th>SoftwareID</th>
                    <th>
                        <?php
                        if (isset($_SESSION['role'])) {
                            if ($_SESSION['role'] == 'admin') {
                                echo "Operation";
                            }
                        }
                        ?>
                    </th>
                </tr>
            </thead>

            <tbody>
                <?php
                $sql = "SELECT * FROM workstationsoftware";
                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>" . $row["WorkstationID"] . "</td>
                        <td>" . $row["SoftwareID"] . "</td><td>";
                    if (isset($_SESSION['role'])) {
                        if ($_SESSION['role'] == 'admin') {
                            echo "<a class='btn btn-danger' href='delete-workstationsoftware.php'>Delete</a></td></tr>";
                        }
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var role = "<?php echo isset($_SESSION['role']) ? $_SESSION['role'] : ''; ?>";

            if (role === 'operator') {
                var adminButtons = document.querySelectorAll('.admin-only');
                adminButtons.forEach(function(button) {
                    button.style.display = 'none';
                });
            }

            loadTable('servers');
        });

        function loadTable(table) {
            document.getElementById('operatorTableContainer').style.display = 'none';
            document.getElementById('serverTableContainer').style.display = 'none';
            document.getElementById('workstationTableContainer').style.display = 'none';
            document.getElementById('operatorworkstationsTableContainer').style.display = 'none';
            document.getElementById('softwareTableContainer').style.display = 'none';
            document.getElementById('positionTableContainer').style.display = 'none';
            document.getElementById('shiftsTableContainer').style.display = 'none';
            document.getElementById('workstationsoftwareTableContainer').style.display = 'none';
            document.getElementById('workstationserversTableContainer').style.display = 'none';

            if (table === 'operators') {
                document.getElementById('operatorTableContainer').style.display = 'block';
            } else if (table === 'servers') {
                document.getElementById('serverTableContainer').style.display = 'block';
            } else if (table === 'workstations') {
                document.getElementById('workstationTableContainer').style.display = 'block';
            } else if (table === 'operatorworkstations') {
                document.getElementById('operatorworkstationsTableContainer').style.display = 'block';
            } else if (table === 'software') {
                document.getElementById('softwareTableContainer').style.display = 'block';
            } else if (table === 'positions') {
                document.getElementById('positionTableContainer').style.display = 'block';
            } else if (table === 'shifts') {
                document.getElementById('shiftsTableContainer').style.display = 'block';
            } else if (table === 'workstationsoftware') {
                document.getElementById('workstationsoftwareTableContainer').style.display = 'block';
            } else if (table === 'workstationservers') {
                document.getElementById('workstationserversTableContainer').style.display = 'block';
            }
        }
    </script>
</body>

</html>