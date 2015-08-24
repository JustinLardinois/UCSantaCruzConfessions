#!/usr/bin/php -q
<?php
	$document_root = "/home/justinla/ucsantacruzconfessions.com";
	$deletion_period = 14 * 24 * 60 * 60; // 14 days
	$current_time = time();
	
	$relays = glob($document_root . "/email/relays/*");
	
	for($i = 0; $i < count($relays); $i++)
	{
		if(($current_time - filemtime($relays[$i])) > $deletion_period) unlink($relays[$i]);
	}
?>