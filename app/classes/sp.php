<?php

class StudentPickup
{

    public $testing = false;
    public $dbconfig = array();

    function __construct()
    {
        $this->dbconfig = array(
            "host" => ($this->isLocal()) ? "localhost" : "localhost",
            "user" => ($this->isLocal()) ? "root" : "schpkp_usr",
            "pass" => ($this->isLocal()) ? "root" : "Wt*4z7p1",
            "dbname" => ($this->isLocal()) ? "studentpickup" : "local_prod__schoolpickup_net"
        );
    }

    public function isWin() {
        return ($_SERVER['OS'] == "Windows_NT") ? true : false;
    }

    private function isLocal()
    {
        return (stripos($_SERVER['SERVER_NAME'], "local") !== false);
    }

    public function setToken()
    {
        $theToken = $this->createGuid();

        $returnArray = array("login_token" => $theToken);
        return $returnArray;
    }

    private function createGuid()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    public function startSession($userId, $startTime, $endTime) {
        $mysqli = new mysqli($this->dbconfig['host'], $this->dbconfig['user'], $this->dbconfig['pass'], $this->dbconfig['dbname']);

        $sql = "INSERT INTO session (user_id, start, end) VALUES ($userId, $startTime, $endTime);";
        $result = mysqli_fetch_all($mysqli->query($sql), MYSQLI_ASSOC);
        $mysqli->close();

        session_start();
        $_SESSION['user_id'] = $userId;
        $thisUser = $this->getUser('', $userId)[0];
        $_SESSION['user_type'] = $thisUser['user_type'];
    }

    public function checkSession($userId) {
        $mysqli = new mysqli($this->dbconfig['host'], $this->dbconfig['user'], $this->dbconfig['pass'], $this->dbconfig['dbname']);

        $sql = "SELECT start_time, end_time, user_id FROM session WHERE user_id = $userId;";
        $result = mysqli_fetch_all($mysqli->query($sql), MYSQLI_ASSOC);
        $mysqli->close();

        if ($result['start_time'] < time() && $result['end_time'] < time()) {
            return true;
        } else {
            return false;
        }


    }

    public function endSession($userId) {
        $mysqli = new mysqli($this->dbconfig['host'], $this->dbconfig['user'], $this->dbconfig['pass'], $this->dbconfig['dbname']);

        $sql = "DELETE FROM session WHERE user_id = $userId;"; //AND district_id = $district_id (TODO)
        $result = mysqli_fetch_all($mysqli->query($sql), MYSQLI_ASSOC);
        $mysqli->close();

        return true;
    }


    public function setTesting($t)
    {
        $this->testing = $t;
    }

    public function echodebug($content)
    {
        if ($this->testing == true) {
            echo "<pre>";
            print_r($content);
            echo "</pre>";
            echo "<pre>";
            debug_backtrace();
            echo "</pre>";
        }
    }

    public function doesUserExist($email) {
        $mysqli = new mysqli($this->dbconfig['host'], $this->dbconfig['user'], $this->dbconfig['pass'], $this->dbconfig['dbname']);

        $sql = "SELECT id FROM user WHERE email LIKE '$email';";
        $result = mysqli_fetch_all($mysqli->query($sql), MYSQLI_ASSOC);
        $mysqli->close();
        if (count($result) > 0) {
            return true;
        } else {
            return false;
        }

    }

    public function isPasswordCorrect($email, $password) {
        //get user
        $savedPassword = $this->getUser($email)[0]['password'];

        if (password_verify($password, substr($savedPassword, 0, 60))) {
            return true;
        } else {
            return false;
        }

        //check if provided password matches the hash returned from this user
    }

    private function encryptPassword($pw) {
        return password_hash($pw, PASSWORD_BCRYPT);
    }

    public function updateUserActivity($id) {
        $mysqli = new mysqli($this->dbconfig['host'], $this->dbconfig['user'], $this->dbconfig['pass'], $this->dbconfig['dbname']);
        $time = time();

        $sql = "UPDATE user SET last_login = $time WHERE id = $id;";
        $mysqli->query($sql);

        $mysqli->close();

        return true;

    }

    public function getUser($email, $user_id = 0) {
        $mysqli = new mysqli($this->dbconfig['host'], $this->dbconfig['user'], $this->dbconfig['pass'], $this->dbconfig['dbname']);

        if ($user_id !== 0) {
            $sql = "SELECT * FROM user WHERE id = $user_id;";
        } else {
            $sql = "SELECT * FROM user WHERE email LIKE '$email';";
        }

        $result = mysqli_fetch_all($mysqli->query($sql), MYSQLI_ASSOC);

        $mysqli->close();
        return $result;
    }

    public function getStudent($student_id, $dbconfig)
    {
        $mysqli = new mysqli($this->dbconfig['host'], $this->dbconfig['user'], $this->dbconfig['pass'], $this->dbconfig['dbname']);

        $sql = "SELECT * FROM student WHERE id = $student_id;";

        return $mysqli->query($sql);
    }

    public function addNewFamily($family_name)
    {
        $timestamp_created = time();
        $sql = "INSERT INTO student (family_name, district_id, timestamp_created) VALUES (\"$family_name\", 1, " . $timestamp_created . ");";

        $mysqli = new mysqli($this->dbconfig['host'], $this->dbconfig['user'], $this->dbconfig['pass'], $this->dbconfig['dbname']);

        $mysqli->query($sql);

        $getSql = "SELECT id FROM student WHERE family_name = \"{$family_name}\" AND timestamp_created = {$timestamp_created};";

        $result = $mysqli->query($getSql);

        $mysqli->close();

        return mysqli_fetch_assoc($result);
    }

    public function saveFamilyQrCode($id, $codeSrc)
    {
        $mysqli = new mysqli($this->dbconfig['host'], $this->dbconfig['user'], $this->dbconfig['pass'], $this->dbconfig['dbname']);

        $sql = "UPDATE student SET qrcode_link = \"$codeSrc\" WHERE id = $id;";
        $result = $mysqli->query($sql);
        $mysqli->close();
        return $result;
    }

    public function getPickups()
    {
        $mysqli = new mysqli($this->dbconfig['host'], $this->dbconfig['user'], $this->dbconfig['pass'], $this->dbconfig['dbname']);
        $timestamp = time() - 43200; //12 hrs ago
        $sql = "SELECT p.*, s.family_name FROM pickups p LEFT JOIN student s ON p.student_id = s.id WHERE p.timestamp > {$timestamp};";

        $result = mysqli_fetch_all($mysqli->query($sql), MYSQLI_ASSOC);

        return $result;
    }
}
