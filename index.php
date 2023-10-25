<?php

@include 'config.php';

session_start();

if(isset($_POST['submit'])){

   // $name = mysqli_real_escape_string($conn, $_POST['name']);
   // $email = mysqli_real_escape_string($conn, $_POST['email']);
   // $pass = md5($_POST['password']);
   // $cpass = md5($_POST['cpassword']);
   // $user_type = $_POST['user_type'];

   // $select = " SELECT * FROM user_form WHERE email = '$email' && password = '$pass' ";

   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = $_POST['password'];
   $user_type = $_POST['user_type'];

   if($user_type == 'admin'){
      $select = "SELECT * FROM admin_users WHERE email = '$email' AND password = '$pass'";
   } elseif($user_type == 'student'){
      $select = "SELECT * FROM student_users WHERE admn_no = '$email' AND password = '$pass'";
   } else {
      $error = 'Invalid user type!';
   }

   // $result = mysqli_query($conn, $select);

   // if(mysqli_num_rows($result) > 0){

   //    $row = mysqli_fetch_array($result);

   //    if($row['user_type'] == 'admin'){

   //       $_SESSION['admin_name'] = $row['name'];
   //       header('location:admin_page.php');

   //    }elseif($row['user_type'] == 'user'){

   //       $_SESSION['user_name'] = $row['name'];
   //       header('location:user_page.php');

   //    }
     
   // }else{
   //    $error[] = 'incorrect email or password!';
   // }
   if(isset($select)){
      $result = mysqli_query($conn, $select);

      if(mysqli_num_rows($result) > 0){

         $row = mysqli_fetch_array($result);

         if($user_type == 'admin'){
            $_SESSION['admin_name'] = $row['name'];
            header('location:admin_page.php');
            exit();
         } elseif($user_type == 'student'){
            $_SESSION['student_name'] = $row['name'];
            $_SESSION['student_admn_no'] = $row['admn_no'];
            header('location:user_page.php');
            exit();
         }
        
      }else{
         $error = 'Invalid login details!';
      }
   }

};
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">

   <title>Vimal Hriday School | ERP</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="style.css">
   <link rel="stylesheet" href="./css/index_style.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
   

</head>
<body>

<header class="centered-header" style="background-color: #ADD8E6;">
      <div class="logo-container">
        <img src="./img/logo.png" alt="School Logo" class="school-logo">
        <div class="school-details">
            <h1 class="school_name">Vimal Hriday English Medium School</h1>
            <p class="school_description">New Sipahi Tola, Purnea, 854 301, Bihar | UDISE Code: 10090402404</p>
        </div>
    </div>
</header>   
<div class="form-container">
   <form action="" method="post">
      <h3>Login Here!</h3>
      <?php
      // if(isset($error)){
      //    foreach($error as $error){
      //       echo '<span class="error-msg">'.$error.'</span>';
      //    };
      // };
      if(isset($error)){
         echo '<span class="error-msg">'.$error.'</span>';
      };
      ?>
      <!-- <input type="email" name="email" required placeholder="Enter Email">
      <input type="password" name="password" required placeholder="Enter Password">
      <input type="submit" name="submit" value="login now" class="form-btn">
      <p>Don't have an account? <a href="register_form.php">Register Here</a></p> -->
      <input type="text" name="email" required placeholder="Enter UserName or Admission Number">
      <input type="password" name="password" id="password" required placeholder="Enter Password">
      <button type="button" onclick="togglePasswordVisibility()" class="toggle-password">Show</button>
      

      <select name="user_type" required>
         <option value="" disabled selected>Select User Type</option>
         <option value="admin">Admin</option>
         <option value="student">Student</option>
      </select>
      <button type="submit" name="submit" class="form-btn">Login</button>
      <!-- <p>Don't have an account? <a href="register_form.php">Register Here</a></p> -->
   </form>

</div>

<footer style="background-color: #ADD8E6;">
    <div class="footer-content">
        <p>Copyright © 2023 <b>Vimal Hriday School</b>. All rights reserved.</p>
        <div class="developer-info">
            <p>Developed by: <b class="developer_name"> Priyanshu Singh</b></p>
            <a href="https://www.linkedin.com/in/sublime-priyanshu/" target="_blank">
               <i class="fa fa-linkedin-square" style="font-size:36px; color:#0000FF"></i>
            </a>
        </div>
    </div>
</footer>

<script>
   function togglePasswordVisibility() {
      var passwordField = document.getElementById('password');
      var toggleButton = document.querySelector('.toggle-password');
      if (passwordField.type === "password") {
         passwordField.type = "text";
         toggleButton.textContent = "Hide";
      } else {
         passwordField.type = "password";
         toggleButton.textContent = "Show";
      }
   }

</script>


</body>
</html>