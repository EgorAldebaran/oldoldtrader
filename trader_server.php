<?php


// choice your company index
$company_name = $_POST["company_name"];

// api in alpavantage.co
$source = "https://www.alphavantage.co/query?function=TIME_SERIES_DAILY&symbol=\"$company_name\"&apikey=WAII57B91ROAWB4K";

// read JSON file
$system_x = file_get_contents ($source);
// decode JSON
$system = json_decode ($system_x, true);


$servername = 'localhost';
$username = 'employeer';
$password = 'company';
$dbname = 'company';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
die('connection failled! '.$conn->connect_error);
}

$sql = "CREATE TABLE \$company_name\
(
id int not null auto_increment primary key,
timestamp DATE not null,
open decimal(10, 4) not null,
high decimal(10, 4) not null,
low decimal(10, 4) not null,
close decimal(10, 4) not null,
volume int not null
)";

if ($conn -> query($sql) === TRUE) {
    echo '<script>console.log("table stock created succesfully")</script>';
}
else {
    echo "error ".$conn -> error;
}

// prepare and bind
$stmt = $conn -> prepare("INSERT INTO  \"$company_name\" (timestamp, open, high, low, close, volume)
values (?, ?, ?, ?, ?, ?)");
$stmt -> bind_param("sddddi", $timestamp, $open, $high, $low, $close, $volume);

// set parameters and execute

for ($mount = 01; $mount <= 12; $mount++) {

    for ($day = 01; $day <= 30; $day++) {
        $timestamp = "2020-0$mount-$day";
        $open =  $system["Time Series (Daily)"]["2020-0$mount-$day"]["1. open"];
        $high =  $system["Time Series (Daily)"]["2020-0$mount-$day"]["2. high"];
        $low =   $system["Time Series (Daily)"]["2020-0$mount-$day"]["3. low"];
        $close = $system["Time Series (Daily)"]["2020-0$mount-$day"]["4. close"];
        $volume =  $system["Time Series (Daily)"]["2020-0$mount-$day"]["5. volume"];

        $stmt -> execute();
    }
}



echo "new records created successfullly";
$stmt -> close();
$conn -> close();
