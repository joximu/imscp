<?php
/**
 * i-MSCP - internet Multi Server Control Panel
 * Copyright (C) 2010-2018 by Laurent Declercq <l.declercq@nuxwin.com>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

use iMSCP_Registry as Registry;

require_once 'imscp-lib.php';

checkLogin('user');
Registry::get('iMSCP_Application')->getEventsManager()->dispatch(iMSCP_Events::onClientScriptStart);
customerHasFeature('aps') && isset($_GET['id']) or showBadRequestErrorPage();

$softwareId = intval($_GET['id']);
$domainProps = getCustomerProperties($_SESSION['user_id']);
$stmt = execQuery('SELECT software_res_del FROM web_software_inst WHERE software_id = ? AND domain_id = ?', [$softwareId, $domainProps['domain_id']]);
$stmt->rowCount() or showBadRequestErrorPage();

if ($stmt->fetchColumn() == 1) {
    execQuery('DELETE FROM web_software_inst WHERE software_id = ? AND domain_id = ?', [$softwareId, $domainProps['domain_id']]);
    setPageMessage(tr('Software deleted.'), 'success');
    redirectTo('software.php');
}

execQuery("UPDATE web_software_inst SET software_status = 'todelete' WHERE software_id = ? AND domain_id = ?", [
    $softwareId, $domainProps['domain_id']
]);
sendDaemonRequest();
setPageMessage(tr('Software successfully scheduled for deletion.'), 'success');
redirectTo('software.php');
