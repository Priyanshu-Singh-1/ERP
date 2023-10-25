<?php
@include 'config.php'; // Include your database connection
$target_dir = dirname(getcwd());
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
require 'vendor/autoload.php'; 

use PhpOffice\PhpSpreadsheet\IOFactory;

// Check if file is an Excel file
if($fileType != "xlsx" && $fileType != "xls") {
        header("Location: admin_page.php?error=Sorry, there was an error uploading your file.");
        exit();
        $uploadOk = 0;
    }
    
    if ($uploadOk && move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        // Load the spreadsheet
        $spreadsheet = IOFactory::load($target_file);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        // Get the selected class/section from the form
        $class_section = $_POST['class_section'];

        // Determine the table name based on the selected class/section
        $table_name = strtolower("student_fees_" . $class_section);

        // Check if the table exists
        $result = $conn->query("SHOW TABLES LIKE '$table_name'");
        if($result->num_rows == 0) {
                // Table doesn't exist, create it
                $create_table_query = "
                CREATE TABLE `$table_name` (
                `id` int(11) NOT NULL,
                `std` varchar(10) DEFAULT NULL,
                `admn_no` int(11) NOT NULL,
                `student_name` varchar(255) DEFAULT NULL,
                `father_name` varchar(255) DEFAULT NULL,
                `admn_fee` decimal(10,2) DEFAULT NULL,
                `annual_fee` decimal(10,2) DEFAULT NULL,
                `april` decimal(10,2) DEFAULT NULL,
                `may` decimal(10,2) DEFAULT NULL,
                `june` decimal(10,2) DEFAULT NULL,
                `july` decimal(10,2) DEFAULT NULL,
                `august` decimal(10,2) DEFAULT NULL,
                `september` decimal(10,2) DEFAULT NULL,
                `october` decimal(10,2) DEFAULT NULL,
                `november` decimal(10,2) DEFAULT NULL,
                `december` decimal(10,2) DEFAULT NULL,
                `january` decimal(10,2) DEFAULT NULL,
                `february` decimal(10,2) DEFAULT NULL,
                `march` decimal(10,2) DEFAULT NULL,
                `class` varchar(50) NOT NULL,
                PRIMARY KEY (`admn_no`)
                )";
                if (!$conn->query($create_table_query)) {
                        header("Location: admin_page.php?error=Error creating table: " . $conn->error);
                        exit();
                }
        }
    
        // Skip the header row and loop through each row to insert data
        for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                // Preparing data for student_users table
                $name = $conn->real_escape_string($row[3]); // Assuming student_name is in 4th column
                $admn_no = $conn->real_escape_string($row[2]); // Assuming admn_no is in 3rd column
                
                $name_parts = explode(' ', $name);
                $first_name = strtolower($name_parts[0]);
                
                $password = strtolower($first_name . "_" . $admn_no); // Generating password hash - UPDATE - HAVE NOT DONE NOW
                
                // Inserting data into student_users table
                $stmt_user = $conn->prepare("INSERT INTO student_users (name, admn_no, class_section, password) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name), class_section = VALUES(class_section), password = VALUES(password)");
                $stmt_user->bind_param("siss", $name, $admn_no, $class_section, $password);
                if (!$stmt_user->execute()) {
                header("Location: admin_page.php?error=Error: " . $stmt_user->error);
                exit();
                }
                $stmt_user->close();

                $stmt = $conn->prepare("INSERT INTO $table_name (std, admn_no, student_name, father_name, admn_fee, annual_fee, april, may, june, july, august, september, october, november, december, january, february, march, class) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                $months = array_slice($row, 7, 12);
                foreach ($months as $key => $value) {
                $months[$key] = $value;
                }

                $params = array_merge([$row[1], $row[2], $row[3], $row[4], $row[5], $row[6]], $months, [$row[1]]);
                
                $stmt = $conn->prepare("INSERT INTO $table_name (std, admn_no, student_name, father_name, admn_fee, annual_fee, april, may, june, july, august, september, october, november, december, january, february, march, class) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE student_name = VALUES(student_name), father_name = VALUES(father_name), admn_fee = VALUES(admn_fee), annual_fee = VALUES(annual_fee), april = VALUES(april), may = VALUES(may), june = VALUES(june), july = VALUES(july), august = VALUES(august), september = VALUES(september), october = VALUES(october), november = VALUES(november), december = VALUES(december), january = VALUES(january), february = VALUES(february), march = VALUES(march), class = VALUES(class)");

                $stmt->bind_param("sisssdsssssssssssss", ...$params);

                if (!$stmt->execute()) {
                        header("Location: admin_page.php?error=Error: " . $stmt->error);
                        exit();
                        
                }
                $stmt->close();
        }

        header("Location: admin_page.php?class_section=$class_section");

        } else {
                header("Location: admin_page.php?error=Invalid file type. Only XLS & XLSX files are allowed.");
                exit();
        }
?>