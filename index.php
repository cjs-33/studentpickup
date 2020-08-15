<?php
ini_set('display_errors', 0);

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/app/classes/sp.php';

$SP = new StudentPickup();

// file_put_contents($imgname, file_get_contents($url));
$SP->echodebug(parse_url($_SERVER['REQUEST_URI']));

$parsed_url = parse_url($_SERVER['REQUEST_URI']);
$url_array = explode('/', trim($parsed_url['path']));

$SP->setTesting(false);
$SP->echodebug($url_array);
$function = $url_array[1];
$district = $url_array[2];
$student_id = $url_array[3];

$SP->setDbConfig(array(
    "host" => "localhost",
    "user" => "root",
    "pass" => "root",
    "dbname" => "studentpickup"
));

$mysqli = new mysqli($SP->dbconfig['host'], $SP->dbconfig['user'], $SP->dbconfig['pass'], $SP->dbconfig['dbname']);


if ($function == "createcode") {
    //create code
} else if ($function == "view") {
    //monitor and refresh the page every 5 secs (this is the sheets workaround)
    include_once("app/dochead.php");

    //get all pickups for this 12 hrs

    $list = $SP->getPickups();

    ?>
    <h2>View Pickups</h2>
    <table class="table table-striped table-hover" id="dataTable">
        <thead>
            <tr>
                <th>
                    Family name
                </th>
                <th>
                    Date/Time
                </th>
            </tr>
        </thead>
        <tbody>
            
<?php

    foreach($list as $k) {
        $formatted_time = date("M d Y, h:i A");
       echo "<tr><td>{$k['family_name']}</td><td>{$formatted_time}</td></tr>";
    }

    if (count($list) == 0) {
        echo "<tr><td colspan='2'><p><em>No Pickups yet today!</em></p></td></tr>";
    }

?>
        </tbody>
    </table>

    <script type="text/javascript">
        setTimeout(function(){
            window.location.reload(1);
        }, 5000);
    </script>

    <?php

} else if ($function == "pickup") {
    include_once("app/dochead.php");
    echo "<h2>Pickup</h2>";
    $SP->echodebug("<p>Looking for Student # <strong>{$student_id}</strong> from the <strong>{$district}</strong> District.....</p>");

    $inLastHour = time() - (time() - 3600);

    $checkSql = "SELECT p.*, s.family_name FROM pickups p LEFT JOIN student s ON p.student_id = s.id WHERE p.student_id = $student_id AND p.timestamp > $inLastHour;";
    $SP->echodebug($checkSql);

    $timeresult = mysqli_fetch_assoc($mysqli->query($checkSql));
    if (count($timeresult) > 0) {
        echo "<p>Family Name: <strong>" . $timeresult['family_name'] . "</strong></p>";
        echo "<p>This student has already been marked as READY for pickup.</p>";
        exit();
    }

    $sql = "INSERT INTO pickups (student_id, timestamp) VALUES ($student_id, " . time() . ");";
    $result = $mysqli->query($sql);

    $SP->echodebug($sql);
    $SP->echodebug($result);

    if ($result) {

        //get that student

        $result = mysqli_fetch_assoc($SP->getStudent($student_id, $dbconfig));
        echo "<h1>Pickup logged!</h1> <h2><small>Family name: {$result['family_name']}</small></h2>";
    }

} else if ($function == "app" && $district == "api") {
    include_once("app/api/public.php");
    exit();
} else if ($function == "edit") {
    //show edit functionality
    include_once(__DIR__ . "/app/dochead.php");

?>
    <h2>Edit</h2>
    <h2><small>School System: <?php echo strtoupper($district); ?></small></h2>

    <section class="card p-2">
    <h4>Add New Pickup ID / Generate QR Code</h4>

        <div class='row'>
            <div class="col-xs-12 col-sm-6">
                <label for="family_name">Student/Family Name:</label>
                <input name="family_name" id="family_name" class="form-control" />
            </div>
            <div class='col-xs-12 col-sm-6'>
                <button id="saveNew" class="btn btn-primary m-2">ADD</button>
            </div>
        </div>

        <div class='row'>
            <div class='col-xs-12 col-sm-6'>
                <section id="qrCodeArea">
                    <a href="#" target='_blank'>
                        <img id="qrcode"></img>
                    </a>
                </section>
            </div>
            <div class="col-xs-12 col-sm-6 hide" id="instructionsArea">
                <p><a href="#" target='_blank' class="btn-link" id="aLink">Click Here to Download/Print QR Code</a></p>
            </div>
        </div>

    </section>

    <script type="text/javascript">

        $("#instructionsArea, #qrCodeArea").hide();

        $("#saveNew").on('click', function(e) {
            e.preventDefault();
            $("#instructionsArea").hide();
            var bodySend = {"family_name" : $("#family_name").val(), "fname" : "saveNewFamily"};
            $.ajax({
                url: "/app/api/public.php",
                method: "POST",
                data: bodySend,
                dataType: 'json',
                success:  function(response) {
                    console.log(response);
                    var id = response.id;

                    //set up QR code for this entry
                    $.ajax({
                        url: "/app/api/public.php",
                        method: "POST",
                        data: {"fname" : "newQRCode", "student_id" : id, "district" : "<?php echo $district;?>"},
                        dataType: 'json',
                        success: function(response) {
                            console.log(response);
                            $("#instructionsArea, #qrCodeArea").show();
                            $("#qrCodeArea img#qrcode").prop('src', response.src);
                            $("#instructionsArea a#aLink, #qrCodeArea a").prop('href', response.src);

                            $("button#saveNew").html("ADD ANOTHER");

                        }, error: function(response) {
                            console.log('ERROR INSIDE');
                            console.error(response);

                            alert(response.status);
                        }
                    })

                }, error: function(response) {
                    console.log('ERROR OUTSIDE');

                    console.error(response);

                }
            });

            // req.done(function(response) {
            //     console.log(response);


            // })

        });
    </script>



<?php
} else {
    //show welcome & login
?>
    <?php include_once(__DIR__ . "/app/dochead.php"); ?>

<h1 class='display-3 text-center'>Welcome</h1>
<p>To view pickups recorded in the last 12 hours, click "Pickups" above.</p>
<p>To add students/families and create QR codes for printing, click "Add/Edit".</p>




<?php
}

include_once(__DIR__ . "/app/docfoot.php");
