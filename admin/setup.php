<?php
/* Copyright (C) 2004-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2022 Mathieu Moulin <mathieu@iprospective.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    mmivateec/admin/setup.php
 * \ingroup mmivateec
 * \brief   MMIVATEEC setup page.
 */

// Load Dolibarr environment
require_once '../env.inc.php';
require_once '../main_load.inc.php';

$arrayofparameters = array(
	'VAT_INTRA_CHECK_VIES_WS_METHOD'=>array('type'=>'string', 'css'=>'minwidth500' ,'enabled'=>1),
	'VAT_INTRA_CHECK_VIES_LOG'=>array('type'=>'yesno','enabled'=>1),
	//'MMIVATEEC_MYPARAM3'=>array('type'=>'category:'.Categorie::TYPE_CUSTOMER, 'enabled'=>1),
	//'MMIVATEEC_MYPARAM4'=>array('type'=>'emailtemplate:thirdparty', 'enabled'=>1),
	//'MMIVATEEC_MYPARAM5'=>array('type'=>'yesno', 'enabled'=>1),
	//'MMIVATEEC_MYPARAM5'=>array('type'=>'thirdparty_type', 'enabled'=>1),
	//'MMIVATEEC_MYPARAM6'=>array('type'=>'securekey', 'enabled'=>1),
	//'MMIVATEEC_MYPARAM7'=>array('type'=>'product', 'enabled'=>1),
);

require_once('../../mmicommon/admin/mmisetup_1.inc.php');
