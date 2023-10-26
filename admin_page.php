<?php

@include 'config.php';
header('Cache-Control: no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
session_start();

if(!isset($_SESSION['admin_name'])){
   header('location:index.php');
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
            <div class="username"><?php echo $_SESSION['admin_name'] ?>!</div>
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
            <div class="upload-section">
            <h3>Select Class/Section</h3>
                <select name="class_section" id="classSectionDropdown">
                    <option value="">Please select...</option>
                    <option value="class1_sectionA" <?php if (isset($_GET['class_section']) && $_GET['class_section'] == 'class1_sectionA') echo 'selected'; ?>>Class 1 - Section A</option>
                    <option value="class1_sectionB" <?php if (isset($_GET['class_section']) && $_GET['class_section'] == 'class1_sectionB') echo 'selected'; ?>>Class 1 - Section B</option>
                    <option value="class2_sectionB" <?php if (isset($_GET['class_section']) && $_GET['class_section'] == 'class2_sectionB') echo 'selected'; ?>>Class 2 - Section B</option>
                    <option value="class4_sectionB" <?php if (isset($_GET['class_section']) && $_GET['class_section'] == 'class4_sectionB') echo 'selected'; ?>>Class 4 - Section B</option>
                    <option value="class8_sectionA" <?php if (isset($_GET['class_section']) && $_GET['class_section'] == 'class8_sectionA') echo 'selected'; ?>>Class 8 - Section A</option>
                </select>
                <!-- <button id="addClassSectionBtn">Add Class/Section</button> -->
            </div>

            <!-- Upload form -->
            <div class="upload-section">
                <h3>Upload Excel File for Class/Section</h3>
                <form action="upload.php" method="post" enctype="multipart/form-data" id="uploadForm">
                    <input type="hidden" name="class_section" value="<?php echo isset($_GET['class_section']) ? $_GET['class_section'] : ''; ?>">
                    Select Excel File:
                    <input type="file" name="fileToUpload" id="fileToUpload">
                    <input type="submit" value="Upload File" name="submit">
                </form>
            </div>
            
            <!-- Message Section -->
            <div id="message">
                    <?php
                    if (isset($_GET['message'])) {
                        echo "<p style='color:red'>" . htmlspecialchars($_GET['message']) . "</p>";
                    }
                    if (isset($_GET['error'])) {
                        echo "<p style='color:red'>" . htmlspecialchars($_GET['error']) . "</p>";
                    }
                    ?>
            </div>
            
            <?php
                if (isset($_GET['class_section'])) {
                    $class_section = $_GET['class_section'];
                    $table_name = strtolower("student_fees_" . $class_section);
                    $tableExists = $conn->query("SHOW TABLES LIKE '$table_name'")->num_rows > 0;

                    if ($tableExists) {
                        $result = $conn->query("SELECT * FROM $table_name");
                        $admin = '';
                        if ($result->num_rows > 0) {

                            echo "<div class='table-container'>";
                            echo "<h2>Data for $class_section</h2>";
                            echo "<table border='1'>";
                            echo "<tr><th>Option</th><th>STD</th><th>Admn No.</th><th>Student's Name</th><th>Father's Name</th><th>Admn Fee</th><th>Annual Fee</th><th>April</th><th>May</th><th>June</th><th>July</th><th>August</th><th>September</th><th>October</th><th>November</th><th>December</th><th>January</th><th>February</th><th>March</th></tr>";

                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";

                                echo "<td>";
                                echo "<form method='post' action='delete_student.php' onsubmit='return confirm(\"Are you sure you want to delete this student?\")'>";
                                echo "<input type='hidden' name='student_id' value='" . $row['admn_no'] . "'>";
                                echo "<input type='hidden' name='class_section' value='$class_section'>";
                                echo "<button type='submit' class='delete-btn' name='delete_student'>-</button>";
                                echo "</form>";
                                echo "</td>";

                                echo "<td>" . $row['std'] . "</td>";
                                echo "<td>" . $row['admn_no'] . "</td>";
                                echo "<td>" . $row['student_name'] . "</td>";
                                echo "<td>" . $row['father_name'] . "</td>";
                                echo "<td>" . $row['admn_fee'] . "</td>";
                                echo "<td>" . $row['annual_fee'] . "</td>";
                                echo "<td style='background-color:" . ($row['april'] != 0 ? "red" : "green") . "'>"  . $row['april'] . "</td>";
                                echo "<td style='background-color:" . ($row['may'] != 0 ? "red" : "green") . "'>" . $row['may'] . "</td>";
                                echo "<td style='background-color:" . ($row['june'] != 0 ? "red" : "green") . "'>"  . $row['june'] . "</td>";
                                echo "<td style='background-color:" . ($row['july'] != 0 ? "red" : "green") . "'>"  . $row['july'] . "</td>";
                                echo "<td style='background-color:" . ($row['august'] != 0 ? "red" : "green") . "'>"  . $row['august'] . "</td>";
                                echo "<td style='background-color:" . ($row['september'] != 0 ? "red" : "green") . "'>"  . $row['september'] . "</td>";
                                echo "<td style='background-color:" . ($row['october'] != 0 ? "red" : "green") . "'>"  . $row['october'] . "</td>";
                                echo "<td style='background-color:" . ($row['november'] != 0 ? "red" : "green") . "'>"  . $row['november'] . "</td>";
                                echo "<td style='background-color:" . ($row['december'] != 0 ? "red" : "green") . "'>"  . $row['december'] . "</td>";
                                echo "<td style='background-color:" . ($row['january'] != 0 ? "red" : "green") . "'>"  . $row['january'] . "</td>";
                                echo "<td style='background-color:" . ($row['february'] != 0 ? "red" : "green") . "'>"  . $row['february'] . "</td>";
                                echo "<td style='background-color:" . ($row['march'] != 0 ? "red" : "green") . "'>"  . $row['march'] . "</td>";
                            

                                echo "</tr>";
                            }
                            echo "</table>";
                            echo "</div>";

                            // Delete Data Button
                            echo "<div class='delete-section'>";
                            echo "<form method='post' action='delete_data.php'>";
                            echo "<input type='hidden' name='class_section' value='$class_section'>";
                            echo "<button type='submit' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete all data for this class?\")'>Delete Data</button>";
                            echo "</form>";
                            echo "</div>";
                        } else {
                            echo "<div class='info-message'>No information available for $class_section</div>";
                        }
                    }else{
                        echo "<div class='info-message'>No information available for $class_section</div>";
                    }
                }
                ?>
                
                


    </div>
 
    <script src="./js/index.js"></script>
    <script>
        document.getElementById('classSectionDropdown').addEventListener('change', function() {
            if (this.value !== "") {
                window.location.href = 'admin_page.php?class_section=' + this.value;
            }
        });

        const urlParams = new URLSearchParams(window.location.search);
        const errorMessage = urlParams.get('error');
        if (errorMessage) {
            alert(errorMessage);
        }


        document.getElementById('uploadForm').addEventListener('submit', function(event) {
            const classSectionValue = this.querySelector('input[name="class_section"]').value;
            const fileInput = document.getElementById('fileToUpload');
            
            if (classSectionValue === "" || !fileInput.files.length) {
                event.preventDefault(); // Prevent the form from submitting
                alert('Please select a class/section and a file before uploading.');
            }
        });


        function deleteRecord(studentId) {
            const confirmation = confirm('Are you sure you want to delete this record?');
            if (confirmation) {
                // If you have a specific URL for your delete operation, replace 'delete_student.php' with that URL
                // Also, pass the necessary parameters to your backend for the delete operation
                fetch('delete_student.php?student_id=' + studentId, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Record deleted successfully!');
                        location.reload(); // Reload the page to update the table
                    } else {
                        alert('Failed to delete record!');
                    }
                })
                .catch(error => {
                    console.error('Error deleting record:', error);
                    alert('Failed to delete record!');
                });
            }
        }




        // Removing the duplicate class option
        // function optionExists(value) {
        //     const options = document.getElementById('classSectionDropdown').options;
        //     for (let i = 0; i < options.length; i++) {
        //         if (options[i].value === value) {
        //             return true;
        //         }
        //     }
        //     return false;
        // }
        
        // Functionality to add class section option, not implementing it now
        // document.getElementById('addClassSectionBtn').addEventListener('click', function() {
        //     const className = prompt("Enter the class name (e.g., Class 1, Class 2, etc.):");
        //     if (className === null || className.trim() === "") return; // User cancelled or entered empty class name

        //     const sectionName = prompt("Enter the section name (e.g., Section A, Section B, etc.):");
        //     if (sectionName === null || sectionName.trim() === "") return; // User cancelled or entered empty section name

        //     const optionValue = className.toLowerCase().replace(/\s+/g, '') + "_" + sectionName.toLowerCase().replace(/\s+/g, '');
        //     const optionText = className + " - Section " + sectionName;

        //     if (optionExists(optionValue)) {
        //         alert('This class/section already exists!');
        //     } else {
        //         // Add the new class/section to the dropdown
        //         const newOption = new Option(optionText, optionValue);
        //         document.getElementById('classSectionDropdown').add(newOption);
        //     }
        // });

    </script>
</body>
</html>