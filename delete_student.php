<?php

@include 'config.php';
session_start();

if (!isset($_SESSION['admin_name'])) {
    header('location:index.php');
    exit;
}

if (isset($_POST['delete_student'])) {
    $student_id = $_POST['student_id'];
    $class_section = $_POST['class_section'];
    $table_name = "student_fees_" . $class_section;

    $conn->begin_transaction();

    // Step 1: Delete from class section table
    $sql = "DELETE FROM $table_name WHERE admn_no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    
    if (!$stmt->execute()) {
        $conn->rollback();
        header("Location: admin_page.php?class_section=$class_section&error=Unable to Delete Student Data from Class Section");
        exit;
    }

    // Step 2: Delete from student_users table
    $sql2 = "DELETE FROM student_users WHERE admn_no = ? AND class_section = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("is", $student_id, $class_section);

    if (!$stmt2->execute()) {
        $conn->rollback();
        header("Location: admin_page.php?class_section=$class_section&error=Unable to Delete Student Data from Users");
        exit;
    }

    $conn->commit();
    header("Location: admin_page.php?class_section=$class_section&message=Student Data Deleted Successfully!");
} else {
    header("Location: admin_page.php?class_section=$class_section&error=Invalid Request");
    exit;
}
?>
