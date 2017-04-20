<?php

	function connectionToDataBase() {
		$servername = "localhost";
		$username = "root";
		$password = "root";
		$dbname = "expresatec";

		$conn = new mysqli($servername, $username, $password, $dbname);
		
		if ($conn->connect_error) {
			return null;
		} else {
			return $conn;
		}
	}

	function attemptLogin($userName) {

		$conn = connectionToDataBase();

		if ($conn != null) {

			$sql = "SELECT * FROM Users WHERE username = '$userName'";
		
			$result = $conn->query($sql);

			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
                    $conn->close();
                    return array("status" => "SUCCESS", "password" => $row['passwrd']);
                }
			}
			else {
				$conn -> close();
				return array("status" => "Wrong credentials provided!");
			}
		}
		else {
			$conn -> close();
			return array("status" => "CONNECTION WITH DB WENT WRONG");
		}
	}

	function verifyUserNotExists($userName) {

		$conn = connectionToDataBase();

		if ($conn != null) {
			$sql = "SELECT username FROM Users WHERE username = '$userName'";

			$result = $conn->query($sql);

			if ($result->num_rows > 0) {
				$conn -> close();
			    return array("status" => "USERNAME ALREADY IN USE");
			} else {
				$conn -> close();
				return array("status" => "SUCCESS");
			}
		} else {
			$conn -> close();
			return array("status" => "CONNECTION WITH DB WENT WRONG");
		}
	}

	function attemptRegister($userName, $userPassword) {
		$conn = connectionToDataBase();

		if ($conn != null) {
			$sql = "INSERT INTO Users (username, passwrd) VALUES ('$userName', '$userPassword')";
		
			if (mysqli_query($conn, $sql))  {
			    $conn -> close();
			    return array("status" => "SUCCESS");
			}
			else  {
				$conn -> close();
			    return array("status" => "SOMETHING WENT WRONG WHILE SAVING YOUR DATA.");
			}
		} else {
			$conn -> close();
			return array("status" => "CONNECTION WITH DB WENT WRONG");
		}
	}

	function attemptLoadComments($category) {
		$conn = connectionToDataBase();

		if ($conn != null) {
			session_start();

			if (isset($_SESSION['username'])) {
			    
			    if ($category == "all" || $category == "oldest") {
			        $sql = "SELECT * FROM Comments";
			    } elseif ($category == "popular") {
			    	$sql = "SELECT * FROM Comments ORDER BY votes ASC";
			    } elseif ($category == "unpopular") {
			    	$sql = "SELECT * FROM Comments ORDER BY votes DESC";
			    } elseif($category == "controversial") {
			    	$sql = "SELECT * FROM Comments ORDER BY ABS(votes) DESC";
				} else {
				    $sql = "SELECT * FROM Comments WHERE category = '$category'";
			    }
			    
				$result = $conn->query($sql);
				$response = array();
				if ($result->num_rows > 0) {
				    while($row = $result->fetch_assoc()) {
				    	array_push($response, array("category" => $row['category'], "comment" => $row['comment'], "votes" => $row['votes'], "id" => $row['id']));
					}

					$conn -> close();
					return array("status" => "SUCCESS", "comments" => $response);
				} else {
			        $conn->close();
			        return array("status" => "SOMETHING WENT WRONG WHILE RETRIEVING COMMENTS.");
				}
			} else {
				$conn -> close();
                return array("status" => "SUCCESS", "comments" => "");
			}
		} else {
			$conn -> close();
			return array("status" => "CONNECTION WITH DB WENT WRONG");
		}
	}

	function attemptPostComment($commentText, $commentCategory) {
		$conn = connectionToDataBase();

		if ($conn != null) {
			session_start();

			$sql = "INSERT INTO Comments (comment, category, votes) VALUES ('$commentText', '$commentCategory', 0)";
			if (mysqli_query($conn, $sql)) {
			    $conn->close();
			    return array("status" => "SUCCESS");
			} else {
			    $conn->close();
			    return array("status" => "SOMETHING WENT WRONG ATTEMPTING TO POST THE COMMENT.");
			}
		} else {
			$conn -> close();
			return array("status" => "CONNECTION WITH DB WENT WRONG");
		}
	}
	
	function attemptUpvoteComment($commentID) {
	    $conn = connectionToDataBase();
	    
	    if ($conn != null) {
	        session_start();
	        
	        $voteUserName = $_SESSION['username'];
	        $sql = "INSERT INTO Votes (username, commentID) VALUES ('$voteUserName', '$commentID')";
	        if (mysqli_query($conn, $sql)) {
	            
    	        $sql = "UPDATE Comments SET votes = votes + 1 WHERE id = '$commentID'";
                
                if (mysqli_query($conn, $sql)) {
                    
                    $sql = "SELECT votes FROM Comments WHERE id = '$commentID'";
                    $result = $conn->query($sql);

        			if ($result->num_rows > 0) {
        				while($row = $result->fetch_assoc()) {
                            $conn->close();
                            return array("status" => "SUCCESS", "votes" => $row['votes']);
                        }
        			} else {
        				$conn -> close();
				        return array("status" => "Unable to retrieve votes from comment");
        			}
                } else {
                    $conn -> close();
			        return array("status" => "Unable to increment the votes");
                }
	        } else {
	            $conn -> close();
		        return array("status" => "Unable to register vote in DB");
	        }
	    } else {
	        $conn -> close();
			return array("status" => "CONNECTION WITH DB WENT WRONG");
	    }
	}
	
	function attemptDownvoteComment($commentID) {
	    $conn = connectionToDataBase();
	    
	    if ($conn != null) {
	        session_start();
	        
	        $voteUserName = $_SESSION['username'];
	        $sql = "INSERT INTO Votes (username, commentID) VALUES ('$voteUserName', '$commentID')";
	        if (mysqli_query($conn, $sql)) {
	            
    	        $sql = "UPDATE Comments SET votes = votes - 1 WHERE id = '$commentID'";
                
                if (mysqli_query($conn, $sql)) {
                    
                    $sql = "SELECT votes FROM Comments WHERE id = '$commentID'";
                    $result = $conn->query($sql);

        			if ($result->num_rows > 0) {
        				while($row = $result->fetch_assoc()) {
                            $conn->close();
                            return array("status" => "SUCCESS", "votes" => $row['votes']);
                        }
        			} else {
        				$conn -> close();
				        return array("status" => "Unable to retrieve votes from comment");
        			}
                } else {
                    $conn -> close();
			        return array("status" => "Unable to decrement the votes");
                }
	        } else {
	            $conn -> close();
		        return array("status" => "Unable to register vote in DB");
	        }
	    } else {
	        $conn -> close();
			return array("status" => "CONNECTION WITH DB WENT WRONG");
	    }
	}
?>