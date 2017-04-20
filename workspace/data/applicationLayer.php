<?php

header('Content-type: application/json');
require_once __DIR__ . '/dataLayer.php';

$action = $_POST["action"];

switch($action) {
	case "LOGIN" : loginFunction();
					break;
	case "LOGOUT" : logoutFunction();
	                break;
	case "REGISTER" : registerFunction();
					break;
	case "LOAD_COMMENTS" : loadCommentsFunction();
					break;
	case "POST_COMMENT" : postCommentFunction();
					break;
	case "HOME" : homeFunction();
                    break;
    case "UPVOTE" : upvoteFunction();
                    break;
    case "DOWNVOTE" : downvoteFunction();
                    break;
}

function loginFunction() {
	$userName = $_POST["username"];

	$result = attemptLogin($userName);

	if ($result["status"] == "SUCCESS") {
        $decryptedPassword = decryptPassword($result['password']);
        $userPassword = $_POST["userPassword"];

        if ($decryptedPassword === $userPassword) {
            session_start();
            $_SESSION['username'] = $userName;
            
            $response = array("message" => "Login Successful");
            echo json_encode($response);
        }
        else{
            header('HTTP/1.1 306 Wrong credentials');
            die("Wrong credentials");
        }
	}	
	else {
		header('HTTP/1.1 500' . $result["status"]);
		die($result["status"]);
	}
}

function registerFunction() {
	$userName = $_POST['username'];
	$result = verifyUserNotExists($userName);
	if ($result["status"] == "SUCCESS") {
		$userPassword = encryptPassword();

		$result = attemptRegister($userName, $userPassword);

		if ($result["status"] == "SUCCESS") {
		    session_start();
            $_SESSION['username'] = $userName;
			echo json_encode(array("message" => "Register Successful"));
		} else {
			header('HTTP/1.1 500' . $result["status"]);
			die($result["status"]);
		}
	} else {
		header('HTTP/1.1 409 ' . $result["status"]);
		die($result["status"]);
	}
}

function loadCommentsFunction() {
    $category = $_POST['category'];
	$result = attemptLoadComments($category);

	if ($result["status"] == "SUCCESS") {
		echo json_encode(array("message" => "Comments loaded successfully", "comments" => $result["comments"]));
	} else {
		header('HTTP/1.1 500' . $result["status"]);
		die($result["status"]);
	}
}

function logoutFunction() {
    session_start();
    if (isset($_SESSION['username'])) {
        unset($_SESSION['username']);
        session_destroy();
        echo json_encode(array('SUCCESS' => 'Session deleted'));
    }
    else {
        header('HTTP/1.1 406 Session has expired, you will be redirected to the login page');
        die("Session has expired");
    }
}

function homeFunction() {
    session_start();
    if (isset($_SESSION['username'])) {
        echo json_encode(array("message" => ""));
    }
    else {
        header('HTTP/1.1 406 Session not started');
        die("You haven't logged in! You will be redirected to the login page");
    }
}

function postCommentFunction() {
	$commentText = $_POST['commentText'];
	$commentCategory = $_POST['commentCategory'];
	
	$result = attemptPostComment($commentText, $commentCategory);

	if ($result["status"] == "SUCCESS") {
		echo json_encode(array("message" => "Comment posted successfully"));
	} else {
		header('HTTP/1.1 500' . $result["status"]);
		die($result["status"]);
	}
}

function upvoteFunction() {
    $commentID = $_POST['commentID'];
    
    $result = attemptUpvoteComment($commentID);
    
    if ($result["status"] == "SUCCESS") {
        echo json_encode(array("message" => "Upvoted successfully", "votes" => $result["votes"]));
    } else {
        header('HTTP/1.1 500' . $result["status"]);
		die($result["status"]);
    }
}

function downvoteFunction() {
    $commentID = $_POST['commentID'];
    
    $result = attemptDownvoteComment($commentID);
    
    if ($result["status"] == "SUCCESS") {
        echo json_encode(array("message" => "Downvoted successfully", "votes" => $result["votes"]));
    } else {
        header('HTTP/1.1 500' . $result["status"]);
		die($result["status"]);
    }
}

function encryptPassword() {
    $userPassword = $_POST['userPassword'];

    $key = pack('H*', "bcb04b7e103a05afe34763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3");
    $key_size =  strlen($key);
    
    $plaintext = $userPassword;

    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    
    $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $plaintext, MCRYPT_MODE_CBC, $iv);
    $ciphertext = $iv . $ciphertext;
    
    $userPassword = base64_encode($ciphertext);

    return $userPassword;
}

function decryptPassword($password) {
    $key = pack('H*', "bcb04b7e103a05afe34763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3");
    
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
    
    $ciphertext_dec = base64_decode($password);
    
    $iv_dec = substr($ciphertext_dec, 0, $iv_size);

    $ciphertext_dec = substr($ciphertext_dec, $iv_size);

    $password = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);
    
    $count = 0;
    $length = strlen($password);
    for ($i = $length - 1; $i >= 0; $i --) {
        if (ord($password{$i}) === 0) {
            $count++;
        }
    }

    $password = substr($password, 0,  $length - $count);

    return $password;
}

?>