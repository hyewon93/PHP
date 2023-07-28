<?php 
    include "database.class.php";

    class basic {

        public $errors;
        public $messages;
        public $db;

        public function __construct() {
            $this->db = new database();
            $this->messages = array();
        }

        public function addMessage($message) {
            array_push($this->messages, $message);
        }

        public function error($message) {
            $this->errors = true;
            $this->addMessage($message);
        }

        public function printMessages($seperator = " ") {

            $return = implode($seperator, $this->messages);

            return $return;
        }
    }
?>