<?php

	session_start();
	
	function message() {
		if (isset($_SESSION["message"])) {
			$output = "<p>";
			$output .= htmlentities($_SESSION["message"]);
			$output .= "</p>";
			
			// clear message after use
			$_SESSION["message"] = null;
			
			return $output;
		}
	}

	function errors() {
		if (isset($_SESSION["errors"])) {
			$errors = $_SESSION["errors"];
			
			// clear message after use
			$_SESSION["errors"] = null;
			
			return $errors;
		}
	}
	
?>