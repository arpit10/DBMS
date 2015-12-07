<?php
    session_start();
	$_SESSION["criteria"] = $_POST["criteria"];
		$_SESSION["Year"] = $_POST["Year"];
?>
<body>
	<?php
       $connection = ocilogon("tanis2", "oracle", "oracle.uis.edu");
	if(!$connection){
		echo "Couldn't make a connection!";
		exit;
	}
	else {
		echo "You have connected to the UIS Oracle Database!! <p>";
	}
	
	$criteria=$_POST["criteria"];
	$Year=$_POST["Year"];
if($criteria=='Product')
{
	$sqlquery = "SELECT STORE_MSA.Store,UPC,Quantity,SALEDATE FROM SKUINFO,TRNSACT,STORE_MSA 
	WHERE SKUINFO.SKU = TRNSACT.SKU AND STORE_MSA.Store=TRNSACT.Store GROUP BY UPC,STORE_MSA.Store,Quantity,SALEDATE ORDER BY QUANTITY DESC";
	
	$sql_id = ociparse($connection,$sqlquery);
if(!$sql_id){
	$e=oci_error($connection);
	print htmlentities($e['message']);
	exit;
}
ociexecute($sql_id,OCI_DEFAULT);
	echo "<h3>Report - Products Summary</h3>";
	echo "<table>";
	echo "<tr><td>".Store ."</td><td></td><td>".UPC ."</td><td></td><td>".Quantity ."</td><td></td><td>
	".SaleDate ."</td><td></td><td></td></tr>";
	while($row = OCI_Fetch_Array($sql_id,OCI_NUM)){

		echo "<tr><td>".$row[0] ."</td><td></td><td>".$row[1] ."</td><td></td><td>".$row[2] ."</td><td></td><td>".$row[3] ."</td><td></td><td></td></tr>";
	}
	echo "</table>";
}
elseif ($criteria=='Dept') {
	$sqlquery = "SELECT STORE_MSA.Store As Store,MSA_Name,Dept,Brand,Amt,SaleDate FROM SKUINFO,TRNSACT,STORE_MSA 
	WHERE SKUINFO.SKU = TRNSACT.SKU AND STORE_MSA.Store=TRNSACT.Store GROUP BY STORE_MSA.Store,MSA_Name,Dept,Brand,Amt,SaleDate ORDER BY Amt DESC";
	$sql_id = ociparse($connection,$sqlquery);
if(!$sql_id){
	$e=oci_error($connection);
	print htmlentities($e['message']);
	exit;
}
ociexecute($sql_id,OCI_DEFAULT);
	echo "<h3>Report - Departments Summary</h3>";
	echo "<table>";
	echo "<tr><td>".Store ."</td><td></td><td>".MSA ."</td><td></td><td>".Dept ."</td><td></td><td>".Brand ."</td><td></td><td>".Amt ."</td><td></td><td>".SaleDate ."</td><td></td><td></td></tr>";
	while($row = OCI_Fetch_Array($sql_id,OCI_NUM)){

		echo "<tr><td>".$row[0] ."</td><td></td><td>".$row[1] ."</td><td></td><td>".$row[2] ."</td><td></td><td>
		".$row[3] ."</td><td></td><td>".$row[4] ."</td><td></td><td>".$row[5] ."</td><td></td><td></td></tr>";
	}
	echo "</table>";
}
	
ocilogoff($connection);
	?>
</body>