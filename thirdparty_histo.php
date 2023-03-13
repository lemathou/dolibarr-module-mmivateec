<?php
/* Copyright (C) 2022  Mathieu Moulin    <mathieu@iprospective.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */


// Load Dolibarr environment
require_once 'load_main.inc.php';

require_once DOL_DOCUMENT_ROOT . '/core/lib/company.lib.php';

require_once DOL_DOCUMENT_ROOT . '/custom/mmivateec/class/mmivateec.class.php';

$socid = GETPOST('socid', 'int') ?GETPOST('socid', 'int') : GETPOST('id', 'int');
if ($user->socid) {
	$socid = $user->socid;
}

$object = new Societe($db);
$extrafields = new ExtraFields($db);
if ($socid > 0) {
	$object->fetch($socid);
}

$title = 'VAT EEC';

llxHeader('', $title, '');
$head = societe_prepare_head($object);
print dol_get_fiche_head($head, 'mmivateec', $langs->trans("ThirdParty"), -1, 'company');
//dol_banner_tab($object, 'socid', $linkback, ($user->socid ? 0 : 1), 'rowid', 'nom');

$param = '&element_type=' . GETPOST('element_type') . '&id=' . GETPOST('id');
if (GETPOST('element_type') == 'thirdparty') {
    require_once DOL_DOCUMENT_ROOT . '/core/lib/company.lib.php';
    require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
    $soc = new Societe($db);
    $soc->fetch(GETPOST('socid'));
    $head = societe_prepare_head($soc);
    dol_fiche_head($head, 'historyexchange', $langs->trans("ThirdParty"), -1, 'company');
    $param = '&element_type=' . GETPOST('element_type') . '&socid=' . GETPOST('socid');
}

// *****

echo '<h1>'.$title.'</h1>';

$l = mmivateec::listbysoc($socid);
echo '<table border="1"><thead><tr>
<th>Date</th>
<!--<th>Société</th>-->
<th>Objet</th>
<th>TVA Intra</th>
<th>Réponse</th>
<th>Société Réponse</th>
<th>Adresse Réponse</th>
<th>ID Réponse</th>
</thead><tbody>';
foreach($l as $row) {
    //var_dump($row);
    echo '<tr>';
    echo '<td>'.$row->ctime.'</td>';
    //echo '<td>'.$row->fk_soc.'</td>';
    echo '<td>'.(!empty($row->object_ref) ?'<a href="/'.($row->object_type=='facture' ?'compta/'.$row->object_type :($row->object_type=='propal' ?'comm/'.$row->object_type :$row->object_type)).'/card.php?id='.$row->fk_object.'">'.$row->object_type.' '.$row->object_ref.'</a>' :'').'</td>';
    echo '<td>'.$row->country_code.$row->vat_number.'</td>';
    echo '<td>'.$row->valid.'</td>';
    echo '<td>'.$row->response_name.'</td>';
    echo '<td>'.$row->response_address.'</td>';
    echo '<td>'.$row->request_id.'</td>';
    echo '</tr>';
}
echo '</tbody></table>';
//var_dump($l);

// End of page
llxFooter();
$db->close();
