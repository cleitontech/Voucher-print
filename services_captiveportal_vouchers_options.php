<?php
/*
	services_captiveportal_vouchers_options.php
	Daniele Arrighi <daniele.arrighi@gmail.com>
	Ver. 2.3
*/
/* ====================================================================
 *	Copyright (c)  2004-2015  Electric Sheep Fencing, LLC. All rights reserved.
 *
 *	Redistribution and use in source and binary forms, with or without modification,
 *	are permitted provided that the following conditions are met:
 *
 *	1. Redistributions of source code must retain the above copyright notice,
 *		this list of conditions and the following disclaimer.
 *
 *	2. Redistributions in binary form must reproduce the above copyright
 *		notice, this list of conditions and the following disclaimer in
 *		the documentation and/or other materials provided with the
 *		distribution.
 *
 *	3. All advertising materials mentioning features or use of this software
 *		must display the following acknowledgment:
 *		"This product includes software developed by the pfSense Project
 *		 for use in the pfSense software distribution. (http://www.pfsense.org/).
 *
 *	4. The names "pfSense" and "pfSense Project" must not be used to
 *		 endorse or promote products derived from this software without
 *		 prior written permission. For written permission, please contact
 *		 coreteam@pfsense.org.
 *
 *	5. Products derived from this software may not be called "pfSense"
 *		nor may "pfSense" appear in their names without prior written
 *		permission of the Electric Sheep Fencing, LLC.
 *
 *	6. Redistributions of any form whatsoever must retain the following
 *		acknowledgment:
 *
 *	"This product includes software developed by the pfSense Project
 *	for use in the pfSense software distribution (http://www.pfsense.org/).
 *
 *	THIS SOFTWARE IS PROVIDED BY THE pfSense PROJECT ``AS IS'' AND ANY
 *	EXPRESSED OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 *	IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 *	PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE pfSense PROJECT OR
 *	ITS CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 *	SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
 *	NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 *	LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 *	HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
 *	STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 *	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 *	OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 *	====================================================================
 *
 */

##|+PRIV
##|*IDENT=page-services-captiveportal-voucher-edit
##|*NAME=Services: Captive portal Voucher Rolls
##|*DESCR=Allow access to the 'Services: Captive portal Edit Voucher Rolls' page.
##|*MATCH=services_captiveportal_vouchers_edit.php*
##|-PRIV

require("guiconfig.inc");
require("functions.inc");
require_once("filter.inc");
require("shaper.inc");
require("captiveportal.inc");
require_once("voucher.inc");

$cpzone = $_GET['zone'];

//CONFIG SECTION FOR VOUCHER OPTIONS
//****************************************************************************************
$errors = '';
$vpConfig = include("voucherfiles/config.php");
if (!isset($vpConfig))
	$errors += " Mission configuration file";
//****************************************************************************************

if (empty($cpzone) || empty($config['captiveportal'][$cpzone])) {
	header("Location: services_captiveportal_vouchers.php");
	exit;
}

if (!is_array($config['captiveportal'])) {
	$config['captiveportal'] = array();
}
$a_cp =& $config['captiveportal'];

$pgtitle = array(gettext("Services"), gettext("Captive Portal"), $a_cp[$cpzone]['zone'], gettext("Vouchers"), gettext("Print"));
$shortcut_section = "";

if (!is_array($config['voucher'])) {
	$config['voucher'] = array();
}

if (!is_array($config['voucher'][$cpzone]['roll'])) {
	$config['voucher'][$cpzone]['roll'] = array();
}

$a_roll = &$config['voucher'][$cpzone]['roll'];

if (is_numericint($_GET['id'])) {
	$id = $_GET['id'];
}
if (isset($_POST['id']) && is_numericint($_POST['id'])) {
	$id = $_POST['id'];
}

if (isset($id) && $a_roll[$id]) {
	$pconfig['zone'] = $a_roll[$id]['zone'];
	$pconfig['number'] = $a_roll[$id]['number'];
	$pconfig['count'] = $a_roll[$id]['count'];
	$pconfig['minutes'] = $a_roll[$id]['minutes'];
	$pconfig['descr'] = $a_roll[$id]['descr'];
}

include("head.inc");

if ($input_errors) {
	print_input_errors($input_errors);
}

if ($savemsg) {
	print_info_box($savemsg, 'success');
}
?>

<form action="services_captiveportal_vouchers_print.php" class="form-horizontal" method="post" name="iform" id="iform">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h2 class="panel-title"><?=gettext("Voucher Print Options"); ?></h2>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-sm-2 control-label">
					<?=gettext("Voucher Title"); ?>
				</label>
				<div class="col-sm-10">
				<input class="form-control" name="voucherTitle" id="voucherTitle" type="text" value="<?=htmlspecialchars($vpConfig['voucherTitle']);?>">
				<span class="help-block"><?=gettext("The title of the voucher shown on top"); ?></span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">
					<?=gettext("Voucher Info"); ?>
				</label>
				<div class="col-sm-10">
				<input class="form-control" name="voucherInfo" id="voucherInfo" type="text" value="<?=htmlspecialchars($vpConfig['voucherInfo']);?>">
				<span class="help-block"><?=gettext("Voucher info on how to connect etc."); ?></span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">
					<?=gettext("Voucher Duration"); ?>
				</label>
				<div class="col-sm-10">
				<input class="form-control" name="voucherDuration" id="voucherDuration" type="text" value="<?=htmlspecialchars($vpConfig['voucherDuration']);?>">
				<input name="useRollMinutes" type="checkbox" class="formcheckbox" id="useRollMinutes" value="1" checked="checked" autocomplete="off"> Use Voucher Minutes as Voucher Duration
				
				<span class="help-block"><?=gettext("The total duration of the voucher."); ?></span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">
					<?=gettext("Voucher Expiration"); ?>
				</label>
				<div class="col-sm-10">
				<input class="form-control" name="voucherExpiration" id="voucherExpiration" type="text" value="<?=htmlspecialchars($vpConfig['voucherExpiration']);?>">
				<span class="help-block"><?=gettext("Tell the user that you will delete the roll after this date"); ?></span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">
					<?=gettext("Active only?"); ?>
				</label>
				<div class="col-sm-10">
				<input name="printUnusedOnly" type="checkbox" id="printUnusedOnly" value="1" checked="checked" autocomplete="off"> Print only non-expired vouchers
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">
					<?=gettext("Save?"); ?>
				</label>
				<div class="col-sm-10">
				<input name="saveConfig" type="checkbox" id="saveConfig" value="1" checked="checked" autocomplete="off"> Save this values as defaults
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">
				</label>
					<div class="col-sm-10">
					<input type="hidden" name="zone" value="<?=$cpzone?>" />
					<input type="hidden" name="id" value="<?=$id?>" />
			</div>
		</div>
		</div>
	</div>
	<div class="col-sm-10 col-sm-offset-2">
		<button class="btn btn-primary" type="submit" value="Save" name="save" id="save"><i class="fa fa-print icon-embed-btn"> </i>Print Vouchers</button>
	</div>
</form>

<?php
include("foot.inc");
?>