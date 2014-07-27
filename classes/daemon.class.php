<?php
abstract class Daemon
{
	static private $lastBlock = 1;
	
	static private $curUpdate = 0;
	static private $maxUpdates = 50000;
	
	static private $clientd;
	static private $lastUpdate;
	static private $updateCycle;
	
	static public function init()
	{
		self::$clientd = new jsonRPCClient("http://".WALLET_RPC_USER.":".WALLET_RPC_PASS."@".WALLET_RPC_SERVER.":".WALLET_RPC_PORT."");

		self::getLastBlock();
		self::doFetch();
		self::updateRichList();
	}
	
	static private function cleanUp()
	{
		$updateCycles = getKeyedDataByQuery("
			SELECT id,	`update`
			FROM `update`
			WHERE cryptocurrency = '".CRYPTOSHORTNAME."'
			ORDER BY `update` DESC
		");
		
		$removeCycles = array_slice($updateCycles, 2, NULL, true);
		
		foreach($removeCycles as $removeId => $removeDate)
		{
			query("DELETE FROM `update` WHERE id = ".$removeId);
			query("DELETE FROM `richlist` WHERE update_id = ".$removeId);
		}
	}
	
	static private function doFetch()
	{
		echo "Fetching blocks: ".self::$lastBlock."-".self::$clientd->getblockcount()."\n";
		for ($block = self::$lastBlock; $block < self::$clientd->getblockcount() + 1; $block++) 
		{
            $last_block_hash 	= self::$clientd->getblockhash($block);
	        $last_block 		= self::$clientd->getblock($last_block_hash);
            $transactions 		= $last_block["tx"];
			
			$txid = null;
			
	        foreach ($transactions as $tx)
			{
				$empty_tx = true;
				
 				$txid = $tx;
				$tx = self::$clientd->gettransaction($tx);

            	$get_vin = false;

				foreach ($tx["vout"] as $tx_vout)
				{
					if ($tx_vout["value"] > 0) 
					{
						$get_vin 	= true;
						$addy 		= self::GetAddress($tx_vout["scriptPubKey"]);
						$amount 	= $tx_vout["value"];
						
						echo "TO: $addy ($amount)($block)\n";
													
						query("INSERT INTO tx_data (txid, address, amount, block) VALUES ('$txid','$addy','$amount','$block')");
						
						$empty_tx = false;
					}
				}

				if ($get_vin) 
				{
					if (isset($tx["vin"]) && !empty($tx["vin"])) 
					{
						foreach ($tx["vin"] as $tx_vin) 
						{
							if (isset($tx_vin["vout"])) 
							{
								$txid_transaction = $txid;
								$vout = $tx_vin["vout"];
								$txid = $tx_vin["txid"]; // tx-in id
								$addy = self::GetVINAddress($txid, $vout, $block, $txid_transaction);
								echo "FROM: $addy($block)\n";
								
								$empty_tx = false;
							}
						}
					}
				}
            }
						
			if ($empty_tx){
				echo "No transactions ($block)\n";
				query("INSERT INTO tx_data (txid, address, amount, block) VALUES ('$txid','','','$block')");
			}
		}
	}
	
	static private function GetAddress($array) 
	{
		$val = "";
		$cnt = 0;
	
		foreach ($array["addresses"] as $addr) 
		{
			$val = $addr;
			$cnt++;
		}

		if ($cnt > 1)
		{
			echo "SOME ERROR IN FUNCTION GetAddress";
		}
		
		return $val;
	}
	
	static private function getLastBlock()
	{
		$lastBlock =  (int) getValueByQuery("SELECT block FROM tx_data ORDER BY block DESC LIMIT 1");
		self::$lastBlock = ($lastBlock == 0) ? 1 : $lastBlock + 1;
	}
	
	static private function GetVINAddress($txid, $vout, $block, $txid_transaction) 
	{
		$tx 		= self::$clientd->gettransaction($txid);
		$amount 	= -$tx["vout"][$vout]["value"];
		$addy 		= self::GetAddress($tx["vout"][$vout]["scriptPubKey"]);
	
		query("INSERT INTO tx_data (txid, address, amount, block) VALUES ('$txid_transaction','$addy','$amount','$block')");
	
		$val = "$addy ($amount)";
		return $val;
	}
	
	static private function getTxData()
	{
		return getRowsByQuery("
			SELECT *, IF(SUM(amount) > 0, SUM(amount), 0) as `amount`
			FROM tx_data
			Group By address
			ORDER BY `amount` DESC
			LIMIT 1000
		");
	}
	
	static private function updateCycle()
	{
		self::$updateCycle = query('INSERT INTO `update` (cryptocurrency, `update`, finished) VALUES ("'.CRYPTOSHORTNAME.'","'.date("Y-m-d H:i:s").'",0)', true);
	}
	
	static public function updateRichList()
	{
		$txData = self::getTxData();
		
		echo "Processing new update Cycle\n";
		self::updateCycle();
		echo "Doing cleanup\n";
		self::cleanUp();
		
		echo "Updating data in richlist\n";
		$pos = 1;
		foreach($txData as $position => $tx)
		{
			echo $pos."/1000 - ";
			query("INSERT INTO richlist (address, position, amount,update_id) VALUES ('".$tx['address']."','".$pos."','".$tx['amount']."','".self::$updateCycle."')");
		
			$pos++;
		}
		echo "Marking as finished and fetching extra info\n";
		
		query("UPDATE `update` SET finished=1,last_processed_block = ".self::$lastBlock."  WHERE id=".self::$updateCycle.";");
		
	}
}
?>