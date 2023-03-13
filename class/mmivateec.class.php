<?php

require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once NUSOAP_PATH.'/nusoap.php';

class mmivateec
{
    public static function check(String $vatNumber, int $fk_soc=null, String $object_type=null, int $fk_object=null)
    {
        global $conf, $mysoc;

        //http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl
        $WS_DOL_URL = 'https://ec.europa.eu/taxation_customs/vies/services/checkVatService';
        //$WS_DOL_URL_WSDL=$WS_DOL_URL.'?wsdl';
        $WS_DOL_URL_WSDL = 'https://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';
        $WS_METHOD = !empty($conf->global->VAT_INTRA_CHECK_VIES_WS_METHOD) ?$conf->global->VAT_INTRA_CHECK_VIES_WS_METHOD :'checkVat';
        //$WS_METHOD = 'checkVatApprox';

        $vatNumber = preg_replace('/\^\w/', '', $vatNumber);
        $vatNumber = str_replace(array(' ', '.'), '', $vatNumber);
        $countryCode = substr($vatNumber, 0, 2);
        $vatNumber = substr($vatNumber, 2);

        // Set the parameters to send to the WebService
        $parameters = array("countryCode" => $countryCode,
                            "vatNumber" => $vatNumber);
        if ($WS_METHOD == 'checkVatApprox') {
            $parameters['requesterCountryCode'] = substr($mysoc->tva_intra, 0, 2);
            $parameters['requesterVatNumber'] = substr($mysoc->tva_intra, 2);
        }
        // Set the WebService URL
        dol_syslog("Create nusoap_client for URL=".$WS_DOL_URL." WSDL=".$WS_DOL_URL_WSDL);
        require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
        $params = getSoapParams();
        //ini_set('default_socket_timeout', $params['response_timeout']);
        //$soapclient = new SoapClient($WS_DOL_URL_WSDL,$params);
        $soapclient = new nusoap_client($WS_DOL_URL_WSDL, true, $params['proxy_host'], $params['proxy_port'], $params['proxy_login'], $params['proxy_password'], $params['connection_timeout'], $params['response_timeout']);
        $soapclient->soap_defencoding = 'utf-8';
        $soapclient->xml_encoding = 'utf-8';
        $soapclient->decode_utf8 = false;

        // Check for an error
        $err = $soapclient->getError();
        if ($err) {
            dol_syslog("Constructor error ".$WS_DOL_URL, LOG_ERR);
        }

        // Call the WebService and store its result in $result.
        dol_syslog("Call method ".$WS_METHOD);
        //var_dump($parameters);
        $result = $soapclient->call($WS_METHOD, $parameters);
        // Association client / document
        if (!empty($fk_soc))
            $result['fk_soc'] = $fk_soc;
        if (!empty($object_type))
            $result['object_type'] = $object_type;
        if (!empty($fk_object))
            $result['fk_object'] = $fk_object;
        if (!empty($result['traderName']))
            $result['name'] = $result['traderName'];
        if (!empty($result['traderAddress']))
            $result['address'] = $result['traderAddress'];
        //var_dump($result); //die();

        if (!empty($result['valid'])) {
            //var_dump($result); //die();
            static::save($result);
        }
    }

    public static function save(Array $result)
    {
        global $db;

        //var_dump($result); die();
        $sql = 'INSERT INTO `'.MAIN_DB_PREFIX.'societe_vat_number_check`
            ( `fk_soc`, `object_type`, `fk_object`, `country_code`, `vat_number`, `valid`, `response_name`, `response_address`, `request_id`)
            VALUES
            ('.(!empty($result['fk_soc']) ?$result['fk_soc'] :'NULL').', '.(!empty($result['object_type']) ?'"'.$result['object_type'].'"' :'NULL').', '.(!empty($result['fk_object']) ?$result['fk_object'] :'NULL').', "'.$result['countryCode'].'", "'.$result['vatNumber'].'", '.($result['valid']=='true' ?1 :0).', "'.$db->escape($result['name']).'", "'.$db->escape($result['address']).'", "'.(isset($result['requestIdentifier']) ?$db->escape($result['requestIdentifier']) :'').'")';
        $q = $db->query($sql);
        //var_dump($q, $db);
        //echo $sql; die();
    }

    public static function listbysoc(int $socid)
    {
        global $db;

        $sql = 'SELECT *
            FROM `'.MAIN_DB_PREFIX.'societe` s
            INNER JOIN `'.MAIN_DB_PREFIX.'societe_vat_number_check` h
                ON h.fk_soc=s.rowid OR CONCAT(h.country_code, h.vat_number)=REPLACE(s.tva_intra, " ", "")
            WHERE s.rowid='.$socid;
        $q = $db->query($sql);
        //var_dump($q);
        $l = [];
        while($row=$q->fetch_object()) {
            if (!empty($row->fk_object)) {
                $sql = 'SELECT ref
                    FROM '.MAIN_DB_PREFIX.$row->object_type.'
                    WHERE rowid='.$row->fk_object;
                //echo $sql;
                $q2 = $db->query($sql);
                if ($row2=$q2->fetch_object())
                    $row->object_ref = $row2->ref;
            }
            //var_dump($row);
            $l[] = $row;
        }
        return $l;
    }
}