<?php
/**
 * ispCP ω (OMEGA) a Virtual Hosting Control System
 *
 * @copyright 	2001-2006 by moleSoftware GmbH
 * @copyright 	2006-2010 by ispCP | http://isp-control.net
 * @version 	SVN: $Id$
 * @link 		http://isp-control.net
 * @author 		ispCP Team
 *
 * @license
 * The contents of this file are subject to the Mozilla Public License
 * Version 1.1 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS"
 * basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
 * License for the specific language governing rights and limitations
 * under the License.
 *
 * The Original Code is "VHCS - Virtual Hosting Control System".
 *
 * The Initial Developer of the Original Code is moleSoftware GmbH.
 * Portions created by Initial Developer are Copyright (C) 2001-2006
 * by moleSoftware GmbH. All Rights Reserved.
 * Portions created by the ispCP Team are Copyright (C) 2006-2010 by
 * isp Control Panel. All Rights Reserved.
 */

require '../include/i-mscp-lib.php';

check_login(__FILE__);

$cfg = ispCP_Registry::get('Config');

$tpl = new iMSCP_pTemplate();
$tpl->define_dynamic('page', $cfg->ADMIN_TEMPLATE_PATH . '/personal_change.tpl');
$tpl->define_dynamic('page_message', 'page');
$tpl->define_dynamic('hosting_plans', 'page');

$tpl->assign(
	array(
		'TR_ADMIN_CHANGE_PERSONAL_DATA_PAGE_TITLE' => tr('ispCP - Admin/Change Personal Data'),
		'THEME_COLOR_PATH' => "../themes/{$cfg->USER_INITIAL_THEME}",
		'THEME_CHARSET' => tr('encoding'),
		'ISP_LOGO' => get_logo($_SESSION['user_id'])
	)
);

if (isset($_POST['uaction']) && $_POST['uaction'] === 'updt_data') {
	update_admin_personal_data($sql, $_SESSION['user_id']);
}

gen_admin_personal_data($tpl, $sql, $_SESSION['user_id']);

function gen_admin_personal_data(&$tpl, &$sql, $user_id) {

	$cfg = ispCP_Registry::get('Config');

	$query = "
		SELECT
			`fname`,
			`lname`,
			`gender`,
			`firm`,
			`zip`,
			`city`,
			`state`,
			`country`,
			`street1`,
			`street2`,
			`email`,
			`phone`,
			`fax`
		FROM
			`admin`
		WHERE
			`admin_id` = ?
	";

	$rs = exec_query($sql, $query, $user_id);

	$tpl->assign(
		array(
			'FIRST_NAME' => empty($rs->fields['fname']) ? '' : tohtml($rs->fields['fname']),
			'LAST_NAME' => empty($rs->fields['lname']) ? '' : tohtml($rs->fields['lname']),
			'FIRM' => empty($rs->fields['firm']) ? '' : tohtml($rs->fields['firm']),
			'ZIP' => empty($rs->fields['zip']) ? '' : tohtml($rs->fields['zip']),
			'CITY' => empty($rs->fields['city']) ? '' : tohtml($rs->fields['city']),
			'STATE' => empty($rs->fields['state']) ? '' : tohtml($rs->fields['state']),
			'COUNTRY' => empty($rs->fields['country']) ? '' : tohtml($rs->fields['country']),
			'STREET_1' => empty($rs->fields['street1']) ? '' : tohtml($rs->fields['street1']),
			'STREET_2' => empty($rs->fields['street2']) ? '' : tohtml($rs->fields['street2']),
			'EMAIL' => empty($rs->fields['email']) ? '' : tohtml($rs->fields['email']),
			'PHONE' => empty($rs->fields['phone']) ? '' : tohtml($rs->fields['phone']),
			'FAX' => empty($rs->fields['fax']) ? '' : tohtml($rs->fields['fax']),
			'VL_MALE' => (($rs->fields['gender'] == 'M') ? $cfg->HTML_SELECTED : ''),
			'VL_FEMALE' => (($rs->fields['gender'] == 'F') ? $cfg->HTML_SELECTED : ''),
			'VL_UNKNOWN' => ((($rs->fields['gender'] == 'U') || (empty($rs->fields['gender']))) ? $cfg->HTML_SELECTED : '')
		)
	);
}

function update_admin_personal_data(&$sql, $user_id) {

	$fname = clean_input($_POST['fname']);
	$lname = clean_input($_POST['lname']);
	$gender = $_POST['gender'];
	$firm = clean_input($_POST['firm']);
	$zip = clean_input($_POST['zip']);
	$city = clean_input($_POST['city']);
	$state = clean_input($_POST['state']);
	$country = clean_input($_POST['country']);
	$street1 = clean_input($_POST['street1']);
	$street2 = clean_input($_POST['street2']);
	$email = clean_input($_POST['email']);
	$phone = clean_input($_POST['phone']);
	$fax = clean_input($_POST['fax']);

	$query = "
		UPDATE
			`admin`
		SET
			`fname` = ?,
			`lname` = ?,
			`firm` = ?,
			`zip` = ?,
			`city` = ?,
			`state` = ?,
			`country` = ?,
			`street1` = ?,
			`street2` = ?,
			`email` = ?,
			`phone` = ?,
			`fax` = ?,
			`gender` = ?
		WHERE
			`admin_id` = ?
";

	$rs = exec_query($sql, $query, array($fname,
			$lname,
			$firm,
			$zip,
			$city,
			$state,
			$country,
			$street1,
			$street2,
			$email,
			$phone,
			$fax,
			$gender,
			$user_id));

	set_page_message(tr('Personal data updated successfully!'));
}

/*
 *
 * static page messages.
 *
 */
gen_admin_mainmenu($tpl, $cfg->ADMIN_TEMPLATE_PATH . '/main_menu_general_information.tpl');
gen_admin_menu($tpl, $cfg->ADMIN_TEMPLATE_PATH . '/menu_general_information.tpl');

$tpl->assign(
	array(
		'TR_CHANGE_PERSONAL_DATA' => tr('Change personal data'),
		'TR_PERSONAL_DATA' => tr('Personal data'),
		'TR_FIRST_NAME' => tr('First name'),
		'TR_LAST_NAME' => tr('Last name'),
		'TR_COMPANY' => tr('Company'),
		'TR_ZIP_POSTAL_CODE' => tr('Zip/Postal code'),
		'TR_CITY' => tr('City'),
		'TR_STATE' => tr('State/Province'),
		'TR_COUNTRY' => tr('Country'),
		'TR_STREET_1' => tr('Street 1'),
		'TR_STREET_2' => tr('Street 2'),
		'TR_EMAIL' => tr('Email'),
		'TR_PHONE' => tr('Phone'),
		'TR_FAX' => tr('Fax'),
		'TR_GENDER' => tr('Gender'),
		'TR_MALE' => tr('Male'),
		'TR_FEMALE' => tr('Female'),
		'TR_UNKNOWN' => tr('Unknown'),
		'TR_UPDATE_DATA' => tr('Update data'),
	)
);

gen_page_message($tpl);

$tpl->parse('PAGE', 'page');
$tpl->prnt();

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug();
}

unset_messages();
