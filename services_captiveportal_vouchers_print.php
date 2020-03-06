<?php
/*
	services_captiveportal_vouchers_options.php
	Daniele Arrighi <daniele.arrighi@gmail.com>
	Reward Gagarin <rewardgms@gmail.com>
		
	part of the pfSense project	(https://www.pfsense.org)
	Copyright (C) 2010 Ermal Luçi
	Copyright (C) 2013-2015 Electric Sheep Fencing, LP
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	1. Redistributions of source code must retain the above copyright notice,
	this list of conditions and the following disclaimer.

	2. Redistributions in binary form must reproduce the above copyright
	notice, this list of conditions and the following disclaimer in the
	documentation and/or other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
	INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
	AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
	AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
	OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
	POSSIBILITY OF SUCH DAMAGE.
*/

##|+PRIV
##|*IDENT=page-services-captiveportal-vouchers-print
##|*NAME=Services: Captive portal Vouchers page
##|*DESCR=Allow access to the 'Services: Captive portal Vouchers Print' page.
##|*MATCH=services_captiveportal_vouchers_print.php*
##|-PRIV

require("guiconfig.inc");
require("functions.inc");
require_once("filter.inc");
require("shaper.inc");
require("captiveportal.inc");
require_once("voucher.inc");

?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="voucherfiles/voucherprint.css"> 
</head>
<body>
<?php
if ($_POST) {
	/* print all vouchers of the selected roll */
	$id = $_POST['id'];
	$cpzone = $_POST['zone'];
	$voucherTitle = $_POST['voucherTitle'];
	$voucherInfo = $_POST['voucherInfo'];
	$voucherDuration = $_POST['voucherDuration'];
	$useRollMinutes = $_POST['useRollMinutes'];
	$saveConfig = $_POST['saveConfig'];
	$printUnusedOnly = $_POST['printUnusedOnly'];
	$voucherExpiration = $_POST['voucherExpiration'];
	
	if ($saveConfig == '1')
	{
		//Overwrite configuration with the new values
		$vpConfig = include("voucherfiles/config.php");
		$vpConfig['voucherTitle'] = $voucherTitle;
		$vpConfig['voucherInfo'] = $voucherInfo;
		$vpConfig['voucherDuration'] = $voucherDuration;
		$vpConfig['voucherExpiration'] = $voucherExpiration;
		
		file_put_contents('voucherfiles/config.php', '<?php return ' . var_export($vpConfig, true) . ';');
	}
} else {
	header("Location: services_captiveportal_zones.php");
	exit();
}

function convertToHoursMins($time, $format = '%02d:%02d') {
    if ($time < 1) {
        return;
    }
    $hours = floor($time / 60);
    $minutes = ($time % 60);
	//return sprintf($format, $hours, $minutes);
	
	if ($hours < 1)
		return $minutes." minute(s)";
	if ($hours >= 1 && $minutes == 0)
		return $hours." hour(s)";
	if ($hours >= 1 && $minutes > 0)
		return $hours.":".$minutes. " hours(s)";
}

function getvalidity($vou){
	$test_results = voucher_auth($vou, 1);
	$isValid = 0;
	$remainingMinutes = 0;
	
	foreach ($test_results as $result) {
		preg_match("/good for (.*?) Minutes/", $result, $m);
		if (count($m) >= 2) 
		{
			$isValid = 1;
			$remainingMinutes = $m[1];
		}
	}
	
	if ($isValid == 1) {
		$validity = "Valid for <strong>".convertToHoursMins($remainingMinutes)."</strong>";
	} else {
		$validity = "Voucher Expired!";
	}
	
	return $validity;	
}

function getvalidityMinutes($vou){
	$test_results = voucher_auth($vou, 1);
	$isValid = 0;
	$remainingMinutes = 0;
	
	foreach ($test_results as $result) {
		preg_match("/good for (.*?) Minutes/", $result, $m);
		if (count($m) >= 2) 
		{
			$isValid = 1;
			$remainingMinutes = $m[1];
		}
	}
	
	if ($isValid == 1) {
		$validityMinutes = $remainingMinutes;
	} else {
		$validityMinutes = 0;
	}
	
	return $validityMinutes;	
}

	$privkey = base64_decode($config['voucher'][$cpzone]['privatekey']);
	if (strstr($privkey,"BEGIN RSA PRIVATE KEY")) {
		$fd = fopen("{$g['varetc_path']}/voucher_{$cpzone}.private","w");
		if (!$fd) {
			$input_errors[] = gettext("Cannot write private key file") . ".\n";
		} else {
			chmod("{$g['varetc_path']}/voucher_{$cpzone}.private", 0600);
			fwrite($fd, $privkey);
			fclose($fd);
			$a_voucher = &$config['voucher'][$cpzone]['roll'];

			if (isset($id) && $a_voucher[$id]) {
				$number = $a_voucher[$id]['number'];
				$count = $a_voucher[$id]['count'];
				$vout = shell_exec("/usr/local/bin/voucher -c {$g['varetc_path']}/voucher_{$cpzone}.cfg -p {$g['varetc_path']}/voucher_{$cpzone}.private $number $count");
				$split_sizes = explode('"',$vout);
				$count = count($split_sizes);
				for ( $i = 1; $i < $count; $i += 2) {
					$sizes[] = $split_sizes[$i];
				}
				//print_r($sizes); /*for debugging*
				$i = 0;
			
				$maxid = count($sizes);

				echo "<div id='vouchers'>";
				        
				foreach ($sizes as $k => $v) 
				{
					$thisVoucherDuration = $voucherDuration;
					$printThis = 1;
					
					if ($useRollMinutes == '1')
						$thisVoucherDuration = getvalidity($v);
					
					if ($printUnusedOnly == '1')
						if (getvalidityMinutes($v) == 0) 
							$printThis = 0;
					
					if ($printThis == 1) {
						echo "<div class='voucher'><div class='title'>".$voucherTitle."</div><div class='logo'><img alt='Logo' src='voucherfiles/logo.png'></div><div class='info'>".$voucherInfo."</div><div class='vcode'>{$v}</div><div class='vtime'>".$thisVoucherDuration."</div><div class='vexpire'>".$voucherExpiration."</div></div>"; $i++;
						if ($i == 21) {
							echo "<div class='page-break'></div>";
							$i = 0;
						}
					}
				} 

				echo "</div>";	
				?>
				<script type="text/javascript">
				window.onload=function(){self.print();}
				</script>				
				<?php
				exit;
			}
		}
	} else {
		$input_errors[] = gettext("Need private RSA key to print vouchers") . "\n";
	}

?>
</body>
</html>