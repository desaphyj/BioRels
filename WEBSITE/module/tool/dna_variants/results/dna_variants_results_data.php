<?php

ini_set('memory_limit','5000M');
if (!defined("BIORELS")) header("Location:/");


$MD5_HASH=$USER_INPUT['PAGE']['VALUE'];

$MODULE_DATA['INFO']=runQuery("SELECT * FROM web_job where md5id = '".$MD5_HASH."'")[0];
$MODULE_DATA['INFO']['JOB_STATUS']=json_decode($MODULE_DATA['INFO']['JOB_STATUS'],true);
$MODULE_DATA['INFO']['PARAMS']=json_decode($MODULE_DATA['INFO']['PARAMS'],true);
$LEN=$MODULE_DATA['INFO']['PARAMS']['END_POS']-$MODULE_DATA['INFO']['PARAMS']['START_POS'];

	
$MODULE_DATA['FILES']=runQuery("SELECT document_name,document_description ,mime_Type
FROM web_job_document where  web_job_id = ".$MODULE_DATA['INFO']['WEB_JOB_ID']);


//$list_id=array_keys($F['DOCUMENT_CONTENT']);

// $res=runQuery("SELECT * FROM gn_entry g, (SELECT count(*) co, gn_entry_id FROM drug_disease dd WHERE drug_entry_id IN (".implode(',',$list_id).') group by gn_entry_id) d where d.gn_entry_id = g.gn_entry_id ORDER BY co desc');
// echo '<pre>';print_r($res);exit;
?>