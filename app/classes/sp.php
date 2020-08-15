<?php

class StudentPickup
{

    public $testing = false;

    public function setTesting($t) {
        $this->testing = $t;
    }

    public function echodebug($content) {
        if ($this->testing == true) {
            echo "<pre>";
            print_r($content);
            echo "</pre>";
            echo "<pre>";
            debug_backtrace();
            echo "</pre>";
        }
    }

    public function getStudent($student_id, $dbconfig)
    {
        $mysqli = new mysqli($dbconfig['host'], $dbconfig['user'], $dbconfig['pass'], $dbconfig['dbname']);

        $sql = "SELECT * FROM student WHERE id = $student_id;";

        return $mysqli->query($sql);
    }
}
