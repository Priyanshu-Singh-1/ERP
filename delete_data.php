<?php
@include 'config.php';
session_start();

if(!isset($_SESSION['admin_name'])){
    header('location:index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['class_section'])) {
    $class_section = $_POST['class_section'];
    $table_name = "student_fees_" . $class_section;

    // Step 1: Get the admission numbers of students in the class section
    $admnNumbers = [];
    $selectQuery = "SELECT admn_no FROM `$table_name`";
    $result = $conn->query($selectQuery);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $admnNumbers[] = $row['admn_no'];
        }
    }

    // Step 2: Delete data from the class section table
    $deleteQuery = "DELETE FROM `$table_name`";
    if ($conn->query($deleteQuery) === TRUE) {

        // Step 3: Delete corresponding students from student_users table
        if (!empty($admnNumbers)) {
            $admnNumbersStr = implode(',', $admnNumbers);
            $deleteUsersQuery = "DELETE FROM student_users WHERE admn_no IN ($admnNumbersStr) AND class_section = '$class_section'";
            if ($conn->query($deleteUsersQuery) === FALSE) {
                // Optional: Handle error if student_users deletion fails
                header("Location: admin_page.php?class_section=$class_section&error=Error deleting student users: " . $conn->error);
                exit;
            }
        }

        // Success: Data deleted from both tables
        header("Location: admin_page.php?class_section=$class_section&message=Data Deleted Successfully");
        exit;
        
    } else {
        // Error: Could not delete data from class section table
        header("Location: admin_page.php?class_section=$class_section&error=Error deleting record: " . $conn->error);
        exit;
    }

} else {
    header('location:admin_page.php');
    exit;
}
?>
