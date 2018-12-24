<?php
date_default_timezone_set('Asia/Shanghai');
include '../../../../config.inc.php';
require_once("../libs/ZipFolder.php");
$db = Typecho_Db::get();
$prefix = $db->getPrefix();

$action = isset($_POST['action']) ? addslashes($_POST['action']) : '';
if($action=='downloadTool'){
	$cid = isset($_POST['cid']) ? addslashes($_POST['cid']) : '';
	$name = isset($_POST['name']) ? addslashes($_POST['name']) : '';
	$dir = isset($_POST['dir']) ? addslashes($_POST['dir']) : '';
	$url = isset($_POST['url']) ? addslashes($_POST['url']) : '';
	
	$queryTheme= $db->select('value')->from('table.options')->where('name = ?', 'theme'); 
	$rowTheme = $db->fetchRow($queryTheme);
	
	$zipsdir=dirname(__FILE__)."/../page/tools/module/";
	$toolsdir=dirname(__FILE__)."/../../../themes/".$rowTheme['value']."/tools/module/";
	$filename= substr( $url , strrpos($url , '/')+1 ); 
	
	$zip = new ZipFolder;
	$arr=$zip->getFile($url,$zipsdir,$filename,1);
	if($arr){
		$zip->unzip($zipsdir.$filename,$toolsdir);
		
		$outindex=file_get_contents($toolsdir.basename($filename,".zip")."/index.php");
		preg_match_all('/ \* @picture (.*)/',$outindex,$picture);
		preg_match_all('/ \* @description (.*)/',$outindex,$description);
		preg_match_all('/ \* @author (.*)/',$outindex,$author);
		if($picture[1][0]==""||$description[1][0]==""||$author[1][0]==""){
			echo 0;exit;
		}
		
		$insertData=array(
			'tools_cid' => $cid,
			'tools_name' => $name,
			'tools_dir' => $dir
		);
		$insert = $db->insert('table.tools')->rows($insertData);
		$insertId = $db->query($insert);
		
		echo 1;exit;
	}
	echo -1;exit;
}
?>