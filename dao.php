<?php
    $servername = "localhost";
    $username = "id1089148_matty";
    $password = "austin";
    $dbname = "id1089148_beer";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
