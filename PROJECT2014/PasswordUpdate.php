<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=big5">
	</head>
<body style="background-color:#E6E6FA">
<?php
    $username = $_POST['username'];
		$password = $_POST['pwd'];
		if($password==null)
		{
			echo '<font color="'.red.'">Password cannot be blank. Please enter a valid password</font>';
			echo "<br/><br/>";
				Echo "<a href='http://uisacad.uis.edu/~kmulpu2/PasswordReset.html'>Back To Password Reset Page</a>";	
			exit;
		}
		$connection = ocilogon("tanis2","oracle","oracle.uis.edu");
		$sqlquery = "UPDATE CREDENTIALS SET password='".$password."' WHERE username='".$username."'";
		$sql_id = ociparse($connection,$sqlquery);
		if(!$sql_id) {
			$e=oci_error($connection);
			echo "The following error occured:";
			print htmlentities($e['message']);
			exit;
		} 
		$returnValue = ociexecute($sql_id,OCI_DEFAULT);
		
		if($returnValue==1)
		{
			echo '<font color="'.blue.'">Password was reset successfully</font>';
		ocicommit($connection);
		echo "<br/><br/>";
				Echo "<a href='http://uisacad.uis.edu/~kmulpu2/DillardsReporting.html'>Back To Login Page</a>";	
		}
		else {
			echo '<font color="'.red.'">Unable to reset password due to server issues. Please try again later</font>';
		}
		
OCIFreeStatement($sql_id);

OCILogoff ($connection);
?>
</body>
</html>