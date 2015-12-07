<?php

    session_start();
	
echo "Details were updated for the following customer:";
echo "<ul>";
if (isset($_SESSION["newCustomerLastName"]) & isset($_SESSION["newCustomerLastName"]) & isset($_SESSION["newCustomerEmail"])){
echo "<li>".$_SESSION["newCustomerLastName"].", ".$_SESSION["newCustomerFirstName"]."</li>";
echo "Email ID: ";
echo $_SESSION["newCustomerEmail"];
}
echo "</ul>";
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
	$sqlquery = "SELECT CustomerID FROM CUSTOMER WHERE LastName='".$_SESSION["newCustomerLastName"]."' AND FirstName='".$_SESSION["newCustomerFirstName"]."' AND Email='".$_SESSION["newCustomerEmail"]."'";
$sql_id = ociparse($connection,$sqlquery);
if(!$sql_id){
	$e=oci_error($connection);
	print htmlentities($e['message']);
	exit;
}
ociexecute($sql_id,OCI_DEFAULT);
echo "Customer ID =";
while(oci_fetch($sql_id))
echo ociresult($sql_id, 1);

$sqlquery1 = "SELECT * FROM TRANS WHERE CustomerID IN(SELECT CustomerID FROM CUSTOMER WHERE LastName='".$_SESSION["newCustomerLastName"]."' AND FirstName='".$_SESSION["newCustomerFirstName"]."' AND Email='".$_SESSION["newCustomerEmail"]."')";

$sql_id1 = ociparse($connection,$sqlquery1);
if(!$sql_id1){
	$e=oci_error($connection);
	print htmlentities($e['message']);
	exit;
}
ociexecute($sql_id1,OCI_DEFAULT);
echo "<h3>Transaction Details of the Customer</h3>";
	echo "<table>";
	echo "<tr><td>".TransactionID ."</td><td></td><td>".DateAcquired ."</td><td></td><td>".AcquisitionPrice ."</td><td></td><td>".DateSold ."</td><td></td><td>".AskingPrice ."</td><td></td><td>".SalesPrice ."</td><td></td><td>".CustomerID."</td><td></td><td>".WorkID."</td></tr>";
	while($row = OCI_Fetch_Array($sql_id1,OCI_NUM)){

		echo "<tr><td>".$row[0] ."</td><td></td><td>".$row[1] ."</td><td></td><td>".$row[2] ."</td><td></td><td>".$row[3]."</td><td></td><td>".$row[4]."</td><td></td><td>".$row[5]."</td><td></td><td>".$row[6]."</td><td></td><td>".$row[7]."</td></tr>";
	}
	echo "</table>";
ocilogoff($connection);
?>
</body>