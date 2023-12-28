<?php
session_start();
include('database.php');

$queryDatabaseSize = "SELECT SUM(data_length + index_length) AS DatabaseSize FROM information_schema.tables WHERE table_schema = DATABASE()";
$resultDatabaseSize = mysqli_query($conn, $queryDatabaseSize);
$databaseSize = mysqli_fetch_assoc($resultDatabaseSize);

$queryDatabaseInfo = "SELECT DATABASE() AS DatabaseName";
$resultDatabaseInfo = mysqli_query($conn, $queryDatabaseInfo);
$databaseInfo = mysqli_fetch_assoc($resultDatabaseInfo);

$queryTableCount = "SELECT COUNT(*) AS TableCount FROM information_schema.tables WHERE table_schema = DATABASE()";
$resultTableCount = mysqli_query($conn, $queryTableCount);
$tableCount = mysqli_fetch_assoc($resultTableCount);

$queryOperatorCount = "SELECT COUNT(*) AS OperatorCount FROM workstations";
$resultOperatorCount = mysqli_query($conn, $queryOperatorCount);
$operatorCount = mysqli_fetch_assoc($resultOperatorCount);

$queryServerCount = "SELECT COUNT(*) AS ServerCount FROM servers";
$resultServerCount = mysqli_query($conn, $queryServerCount);
$serverCount = mysqli_fetch_assoc($resultServerCount);

if ($conn) {
  $databaseStatus = "Online";
} else {
  $databaseStatus = "Offline";
}
?>

<?php
// Function to get MySQL version using stored procedure
function getMySQLVersion()
{
  global $conn;

  // Execute stored procedure to get MySQL version
  $result = mysqli_query($conn, "CALL GetMySQLVersion(@mysqlVersion)");

  if (!$result) {
    return "N/A";
  }

  // Fetch the result from the user-defined variable
  $versionResult = mysqli_query($conn, "SELECT @mysqlVersion AS MySQLVersion");
  $row = mysqli_fetch_assoc($versionResult);

  return $row['MySQLVersion'];
}
?>

<?php
function getTotalShiftTimeForAll()
{
  global $conn;

  $query = "SELECT SUM(TIMESTAMPDIFF(SECOND, DateStart, DateEnd)) AS TotalShiftTime FROM shifts";
  $result = mysqli_query($conn, $query);

  if (!$result) {
    return "N/A";
  }

  $row = mysqli_fetch_assoc($result);
  mysqli_free_result($result);

  $totalShiftTimeInSeconds = $row['TotalShiftTime'];
  $totalShiftTimeInHours = floor($totalShiftTimeInSeconds / 3600); // 1 hour = 3600 seconds

  return $totalShiftTimeInHours;
}
?>

<?php
function formatBytes($bytes, $precision = 2)
{
  $units = array('B', 'KB', 'MB', 'GB', 'TB');

  $bytes = max($bytes, 0);
  $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
  $pow = min($pow, count($units) - 1);

  $bytes /= (1 << (10 * $pow));

  return round($bytes, $precision) . ' ' . $units[$pow];
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

<body>
  <nav class="navbar navbar-dark bg-dark">
    <?php
    if (isset($_SESSION['role'])) {
      if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'operator') {
        echo '<div class="d-flex justify-content-start">';
        echo '<a href="index.php" class="navbar-brand ml-3">OperatorManager</a>';
        echo '<a class="btn btn-secondary mr-2" href="create.php">Create</a>';
        echo '<a class="btn btn-secondary mr-2" href="manage.php">Manage</a>';
        echo '<a class="btn btn-info mr-2" href="account.php">Account</a></div>';;
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
      echo '<a href="index.php" class="navbar-brand ml-3">OperatorManager</a></div>';
      echo '<div class="d-flex justify-content-end">';
      echo '<a class="btn btn-primary mr-3" href="register.php">Register</a>';
      echo '<a class="btn btn-primary mr-3" href="login.php">Login</a></div>';
    }
    ?>
  </nav>

  <div class="container mt-5">
    <div class="jumbotron">
      <h1 class="display-5">Operator management system</h1>
      <p class="lead">Here you are able to work with our database</p>
      <hr>
    </div>
  </div>

  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card <?php echo ($databaseStatus == "Online") ? 'border-success' : 'border-danger'; ?>">
          <div class="card-header">
            Database Statistics
          </div>
          <div class="card-body">
            <p>Database Status:
              <?php
              if ($databaseStatus == "Online") {
                echo '<span class="badge bg-success">' . $databaseStatus . '</span>';
              } else {
                echo '<span class="badge bg-danger">' . $databaseStatus . '</span>';
              }
              ?>
            </p>
            <p>Database Size: <?php echo formatBytes($databaseSize['DatabaseSize']); ?></p>
            <p>MySQL Version: <?php echo getMySQLVersion(); ?></p>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            Additional Statistics
          </div>
          <div class="card-body">
            <p>Total Operators Workload: <?php echo getTotalShiftTimeForAll(); ?> hours</p>
            <p>Workstations amount: <?php echo $operatorCount['OperatorCount']; ?></p>
            <p>Servers amount: <?php echo $serverCount['ServerCount']; ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="container mt-5">
    <div class="row">
      <div class="col-md-6">
        <h4 class="lead">Roles population</h4>
        <canvas id="userRolesChart"></canvas>
      </div>

      <div class="col-md-6">
        <h4 class="lead">Average Salary</h4>
        <canvas id="averageSalaryChart"></canvas>
      </div>

      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      <script>
        <?php
        $query = "SELECT Role, COUNT(*) AS Count FROM operators GROUP BY Role";
        $result = mysqli_query($conn, $query);
        $roles = [];
        $count = [];

        while ($row = mysqli_fetch_assoc($result)) {
          $roles[] = $row['Role'];
          $count[] = $row['Count'];
        }

        $userRolesChartData = [
          'labels' => $roles,
          'datasets' => [
            [
              'label' => 'Number of Users',
              'data' => $count,
              'backgroundColor' => [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
              ],
              'borderColor' => [
                'rgba(255,99,132,1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
              ],
              'borderWidth' => 1,
            ],
          ],
        ];
        ?>

        const userRolesChartCtx = document.getElementById('userRolesChart').getContext('2d');
        const userRolesChart = new Chart(userRolesChartCtx, {
          type: 'bar',
          data: <?php echo json_encode($userRolesChartData); ?>,
          options: {
            scales: {
              y: {
                beginAtZero: true
              }
            }
          },
        });
        <?php
        $queryAverageSalary = "SELECT PositionName, AVG(SalaryUSD) AS AverageSalary FROM positions GROUP BY PositionName";
        $resultAverageSalary = mysqli_query($conn, $queryAverageSalary);

        $positionsAverageSalary = [];
        $averageSalaries = [];

        while ($row = mysqli_fetch_assoc($resultAverageSalary)) {
          $positionsAverageSalary[] = $row['PositionName'];
          $averageSalaries[] = $row['AverageSalary'];
        }

        $averageSalaryChartData = [
          'labels' => $positionsAverageSalary,
          'datasets' => [
            [
              'label' => 'Average Salary (USD)',
              'data' => $averageSalaries,
              'fill' => false, 
              'borderColor' => 'rgba(75, 192, 192, 1)',
              'borderWidth' => 1,
              'pointBackgroundColor' => 'rgba(75, 192, 192, 1)',
              'pointRadius' => 5,
              'pointHoverRadius' => 8,
            ],
          ],
        ];
        ?>

        const averageSalaryChartCtx = document.getElementById('averageSalaryChart').getContext('2d');
        const averageSalaryChart = new Chart(averageSalaryChartCtx, {
          type: 'line', 
          data: <?php echo json_encode($averageSalaryChartData); ?>,
          options: {
            scales: {
              y: {
                beginAtZero: true
              }
            }
          },
        });
      </script>
    </div>
</body>

</html>