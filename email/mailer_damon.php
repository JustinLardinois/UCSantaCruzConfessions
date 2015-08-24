#!/usr/bin/php -q
<?php
	$document_root = "/home/justinla/ucsantacruzconfessions.com";
	$stdin = fopen("php://stdin","r");
	$relay_regex = "#[0-9]{10}_[0-9a-f]{32}@ucsantacruzconfessions.com#i";
	
	$email = "";
	while(!feof($stdin)) $email .= fread($stdin,1024);
	$email = trim($email);
	
	$lines = explode("\n",$email);
	
	$to;
	$from;
	$subject;
	$body = "";
	
	for($i = 0; $i < count($lines); $i++)
	{
		$line = trim($lines[$i]);
		if(substr($line,0,strlen("To:")) == "To:")
		{
			$matches = array();
			preg_match($relay_regex,$line,$matches);
			$to = $matches[0];
		}
		else if(substr($line,0,strlen("From:")) == "From:")
		{
			$from = trim(substr($line,strlen("From:")));
		}
		else if(substr($line,0,strlen("Subject:")) == "Subject:")
		{
			$subject = trim(substr($line,strlen("Subject:")));
		}
		else if($line == "")
		{
			for($i++; $i < count($lines); $i++)
			{
				if(substr($lines[$i],0,strlen("Content-Type: text/plain")) ==
					"Content-Type: text/plain")
				{
					for($i++; substr($lines[$i],0,strlen("--")) != "--"; $i++)
					{
						$body .= $lines[$i] . "\r\n";
					}
					break;
				}
			}
			trim($body);
		}
	}
	$headers = "From: " . $from;
	
	$relay_filename = substr($to,0,43);
	$relay = fopen($document_root . "/email/relays/" . $relay_filename,"r");

	if($relay != FALSE)
	{
		$address = trim(fgets($relay));
		$body = "---\r\nThis message was originally sent to " . $to .
			" and was relayed to " . $address . ". If you think you recieved this" .
			" message in error, please contact" .
			" administrator@ucsantacruzconfessions.com.\r\n---\r\n\r\n" . $body;
		mail($address,$subject,$body,$headers);
		fclose($relay);
	}
	else
	{
		$error_mail = "There is no relay for the address " . $to .
			". Note that relays are deleted 14 days after creation.";
		mail($from,"Error: Unable to relay message",$error_mail,
			"From: mailer_damon@ucsantacruzconfessions.com");
	}

	fclose($output);
	fclose($stdin);
?>