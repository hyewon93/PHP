<?php
    class database {

        public $insertID = null;

        public function __construct() {

        }

        private function connect() {
            return mysqli_connect("localhost", "root", "hyewonDevDB", "hyewon_dev", 3308);
        }

        public function query($sql) {
            $conn = $this->connect();

            $result = mysqli_query($conn, $sql);
            $this->insertID = mysqli_insert_id($conn);

            return $result;
        }
    }
?>