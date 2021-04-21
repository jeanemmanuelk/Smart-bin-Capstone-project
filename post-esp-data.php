<?php


$dbname = 'kjesendata'; //my server database
$dbuser = 'ggt6zshwjs19';  //my server username
$dbpass = 'Kouadio@7'; //database password
$dbhost = 'localhost'; //database hosting server name "localhost for godaddy"

// Keep this API Key value to be compatible with the ESP32 code provided in the project page. 
// If you change this value, the ESP32 sketch needs to match
$api_key_value = "tPmAT5Ab3j7F9";

$api_key= $distance = $weight = $binState = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $api_key = test_input($_POST["api_key"]);
    if($api_key == $api_key_value) {
        $distance = test_input($_POST["distance"]);
        $weight = test_input($_POST["weight"]);
        $binState = test_input($_POST["binState"]);
        
        // Create connection
        $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 
        
         $sql = "INSERT INTO BinSensor (distance, weight, binState) VALUES ('" . $distance . "', '" . $weight . "', '" . $binState . "')";
        
        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
        } 
        else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        
                $conn->close();
    }
    else {
        echo "Wrong API Key provided.";
    }

}
else {
    echo "No data posted with HTTP POST.";
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
