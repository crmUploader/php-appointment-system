<?php

    //learn from w3schools.com

    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){
            header("location: ../login.php");
        }else{
            $useremail=$_SESSION["user"];
        }

    }else{
        header("location: ../login.php");
    }
    

    //import database
    include("../connection.php");
    $sqlmain= "select * from patient where pemail=?";
    $stmt = $database->prepare($sqlmain);
    $stmt->bind_param("s",$useremail);
    $stmt->execute();
    $userrow = $stmt->get_result();
    $userfetch=$userrow->fetch_assoc();
    $userid= $userfetch["pid"];
    $username=$userfetch["pname"];


    // if($_POST){
    //     if(isset($_POST["booknow"])){
    //         $apponum=$_POST["apponum"];
    //         $scheduleid=$_POST["scheduleid"];
    //         $date=$_POST["date"];
    //         $scheduleid=$_POST["scheduleid"];
    //         $sql2="insert into appointment(pid,apponum,scheduleid,appodate) values ($userid,$apponum,$scheduleid,'$date')";
    //         $result= $database->query($sql2);
    //         //echo $apponom;
    //         header("location: appointment.php?action=booking-added&id=".$apponum."&titleget=none");

    //     }
    // }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $scheduleid = $_POST['scheduleid'];
        $apponum = $_POST['apponum'];
        $date = $_POST['date'];
        $username = $_POST['username'];
        $useremail = $_POST['useremail'];
        $docname = $_POST['docname'];
        $docemail = $_POST['docemail'];
    
        // Insert booking data into the database
        $sql = "INSERT INTO appointment (scheduleid, apponum, pid, appodate) values ($scheduleid, $apponum, $userid,'$date')";
        $stmt = $database->prepare($sql);
        // $stmt->bind_param("iiss", $scheduleid, $apponum, $useremail, $date);
    
        if ($stmt->execute()) {
            // SMTP email setup
            require_once __DIR__ . '/../vendor/autoload.php'; // Include PHPMailer autoload (Make sure you have installed PHPMailer via Composer)
    
            $mail = new PHPMailer\PHPMailer\PHPMailer();
    
            // SMTP configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Your SMTP host
            $mail->SMTPAuth = true;
            $mail->Username = 'cashgrabapp5@gmail.com'; // Your email
            $mail->Password = 'ptcipqszazjgrbaw'; // Your email password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
    
            // Email content for the patient
            $mail->setFrom('mahmedraza0805@gmail.com', 'Appointment System');
            $patient_query = "SELECT pemail FROM patient WHERE pid = ?";
            $patient_stmt = $database->prepare($patient_query);
            $patient_stmt->bind_param("i", $pid);
            $patient_stmt->execute();
            $patient_stmt->bind_result($useremail);
            $patient_stmt->fetch();
            $patient_stmt->close();

            $mail->addAddress($useremail);
            $mail->Subject = 'Appointment Confirmation';
            $mail->Body = "Dear $username,\n\nYour appointment with Dr. $docname has been booked successfully.\n\nDate: $date\nAppointment Number: $apponum\n\nThank you!";
            $mail->send();

            // Email content for the doctor/admin
            $mail->clearAddresses();
            $mail->addAddress($docemail);
            $mail->Subject = 'New Appointment Booking';
            $mail->Body = "Dear Dr. $docname,\n\nA new appointment has been booked.\n\nPatient: $username\nAppointment Number: $apponum\nDate: $date\n\nThank you!";
        $mail->send();
    
            // Redirect to a success page
            // header("Location: booking-success.php");
            header("location: appointment.php?action=booking-added&id=".$apponum."&titleget=none");
        } else {
            echo "Error booking appointment!";
        }
    }
 ?>