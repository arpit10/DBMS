<?php
    session_start();
	$_SESSION["category"] = $_POST["category"];
		$_SESSION["year"] = $_POST["year"];
		$_SESSION["quarter"]=$_POST["quarter"];
		$_SESSION["storeID"]=$_POST["storeID"];
?>
<body style="background-color:#E6E6FA">
	<div id="logout" align="right" onclick="{alert('You are going to logout');}">
		<a href="http://uisacad.uis.edu/~kmulpu2/DillardsReporting.html">Logout</a>
	</div>
	<?php
       $connection = ocilogon("tanis2", "oracle", "oracle.uis.edu");
	if(!$connection){
		echo "Service is currently unavailable. Please try later.";
		exit;
	}
	
	
	$criteria=$_POST["category"];
	$Year=$_POST["year"];
	$quarter = $_POST["quarter"];
	$store=$_POST["storeID"];
	
	if($quarter=="Q1")
	{
		$month1 = "JAN";
		$month2 = "FEB";
		$month3 = "MAR";
	}
	elseif($quarter=="Q2")
	{
		$month1 = "APR";
		$month2 = "MAY";
		$month3 = "JUN";
	}
	elseif($quarter=="Q3")
	{
		$month1 = "JUL";
		$month2 = "AUG";
		$month3 = "SEP";
	}
	elseif($quarter=="Q4")
	{
		$month1 = "OCT";
		$month2 = "NOV";
		$month3 = "DEC";
	}
	
if($criteria=='Product')
{
	if ($store!=null && $quarter!="None") {
		$sqlquery = "SELECT UPC,BRAND,STORE_MSA.Store,SUM(Quantity) As TotalQuantity,SALEDATE 
		FROM SKUINFO,TRNSACT,STORE_MSA 
		WHERE SKUINFO.SKU = TRNSACT.SKU 
		AND STORE_MSA.Store=TRNSACT.Store 
		AND substr(SALEDATE, 8,2) = substr(".$Year.", 3,2) 
		AND substr(SALEDATE, 4,3) IN('".$month1."','".$month2."','".$month3."') 
		AND STORE_MSA.Store = ".$store." 
		GROUP BY UPC,BRAND,STORE_MSA.Store,SALEDATE 
		ORDER BY TotalQuantity DESC,UPC,SALEDATE";	
	} 
	elseif ($store!=null && $quarter=="None") {
		$sqlquery = "SELECT UPC,BRAND,STORE_MSA.Store,SUM(Quantity) As TotalQuantity,SALEDATE 
		FROM SKUINFO,TRNSACT,STORE_MSA 
		WHERE SKUINFO.SKU = TRNSACT.SKU 
		AND STORE_MSA.Store=TRNSACT.Store 
		AND substr(SALEDATE, 8,2) = substr(".$Year.", 3,2)
		AND STORE_MSA.Store = ".$store." 
		GROUP BY UPC,BRAND,STORE_MSA.Store,SALEDATE 
		ORDER BY TotalQuantity DESC,UPC,SALEDATE";	
	}
	elseif ($store==null && $quarter!="None") {
		$sqlquery = "SELECT UPC,BRAND,STORE_MSA.Store,SUM(Quantity) As TotalQuantity,SALEDATE 
		FROM SKUINFO,TRNSACT,STORE_MSA 
		WHERE SKUINFO.SKU = TRNSACT.SKU 
		AND STORE_MSA.Store=TRNSACT.Store 
		AND substr(SALEDATE, 8,2) = substr(".$Year.", 3,2) 
		AND substr(SALEDATE, 4,3) IN('".$month1."','".$month2."','".$month3."') 
		GROUP BY UPC,BRAND,STORE_MSA.Store,SALEDATE 
		ORDER BY TotalQuantity DESC,UPC,SALEDATE";
	
	}
	else{
		$sqlquery = "SELECT UPC,BRAND,STORE_MSA.Store,SUM(Quantity) As TotalQuantity,SALEDATE 
		FROM SKUINFO,TRNSACT,STORE_MSA 
		WHERE SKUINFO.SKU = TRNSACT.SKU 
		AND STORE_MSA.Store=TRNSACT.Store 
		AND substr(SALEDATE, 8,2) = substr(".$Year.", 3,2)
		GROUP BY UPC,BRAND,STORE_MSA.Store,SALEDATE 
		ORDER BY TotalQuantity DESC,UPC,SALEDATE";
	}
	
	$sql_id = ociparse($connection,$sqlquery);

if(!$sql_id){
	$e=oci_error($connection);
	print htmlentities($e['message']);
	exit;
}
ociexecute($sql_id,OCI_DEFAULT);
$result=array();
$numrows = oci_fetch_all($sql_id, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);

if($numrows==0)
		{echo '<font color="'.red.'">There is no information available for the selected criteria. Please enter a valid combination of Category, Year, Quarter and StoreID</font>';}
else{

	echo "<h3>Report - Products Summary</h3>";
	echo "<table border=1>";
	echo "<tr><td>".UPC ."</td><td></td><td>".Brand ."</td><td></td><td>".Store ."</td><td></td><td>
	".TotalQuantity ."</td><td></td><td>".SaleDate ."</td><td></td><td></td></tr>";
	ociexecute($sql_id,OCI_DEFAULT);
	while($row = OCI_Fetch_Array($sql_id,OCI_NUM)){
		
		echo "<tr><td>".$row[0] ."</td><td></td><td>".$row[1] ."</td><td></td><td>".$row[2] ."</td><td></td><td>".$row[3] ."</td><td></td><td>".$row[4] ."</td><td></td><td></td></tr>";
	}
	echo "</table>";
}
}
elseif ($criteria=='Dept') {
	if($store!=null && $quarter!="None")
	{
	$sqlquery = "SELECT DeptDesc,Dept,STORE_MSA.Store As Store,MSA_Name,SUM(Amt) As TotalAmount,SaleDate 
	FROM SKUINFO,TRNSACT,STORE_MSA 
	WHERE SKUINFO.SKU = TRNSACT.SKU 
	AND STORE_MSA.Store=TRNSACT.Store 
	AND substr(SALEDATE, 8,2) = substr(".$Year.", 3,2) 
	AND STORE_MSA.Store = ".$store." 
	AND substr(SALEDATE, 4,3) IN('".$month1."','".$month2."','".$month3."') 
	GROUP BY DeptDesc,Dept,STORE_MSA.Store,MSA_Name,SaleDate 
	ORDER BY TotalAmount DESC,DeptDesc,SaleDate";
	}
	elseif($store!=null && $quarter=="None") {
		$sqlquery = "SELECT DeptDesc,Dept,STORE_MSA.Store As Store,MSA_Name,SUM(Amt) As TotalAmount,SaleDate 
		FROM SKUINFO,TRNSACT,STORE_MSA 
		WHERE SKUINFO.SKU = TRNSACT.SKU 
		AND STORE_MSA.Store=TRNSACT.Store 
		AND STORE_MSA.Store = ".$store." 
		AND substr(SALEDATE, 8,2) = substr(".$Year.", 3,2) 
		GROUP BY DeptDesc,Dept,STORE_MSA.Store,MSA_Name,SaleDate 
		ORDER BY TotalAmount DESC,DeptDesc,SaleDate";
	}
	elseif($store==null && $quarter!="None") {
		$sqlquery = "SELECT DeptDesc,Dept,STORE_MSA.Store As Store,MSA_Name,SUM(Amt) As TotalAmount,SaleDate 
		FROM SKUINFO,TRNSACT,STORE_MSA 
		WHERE SKUINFO.SKU = TRNSACT.SKU 
		AND STORE_MSA.Store=TRNSACT.Store 
		AND substr(SALEDATE, 4,3) IN('".$month1."','".$month2."','".$month3."') 
		AND substr(SALEDATE, 8,2) = substr(".$Year.", 3,2) 
		GROUP BY DeptDesc,Dept,STORE_MSA.Store,MSA_Name,SaleDate 
		ORDER BY TotalAmount DESC,DeptDesc,SaleDate";
	}
else{
	$sqlquery = "SELECT DeptDesc,Dept,STORE_MSA.Store As Store,MSA_Name,SUM(Amt) As TotalAmount,SaleDate 
		FROM SKUINFO,TRNSACT,STORE_MSA 
		WHERE SKUINFO.SKU = TRNSACT.SKU 
		AND STORE_MSA.Store=TRNSACT.Store 
		AND substr(SALEDATE, 8,2) = substr(".$Year.", 3,2) 
		GROUP BY DeptDesc,Dept,STORE_MSA.Store,MSA_Name,SaleDate 
		ORDER BY TotalAmount DESC,DeptDesc,SaleDate";
		}
	$sql_id = ociparse($connection,$sqlquery);
if(!$sql_id){
	$e=oci_error($connection);
	print htmlentities($e['message']);
	exit;
}
ociexecute($sql_id,OCI_DEFAULT);
$result=array();
$numrows = oci_fetch_all($sql_id, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);

if($numrows==0)
		{echo '<font color="'.red.'">There is no information available for the selected criteria. Please enter a valid combination of Category, Year and StoreID</font>';}
else{
	echo "<h3>Report - Departments Summary</h3>";
	echo "<table border=1>";
	echo "<tr><td>".Department ."</td><td></td><td>".DeptID ."</td><td></td><td>".Store ."</td><td></td><td>".MSA ."</td><td></td><td>".TotalAmount ."</td><td></td><td>".SaleDate ."</td><td></td><td></td></tr>";
	ociexecute($sql_id,OCI_DEFAULT);
	while($row = OCI_Fetch_Array($sql_id,OCI_NUM)){

		echo "<tr><td>".$row[0] ."</td><td></td><td>".$row[1] ."</td><td></td><td>".$row[2] ."</td><td></td><td>
		".$row[3] ."</td><td></td><td>".$row[4] ."</td><td></td><td>".$row[5] ."</td><td></td><td></td></tr>";
	}
	echo "</table>";
	}
}
	elseif ($criteria=='Brand') {
	if($store!=null && $quarter!="None")
	{
	$sqlquery = "SELECT BRAND, SUM(AMT) AS TotalAmount,TRNSACT.Store,SALEDATE AS SKU 
	FROM SKUINFO, TRNSACT 
	WHERE TRNSACT.SKU = SKUINFO.SKU 
	AND Store=TRNSACT.Store 
	AND substr(SALEDATE, 8,2) = substr(".$Year.", 3,2) 
	AND Store = ".$store." 
	AND substr(SALEDATE, 4,3) IN('".$month1."','".$month2."','".$month3."') 
	GROUP BY BRAND,TRNSACT.STORE,SALEDATE 
	ORDER BY TotalAmount DESC,BRAND,SALEDATE";
	}
elseif($store!=null && $quarter=="None")
	{
	$sqlquery = "SELECT BRAND, SUM(AMT) AS TotalAmount,TRNSACT.Store,SALEDATE AS SKU 
	FROM SKUINFO, TRNSACT 
	WHERE TRNSACT.SKU = SKUINFO.SKU 
	AND Store=TRNSACT.Store 
	AND substr(SALEDATE, 8,2) = substr(".$Year.", 3,2) 
	AND Store = ".$store." 
	GROUP BY BRAND,TRNSACT.STORE,SALEDATE 
	ORDER BY TotalAmount DESC,BRAND,SALEDATE";
	}
	elseif($store==null && $quarter!="None")
	{
	$sqlquery = "SELECT BRAND, SUM(AMT) AS TotalAmount,TRNSACT.Store,SALEDATE AS SKU 
	FROM SKUINFO, TRNSACT 
	WHERE TRNSACT.SKU = SKUINFO.SKU 
	AND Store=TRNSACT.Store 
	AND substr(SALEDATE, 8,2) = substr(".$Year.", 3,2) 
	AND substr(SALEDATE, 4,3) IN('".$month1."','".$month2."','".$month3."') 
	GROUP BY BRAND,TRNSACT.STORE,SALEDATE 
	ORDER BY TotalAmount DESC,BRAND,SALEDATE";
	}
	
	else {
		$sqlquery = "SELECT BRAND, SUM(AMT) AS TotalAmount,TRNSACT.Store,SALEDATE AS SKU 
	FROM SKUINFO, TRNSACT 
	WHERE TRNSACT.SKU = SKUINFO.SKU 
	AND Store=TRNSACT.Store 
	AND substr(SALEDATE, 8,2) = substr(".$Year.", 3,2)
	GROUP BY BRAND,TRNSACT.STORE,SALEDATE 
	ORDER BY TotalAmount DESC,BRAND,SALEDATE";
	}
	$sql_id = ociparse($connection,$sqlquery);
if(!$sql_id){
	$e=oci_error($connection);
	print htmlentities($e['message']);
	exit;
}
ociexecute($sql_id,OCI_DEFAULT);
$result=array();
$numrows = oci_fetch_all($sql_id, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);

if($numrows==0)
		{echo '<font color="'.red.'">There is no information available for the selected criteria. Please enter a valid combination of Category, Year and StoreID</font>';}
else{
	echo "<h3>Report - Brands Summary</h3>";
	echo "<table border=1>";
	echo "<tr><td>".Brand ."</td><td></td><td>".TotalAmount ."</td><td></td><td>".Store ."</td><td></td><td>".SaleDate ."</td><td></td><td></td></tr>";
	ociexecute($sql_id,OCI_DEFAULT);
	while($row = OCI_Fetch_Array($sql_id,OCI_NUM)){

		echo "<tr><td>".$row[0] ."</td><td></td><td>".$row[1] ."</td><td></td><td>".$row[2] ."</td><td></td><td>
		".$row[3] ."</td><td></td><td></td></tr>";
	}
	echo "</table>";
	}

}

elseif ($criteria=='Vendor') {
	if($store!=null && $quarter!="None")
	{
	$sqlquery = "SELECT VENDOR,DEPTDESC, SUM(AMT) AS TotalAmount,TRNSACT.Store,SALEDATE 
	FROM SKUINFO, TRNSACT 
	WHERE TRNSACT.SKU = SKUINFO.SKU 
	AND Store=TRNSACT.Store 
	AND substr(SALEDATE, 8,2) = substr(".$Year.", 3,2) 
	AND Store = ".$store." 
	AND substr(SALEDATE, 4,3) IN('".$month1."','".$month2."','".$month3."') 
	GROUP BY VENDOR,DEPTDESC,TRNSACT.STORE,SALEDATE 
	ORDER BY TotalAmount DESC,VENDOR,SALEDATE";
	}
elseif($store!=null && $quarter=="None")
	{
	$sqlquery = "SELECT VENDOR,DEPTDESC, SUM(AMT) AS TotalAmount,TRNSACT.Store,SALEDATE 
	FROM SKUINFO, TRNSACT 
	WHERE TRNSACT.SKU = SKUINFO.SKU 
	AND Store=TRNSACT.Store 
	AND substr(SALEDATE, 8,2) = substr(".$Year.", 3,2) 
	AND Store = ".$store." 
	GROUP BY VENDOR,DEPTDESC,TRNSACT.STORE,SALEDATE 
	ORDER BY TotalAmount DESC,VENDOR,SALEDATE";
	}
elseif($store==null && $quarter!="None")
	{
	$sqlquery = "SELECT VENDOR,DEPTDESC, SUM(AMT) AS TotalAmount,TRNSACT.Store,SALEDATE 
	FROM SKUINFO, TRNSACT 
	WHERE TRNSACT.SKU = SKUINFO.SKU 
	AND Store=TRNSACT.Store 
	AND substr(SALEDATE, 8,2) = substr(".$Year.", 3,2)
	AND substr(SALEDATE, 4,3) IN('".$month1."','".$month2."','".$month3."') 
	GROUP BY VENDOR,DEPTDESC,TRNSACT.STORE,SALEDATE 
	ORDER BY TotalAmount DESC,VENDOR,SALEDATE";
	}
	else {
		$sqlquery = "SELECT VENDOR,DEPTDESC, SUM(AMT) AS TotalAmount,TRNSACT.Store,SALEDATE  
	FROM SKUINFO, TRNSACT 
	WHERE TRNSACT.SKU = SKUINFO.SKU 
	AND Store=TRNSACT.Store 
	AND substr(SALEDATE, 8,2) = substr(".$Year.", 3,2) 
	GROUP BY VENDOR,DEPTDESC,TRNSACT.STORE,SALEDATE 
	ORDER BY TotalAmount DESC,VENDOR,SALEDATE";
	}
	$sql_id = ociparse($connection,$sqlquery);
if(!$sql_id){
	$e=oci_error($connection);
	print htmlentities($e['message']);
	exit;
}
ociexecute($sql_id,OCI_DEFAULT);
$result=array();
$numrows = oci_fetch_all($sql_id, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);

if($numrows==0)
		{echo '<font color="'.red.'">There is no information available for the selected criteria. Please enter a valid combination of Category, Year and StoreID</font>';}
else{
	echo "<h3>Report - Vendors Summary</h3>";
	echo "<table border=1>";
	echo "<tr><td>".Vendor ."</td><td></td><td>".Department ."</td><td></td><td>".TotalAmount ."</td><td></td><td>".Store ."</td><td></td><td>".SaleDate ."</td><td></td><td></td></tr>";
	ociexecute($sql_id,OCI_DEFAULT);
	while($row = OCI_Fetch_Array($sql_id,OCI_NUM)){

		echo "<tr><td>".$row[0] ."</td><td></td><td>".$row[1] ."</td><td></td><td>".$row[2] ."</td><td></td><td>
		".$row[3] ."</td><td></td><td>".$row[4] ."</td><td></td><td></td></tr>";
	}
	echo "</table>";
	}
}
elseif ($criteria=='ProdDept') {
	if($store!=null && $quarter!="None")
	{
	$sqlquery = "SELECT Store,DEPTDESC,UPC,SUM(AMT) AS TotalAmount,SALEDATE 
	FROM SKUINFO, TRNSACT 
	WHERE TRNSACT.SKU = SKUINFO.SKU 
	AND substr(SALEDATE, 8,2) = substr(".$Year.", 3,2) 
	AND Store = ".$store." 
	AND substr(SALEDATE, 4,3) IN('".$month1."','".$month2."','".$month3."') 
	GROUP BY UPC,DEPTDESC,Store,Saledate 
	ORDER BY TotalAmount DESC,DEPTDESC,UPC";
	}
elseif($store!=null && $quarter=="None")
	{
	$sqlquery = "SELECT Store,DEPTDESC,UPC,SUM(AMT) AS TotalAmount,SALEDATE 
	FROM SKUINFO, TRNSACT 
	WHERE TRNSACT.SKU = SKUINFO.SKU 
	AND substr(SALEDATE, 8,2) = substr(".$Year.", 3,2) 
	AND Store = ".$store." 
	GROUP BY UPC,DEPTDESC,Store,Saledate 
	ORDER BY TotalAmount DESC,DEPTDESC,UPC";
	}
elseif($store==null && $quarter!="None")
	{
	$sqlquery = "SELECT Store,DEPTDESC,UPC,SUM(AMT) AS TotalAmount,SALEDATE 
	FROM SKUINFO, TRNSACT 
	WHERE TRNSACT.SKU = SKUINFO.SKU 
	AND substr(SALEDATE, 8,2) = substr(".$Year.", 3,2)
	AND substr(SALEDATE, 4,3) IN('".$month1."','".$month2."','".$month3."') 
	GROUP BY UPC,DEPTDESC,Store,Saledate 
	ORDER BY TotalAmount DESC,DEPTDESC,UPC";
	}
	else {
		$sqlquery = "SELECT Store,DEPTDESC,UPC,SUM(AMT) AS TotalAmount,SALEDATE 
	FROM SKUINFO, TRNSACT 
	WHERE TRNSACT.SKU = SKUINFO.SKU 
	AND substr(SALEDATE, 8,2) = substr(".$Year.", 3,2)
	GROUP BY UPC,DEPTDESC,Store,Saledate 
	ORDER BY TotalAmount DESC,DEPTDESC,UPC";
	}
	$sql_id = ociparse($connection,$sqlquery);
if(!$sql_id){
	$e=oci_error($connection);
	print htmlentities($e['message']);
	exit;
}
ociexecute($sql_id,OCI_DEFAULT);
$result=array();
$numrows = oci_fetch_all($sql_id, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);

if($numrows==0)
		{echo '<font color="'.red.'">There is no information available for the selected criteria. Please enter a valid combination of Category, Year and StoreID</font>';}
else{
	echo "<h3>Report - Product And Department Summary</h3>";
	echo "<table border=1>";
	echo "<tr><td>".Store ."</td><td></td><td>".Department ."</td><td></td><td>".UPC ."</td><td></td><td>".TotalAmount ."</td><td></td><td>".SaleDate ."</td><td></td><td></td></tr>";
	ociexecute($sql_id,OCI_DEFAULT);
	while($row = OCI_Fetch_Array($sql_id,OCI_NUM)){

		echo "<tr><td>".$row[0] ."</td><td></td><td>".$row[1] ."</td><td></td><td>".$row[2] ."</td><td></td><td>
		".$row[3] ."</td><td></td><td>".$row[4] ."</td><td></td><td></td></tr>";
	}
	echo "</table>";
	}
	}
	
	elseif ($criteria=='RegisterSKU') {
	if($store!=null && $quarter!="None")
	{
	$sqlquery = "SELECT Store,Register,SKU,SUM(AMT) AS TotalAmount,substr(SALEDATE, 4,6)
	FROM TRNSACT 
	WHERE substr(SALEDATE, 8,2) = substr(".$Year.", 3,2) 
	AND Store = ".$store." 
	AND substr(SALEDATE, 4,3) IN('".$month1."','".$month2."','".$month3."') 
	GROUP BY Register,SKU,Store,SALEDATE 
	ORDER BY TotalAmount DESC,Register,SKU,Store";
	}
elseif($store!=null && $quarter=="None")
	{
	$sqlquery = "SELECT Store,Register,SKU,SUM(AMT) AS TotalAmount,substr(SALEDATE, 4,6)
	FROM TRNSACT 
	WHERE substr(SALEDATE, 8,2) = substr(".$Year.", 3,2) 
	AND Store = ".$store." 
	GROUP BY Register,SKU,Store,SALEDATE 
	ORDER BY TotalAmount DESC,Register,SKU,Store";
	}
elseif($store==null && $quarter!="None")
	{
	$sqlquery = "SELECT Store,Register,SKU,SUM(AMT) AS TotalAmount,substr(SALEDATE, 4,6)
	FROM TRNSACT 
	WHERE substr(SALEDATE, 8,2) = substr(".$Year.", 3,2)
	AND substr(SALEDATE, 4,3) IN('".$month1."','".$month2."','".$month3."') 
	GROUP BY Register,SKU,Store,SALEDATE 
	ORDER BY TotalAmount DESC,Register,SKU,Store";
	}
else {
		$sqlquery = "SELECT Store,Register,SKU,SUM(AMT) AS TotalAmount,substr(SALEDATE, 4,6)
	FROM TRNSACT 
	WHERE substr(SALEDATE, 8,2) = substr(".$Year.", 3,2) 
	GROUP BY Register,SKU,Store,SALEDATE 
	ORDER BY TotalAmount DESC,Register,SKU,Store";
	}
	$sql_id = ociparse($connection,$sqlquery);
if(!$sql_id){
	$e=oci_error($connection);
	print htmlentities($e['message']);
	exit;
}
ociexecute($sql_id,OCI_DEFAULT);
$result=array();
$numrows = oci_fetch_all($sql_id, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);

if($numrows==0)
		{echo '<font color="'.red.'">There is no information available for the selected criteria. Please enter a valid combination of Category, Year and StoreID</font>';}
else{
	echo "<h3>Report - Register And SKU Summary</h3>";
	echo "<table border=1>";
	echo "<tr><td>".Store ."</td><td></td><td>".Register ."</td><td></td><td>".SKU ."</td><td></td><td>".TotalAmount ."</td><td></td><td>".MonthYear ."</td><td></td><td></td></tr>";
	ociexecute($sql_id,OCI_DEFAULT);
	while($row = OCI_Fetch_Array($sql_id,OCI_NUM)){

		echo "<tr><td>".$row[0] ."</td><td></td><td>".$row[1] ."</td><td></td><td>".$row[2] ."</td><td></td><td>
		".$row[3] ."</td><td></td><td>".$row[4] ."</td><td></td><td></td></tr>";
	}
	echo "</table>";
	}
	}
	
ocilogoff($connection);
	?>
</body>