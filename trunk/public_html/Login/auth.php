<?php
$bypass_auth = false;
$showErrors = false;

if (isset($_SESSION['SIGNATURE']) && isset($_SESSION['username'])) {
	if ($_SESSION['SIGNATURE'] != sha1($_SERVER['HTTP_USER_AGENT'] . dirname($_SERVER["REQUEST_URI"]))) {
		unset($_SESSION['username']);
		$_SESSION['SIGNATURE'] = sha1($_SERVER['HTTP_USER_AGENT'] . dirname($_SERVER["REQUEST_URI"]));
	}
} else {
	$_SESSION['SIGNATURE'] = sha1($_SERVER['HTTP_USER_AGENT'] . dirname($_SERVER["REQUEST_URI"]));
}

$timedout = false;
// Timeout after 10 minutes of inactivity.
if (isset($_SESSION['lastActivity'])) {
	$session_life = time() - $_SESSION['lastActivity'];

	if ($session_life > 10 * 60) {
		$timedout = true;
		unset($_SESSION['username']);
	}
}

$_SESSION['lastActivity'] = time();

require_once $app_root . '/config.inc.php';

function serverError($message) {
	global $showErrors;
	if (headers_sent()) {
		if (((strlen($_SERVER["HTTP_HOST"]) >= 9) && (substr($_SERVER["HTTP_HOST"], -9) == "localhost")) || $showErrors) {
			die("Internal Server Error: $message");
		} else {
			die("Internal Server Error");
		}
	} else {
		if (((strlen($_SERVER["HTTP_HOST"]) >= 9) && (substr($_SERVER["HTTP_HOST"], -9) == "localhost")) || $showErrors) {
			header("Internal Server Error: $message", true, 500);
		} else {
			header("Internal Server Error", true, 500);
		}
	}
}

// Initialize session variables
if (!isset($_SESSION['username'])) {
	$_SESSION['user_id']=0;
	$_SESSION['username']='';
	$_SESSION['password']='';
	$_SESSION['cur_page']='';
	$_SESSION['last_access']='';
}
else if (!isset($_SESSION['password'])) {
	$_SESSION['password']='';
	$_SESSION['cur_page']='';
	$_SESSION['last_access']='';
}
else if (!isset($_SESSION['cur_page'])) {
	$_SESSION['cur_page']='';
	$_SESSION['last_access']='';
}
else if (!isset($_SESSION['last_access'])) {
	$_SESSION['last_access']='';
}

// Check to see if the user ID is still valid, if not, then clear them!
if (($_SESSION['user_id'] != 0)
		&& ($_SESSION['username'] != '')
		&& ($_SESSION['password'] != '')) {
	$link = new mysqli($dbHostName, $dbUserName, $dbUserPasswd, $dbName);
	
	if (mysqli_connect_error()) {
		serverError('Could not connect: (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
	}

	$userOK = false;
	
	$query = 'SELECT id FROM users'
			. ' WHERE username = ?'
			. ' AND password = ?'
			. ' AND id = ?';
	if ($stmt = $link->prepare($query)) {
		$stmt->bind_param("sss", $_SESSION['username'], $_SESSION['password'], $_SESSION['user_id']);
		
		$stmt->execute();
		
		$stmt->store_result();
		
		if ($stmt->num_rows == 1)
			$userOK = true;
		
		$stmt->free_result();
		
		$stmt->close();
	} else {
		serverError('Query failed: (' . $link->errno . ') ' . $link->error);
	}
	
	$link->close();
	
	if (!$userOK) {
		// username, password, id isn't a valid triplet, clear everything!
		$_SESSION['user_id']=0;
		$_SESSION['username']='';
		$_SESSION['password']='';
		$_SESSION['cur_page']='';
		$_SESSION['last_access']='';
	}
}

// See if the user is logging in.  If so, then verify the username and password
// against the database of usernames and passwords.  At this point, we're not
// encrypting the passwords across the network.
if (($_SESSION['username'] == '')
		&& isset($_POST['username'])
		&& isset($_POST['password'])) {
	$link = new mysqli($dbHostName, $dbUserName, $dbUserPasswd, $dbName);
	
	if (mysqli_connect_error()) {
		serverError('Could not connect: (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
	}

	$username = $_POST['username'];
	$password = $_POST['password'];
	if (get_magic_quotes_gpc()) { 
		$username = stripslashes($username);
		$password = stripslashes($password);
	} 

	$query = 'SELECT id, username, password, temp_password, last_access FROM users'
			. ' WHERE username = ?'
			. ' AND ((password = ?)'
			. ' OR (temp_password = ?))';
	if ($stmt = $link->prepare($query)) {
		$stmt->bind_param('sss', $username, $password, $password);
		$stmt->execute();
		
		$stmt->store_result();
		
		if ($stmt->num_rows == 1) {
			$stmt->bind_result($_SESSION['user_id'],
					$_SESSION['username'],
					$_SESSION['password'],
					$tempPassword,
					$_SESSION['last_access']);
			$stmt->fetch();
			
			if ($_SESSION['last_access'] == '') {
				$_SESSION['last_access'] = 'Never';
			}

			if ($updStmt = $link->prepare('UPDATE users SET last_access = NOW() WHERE id = ?')) {
				$updStmt->bind_param('s', $_SESSION['user_id']);
				$updStmt->execute();
				
				$updStmt->close();
			} else {
				serverError('Query failed: (' . $link->errno . ') ' . $link->error);
			}
			
			if ($tempPassword == $password) {
				if ($updStmt = $link->prepare('UPDATE users SET password = ?, temp_password = "" WHERE id = ?')) {
					$updStmt->bind_param('ss', $password, $_SESSION['user_id']);
					$updStmt->execute();
					
					$updStmt->close();
				} else {
					serverError('Query failed: (' . $link->errno . ') ' . $link->error);
				}
			}
		}
		
		$stmt->free_result();
		
		$stmt->close();
		
		setcookie('MortonArbAdminUser', base64_encode($username), time() + 365 * 86400,
				'/Login', '', '', true);
	} else {
		if ($stmt=$link->prepare('SELECT * FROM users')) {
			serverError('Query failed: (' . $link->errno . ') ' . $link->error);
		} else {
			if ($stmt=$link->prepare('CREATE TABLE IF NOT EXISTS `users` ('
				. ' `id` int(10) NOT NULL auto_increment,'
				. ' `username` varchar(256) NOT NULL,'
  				. ' `password` char(40) NOT NULL,'
				. ' `temp_password` char(40) NOT NULL,'
 				. ' `last_access` datetime default NULL,'
				. ' PRIMARY KEY  (`id`)'
				. ') DEFAULT CHARSET=utf8 COLLATE=utf8_bin')) {
				$stmt->execute();
				$stmt->close();

				if ($stmt=$link->prepare('INSERT INTO `users` (`username`, `password`)'
					. ' VALUES (\'Administrator\', \'3a7ee1b4a159efb9e069dc1eeba8ab5c74e701d0\')')) {
					$stmt->execute();
					$stmt->close();	
				}
				
				if ($stmt=$link->prepare('CREATE TABLE IF NOT EXISTS `longProcess` ('
 					. ' `key` varchar(128) NOT NULL,'
 					. ' `subkey` varchar(128) NOT NULL,'
 					. ' `value` varchar(1024) NOT NULL'
					. ') DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;')) {
					$stmt->execute();
					$stmt->close();
				}
			}
			
			if (($username == 'Administrator') && ($password == '3a7ee1b4a159efb9e069dc1eeba8ab5c74e701d0')) {
				if ($_SESSION['last_access'] == '') {
					$_SESSION['last_access'] = 'Never';
				}

				$_SESSION['user_id'] = 1;
				$_SESSION['username'] = 'Administrator';
				$_SESSION['password'] = 'e5c446ea96ebe680608d01898be90f719a031fd2';
				
				if ($updStmt = $link->prepare('UPDATE users SET last_access = NOW() WHERE id = ?')) {
					$updStmt->bind_param('s', $_SESSION['user_id']);
					$updStmt->execute();
					
					$updStmt->close();
				} else {
					serverError('Query failed: (' . $link->errno . ') ' . $link->error);
				}
			}
		}
	}
	
	$link->close();
}

// Our authentication is complete.
// $_SESSION['username'] will be empty if not authenticated.
?>
