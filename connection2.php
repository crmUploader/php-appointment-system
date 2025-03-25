<?php

    $database= new mysqli("localhost","root","","appointment_system");
    if ($database->connect_error){
        die("Connection failed:  ".$database->connect_error);
    }

?>