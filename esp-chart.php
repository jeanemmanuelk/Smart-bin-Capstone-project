<?php
/**
 * Smart Bin Capstone Project - Data Visualization
 *
 * This script retrieves data from the database and displays it in a visual format using Highcharts library.
 * It provides real-time charts to monitor the distance, weight, and bin state readings from the bin sensors.
 *
 * Instructions:
 * 1. Set up a MySQL database and update the database credentials ($dbname, $dbuser, $dbpass) accordingly.
 * 2. Make sure to include the Highcharts library (https://code.highcharts.com/highcharts.js) in your HTML file.
 * 3. Configure the HTML file to send HTTP GET requests to this script's URL.
 *
 * @version 1.0
 * @author Your Name
 */

$dbname = '';       // Your server database name
$dbuser = '';       // Your server username
$dbpass = '';       // Your database password
$dbhost = 'localhost';  // Your database hosting server name ("localhost" for local development)

// Create database connection
$connect = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

// Check database connection
if (!$connect) {
    echo "Error: " . mysqli_connect_error();
    exit();
}

// Retrieve data from the database
$sql = "SELECT id, distance, weight, binState, reading_time FROM BinSensor ORDER BY id DESC";
$result = $connect->query($sql);

// Store sensor data in an array
$sensor_data = [];
while ($data = $result->fetch_assoc()) {
    $sensor_data[] = $data;
}

// Format the reading_time array to adjust timezone
$i = 0;
foreach ($sensor_data as $reading) {
    // Uncomment to set timezone to - 1 hour (you can change 1 to any number)
    $sensor_data[$i]['reading_time'] = date("Y-m-d H:i:s", strtotime($reading['reading_time'] . " - 1 hours"));
    // Uncomment to set timezone to + 4 hours (you can change 4 to any number)
    // $sensor_data[$i]['reading_time'] = date("Y-m-d H:i:s", strtotime($reading['reading_time'] . " + 4 hours"));
    $i += 1;
}

// Convert data arrays to JSON format
$distance = json_encode(array_reverse(array_column($sensor_data, 'distance')), JSON_NUMERIC_CHECK);
$weight = json_encode(array_reverse(array_column($sensor_data, 'weight')), JSON_NUMERIC_CHECK);
$reading_time = json_encode(array_reverse(array_column($sensor_data, 'reading_time')), JSON_NUMERIC_CHECK);
$binState = json_encode(array_reverse(array_column($sensor_data, 'binState')), JSON_NUMERIC_CHECK);

// Free the result set and close the database connection
$result->free();
$connect->close();
?>

<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="https://code.highcharts.com/highcharts.js"></script>
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        setInterval(function() {
            $("#chart-distance").load("#chart-distance");
            $("#chart-weight").load("#chart-weight");
            $("#chart-binval").load("#chart-binval");
            refresh();
        }, 5000);
    });
</script>
<style>
#container {
    position: absolute;
}

.div-1 {
    float: left;
    width: 45%;
    padding: 2%;
}

.div-2 {
    width: 45%;
    padding: 2%;
    float: left;
}

.div-3 {
    float: right;
    width: 45%;
    padding: 2%;
}

.div-4 {
    width: 45%;
    padding: 2%;
    float: right;
}

body {
    min-width: 310px;
    max-width: 1280px;
    height: 500px;
    margin: 0 auto;

}

h2 {
    font-family: Arial;
    font-size: 2.5rem;
    text-align: center;
}
</style>

<body>
    <center>
        <h2>BIN SENSOR MONITORING</h2>
        <hr />
    </center>
    <div id="chart-distance" class="div-1"></div>
    <div id="chart-weight" class="div-3"></div>
    <div id="chart-binval" class="div-2"></div>
    <div class="div-4">
        <center>
            <h1>Bin State interpret</h1>
            <hr />
            <p>0: <span class="w3-tag" style="background-color:MediumSeaGreen">BIN NOT FULL</span></p>
            <p>1: <span class="w3-tag" style="background-color:Tomato">BIN FULL</span></p>
        </center>
    </div>

    <script>
    var distance = <?php echo $distance; ?>;
    var weight = <?php echo $weight; ?>;
    var reading_time = <?php echo $reading_time; ?>;
    var bin_val = <?php echo $binState; ?>;

    var chartD = new Highcharts.Chart({
        chart: {
            renderTo: 'chart-distance'
        },
        title: {
            text: 'SR-04 Distance'
        },
        series: [{
            showInLegend: false,
            data: distance
        }],
        plotOptions: {
            line: {
                animation: false,
                dataLabels: {
                    enabled: true
                }
            },
            series: {
                color: '#059e8a'
            }
        },
        xAxis: {
            type: 'datetime',
            categories: reading_time,
        },
        yAxis: {
            title: {
                text: 'Distance (cm)'
            }
        },
        credits: {
            enabled: false
        }
    });

    var chartW = new Highcharts.Chart({
        chart: {
            renderTo: 'chart-weight'
        },
        title: {
            text: 'Load Sensor'
        },
        series: [{
            showInLegend: false,
            data: weight
        }],
        plotOptions: {
            line: {
                animation: false,
                dataLabels: {
                    enabled: true
                }
            }
        },
        xAxis: {
            type: 'datetime',
            categories: reading_time,
        },
        yAxis: {
            title: {
                text: 'Weight (lbs)'
            }
        },
        credits: {
            enabled: false
        }
    });

    var chartV = new Highcharts.Chart({
        chart: {
            renderTo: 'chart-binval'
        },
        title: {
            text: 'Bin State'
        },
        series: [{
            showInLegend: false,
            data: bin_val
        }],
        plotOptions: {
            line: {
                animation: false,
                dataLabels: {
                    enabled: false
                }
            },
            series: {
                color: '#059e8a'
            }
        },
        xAxis: {
            type: 'datetime',
            categories: reading_time,
        },
        yAxis: {
            title: {
                text: '1 = full | 0 = Not Full'
            }
        },
        credits: {
            enabled: false
        }
    });
    </script>
</body>

</html>
