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
customerHasFeature('protected_areas') && isset($_GET['id']) or showBadRequestErrorPage();
$id = intval($_GET['id']);
$stmt = execQuery("UPDATE htaccess SET status = 'todelete' WHERE id = ? AND dmn_id = ? AND status = 'ok'", [
    $id, getCustomerMainDomainId($_SESSION['user_id'])
]);
$stmt->rowCount() or showBadRequestErrorPage();
sendDaemonRequest();
writeLog(sprintf('%s deleted protected area with ID %s', $_SESSION['user_logged'], $id), E_USER_NOTICE);
setPageMessage(tr('Protected area successfully scheduled for deletion.'), 'success');
redirectTo('protected_areas.php');
