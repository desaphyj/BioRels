<?php

if (!defined("BIORELS")) header("Location:/");
$MODULE_DATA['API']=array();


try{
require_once('module/api/api_home/api_queries.php');


$str=file_get_contents('module/api/api_home/api_queries.php');
$MODULE_DATA['API']=findBlocks($str);

}catch (Exception $e)
{
	$MODULE_DATA['ERROR']=$e->getMessage();
}

try{
if ($USER_INPUT['PAGE']['VALUE']!=array())
{

	define("NO_CASE_CHANGE",1);
	$MODULE_DATA=runAPIQuery($USER_INPUT['PAGE']['VALUE'],$USER_INPUT['PARAMS'],$MODULE_DATA['API']);
	ob_end_clean();
	header('Content-type: application/json');
		
	echo json_encode($MODULE_DATA);
	exit;
}


}catch (Exception $e)
{
	$MODULE_DATA=array();
	$MODULE_DATA['ERROR']=$e->getMessage();
	ob_end_clean();
	header('Content-type: application/json');
		
	echo json_encode($MODULE_DATA);
	exit;
}



function processBlock(&$BLOCK)
{
   $lines=explode("\n",$BLOCK);
   
   $PARAMS=array();
   $TITLE='';
   $FUNCTION='';
   $DESCRIPTION='';
   $ALIAS=array();
   $PORTAL=array();
   $FOUND=false;
   foreach ($lines as $line)
   {
	   $pos=stripos($line,'Title:');
	   if ($pos!==false)
	   {
		   $TITLE=trim(substr($line,$pos+6));
		   continue;
	   }
	   $pos=stripos($line,'Function:');
	   if ($pos!==false)
	   {
		   $FUNCTION=trim(substr($line,$pos+9));
		   continue;
	   }
	   $pos=stripos($line,'Description:');
	   if ($pos!==false)
	   {
		   $DESCRIPTION.=trim(substr($line,$pos+12))."<br/>";
		   continue;
	   }
	   $pos=stripos($line,'Ecosystem:');
	   if ($pos!==false)
	   {
		   $ini_p=trim(substr($line,$pos+11));
		   $tab=explode(";",$ini_p);
		   $PORTAL=array();
		   foreach ($tab as $V)
		   {
			$t2=explode(":",$V);
			$t3=explode("|",$t2[1]);
			$PORTAL[$t2[0]]=$t3;
		   }
		   
		   continue;
	   }
	   $pos=stripos($line,'Alias:');
	   if ($pos!==false)
	   {
		   $ALIAS[]=trim(substr($line,$pos+6));
		   continue;
	   }
	   $pos=stripos($line,'Parameter:');
	   if ($pos!==false)
	   {//echo '<pre>';
		//echo "|".$line."|\n";
		   $tab=explode("|",substr($line,$pos+10));
		   if (count($tab)<4) die('Unable to parse line '.$line);
		   $tab[0]=str_replace('$',"",trim($tab[0]));
		   
		   //if (!is_numeric($tab[0]))die('Parameter name should be numeric in line '.$line);
		   $tab[1]=trim($tab[1]);
		   $tab[2]=trim($tab[2]);
		   $tab[3]=trim($tab[3]);
		   $tab[4]=trim($tab[4]);  
		   // if ($tab[2]!='int' && $tab[2]!='string' && $tab[2]!='float' && $tab[2]!='bool' && $tab[2]!='array')
		   // die('Parameter type should be '.$tab[2].' for parameter '.$tab[0].' in line '.$line);
		   
		   //if ($tab[3]!='required' && $tab[3]!='optional')die('Parameter type should be required or optional in line '.$line);
		   if (isset($tab[5]))
		   {
			   $pos=strpos($tab[5],'Default:');
			   if ($pos!==false) $tab[5]=substr($tab[5],$pos+8);
			   
			   $tab[5]=trim($tab[5]);
			   if ($tab[5]=="''")$tab[5]='';
		   }
		   $PARAMS[$tab[0]]=array('NAME'=>$tab[1],'TYPE'=>$tab[2],'EXAMPLE'=>$tab[3],'REQUIRED'=>$tab[4],'DEFAULT'=>isset($tab[5])?$tab[5]:'');
		   continue;
	   }
	   $pos=stripos($line,'Multi-array:');
	   if ($pos!==false)
	   {//echo '<pre>';
		//echo "|".$line."|\n";
		   $tab=explode("|",substr($line,$pos+12));
		   if (count($tab)<4) die('Unable to parse line '.$line);
		   $tab[0]=str_replace('$',"",trim($tab[0]));
		   
		   //if (!is_numeric($tab[0]))die('Parameter name should be numeric in line '.$line);
		   
		   $tab[1]=trim($tab[1]);
		   $tab[2]=trim($tab[2]);
		   $tab[3]=trim($tab[3]);
		   $tab[4]=trim($tab[4]);  
		   $tab[5]=trim($tab[5]);  
		   // if ($tab[2]!='int' && $tab[2]!='string' && $tab[2]!='float' && $tab[2]!='bool' && $tab[2]!='array')
		   // die('Parameter type should be '.$tab[2].' for parameter '.$tab[0].' in line '.$line);
		   
		   //if ($tab[3]!='required' && $tab[3]!='optional')die('Parameter type should be required or optional in line '.$line);
		   if (isset($tab[6]))
		   {
			   $pos=strpos($tab[6],'Default:');
			   if ($pos!==false) $tab[6]=substr($tab[6],$pos+8);
			   
			   $tab[6]=trim($tab[6]);
			   if ($tab[6]=="''")$tab[6]='';
		   }
		   $PARAMS[$tab[0]]['MULTI'][$tab[1]]=array('NAME'=>$tab[2],'TYPE'=>$tab[3],'EXAMPLE'=>$tab[4],'REQUIRED'=>$tab[5],'DEFAULT'=>isset($tab[6])?$tab[6]:'');
		   //print_R($PARAMS[$tab[0]]);
		   continue;
	   }

   }
   
   //if ($TITLE=='' || $FUNCTION=='' || $DESCRIPTION=='')die('Unable to parse block '.$BLOCK);
   //if ($PARAMS==array())die('No parameter found in block '.$BLOCK);
   return array('TITLE'=>$TITLE,'FUNCTION'=>$FUNCTION,'DESCRIPTION'=>$DESCRIPTION,'PARAMS'=>$PARAMS,'ALIAS'=>$ALIAS,'PORTAL'=>$PORTAL);
}

function findBlocks(&$str_file)
{
   $BLOCKS=array();
   $N=0;$prev_pos=0;
  do {
	   $pos = strpos($str_file, '$[API]',$prev_pos);
	   
	   $end_pos = strpos($str_file, '$[/API]', $pos);
	   
	   if ($pos !== false && $end_pos !== false) {
		   $str=substr($str_file, $pos, $end_pos - $pos + 7);
		   $BLOCKS[]=processBlock($str);
		   
	   }
	   $prev_pos=$end_pos+7;
	   
   } while ($pos !== false);
   return $BLOCKS;
}




function runAPIQuery($function_name,&$args,&$BLOCKS)
{
	
	
	$USER_PARAM=array();
	$KEY_MAP=array();
	for ($I=0;$I<count($args);++$I)
	{
		$tab=explode("__",$args[$I]);
		if (count($tab)==3)
		{
			
			if ($tab[2]=='key')
			{
				$KEY_MAP[$tab[0]][$tab[1]]=$args[$I+1];
			}
			if ($tab[2]=='value')
			{
				$USER_PARAM[$tab[0]][]=array($KEY_MAP[$tab[0]][$tab[1]],explode(",",$args[$I+1]));
				
			}
			++$I;
			continue;
		}
		if (count($tab)==2)
		{
			if (!isset($USER_PARAM[$tab[0]]))$USER_PARAM[$tab[0]]=array();
			$USER_PARAM[$tab[0]][$tab[1]]=explode(",",$args[$I+1]);
			++$I;
			continue;
		}
		else 
		{
			$USER_PARAM[$args[$I]]=$args[$I+1];
			++$I;
		}
	}

	
	$MODULE_DATA=array('RESULTS'=>array(),'PARAMETERS'=>array());
	foreach ($BLOCKS as &$BLOCK)
	{
		$FOUND=false;
		if ($BLOCK['FUNCTION']==$function_name)$FOUND=true;
		if (isset($BLOCK['ALIAS']) && in_array($function_name,$BLOCK['ALIAS']))$FOUND=true;	
		if (!$FOUND)continue;

		$PARAMS=&$BLOCK['PARAMS'];
		
		//if (count($PARAMS)!=count($args)-1) die("Different number of parameters");
		$N_PARAM=0;
		$FCT_VALUES=array();
		foreach ($PARAMS as $KEY_PARAM=>&$PARAM)
		{
			
			if ($PARAM['REQUIRED']=='required' && (!isset($USER_PARAM[$KEY_PARAM]) || $USER_PARAM[$KEY_PARAM]==''))
				throw new Exception('Parameter '.$KEY_PARAM.' is required' );
			if ($PARAM['REQUIRED']=='optional' && (!isset($USER_PARAM[$KEY_PARAM]) || $USER_PARAM[$KEY_PARAM]==''))
			{
				
				if (isset($PARAM['DEFAULT'])&& $PARAM['DEFAULT']!='')
				{
				
				$N_PARAM++;
				if (strtolower($PARAM['DEFAULT'])=="false")$FCT_VALUES[$N_PARAM]=false;
				else if (strtolower($PARAM['DEFAULT'])=="true")$FCT_VALUES[$N_PARAM]=true;
				else $FCT_VALUES[$N_PARAM]=$PARAM['DEFAULT'];
				}
				continue;
			}
			
			if (!isset($USER_PARAM[$KEY_PARAM])) continue;
			$value=$USER_PARAM[$KEY_PARAM];
			
			if ($PARAM['TYPE']=='array')
			{
				
				$value=explode(",",$value);
			}
			if ($PARAM['TYPE']=='int' && !is_numeric($value))
				throw new Exception ('Parameter '.$KEY_PARAM.' should be int: '. $value );
			if ($PARAM['TYPE']=='float' && !is_float($value))
				throw new Exception ('Parameter '.$KEY_PARAM.' should be float' );
			if ($PARAM['TYPE']=='multi_array')
			{
				//$tab=explode(";",$value);
				
				++$N_PARAM;
				$FCT_VALUES[$N_PARAM]=$value;
				// foreach ($tab as $record)
				// {
				// 	if ($record=='')continue;
				// 	$pos=strpos($record,"=");
				// 	if ($pos===false)throw new Exception('Unable to parse multi_array parameter '.$KEY_PARAM.' with value '.$value);
					
				// 	$FCT_VALUES[$N_PARAM][substr($record,0,$pos)]=explode(",",str_Replace("'","''",substr($record,$pos+1)));
					
				// }
				continue;
				
			}
			if ($PARAM['TYPE']=='boolean')
			{
				if ($value=='true')$value=true;
				else if ($value=='false')$value=false;
				else if ($value=='1')$value=true;
				else if ($value=='0')$value=false;
				else if ($value=='Y')$value=true;
				else if ($value=='N')$value=false;
				else if ($value=='yes')$value=true;
				else if ($value=='no')$value=false;
				
			}
			$N_PARAM++;
			$FCT_VALUES[$N_PARAM]=str_replace("'","''",$value);
			
			

		}

		$MODULE_DATA['PARAMETERS']=$FCT_VALUES;

		$RESULTS=array();
		switch ($N_PARAM)
		{
			case 0: $RESULTS=call_user_func($function_name);break;
			case 1: $RESULTS=call_user_func($function_name,$FCT_VALUES[1]);break;
			case 2: $RESULTS=call_user_func($function_name,$FCT_VALUES[1],$FCT_VALUES[2]);break;
			case 3: $RESULTS=call_user_func($function_name,$FCT_VALUES[1],$FCT_VALUES[2],$FCT_VALUES[3]);break;
			case 4: $RESULTS=call_user_func($function_name,$FCT_VALUES[1],$FCT_VALUES[2],$FCT_VALUES[3],$FCT_VALUES[4]);break;
			case 5: $RESULTS=call_user_func($function_name,$FCT_VALUES[1],$FCT_VALUES[2],$FCT_VALUES[3],$FCT_VALUES[4],$FCT_VALUES[5]);break;
			case 6: $RESULTS=call_user_func($function_name,$FCT_VALUES[1],$FCT_VALUES[2],$FCT_VALUES[3],$FCT_VALUES[4],$FCT_VALUES[5],$FCT_VALUES[6]);break;
			case 7: $RESULTS=call_user_func($function_name,$FCT_VALUES[1],$FCT_VALUES[2],$FCT_VALUES[3],$FCT_VALUES[4],$FCT_VALUES[5],$FCT_VALUES[6],$FCT_VALUES[7]);break;
			case 8: $RESULTS=call_user_func($function_name,$FCT_VALUES[1],$FCT_VALUES[2],$FCT_VALUES[3],$FCT_VALUES[4],$FCT_VALUES[5],$FCT_VALUES[6],$FCT_VALUES[7],$FCT_VALUES[8]);break;
			default: throw new Exception('Unable to understand the parameters');break;
		}
		$MODULE_DATA['RESULTS']= $RESULTS;
		return $MODULE_DATA;
	}
	throw new Exception('Function '.$function_name.' not found');
}

?>