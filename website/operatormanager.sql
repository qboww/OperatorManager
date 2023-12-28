-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 28, 2023 at 11:12 AM
-- Server version: 8.0.17
-- PHP Version: 7.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `operatormanager`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `CalculateTotalShiftTimeForAll` ()  BEGIN
    DECLARE totalShiftTime INT;

    SELECT SUM(TIMESTAMPDIFF(SECOND, DateStart, DateEnd)) INTO totalShiftTime
    FROM shifts;

    -- Return the total shift time in seconds
    SELECT totalShiftTime AS TotalShiftTime;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CalculateUserStatistics` ()  BEGIN
    DECLARE totalWorkingHours INT;
    DECLARE averageSalary DECIMAL(10, 2);
    DECLARE totalSalary DECIMAL(10, 2);

    -- Calculate total working hours
    SELECT SUM(TIMESTAMPDIFF(SECOND, DateStart, DateEnd)/3600) INTO totalWorkingHours
    FROM shifts;

    -- Calculate average salary
    SELECT AVG(p.SalaryUSD) INTO averageSalary
    FROM operators o
    JOIN positions p ON o.PositionID = p.PositionID;

    -- Calculate total salary
    SELECT SUM(p.SalaryUSD) INTO totalSalary
    FROM operators o
    JOIN positions p ON o.PositionID = p.PositionID;

    -- Display the results
    SELECT 
        totalWorkingHours AS TotalWorkingHours,
        averageSalary AS AverageSalary,
        totalSalary AS TotalSalary;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DecryptAllPasswords` (IN `enc_key` VARCHAR(20))  BEGIN
    UPDATE operators
    SET Password = CAST(AES_DECRYPT(UNHEX(Password), enc_key) AS CHAR);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `EncryptAllPasswords` (IN `enc_key` VARCHAR(20))  BEGIN
    UPDATE operators
    SET Password = HEX(AES_ENCRYPT(Password, enc_key));
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetMySQLVersion` (OUT `mysqlVersion` VARCHAR(255))  BEGIN
    SELECT VERSION() INTO mysqlVersion;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetUserSummary` ()  BEGIN
    SELECT
        o.FirstName,
        o.LastName,
        p.PositionName,
        p.SalaryUSD,
        ROUND(SUM(TIMESTAMPDIFF(SECOND, s.DateStart, s.DateEnd) / 3600)) AS TotalHoursWorked
    FROM
        operators o
    JOIN
        positions p ON o.PositionID = p.PositionID
    LEFT JOIN
        shifts s ON o.OperatorID = s.OperatorID
    GROUP BY
        o.OperatorID;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `operators`
--

CREATE TABLE `operators` (
  `OperatorID` int(11) NOT NULL,
  `Login` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `FirstName` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `LastName` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `PositionID` int(11) NOT NULL,
  `Password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Role` enum('admin','operator','guest') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'guest'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `operators`
--

INSERT INTO `operators` (`OperatorID`, `Login`, `FirstName`, `LastName`, `PositionID`, `Password`, `Role`) VALUES
(1, 'admin', 'Yevhenii', 'Sarancha', 3, '$2y$10$qRyXPNBKA8kBGlSSqk1kte02RtvLIttZ9OIo0jqRaGp.OWKhJNWci', 'admin'),
(4, 'devops123', 'Mykola', 'Parasuk', 3, '123', 'operator'),
(6, 'tem4ik', 'artem', 'lyhatskiy', 3, '$2y$10$tBerWbC.qwXbXXrl8FJLPuwZWdAN7GIGLE7fknLjMotQjtKu1pHjW', 'guest'),
(8, 'operator', 'Ihor', 'Sarancha', 2, '$2y$10$IUvZG/YYyT94gfrYDrgc/OCk.L5vnRWIjhnyAXzaggkJd21okxSuG', 'operator');

--
-- Triggers `operators`
--
DELIMITER $$
CREATE TRIGGER `before_insert_update_limit_length` BEFORE INSERT ON `operators` FOR EACH ROW BEGIN
    IF LENGTH(NEW.FirstName) > 60 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'First name must be 60 characters or fewer';
    END IF;
    IF LENGTH(NEW.LastName) > 60 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Last name must be 60 characters or fewer';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_update_limit_length` BEFORE UPDATE ON `operators` FOR EACH ROW BEGIN
    IF LENGTH(NEW.FirstName) > 60 OR LENGTH(NEW.LastName) > 60 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Name length must be 60 characters or fewer';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_update_operators_duplicates` BEFORE UPDATE ON `operators` FOR EACH ROW BEGIN
    IF (SELECT COUNT(*) FROM operators WHERE Login = NEW.Login AND OperatorID != NEW.OperatorID) > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Duplicate login is not allowed';
    END IF;
    IF (SELECT COUNT(*) FROM operators WHERE FirstName = NEW.FirstName AND LastName = NEW.LastName AND OperatorID != NEW.OperatorID) > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Duplicate first name, middle name, and last name combination is not allowed';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_update_update_limit_length` BEFORE UPDATE ON `operators` FOR EACH ROW BEGIN
    IF LENGTH(NEW.FirstName) > 60 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'First name must be 60 characters or fewer';
    END IF;
    IF LENGTH(NEW.LastName) > 60 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Last name must be 60 characters or fewer';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `operatorworkstations`
--

CREATE TABLE `operatorworkstations` (
  `OperatorID` int(11) NOT NULL,
  `WorkstationID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `operatorworkstations`
--

INSERT INTO `operatorworkstations` (`OperatorID`, `WorkstationID`) VALUES
(15, 3),
(1, 6);

-- --------------------------------------------------------

--
-- Stand-in structure for view `operator_view`
-- (See below for the actual view)
--
CREATE TABLE `operator_view` (
`OperatorID` int(11)
,`Login` varchar(60)
,`FirstName` varchar(80)
,`LastName` varchar(80)
,`PositionName` varchar(255)
,`SalaryUSD` decimal(10,2)
,`DateStart` datetime
,`DateEnd` datetime
,`HoursWorked` decimal(24,4)
);

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `PositionID` int(11) NOT NULL,
  `PositionName` varchar(255) NOT NULL,
  `SalaryUSD` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`PositionID`, `PositionName`, `SalaryUSD`) VALUES
(2, 'UX Designer', '1250.75'),
(3, 'DevOps', '3000.00');

-- --------------------------------------------------------

--
-- Table structure for table `servers`
--

CREATE TABLE `servers` (
  `ServerID` int(11) NOT NULL,
  `ServerName` varchar(255) NOT NULL,
  `inv_num` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `servers`
--

INSERT INTO `servers` (`ServerID`, `ServerName`, `inv_num`) VALUES
(8, 'apex-2', 'INV0008'),
(11, 'apex-56', 'INV0011');

--
-- Triggers `servers`
--
DELIMITER $$
CREATE TRIGGER `before_insert_servers` BEFORE INSERT ON `servers` FOR EACH ROW BEGIN
    DECLARE newID INT;

    -- Get the latest ServerID
    SELECT MAX(ServerID) + 1 INTO newID FROM servers;

    -- Set the inv_num for the new record
    SET NEW.inv_num = CONCAT('INV', LPAD(newID, 4, '0'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `ShiftID` int(11) NOT NULL,
  `OperatorID` int(11) NOT NULL,
  `WorkstationID` int(11) NOT NULL,
  `DateStart` datetime NOT NULL,
  `DateEnd` datetime NOT NULL,
  `SoftwareID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `shifts`
--

INSERT INTO `shifts` (`ShiftID`, `OperatorID`, `WorkstationID`, `DateStart`, `DateEnd`, `SoftwareID`) VALUES
(2, 4, 6, '2023-12-04 00:00:00', '2023-12-13 00:00:00', 3),
(6, 1, 2, '2023-12-19 16:02:00', '2023-12-26 16:02:00', 3);

-- --------------------------------------------------------

--
-- Table structure for table `shifts_log`
--

CREATE TABLE `shifts_log` (
  `Shift_Log_ID` int(11) NOT NULL,
  `Shift_ID` int(11) DEFAULT NULL,
  `OperatorID` int(11) DEFAULT NULL,
  `WorkstationID` int(11) DEFAULT NULL,
  `DateStart` datetime DEFAULT NULL,
  `DateEnd` datetime DEFAULT NULL,
  `SoftwareID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `software`
--

CREATE TABLE `software` (
  `SoftwareID` int(11) NOT NULL,
  `SoftwareName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `software`
--

INSERT INTO `software` (`SoftwareID`, `SoftwareName`) VALUES
(3, 'redengine_editor'),
(4, 'phpStorm'),
(13, 'visual_studio 2022'),
(14, 'vscode'),
(19, 'photoshop');

-- --------------------------------------------------------

--
-- Table structure for table `workstations`
--

CREATE TABLE `workstations` (
  `WorkstationID` int(11) NOT NULL,
  `WorkstationName` varchar(255) NOT NULL,
  `inv_num` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `workstations`
--

INSERT INTO `workstations` (`WorkstationID`, `WorkstationName`, `inv_num`) VALUES
(2, 'i7-4060ti', 'INV0002'),
(3, 'r7-4060ti', 'INV0003'),
(6, 'r3-gtx1650', 'INV0006'),
(7, 'r7-gtx1070', 'INV0007'),
(9, 'i3-gtx970', 'INV0009');

--
-- Triggers `workstations`
--
DELIMITER $$
CREATE TRIGGER `before_insert_workstations` BEFORE INSERT ON `workstations` FOR EACH ROW BEGIN
    DECLARE newID INT;

    -- Get the latest WorkstationID
    SELECT MAX(WorkstationID) + 1 INTO newID FROM workstations;

    -- Set the inv_num for the new record
    SET NEW.inv_num = CONCAT('INV', LPAD(newID, 4, '0'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `workstationservers`
--

CREATE TABLE `workstationservers` (
  `WorkstationID` int(11) NOT NULL,
  `ServerID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `workstationservers`
--

INSERT INTO `workstationservers` (`WorkstationID`, `ServerID`) VALUES
(2, 8);

-- --------------------------------------------------------

--
-- Table structure for table `workstationsoftware`
--

CREATE TABLE `workstationsoftware` (
  `WorkstationID` int(11) NOT NULL,
  `SoftwareID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `workstationsoftware`
--

INSERT INTO `workstationsoftware` (`WorkstationID`, `SoftwareID`) VALUES
(2, 3),
(9, 4);

-- --------------------------------------------------------

--
-- Structure for view `operator_view`
--
DROP TABLE IF EXISTS `operator_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `operator_view`  AS  select `o`.`OperatorID` AS `OperatorID`,`o`.`Login` AS `Login`,`o`.`FirstName` AS `FirstName`,`o`.`LastName` AS `LastName`,`p`.`PositionName` AS `PositionName`,`p`.`SalaryUSD` AS `SalaryUSD`,`s`.`DateStart` AS `DateStart`,`s`.`DateEnd` AS `DateEnd`,(timestampdiff(SECOND,`s`.`DateStart`,`s`.`DateEnd`) / 3600) AS `HoursWorked` from ((`operators` `o` join `positions` `p` on((`o`.`PositionID` = `p`.`PositionID`))) left join `shifts` `s` on((`o`.`OperatorID` = `s`.`OperatorID`))) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `operators`
--
ALTER TABLE `operators`
  ADD PRIMARY KEY (`OperatorID`),
  ADD KEY `PositionID` (`PositionID`),
  ADD KEY `idx_login` (`Login`);

--
-- Indexes for table `operatorworkstations`
--
ALTER TABLE `operatorworkstations`
  ADD PRIMARY KEY (`OperatorID`,`WorkstationID`),
  ADD KEY `WorkstationID` (`WorkstationID`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`PositionID`),
  ADD KEY `idx_position_name` (`PositionName`);

--
-- Indexes for table `servers`
--
ALTER TABLE `servers`
  ADD PRIMARY KEY (`ServerID`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`ShiftID`),
  ADD KEY `OperatorID` (`OperatorID`),
  ADD KEY `WorkstationID` (`WorkstationID`),
  ADD KEY `SoftwareID` (`SoftwareID`),
  ADD KEY `idx_date_range` (`DateStart`,`DateEnd`);

--
-- Indexes for table `shifts_log`
--
ALTER TABLE `shifts_log`
  ADD PRIMARY KEY (`Shift_Log_ID`),
  ADD KEY `fk_shifts_log_operators` (`OperatorID`),
  ADD KEY `fk_shifts_log_workstations` (`WorkstationID`),
  ADD KEY `fk_shifts_log_software` (`SoftwareID`),
  ADD KEY `fk_shifts_log_shifts` (`Shift_ID`);

--
-- Indexes for table `software`
--
ALTER TABLE `software`
  ADD PRIMARY KEY (`SoftwareID`);

--
-- Indexes for table `workstations`
--
ALTER TABLE `workstations`
  ADD PRIMARY KEY (`WorkstationID`);

--
-- Indexes for table `workstationservers`
--
ALTER TABLE `workstationservers`
  ADD PRIMARY KEY (`WorkstationID`,`ServerID`),
  ADD KEY `ServerID` (`ServerID`);

--
-- Indexes for table `workstationsoftware`
--
ALTER TABLE `workstationsoftware`
  ADD PRIMARY KEY (`WorkstationID`,`SoftwareID`),
  ADD KEY `SoftwareID` (`SoftwareID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `operators`
--
ALTER TABLE `operators`
  MODIFY `OperatorID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `PositionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `servers`
--
ALTER TABLE `servers`
  MODIFY `ServerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `ShiftID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `shifts_log`
--
ALTER TABLE `shifts_log`
  MODIFY `Shift_Log_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `software`
--
ALTER TABLE `software`
  MODIFY `SoftwareID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `workstations`
--
ALTER TABLE `workstations`
  MODIFY `WorkstationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `operators`
--
ALTER TABLE `operators`
  ADD CONSTRAINT `fk_operators_positions` FOREIGN KEY (`PositionID`) REFERENCES `positions` (`PositionID`);

--
-- Constraints for table `operatorworkstations`
--
ALTER TABLE `operatorworkstations`
  ADD CONSTRAINT `operatorworkstations_ibfk_1` FOREIGN KEY (`OperatorID`) REFERENCES `operators` (`OperatorID`),
  ADD CONSTRAINT `operatorworkstations_ibfk_2` FOREIGN KEY (`WorkstationID`) REFERENCES `workstations` (`WorkstationID`);

--
-- Constraints for table `shifts`
--
ALTER TABLE `shifts`
  ADD CONSTRAINT `shifts_ibfk_1` FOREIGN KEY (`OperatorID`) REFERENCES `operators` (`OperatorID`),
  ADD CONSTRAINT `shifts_ibfk_2` FOREIGN KEY (`WorkstationID`) REFERENCES `workstations` (`WorkstationID`),
  ADD CONSTRAINT `shifts_ibfk_3` FOREIGN KEY (`SoftwareID`) REFERENCES `software` (`SoftwareID`);

--
-- Constraints for table `shifts_log`
--
ALTER TABLE `shifts_log`
  ADD CONSTRAINT `fk_shifts_log_operators` FOREIGN KEY (`OperatorID`) REFERENCES `operators` (`OperatorID`),
  ADD CONSTRAINT `fk_shifts_log_shifts` FOREIGN KEY (`Shift_ID`) REFERENCES `shifts` (`ShiftID`),
  ADD CONSTRAINT `fk_shifts_log_software` FOREIGN KEY (`SoftwareID`) REFERENCES `software` (`SoftwareID`),
  ADD CONSTRAINT `fk_shifts_log_workstations` FOREIGN KEY (`WorkstationID`) REFERENCES `workstations` (`WorkstationID`);

--
-- Constraints for table `workstationservers`
--
ALTER TABLE `workstationservers`
  ADD CONSTRAINT `workstationservers_ibfk_1` FOREIGN KEY (`WorkstationID`) REFERENCES `workstations` (`WorkstationID`),
  ADD CONSTRAINT `workstationservers_ibfk_2` FOREIGN KEY (`ServerID`) REFERENCES `servers` (`ServerID`);

--
-- Constraints for table `workstationsoftware`
--
ALTER TABLE `workstationsoftware`
  ADD CONSTRAINT `workstationsoftware_ibfk_1` FOREIGN KEY (`WorkstationID`) REFERENCES `workstations` (`WorkstationID`),
  ADD CONSTRAINT `workstationsoftware_ibfk_2` FOREIGN KEY (`SoftwareID`) REFERENCES `software` (`SoftwareID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
