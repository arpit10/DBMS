<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=big5">
	</head>
<body style="background-color:#E6E6FA">
	<div id="logout" align="right" onclick="{alert('You are going to logout');}">
		<a href="http://uisacad.uis.edu/~kmulpu2/DillardsReporting.html">Logout</a>
	</div>
	<?php
		$username = $_POST['username'];
		$password = $_POST['pwd'];
		$connection = ocilogon("tanis2","oracle","oracle.uis.edu");
		$sqlquery = "SELECT count(*) FROM CREDENTIALS WHERE username='".$username."' AND password='".$password."'";
		$sql_id = ociparse($connection,$sqlquery);
		if(!$sql_id) {
			$e=oci_error($connection);
			echo "The following error occured:";
			print htmlentities($e['message']);
			exit;
		} 
		ociexecute($sql_id,OCI_DEFAULT);
		while (ocifetch($sql_id)) {
			$result = ociresult($sql_id,1);
			
			if ($result == 1) {
				Echo "<h3>You have logged in successfully<h3/>";
				
		Echo "<a href='http://uisacad.uis.edu/~kmulpu2/criteriaForReport.html'>Click here to generate reports</a>";	
		}
			else{
				echo '<font color="'.red.'">Invalid login credentials. Please enter correct username and password</font>';
				echo "<br/><br/>";
				Echo "<a href='http://uisacad.uis.edu/~kmulpu2/DillardsReporting.html'>Back To Login Page</a>";	
			}
		}
		ocicommit($connection);
		OCIFreeStatement($sql_id);
		ocilogoff($connection);
	?>
</body>
</html>