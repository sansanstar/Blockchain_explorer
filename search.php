<?php
	require_once('config.php');
	
	// print_r($_REQUEST);
	
	$id = $_REQUEST["id"];
	
?>
<a href="explorer.php">Return to explorer</a>
<form target="search.php" method="post">
		<input name="id" type="text" placeholder="enter block, txid or address" style="width:450px;"/>
	<input type="submit"/>
</form>
<?php
	
	$data = array();
	$cnt = 0;
	
	if(strlen($id) == 34){
		$addressData	= Client::getAddress($id); 
		foreach($addressData as $val){
			$data[$cnt] = $val;
			$data[$cnt]["block_url"] = '<a href="search.php?id='.$val["block"].'">'.$val["block"].'</a>';
			$data[$cnt]["txid_url"] = '<a href="search.php?id='.$val["txid"].'">'.$val["txid"].'</a>';
			$data[$cnt]["address_url"] = '<a href="search.php?id='.$val["address"].'">'.$val["address"].'</a>';
			$cnt++;
		}
		
		echo "<pre>";
		echo "Data for address $id<br>";
		print_r($data);
		echo "</pre>";
	} else if (strlen($id) > 34) {
		$txData	= Client::getTransaction($id); 
		
		foreach($txData as $val){
			$data[$cnt] = $val;
			$data[$cnt]["block_url"] = '<a href="search.php?id='.$val["block"].'">'.$val["block"].'</a>';
			$data[$cnt]["txid_url"] = '<a href="search.php?id='.$val["txid"].'">'.$val["txid"].'</a>';
			$data[$cnt]["address_url"] = '<a href="search.php?id='.$val["address"].'">'.$val["address"].'</a>';
			$cnt++;
		}
		
		echo "<pre>";
		echo "Data for tx $id<br>";
		print_r($data);
		echo "</pre>";	
	} else {
		$blockData	= Client::getBlock($id); 
	
		foreach($blockData as $val){
			$data[$cnt] = $val;
			$data[$cnt]["block_url"] = '<a href="search.php?id='.$val["block"].'">'.$val["block"].'</a>';
			$data[$cnt]["txid_url"] = '<a href="search.php?id='.$val["txid"].'">'.$val["txid"].'</a>';
			$data[$cnt]["address_url"] = '<a href="search.php?id='.$val["address"].'">'.$val["address"].'</a>';
			$cnt++;
		}

		echo "<pre>";
		echo "Data for block $id<br>";
		print_r($data);
		echo "</pre>";
	}
	
?>