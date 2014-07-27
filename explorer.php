<?php
	require_once('config.php');
	
	$txData		= Client::getTransactionData(10); 
	$blockData	= Client::getBlockData(5); 
	
	// print_r($txData);
	// print_r($blockData);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?=ucfirst(CRYPTONAME)?> - Explorer</title>

<script src="js/jquery-1.11.1.min.js" type="text/javascript"></script>
<script src="js/jquery.balloon.min.js" type="text/javascript"></script>
<!--<script src="js/jquery.tablesorter.js" type="text/javascript"></script>-->
<script src="js/main.js" type="text/javascript"></script>

<!--<link rel="stylesheet" type="text/css" href="css/main.css">-->
</head>

<body>
<div style="width:950px; margin:0 auto;">
	<table cellpadding="0" cellspacing="0" border="0" width="1000">
		<tbody>
		<tr>
			<td>
				<center>
					<h1><?=strtoupper(CRYPTONAME)?> - Explorer</h1>
				</center>
			</td>
		</tr>
		</tbody>
	</table>
	<form target="search.php" method="post">
		<input name="id" type="text" placeholder="enter block, txid or address" style="width:450px;"/>
		<input type="submit"/>
	</form>
	<p>Latest blocks</p>
	<table cellpadding="0" cellspacing="0" border="0" width="1000" id="distribution">
	<thead>
	<tr>
		<td>Height</td>
		<td>Transactions</td>
	</tr>
	</thead>
	<tbody>
	<?php
	foreach($blockData as $block)
	{
		?>
			<tr>
				<td><a href="search.php?id=<?=$block['block']?>"><?=$block['block']?></a></td>
				<td><?=$block['transactions']?></td>
			</tr>
		<?php
	}
?>
	</tbody>
	</table>
	
	<div style="height:100px;width:100px;"></div>
	
	<p>Latest transactions</p>
	<table cellpadding="0" cellspacing="0" border="0" width="1000" id="distribution">
	<thead>
	<tr>
		<td>Block</td>
		<td>Transaction ID</td>
		<td><?=ucfirst(CRYPTONAME)?></td>
	</tr>
	</thead>
	<tbody>
	<?php
	foreach($txData as $tx)
	{
		?>
			<tr>
				<td><a href="search.php?id=<?=$tx['block']?>"><?=$tx['block']?></a></td>
				<td><a href="search.php?id=<?=$tx['txid']?>"><?=$tx['txid']?></a></td>
				<td><?=$tx['amount']?></td>
			</tr>
		<?php
	}
?>
	</tbody>
	</table>
</div>
</body>
</html>
<?
close();
?>