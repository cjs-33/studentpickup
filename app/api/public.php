<?php

if ($_SERVER['OS'] == "Windows_NT") {
    include_once("/app/api/classes/sp.php");
} else {
    include_once("../classes/sp.php");
}
$SP = new StudentPickup();

$SP->setTesting(false);
$SP->echodebug($_POST);
$fname = $_POST['fname'];

if ($fname == "login") {

    //first see if user exists

    $userExists = $SP->doesUserExist($_POST['un']);

    if ($userExists) {
        //is password correct

        $passwordCorrect = $SP->isPasswordCorrect($_POST['un'], $_POST['pw']);

        if ($passwordCorrect) {
            //do login!

            $userData = $SP->getUser($_POST['un'])[0];
            $SP->updateUserActivity($userData['id']);

            $SP->startSession($userData['id'], time(), time() + 86400);

            $returnArray = array('result' => true, "user_id" => $userData['id'], "user_type" => $userData['user_type']);

            echo json_encode($returnArray);

            exit();
        } else {
            echo json_encode(array("result" => false));
            exit();
        }
    }
} else if ($fname == "logout") {
    $SP->endSession($_POST['user_id']);
} else if ($fname == "checkSession") {
    $checkedSession = $SP->checkSession($_POST['user_id']);

    echo json_encode($checkedSession);
    exit();

}else if ($fname == "saveNewFamily") {
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
