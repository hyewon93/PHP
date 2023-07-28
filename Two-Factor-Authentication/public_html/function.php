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

            if(!empty($_POST['type'])) {

                if($_POST['type'] == "email") {
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
        
                                if(!empty($user->twoFactorAuthKey)) {
                                    redir_post("./twoFactorAuth.php", []);
        
                                } else {
                                    redir_post("./home.php", []);
                                }
        
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

                } else if($_POST['type'] == "2fa") {

                    $user = new user($_SESSION['id']);

                    if(empty($_POST['otpCode'])) {
                        $user->error("Missing OTP Code.");
                    } else if(!is_numeric($_POST['otpCode']) || strlen($_POST['otpCode']) != 6) {
                        $user->error("Invalid OTP Code. (LOGIN-2)");
                    }

                    if(empty($user->twoFactorAuthKey)) {
                        $user->error("Missing Secret Code.");
                    }

                    if(!$user->errors) {

                        $authenticator = new googleAuthenticator();
                        $checkResult = $authenticator->verifyCode($user->twoFactorAuthKey, $_POST['otpCode']);
        
                        if($checkResult) {
                            $_SESSION['twoFactorAuth'] = $user->twoFactorAuthKey;
        
                            redir_post("home.php", []);
        
                        } else {
                            redir_post("./twoFactorAuth.php", [
                                "error" => "Invalid OTP Code. (LOGIN-3)"
                            ]);
                        }

                    } else {
                        redir_post("./twoFactorAuth.php", [
                            "error" => $user->printMessages("\n")
                        ]);
                    }
                }

            } else {
                redir_post("../index.php", [
                    "error" => "There was an error logging in. (LOGIN-1)"
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
        } else if($_POST['f'] == "twoFactorAuth") {
            $user = new user($_SESSION['id']);

            if(!empty($user->id)) {

                if(!empty($user->twoFactorAuthKey)) {
                    // OFF

                    $user->save([
                        "twoFactorAuthKey" => null
                    ]);

                    if(!$user->errors) {
                        $_SESSION['twoFactorAuth'] = null;

                        redir_post("home.php", []);

                    } else {
                        redir_post("home.php", [
                            "error" => $user->printMessages("\n")
                        ]);
                    }

                } else {
                    // ON

                    if(empty($_POST['otpCode'])) {
                        $user->error("Missing OTP Code.");
                    } else if(!is_numeric($_POST['otpCode']) || strlen($_POST['otpCode']) != 6) {
                        $user->error("Invalid OTP Code. (2FA-1)");
                    }
        
                    if(empty($_POST['secretCode'])) {
                        $user->error("Missing Secret Code.");
                    }
        
                    if(!$user->errors) {
                        $authenticator = new googleAuthenticator();
                        $checkResult = $authenticator->verifyCode($_POST['secretCode'], $_POST['otpCode']);
        
                        if($checkResult) {
        
                            $user->save([
                                "twoFactorAuthKey" => $_POST['secretCode']
                            ]);
        
                            if(!$user->errors) {
                                $_SESSION['twoFactorAuth'] = $_POST['secretCode'];
        
                                redir_post("home.php", []);
        
                            } else {
                                redir_post("twoFactorAuthSetting.php", [
                                    "error" => $user->printMessages("\n")
                                ]);
                            }
        
                        } else {
                            redir_post("twoFactorAuthSetting.php", [
                                "error" => "Invalid OTP Code. (2FA-2)"
                            ]);
                        }
        
                    } else {
                        redir_post("twoFactorAuthSetting.php", [
                            "error" => $user->printMessages("\n")
                        ]);
                    }
                }

            } else {
                redir_post("home.php", [
                    "error" => "There was an error getting your account. (2FA-3)"
                ]);
            }
        }
    }
?>