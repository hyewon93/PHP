<?php 

    session_start(); 

    require_once "./includes/includes.php";

    function redir_post($redir_url, $redir_values_array){
        echo("<form name='redir_form' action='$redir_url' method='post'>");
        foreach ($redir_values_array as $key => $value) {
            echo("<input type='hidden' name='$key' value='$value'>");
        }
        echo("</form> <script language='javascript'> document.redir_form.submit(); </script>");
    }

    if(!empty($_POST['f'])) {
        if($_POST['f'] == "login") {
            $user = new user();

            if(empty($_POST['email'])) {
                $user->error("Missing Email.");
            }

            if(empty($_POST['password'])) {
                $user->error("Missing Password.");
            }

            if(!$user->errors) {

                $user = new user([
                    "email" => $_POST['email']
                ]);


                if(!empty($user->id)) {

                    if($user->password == md5($_POST['password'])) {
                        $_SESSION['email'] = $user->email;
                        $_SESSION['name'] = $user->name;
                        $_SESSION['id'] = $user->id;

                        redir_post("./home.php", []);

                    } else {
                        redir_post("../index.php", [
                            "error" => "Incorrect password."
                        ]);
                    }

                } else {
                    redir_post("../index.php", [
                        "error" => $user->printMessages("\n")
                    ]);
                }

            } else {
                redir_post("../index.php", [
                    "error" => $user->printMessages("\n")
                ]);
            }
            
        } else if($_POST['f'] == "logout") {

            session_unset();
            session_destroy();

            redir_post("../index.php", []);

        } else if($_POST['f'] == "singup") {
            $user = new user();

            if(empty($_POST['email'])) {
                $user->error("Missing Email.");
            }

            if(empty($_POST['name'])) {
                $user->error("Missing Name.");
            }

            if(empty($_POST['password'])) {
                $user->error("Missing Password.");

            } else if(empty($_POST['re_password'])) {
                $user->error("Missing Re-Password.");

            } else {
                if($_POST['password'] !== $_POST['re_password']) {
                    $user->error("unmatched password.");
                }
            }

            if(!$user->errors) {
                $user->create($_POST);

                if(!$user->errors) {
                    
                    redir_post("../index.php", [
                        "message" => "Your account created successfully."
                    ]);

                } else {

                    redir_post("signup.php", [
                        "error" => $user->printMessages()
                    ]);
                }

            } else {
                redir_post("signup.php", [
                    "error" => $user->printMessages("\n")
                ]);
            }
        }
    }
?>