<body style="background-color:#E6E6FA">
<?php
    $connection = ocilogon("tanis2", "oracle", "oracle.uis.edu");
	if(!$connection){
		echo "Service is currently unavailable. Please try later.";
		exit;
	}
	else {
		$sqlquery = "SELECT Store,City,State,Zip,MSA,MSA_Name FROM Store_MSA";
		$sql_id = ociparse($connection,$sqlquery);

if(!$sql_id){
	$e=oci_error($connection);
	print htmlentities($e['message']);
	exit;
}
ociexecute($sql_id,OCI_DEFAULT);
echo "<h3>List of valid Store IDs</h3>";
	
	echo "<table border=1>";
	echo "<tr><td>".StoreID ."</td><td></td><td>".City ."</td><td></td><td>".State ."</td><td></td><td>".Zip ."</td><td></td><td>".MSA ."</td><td></td><td>".MSAName ."</td><td></td><td></td></tr>";
	ociexecute($sql_id,OCI_DEFAULT);
	while($row = OCI_Fetch_Array($sql_id,OCI_NUM)){

		echo "<tr><td>".$row[0] ."</td><td></td><td>".$row[1] ."</td><td></td><td>".$row[2] ."</td><td></td><td>
		".$row[3] ."</td><td></td><td>".$row[4] ."</td><td></td><td>".$row[5] ."</td><td></td><td></td></tr>";
	}
	echo "</table>";
	}
?>
</body>