<?php 

    class user extends basic {

        public $id;
        public $email;
        public $name;
        public $password;

        function __construct($options = []) {
            parent::__construct();

            if(!is_array($options)) {
                $options = [
                    "id" => ((!empty($options)) ? $options : 0)
                ];
            }

            if(empty($options['id'])) {
                if(!empty($options['email'])) {
                    $options['email'] = trim($options['email']);

                    if(!filter_var($options['email'], FILTER_VALIDATE_EMAIL)) {
                        $this->error("Invalid Email.");
                        return;
                    }

                    $sql = "
                        SELECT id
                        FROM users
                        WHERE email = '" . $options['email'] . "'
                        LIMIT 0, 1
                    ";                    

                    $result = $this->db->query($sql);
                    $row = mysqli_fetch_object($result);

                    if(!empty($row->id)) {
                        $options['id'] = $row->id;
                    }
                }
            }

            if(isset($options['id']) && is_numeric($options['id']) && $options['id'] > 0) {
                $this->loadFromDB([
                    "id" => $options['id']
                ]);
            }
        }

        function create($formdata) {

            // Clean up submitted data.
            foreach($formdata as $key => $row) {
                $formdata[$key] = trim(strip_tags($row));
            }

            if(!$this->errors) {
                $formdata['password'] = md5($formdata['password']);

                $sql = "
                    INSERT INTO users (
                        email,
                        password,
                        name
                    )
                    VALUES (
                        '" . $formdata['email'] . "',
                        '" . $formdata['password'] . "',
                        '" . $formdata['name'] . "'
                    )
                ";

                if($this->db->query($sql)) {

                    $this->loadFromDB($this->db->insertID);

                } else {
                    $this->error("There was an error saving your data (USER-CRE-1).");
                }
            }
        }

        function loadFromDB($options = []) {

            if(!is_array($options)) {
                $options = [
                    "id" => ((!empty($options)) ? $options : 0)
                ];
            }

            $sql = "
                SELECT *
                FROM users
                WHERE id = '" . $options['id'] . "'
                LIMIT 0, 1
            ";

            $result = $this->db->query($sql);
            $row = mysqli_fetch_assoc($result);

            if(!empty($row)) {
                $this->id       = $row['id'];
                $this->email    = $row['email'];
                $this->name     = $row['name'];
                $this->password = $row['password'];
            }

            return $this;
        }
    }
?>