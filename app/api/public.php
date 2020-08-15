<?php

$SP = new StudentPickup();

$SP->setTesting(false);
$SP->echodebug($_POST);
$fname = $_POST['fname'];

if ($fname == "saveNewFamily") {
    $SP->echodebug($_POST);

    $family_name = $_POST['family_name'];

    $resultArray = $SP->addNewFamily($family_name);

    if (isset($resultArray['id'])) {
        echo json_encode(array("id" => $resultArray['id']));
        exit();
    } else {
        echo json_encode(false);
        exit();
    }


    //this needs to return an ID so that the QR code can be generated


} else if ($fname == "newQRCode") {
    $student_id = $_POST['student_id'];
    $district = $_POST['district'];

    $url = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=https://schoolpickup.net/pickup/{$district}/{$student_id}";

    // $imgname = "temp/images/1.png";

    // file_put_contents($imgname, file_get_contents($url));

    // $result = file_get_contents($url);

    $result = $url;

    if ($result) {
        $SP->saveFamilyQrCode($student_id, $result);
        echo json_encode(array("status" => true, "src" => $result));
        exit();
    } else {
        echo json_encode(array("status" => false));
        exit();
    }


} else {
    $SP->setTesting(true);
    $SP->echodebug($_POST);

    header('HTTP/1.0 401 Unauthorized');
    die("Not Authorized");
}