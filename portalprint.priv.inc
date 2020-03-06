<?php
/*
	portalprint.priv.inc
	Derived from privs.def.inc and freeradius.priv.inc
	part of pfSense (http://www.pfSense.org/)
	Copyright (C) 2015 ESF, LLC
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
global $priv_list;

$priv_list['page-status-captiveportal-vouchers-options'] = array();
$priv_list['page-status-captiveportal-vouchers-options']['name'] = gettext("WebCfg - Status: Captive portal Vouchers Options");
$priv_list['page-status-captiveportal-vouchers-options']['descr'] = gettext("Allow access to the 'Status: Captive portal Vouchers Options' page.");
$priv_list['page-status-captiveportal-vouchers-options']['match'] = array();
$priv_list['page-status-captiveportal-vouchers-options']['match'][] = "services_captiveportal_vouchers_options.php*";

$priv_list['page-status-captiveportal-vouchers-print'] = array();
$priv_list['page-status-captiveportal-vouchers-print']['name'] = gettext("WebCfg - Status: Captive portal Vouchers Print");
$priv_list['page-status-captiveportal-vouchers-print']['descr'] = gettext("Allow access to the 'Status: Captive portal Vouchers print' page.");
$priv_list['page-status-captiveportal-vouchers-print']['match'] = array();
$priv_list['page-status-captiveportal-vouchers-print']['match'][] = "services_captiveportal_vouchers_print.php*";

?>
