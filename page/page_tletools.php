<?php
/**
 * 工具集页面
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
include "tools/config/function.php";
date_default_timezone_set('Asia/Shanghai');

$options = Typecho_Widget::widget('Widget_Options');
$plug_url = $options->pluginUrl;
$option=$options->plugin('TleTools');

$queryPlugins= $this->db->select('value')->from('table.options')->where('name = ?', 'plugins'); 
$rowPlugins = $this->db->fetchRow($queryPlugins);
$plugins=@unserialize($rowPlugins['value']);
if(!isset($plugins['activated']['TleTools'])){
	die('未启用同乐工具集插件');
}

$querySiteUrl= $this->db->select('value')->from('table.options')->where('name = ?', 'siteUrl'); 
$rowSiteUrl = $this->db->fetchRow($querySiteUrl);
$queryTheme= $this->db->select('value')->from('table.options')->where('name = ?', 'theme'); 
$rowTheme = $this->db->fetchRow($queryTheme);
$toolpage = isset($_GET['toolpage']) ? addslashes(trim($_GET['toolpage'])) : '';
$queryDir= $this->db->select()->from('table.tools')->where('tools_dir = ?', $toolpage); 
$rowDir = $this->db->fetchRow($queryDir);
if(count($rowDir)>0){
	define("TOOLURL",$rowSiteUrl["value"].__TYPECHO_THEME_DIR__."/".$rowTheme["value"]."/tools/module/".$toolpage."/");
	define("TOOLINCLUDE",dirname(__FILE__)."/tools/include/");
	define("TOOLDIR",dirname(__FILE__)."/tools/module/".$toolpage."/");
	include "tools/module/".$toolpage."/index.php";exit;
}else if($toolpage!=""){
	include "tools/include/404.php";exit;
}
?>
<?php include "tools/include/header.php";?>
<?php
if ($this->user->group=='administrator'){
	include "tools/include/admin.php";exit;
}
?>
<div class="mdui-appbar">
  <div class="mdui-drawer" id="leftmenu" style="display:none;">
	  <ul class="mdui-list">
		<a href="<?=$this->options ->siteUrl();?>">
			<li class="mdui-list-item mdui-ripple">
			  <i class="mdui-list-item-icon mdui-icon material-icons">&#xe88a;</i>
			  <div class="mdui-list-item-content">首页</div>
			</li>
		</a>
		<li class="mdui-subheader">分类</li>
		<?php $this->widget('Widget_Metas_Category_List')->to($cats); ?>
		<?php while ($cats->next()):if($cats->parent!=0)continue; ?>
		<a href="<?php $cats->permalink()?>" target="_blank" title="<?php $cats->name()?>">
			<li class="mdui-list-item mdui-ripple">
			  <i class="mdui-list-item-icon mdui-icon material-icons">&#xe5c8;</i>
			  <div class="mdui-list-item-content"><?php $cats->name()?></small></div>
			</li>
		</a>
		<?php endwhile; ?>
		<li class="mdui-subheader">页面</li>
		<?php $this->widget('Widget_Contents_Page_List')->to($pages); ?>
		<?php while ($pages->next()): ?>
		<a href="<?php $pages->permalink()?>" target="_blank" title="<?php $pages->title()?>">
			<li class="mdui-list-item mdui-ripple">
			  <i class="mdui-list-item-icon mdui-icon material-icons">&#xe5c8;</i>
			  <div class="mdui-list-item-content"><?php $pages->title()?></small></div>
			</li>
		</a>
		<?php endwhile; ?>
	  </ul>
  </div>
  <div class="mdui-toolbar mdui-color-indigo">
	<a href="javascript:;" id="leftMenuToggle" class="mdui-btn mdui-btn-icon"><i class="mdui-icon material-icons">menu</i></a>
	<a href="<?=$this->permalink;?>" class="mdui-typo-title"><?php $this->title(); ?></a>
	<div class="mdui-toolbar-spacer"></div>
	<form method="get" action="">
		<div class="mdui-textfield mdui-textfield-expandable mdui-float-right">
		  <a class="mdui-textfield-icon mdui-btn mdui-btn-icon"><i class="mdui-icon material-icons">search</i></a>
		  <input class="mdui-textfield-input" name="searchwords" style="color:#fff;" type="text" placeholder="输入关键词搜索小工具"/>
		  <a class="mdui-textfield-close mdui-btn mdui-btn-icon"><i class="mdui-icon material-icons">close</i></a>
		</div>
	</form>
	<a href="javascript:;" class="mdui-btn mdui-btn-icon" mdui-menu="{target: '#menu'}"><i class="mdui-icon material-icons">more_vert</i></a>
	<ul class="mdui-menu" id="menu" style="overflow:auto;">
	  <?php
	  $nettools=json_decode($option->nettools,true);
	  foreach($nettools as $val){
	  ?>
	  <li class="mdui-menu-item">
		<a href="<?=$val["link"];?>" target="_blank" rel="nofollow" class="mdui-ripple"><?=$val["name"];?></a>
	  </li>
	  <?php
	  }
	  ?>
	</ul>
  </div>
  <div class="mdui-tab mdui-color-indigo" mdui-tab>
	<?php
	$queryToolsCate= "select * from ".$this->db->getPrefix()."tools_cate";
	$resultToolsCate = $this->db->fetchAll($queryToolsCate);
	foreach($resultToolsCate as $value){
	?>
	<a href="#tools_cateid<?=$value["tools_cateid"];?>" class="mdui-ripple mdui-ripple-white"><?=$value["tools_catename"];?></a>
	<?php
	}
	?>
  </div>
</div>

<div class="mdui-container-fluid" style="padding:0px;">
  <?php
	$queryToolsCate= "select * from ".$this->db->getPrefix()."tools_cate";
	$resultToolsCate = $this->db->fetchAll($queryToolsCate);
	foreach($resultToolsCate as $value){
  ?>
  <div id="tools_cateid<?=$value["tools_cateid"];?>">
	<div class="<?php if(!isMobileBrowse()){?>mdui-row-xs-3 mdui-row-sm-4 mdui-row-md-5 mdui-row-lg-6 mdui-row-xl-7 <?php }?>mdui-grid-list content waterfall"<?php if(!isMobileBrowse()){?> style="column-count: 5;"<?php }?>>
		<?php
		$searchwords = isset($_GET['searchwords']) ? addslashes($_GET['searchwords']) : '';
		$WHERE="";
		if($searchwords){
			$WHERE=' AND tools_name like "%'.$searchwords.'%"';
		}
		$queryTools= "select * from ".$this->db->getPrefix()."tools as t,".$this->db->getPrefix()."tools_cate as tc WHERE tools_cateid=tools_cid AND tools_cid=".$value["tools_cateid"].$WHERE;
		$pagination = isset($_GET['pagination']) ? intval($_GET['pagination']) : 1;
		if($pagination<1){
			$pagination=1;
		}
		$resultToolsTotal = $this->db->fetchAll($queryTools);
		$page_num=25;
		$totalrec=count($resultToolsTotal);
		$pager=ceil($totalrec/$page_num);
		if($pagination>$pager){
			$pagination=$pager;
		}
		if($pagination<=1){
			$top_page=1;
			if($pager>1){
				$bottom_page=$pagination+1;
			}else{
				$bottom_page=1;
			}
		}else{
			$top_page=$pagination-1;
			if($pagination<$pager){
				$bottom_page=$pagination+1;
			}else{
				$bottom_page=$pager;
			}
		}
		$i=($pagination-1)*$page_num<0?0:($pagination-1)*$page_num;
		$queryTools= "select * from ".$this->db->getPrefix()."tools,".$this->db->getPrefix()."tools_cate as tc WHERE tools_cateid=tools_cid AND tools_cid=".$value["tools_cateid"].$WHERE." limit ".$i.",".$page_num;
		$resultTools = $this->db->fetchAll($queryTools);
		foreach($resultTools as $row){
		?>
		<div class="mdui-col item"<?php if(!isMobileBrowse()){?> style="break-inside: avoid;width: 100%;"<?php }?>>
			<div class="mdui-grid-list" style="margin:1px;">
				<?php
				$outindex=file_get_contents(dirname(__FILE__)."/tools/module/".$row["tools_dir"]."/index.php");
				preg_match_all('/ \* @title (.*)/',$outindex,$title);
				preg_match_all('/ \* @subtitle (.*)/',$outindex,$subtitle);
				preg_match_all('/ \* @package (.*)/',$outindex,$package);
				preg_match_all('/ \* @description (.*)/',$outindex,$description);
				preg_match_all('/ \* @author (.*)/',$outindex,$author);
				preg_match_all('/ \* @author email (.*)/',$outindex,$authoremail);
				preg_match_all('/ \* @author qq (.*)/',$outindex,$authorqq);
				preg_match_all('/ \* @author info (.*)/',$outindex,$authorinfo);
				preg_match_all('/ \* @link (.*)/',$outindex,$link);
				preg_match_all('/ \* @version (.*)/',$outindex,$version);
				preg_match_all('/ \* @picture (.*)/',$outindex,$picture);
				if($title[1][0]==""||$subtitle[1][0]==""||$package[1][0]==""||$description[1][0]==""||$author[1][0]==""||$authoremail[1][0]==""||$authorqq[1][0]==""||$authorinfo[1][0]==""||$link[1][0]==""||$version[1][0]==""||$picture[1][0]==""){continue;}
				?>
				<div class="mdui-card">
				  <div class="mdui-card-header">
					<a href="http://wpa.qq.com/msgrd?v=3&uin=<?=trim($authorqq[1][0]);?>&site=qq&menu=yes" target="_blank" rel="nofollow" title="<?=trim($author[1][0]);?>">
						<img class="mdui-card-header-avatar" src="https://q4.qlogo.cn/headimg_dl?dst_uin=<?=trim($authorqq[1][0]);?>&spec=100" alt="<?=trim($author[1][0]);?>"/>
					</a>
					<div class="mdui-card-header-title"><?=trim($author[1][0]);?></div>
					<div class="mdui-card-header-subtitle"><?=trim($authorinfo[1][0]);?></div>
				  </div>
				  <a href="?toolpage=<?=$row["tools_dir"];?>" target="_blank">
					  <div class="mdui-card-media">
						<img src="<?=trim($picture[1][0]);?>" alt="<?=$row["tools_name"];?>"/>
						<div class="mdui-card-menu">
						  <button class="mdui-btn mdui-btn-icon mdui-text-color-white"><i class="mdui-icon material-icons">share</i></button>
						</div>
					  </div>
				  </a>
				  <div class="mdui-card-primary">
					<div class="mdui-card-primary-title"><?=$row["tools_name"];?></div>
					<div class="mdui-card-primary-subtitle"><?=trim($subtitle[1][0]);?></div>
				  </div>
				  <div class="mdui-card-content"><?=trim($description[1][0]);?></div>
				  <div class="mdui-card-actions">
					<a class="mdui-btn mdui-ripple" href="<?=trim($link[1][0]);?>" target="_blank" rel="nofollow">来源网站</a>
				  </div>
				</div>
			</div>
		</div>
		<?php
		}
		?>
	</div>
	<?php if(count($resultTools)>0){?>
	<!--ajax分页加载-->
	<ul class="am-pagination blog-pagination" style="float:right;margin-right:20px;">
	  <?php if($pagination!=1){?>
		<li class="am-pagination-prev" style="float:left;"><a href="?searchwords=<?=$searchwords;?>&pagination=1">首页&nbsp;&nbsp;</a></li>
	  <?php }?>
	  <?php if($pagination>1){?>
		<li class="am-pagination-prev" style="float:left;"><a href="?searchwords=<?=$searchwords;?>&pagination=<?=$top_page;?>">&laquo;上一页</a></li>
	  <?php }?>
	  <?php if($pagination<$pager){?>
		<li class="am-pagination-next" style="float:left;"><a class="next" href="?searchwords=<?=$searchwords;?>&pagination=<?=$bottom_page;?>">下一页&raquo;</a></li>
	  <?php }?>
	  <?php if($pagination!=$pager){?>
		<li class="am-pagination-next" style="float:left;"><a href="?searchwords=<?=$searchwords;?>&pagination=<?=$pager;?>">&nbsp;&nbsp;尾页</a></li>
	  <?php }?>
	</ul>
	<!--
	<script src="<?=$plug_url;?>/TleTools/js/jquery.ias.min.js" type="text/javascript"></script>
	<script>
	var ias = $.ias({
		container: ".content", /*包含所有文章的元素*/
		item: ".item", /*文章元素*/
		pagination: ".am-pagination", /*分页元素*/
		next: ".am-pagination a.next", /*下一页元素*/
	});
	ias.extension(new IASTriggerExtension({
		text: '<div class="cat-nav am-round"><small>阿速度发</small></div>', /*此选项为需要点击时的文字*/
		offset: false, /*设置此项后，到 offset+1 页之后需要手动点击才能加载，取消此项则一直为无限加载*/
	}));
	ias.extension(new IASSpinnerExtension());
	ias.extension(new IASNoneLeftExtension({
		text: '<div class="cat-nav am-round"><small>阿速度发</small></div>', /*加载完成时的提示*/
	}));
	</script>
	-->
	<?php
	}else{
		include "tools/include/404.php";
	}
	?>
  </div>
  <?php
	}
  ?>
</div>
<?php include "tools/include/footer.php";?>
<script>
$(function(){
	var leftmenuInit = new mdui.Drawer('#leftmenu');
	leftmenuInit.close();
	$("#leftmenu").css("display","block");
	$("#leftMenuToggle").click(function(){
		leftmenuInit.toggle();
	});
});
</script>