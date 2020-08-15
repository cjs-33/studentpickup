<?php
    ini_set('display_errors', 1);

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/app/classes/sp.php';
// //test to save image
// $url = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=http://local.studentdata.com/pickup/scm/1";

// $imgname = "temp/images/1.png";

// file_put_contents($imgname, file_get_contents($url));
print_r(parse_url($_SERVER['REQUEST_URI']));

$parsed_url = parse_url($_SERVER['REQUEST_URI']);
$url_array = explode('/', trim($parsed_url['path']));

print_r($url_array);
$function = $url_array[1];
$district = $url_array[2];
$student_id = $url_array[3];

$dbconfig = array(
    "host" => "localhost",
    "user" => "root",
    "pass" => "root",
    "dbname" => "studentpickup"
);

$mysqli = new mysqli($dbconfig['host'], $dbconfig['user'], $dbconfig['pass'], $dbconfig['dbname']);

$SP = new StudentPickup();

if ($function == "createcode") {
    //create code
} else if ($function == "view") {
    //monitor and refresh the page every 5 secs (this is the sheets workaround)

} else if ($function == "pickup") {
    $SP->echodebug("<p>Looking for Student # <strong>{$student_id}</strong> from the <strong>{$district}</strong> District</p>"); 

    $inLastHour = time() - (time() - 3600);

    $checkSql = "SELECT * FROM pickups WHERE student_id = $student_id AND timestamp > $inLastHour;";
    $SP->echodebug($checkSql);

    $timeresult = mysqli_fetch_assoc($mysqli->query($checkSql));
    if(count($timeresult) > 0) {
        echo "<p>This child has already been marked as READY for pickup.</p>";
        exit();
    }

    $sql = "INSERT INTO pickups (student_id, timestamp) VALUES ($student_id, " . time() . ");";
    $result = $mysqli->query($sql);

    $SP->echodebug($sql);
    $SP->echodebug($result);

    if ($result) {

        //get that student

        $result = mysqli_fetch_assoc($SP->getStudent($student_id, $dbconfig));
        echo "<h1>Pickup Ready!</h1> <h2>Family name: {$result['family_name']}</h2>";
    }
} else {
    //show welcome & login
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <title>SchoolPickup.net | Coming Soon</title>
            <!-- app includes -->
            <?php include_once(__DIR__ . "/app/includes/includes.php");?>
        </head>
        <body class='text-center'>
            <div class='container'>
                <?php include_once(__DIR__ . "/app/welcome.php");?>
            </div>
        </body>
    </html>


    <?php
}
