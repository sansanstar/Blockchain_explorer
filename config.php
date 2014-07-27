<?php
	set_time_limit(0);
	
	// CRYPTO OPTIONS
	define("CRYPTONAME",		"");
	define("CRYPTOSHORTNAME",	"");
	
	//DAEMON OPTIONS
	define("WALLET_RPC_USER",	"");
	define("WALLET_RPC_PASS",	"");
	define("WALLET_RPC_SERVER",	"");
	define("WALLET_RPC_PORT",	"");

	//DATABASE SETTINGS
	define("DATABASE_NAME",		"");
	define("DATABASE_HOST",		"");
	define("DATABASE_USER",		"");
	define("DATABASE_PASS",		"");
	
	require_once("classes/json-rpc-client.php");
	require_once("classes/database.class.php");
	require_once("classes/daemon.class.php");
	require_once("classes/client.class.php");
	
	Connect();
?>
