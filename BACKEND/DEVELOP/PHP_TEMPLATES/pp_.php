<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////// pp_${DATASOURCE} //////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////

/// Objectives: 
///  


/// Job name - Do not change
$JOB_NAME='pp_${DATASOURCE}';

/// Get root directories
$TG_DIR= getenv('TG_DIR');
if ($TG_DIR===false)  die('NO TG_DIR found ');
if (!is_dir($TG_DIR)) die('TG_DIR value is not a directory '.$TG_DIR);
require_once($TG_DIR.'/BACKEND/SCRIPT/LIB/loader.php');

/// Get job id
$JOB_ID=getJobIDByName($JOB_NAME);
$PROCESS_CONTROL['DIR']='N/A';
/// Get job info
$JOB_INFO=$GLB_TREE[$JOB_ID];

addLog("Create directory");
	/// Get parent info
	$CK_INFO=$GLB_TREE[getJobIDByName('${PP_PARENT}')];

	/// Setting up directory path:
	$W_DIR=$TG_DIR.'/'.$GLB_VAR['PROCESS_DIR']; if (!is_dir($W_DIR)) 								failProcess($JOB_ID."001",'NO '.$W_DIR.' found ');
	$W_DIR.='/'.$CK_INFO['DIR'].'/';   			if (!is_dir($W_DIR) && !mkdir($W_DIR)) 				failProcess($JOB_ID."002",'Unable to find and create '.$W_DIR);
	$W_DIR.=$CK_INFO['TIME']['DEV_DIR'];		if (!is_dir($W_DIR) && !mkdir($W_DIR)) 				failProcess($JOB_ID."003",'Unable to create new process dir '.$W_DIR);
												if (!chdir($W_DIR)) 								failProcess($JOB_ID."004",'Unable to access process dir '.$W_DIR);
	addLog("Working directory: ".$W_DIR);

	/// Update process control directory to the current release so that the next job can use it
	$PROCESS_CONTROL['DIR']=$CK_INFO['TIME']['DEV_DIR'];



/// If you need to review the changes between the database and the input files prior to processing
/// This can be useful when you need to cleanup the database before performing any changes due to potential conflict
/// Or if you need to prepare the input files first before comparing the data with the database.


successProcess();
?>