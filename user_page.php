<?php

@include 'config.php';

session_start();

if(!isset($_SESSION['student_name'], $_SESSION['student_admn_no'])){
    
   header('location:index.php');
}

?>

<?php
$admn_no = $_SESSION['student_admn_no'];

// Fetch student data
$stmt = $conn->prepare("SELECT * FROM student_users WHERE admn_no = ?");
$stmt->bind_param("i", $admn_no);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    die('Student data not found.');
}

// Determine the class section table
$class_section = $student['class_section'];
$table_name = strtolower("student_fees_" . $class_section);

// Check if table exists
$result = $conn->query("SHOW TABLES LIKE '$table_name'");
if($result->num_rows == 0) {
    die('The fees table for the given class section does not exist.');
}

// Fetch fee details
$stmt = $conn->prepare("SELECT * FROM $table_name WHERE admn_no = ?");
$stmt->bind_param("i", $admn_no);
$stmt->execute();
$result = $stmt->get_result();
$fee_details = $result->fetch_assoc();

if (!$fee_details) {
    die('Fee details not found.');
}
?>




<!DOCTYPE html>
<html lang="en">
 
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible"
          content="IE=edge">
    <meta name="viewport"
          content="width=device-width,
                   initial-scale=1.0">
    <title>Vimal Hriday School | ERP </title>
    <link rel="stylesheet"
          href="./css/style.css">
    
    <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
    
</head>
 
<body>
   
    <!-- for header part -->
    <header class="centered-header">
        <div class="welcome-section">
            <h2>Welcome</h2>
            <div class="username"><?php echo $_SESSION['student_name'] ?>!</div>
        </div>

        <div class="school-info">
            <img src="./img/logo.png" class="school-logo" alt="School Logo">
            <!-- <h2 class="school_name">Vimal Hriday School, Purnea, Bihar</h2> -->
        </div>
 
        <div class="logosec">
            <img src=
"https://media.geeksforgeeks.org/wp-content/uploads/20221210182541/Untitled-design-(30).png"
                class="icn menuicn"
                id="menuicn"
                alt="menu-icon">
        </div>
    </header>
    
 
    <div class="main-container">
        <div class="navcontainer">
            <nav class="nav">
                <div class="nav-upper-options">
                    <div class="nav-option option1">
                        <h3> Dashboard</h3>
                    </div>
 
                    <div class="nav-option logout">
                        <a href="./logout.php" style="text-decoration:none">
                            <h3>Logout</h3>
                        </a>
                    </div>
 
                </div>
            </nav>
        </div>
        <div class="main">
            <section class="school-details-section">
                <h1 class="school_name">Vimal Hriday English Medium School</h1>
                <p class="school_description">New Sipahi Tola, Purnea, 854 301, Bihar | UDISE Code: 10090402404</p>
            </section>
            <div class="class-info">
                <h3>Class: <?php echo htmlspecialchars($student['class_section']); ?></h3>
            </div>


            <!-- Student Details Section -->
            <div class="student-details">
                <h3>Student Details</h3>
                <table>
                    <tr>
                        <th>Field</th>
                        <th>Details</th>
                    </tr>
                    <tr>
                        <td>Name</td>
                        <td><?php echo htmlspecialchars($student['name']); ?></td>
                    </tr>
                    <tr>
                        <td>Admission Number</td>
                        <td><?php echo htmlspecialchars($student['admn_no']); ?></td>
                    </tr>
                    <tr>
                        <?php 
                            // Extract class and section
                            list($class, $section) = explode('_', $student['class_section']);

                            $formatted_class = preg_replace('/(?<=\D)(?=\d)|(?<=\d)(?=\D)/i', ' ', $class);
                            $formatted_section = preg_replace('/(?<=\D)(?=\d)|(?<=\d)(?=\D)/i', ' ', $section);


                            // Capitalize first letter and replace underscore with hyphen
                            $formatted_class = ucfirst($formatted_class);
                            $formatted_section = ucfirst($section);
                        ?>
                        <td>Class</td>
                        <td>
                            <?php echo $formatted_class; ?>
                        </td>

                    </tr>
                    <tr>
                        <td>Section</td>
                        <td>
                            <?php echo $formatted_section; ?>
                        </td>
                    </tr>
                    <!-- Add more rows as needed for other student details -->
                </table>
            </div>

            <div class="fee-details">
                <h3>Fees Details</h3>
                <table>
                    <tr>
                        <th>Month</th>
                        <th>Amount</th>
                    </tr>
                    <?php
                    $total_due = 0;
                    $total_paid = 0;
                    $non_fee_columns = ['admn_no', 'id', 'std', 'student_name', 'father_name', 'class'];
                    foreach ($fee_details as $month => $amount) {
                        if (in_array($month, $non_fee_columns)) {
                            continue;
                        } 
                            if ($amount > 0) {
                                $total_due += intval($amount);
                                echo "<tr>
                                        <td >" . htmlspecialchars(ucfirst($month)) . "</td>
                                        <td style='background-color: red; !important;'>" . htmlspecialchars($amount) . "</td>
                                    </tr>";
                            } else {
                                $total_paid += 1;
                                echo "<tr >
                                        <td>" . htmlspecialchars(ucfirst($month)) . "</td>
                                        <td style='background-color: green; !important;'>Paid</td>
                                    </tr>";
                            }
                        
                    }
                    ?>
                    <tr>
                        <td><strong>Total Due</strong></td>
                        <td><strong><?php echo $total_due; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong>Total Month Paid</strong></td>
                        <td><strong><?php echo $total_paid; ?></strong></td>
                    </tr>
                </table>
            </div>



 
    </div>
 
    <script src="./js/index.js"></script>
</body>
</html>