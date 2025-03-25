<?php
require '../vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

// AWS configuration
$bucketName = 'appointment-system-s3';
$region = 'eu-west-1'; // Replace with your bucket's region

try {
    // Initialize the S3 client
    $s3 = new S3Client([
        'version'     => 'latest',
        'region'      => $region,
        'credentials' => [
            'key'     =>getenv('AWS_ACCESS_KEY_ID'),
            'secret'  => getenv('AWS_SECRET_ACCESS_KEY'),
            'token'   =>getenv('AWS_SESSION_TOKEN'), // Only include if using temporary credentials
        ],
    ]);

    // Specify the file to upload
    $filePath = __DIR__ . '/patient.php'; // Full path to the local file
    $key = basename($filePath); // Use the file's name in S3

    // Upload the file to S3
    $result = $s3->putObject([
        'Bucket'     => $bucketName,
        'Key'        => $key,
        'SourceFile' => $filePath,
        'ACL'        => 'public-read', // Optional: Set access permissions
    ]);
    print_r($result);
    // Output the file URL
    echo "File uploaded successfully. File URL: " . $result['ObjectURL'] . "\n";
    die;

} catch (AwsException $e) {
    // Catch AWS-specific exceptions
    echo "Error: " . $e->getMessage() . "\n";die;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
        
    <title>Doctor</title>
    <style>
        .popup{
            animation: transitionIn-Y-bottom 0.5s;
        }
</style>
</head>
<body>
    <?php

    //learn from w3schools.com

    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='a'){
            header("location: ../login.php");
        } else {
            $email=$_SESSION["user"];
        }

    }else{
        header("location: ../login.php");
    }
    

    include("../connection.php");
    $userrow = $database->query("SELECT * FROM admin WHERE aemail='$email'");
    $userfetch = $userrow->fetch_assoc();
    $adminname = $userfetch["aname"];
    $adminemail = $userfetch["aemail"];



    if($_POST){
        //print_r($_POST);
        $result= $database->query("select * from webuser");
        $name=$_POST['name'];
        $nic=$_POST['nic'];
        $spec=$_POST['spec'];
        $email=$_POST['email'];
        $tele=$_POST['Tele'];
        $password=$_POST['password'];
        $cpassword=$_POST['cpassword'];
        
        if ($password==$cpassword){
            $error='3';
            $result= $database->query("select * from webuser where email='$email';");
            if($result->num_rows==1){
                $error='1';
            }else{

                $sql1="insert into doctor(docemail,docname,docpassword,docnic,doctel,specialties) values('$email','$name','$password','$nic','$tele',$spec);";
                $sql2="insert into webuser values('$email','d')";
                $database->query($sql1);
                $database->query($sql2);

                //echo $sql1;
                //echo $sql2;
                $error= '4';
                
            }
            
        }else{
            $error='2';
        }
    
    
        
        
    }else{
        //header('location: signup.php');
        $error='3';
    }
    

    header("location: doctors.php?action=add&error=".$error);
    ?>
    
   

</body>
</html>