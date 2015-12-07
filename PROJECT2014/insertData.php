<?php session_start();
$_SESSION["newCustomerLastName"] = $_POST["newCustomerLastName"];
	$_SESSION["newCustomerFirstName"] = $_POST["newCustomerFirstName"];
	$_SESSION["newCustomerEmail"] = $_POST["newCustomerEmail"];
	?>
<body>
<?php
       $connection = ocilogon("kmulpu2", "oracle", "oracle.uis.edu");
	if(!$connection){
		echo "Couldn't make a connection!";
		exit;
	}
	else {
		echo "You have connected to the UIS Oracle Database!! <p>";
	}
	
	$newCustomerLastName=$_POST["newCustomerLastName"];
	$newCustomerFirstName=$_POST["newCustomerFirstName"];
	$newCustomerAreaCode=$_POST["newCustomerAreaCode"];
	$newCustomerPhoneNumber=$_POST["newCustomerPhoneNumber"];
	$newCustomerEmail=$_POST["newCustomerEmail"];
	$varArtistLastName=$_POST["varArtistLastName"];
	$varWorkTitle=$_POST["varWorkTitle"];
	$varWorkCopy=$_POST["varWorkCopy"];
	$newTransSalesPrice=$_POST["newTransSalesPrice"];
	
	$sqlquery = "call INSERTCUSTOMERWITHTRANSACTION('".$newCustomerLastName."','".$newCustomerFirstName."','".$newCustomerAreaCode."','".$newCustomerPhoneNumber."','".$newCustomerEmail."','".$varArtistLastName."','".$varWorkTitle."','".$varWorkCopy."','".$newTransSalesPrice."')";
	$sql_id = ociparse($connection,$sqlquery);
	
	if (!$sql_id){
		$e=oci_error($connection);
		print htmlentities($e['message']);
		exit;
	}
	
	$return = ociexecute($sql_id, OCI_DEFAULT);
	if(!$return){
	echo "Error executing stored procedure";
	}
	else {
		echo "Customer and Transaction Data Updated successfully";
	}
	
	OCIFreeStatement($sql_id);
	
	OCILogoff ($connection);
?>
</body>