<?php
	$options = Typecho_Widget::widget('Widget_Options');
	$plug_url = $options->pluginUrl;
	if(strpos($this->permalink,'?')){
		$url=substr($this->permalink,0,strpos($this->permalink,'?'));
	}else{
		$url=$this->permalink;
	}
	$action = isset($_POST['action']) ? addslashes(trim($_POST['action'])) : '';
	if($action=="submit_add_tools_catename"){
		$tools_catename = isset($_POST['tools_catename']) ? addslashes(trim($_POST['tools_catename'])) : '';
		$insertData=array(
			'tools_catename' => $tools_catename
		);
		$insert = $this->db->insert('table.tools_cate')->rows($insertData);
		$insertId = $this->db->query($insert);
		$this->response->redirect($url."#cate");
	}else if($action=="submit_add_tools"){
		$tools_cid = isset($_POST['tools_cid']) ? addslashes(trim($_POST['tools_cid'])) : '';
		$tools_name = isset($_POST['tools_name']) ? addslashes(trim($_POST['tools_name'])) : '';
		$tools_dir = isset($_POST['tools_dir']) ? addslashes(trim($_POST['tools_dir'])) : '';
		$insertData=array(
			'tools_cid' => $tools_cid,
			'tools_name' => $tools_name,
			'tools_dir' => $tools_dir
		);
		$insert = $this->db->insert('table.tools')->rows($insertData);
		$insertId = $this->db->query($insert);
		$this->response->redirect($url."#tools");
	}else if($action=="submit_update_tools_catename"){
		$tools_cateid = isset($_POST['tools_cateid']) ? addslashes(trim($_POST['tools_cateid'])) : '';
		$tools_catename = isset($_POST['tools_catename']) ? addslashes(trim($_POST['tools_catename'])) : '';
		if($tools_catename){
			$update = $this->db->update('table.tools_cate')->rows(array('tools_catename'=>$tools_catename))->where('tools_cateid=?',$tools_cateid);
			$updateRows= $this->db->query($update);
		}
		$this->response->redirect($url."#cate");
	}else if($action=="submit_update_tools"){
		$tools_id = isset($_POST['tools_id']) ? addslashes(trim($_POST['tools_id'])) : '';
		$tools_cid = isset($_POST['tools_cid']) ? addslashes(trim($_POST['tools_cid'])) : '';
		if($tools_cid){
			$update = $this->db->update('table.tools')->rows(array('tools_cid'=>$tools_cid))->where('tools_id=?',$tools_id);
			$updateRows= $this->db->query($update);
		}
		$this->response->redirect($url."#tools");
	}
	$goto = isset($_GET['goto']) ? addslashes(trim($_GET['goto'])) : '';
	if($goto=="del_tools_cate"){
		$tools_cateid = isset($_GET['tools_cateid']) ? addslashes(trim($_GET['tools_cateid'])) : '';
		$delete = $this->db->delete('table.tools_cate')->where('tools_cateid = ?', $tools_cateid);
		$deletedRows = $this->db->query($delete);
		$this->response->redirect($url."#cate");
	}else if($goto=="del_tools"){
		$tools_id = isset($_GET['tools_id']) ? addslashes(trim($_GET['tools_id'])) : '';
		$delete = $this->db->delete('table.tools')->where('tools_id = ?', $tools_id);
		$deletedRows = $this->db->query($delete);
		$this->response->redirect($url."#tools");
	}
?>
<div class="mdui-tab mdui-tab-centered mdui-color-deep-purple-accent" mdui-tab>
  <a href="#cate" class="mdui-ripple">分类</a>
  <a href="#tools" class="mdui-ripple">工具</a>
  <a href="#store" id="btnStore" class="mdui-ripple">市场</a>
</div>
<div id="cate" class="mdui-p-a-2">
	<form id="toolsCateForm" action="" method="post">
		<input name="action" value="submit_add_tools_catename" type="hidden"/>
		<div class="mdui-textfield">
			<input class="mdui-textfield-input" id="tools_catename" name="tools_catename" type="text" placeholder="分类名称"/>
			<button class="mdui-btn mdui-btn-block mdui-color-deep-purple-accent mdui-ripple mdui-btn-raised">添加</button>
		</div>
	</form>
	<div class="mdui-table-fluid">
		<table class="mdui-table">
			<thead>
				<tr>
					<th>ID</th>
					<th>分类</th>
					<th>操作</th>
				</tr>
			</thead>
			<tbody class="contentCate">
				<?php
				$queryCate= "select * from ".$this->db->getPrefix()."tools_cate";
				$pageCate_now = isset($_GET['pageCate_now']) ? intval($_GET['pageCate_now']) : 1;
				if($pageCate_now<1){
					$pageCate_now=1;
				}
				$resultCateTotal = $this->db->fetchAll($queryCate);
				$pageCate_rec=10;
				$totalCaterec=count($resultCateTotal);
				$pageCate=ceil($totalCaterec/$pageCate_rec);
				if($pageCate_now>$pageCate){
					$pageCate_now=$pageCate;
				}
				if($pageCate_now<=1){
					$beforeCate_page=1;
					if($pageCate>1){
						$afterCate_page=$pageCate_now+1;
					}else{
						$afterCate_page=1;
					}
				}else{
					$beforeCate_page=$pageCate_now-1;
					if($pageCate_now<$pageCate){
						$afterCate_page=$pageCate_now+1;
					}else{
						$afterCate_page=$pageCate;
					}
				}
				$i=($pageCate_now-1)*$pageCate_rec<0?0:($pageCate_now-1)*$pageCate_rec;
				$queryCate= "select * from ".$this->db->getPrefix()."tools_cate limit ".$i.",".$pageCate_rec;
				$resultCate = $this->db->fetchAll($queryCate);
				foreach($resultCate as $value){
				?>
				<form id="updateCateForm<?=$value["tools_cateid"];?>" method="post" action="">
					<input type="hidden" name="action" value="submit_update_tools_catename" />
					<input type="hidden" name="tools_cateid" value="<?=$value["tools_cateid"];?>" />
					<tr class="itemCate">
						<td><?=$value["tools_cateid"];?></td>
						<td><input class="mdui-textfield-input" type="text" name="tools_catename" value="<?=$value["tools_catename"];?>" placeholder="分类名称"/></td>
						<td>
							<a id="btnUpdateCate<?=$value["tools_cateid"];?>" class="btnUpdateCate" href="javascript:;">修改</a>
							<a href="<?=$url;?>?goto=del_tools_cate&tools_cateid=<?=$value["tools_cateid"];?>">删除</a>
						</td>
					</tr>
				</form>
				<?php
				}
				?>
			</tbody>
		</table>
		<?php if(count($resultCate)>0){?>
		<!--ajax分页加载-->
		<ul class="am-paginationCate blog-pagination" style="float:right;margin-right:20px;">
		  <?php if($pageCate_now!=1){?>
			<li class="am-pagination-prev" style="float:left;"><a href="?pageCate_now=1#cate">首页&nbsp;&nbsp;</a></li>
		  <?php }?>
		  <?php if($pageCate_now>1){?>
			<li class="am-pagination-prev" style="float:left;"><a href="?pageCate_now=<?=$beforeCate_page;?>#cate">&laquo;上一页</a></li>
		  <?php }?>
		  <?php if($pageCate_now<$pageCate){?>
			<li class="am-pagination-next" style="float:left;"><a class="next" href="?pageCate_now=<?=$afterCate_page;?>#cate">下一页&raquo;</a></li>
		  <?php }?>
		  <?php if($pageCate_now!=$pageCate){?>
			<li class="am-pagination-next" style="float:left;"><a href="?pageCate_now=<?=$pageCate;?>#cate">&nbsp;&nbsp;尾页</a></li>
		  <?php }?>
		</ul>
		<!--
		<script src="<?=$plug_url;?>/TleTools/js/jquery.ias.min.js" type="text/javascript"></script>
		<script>
		var ias = $.ias({
			container: ".contentCate", /*包含所有文章的元素*/
			item: ".itemCate", /*文章元素*/
			pagination: ".am-paginationCate", /*分页元素*/
			next: ".am-paginationCate a.next", /*下一页元素*/
		});
		ias.extension(new IASTriggerExtension({
			text: '<div class="cat-nav am-round"><small></small></div>', /*此选项为需要点击时的文字*/
			offset: false, /*设置此项后，到 offset+1 页之后需要手动点击才能加载，取消此项则一直为无限加载*/
		}));
		ias.extension(new IASSpinnerExtension());
		ias.extension(new IASNoneLeftExtension({
			text: '<div class="cat-nav am-round"><small></small></div>', /*加载完成时的提示*/
		}));
		</script>
		-->
		<?php }?>
	</div>
</div>
<div id="tools" class="mdui-p-a-2">
	<form id="toolsForm" action="" method="post">
		<input name="action" value="submit_add_tools" type="hidden"/>
		<div class="mdui-textfield">
			<select id="tools_cid" name="tools_cid" class="mdui-select">
				<option value="">请选择分类</option>
				<?php foreach($resultCateTotal as $value){?>
				<option value="<?=$value["tools_cateid"];?>"><?=$value["tools_catename"];?></option>
				<?php }?>
			</select>
			<input class="mdui-textfield-input" id="tools_dir" name="tools_dir" type="text" placeholder="工具所在文件夹名称"/>
			<input class="mdui-textfield-input" id="tools_name" name="tools_name" type="text" placeholder="工具名称"/>
			<button class="mdui-btn mdui-btn-block mdui-color-deep-purple-accent mdui-ripple mdui-btn-raised">添加</button>
		</div>
	</form>
	<div class="mdui-table-fluid">
		<table class="mdui-table">
			<thead>
				<tr>
					<th>ID</th>
					<th>分类</th>
					<th>工具</th>
					<th>位置</th>
					<th>操作</th>
				</tr>
			</thead>
			<tbody class="contentTools">
				<?php
				$query= "select * from ".$this->db->getPrefix()."tools as t,".$this->db->getPrefix()."tools_cate as tc WHERE tools_cateid=tools_cid";
				$page_now = isset($_GET['page_now']) ? intval($_GET['page_now']) : 1;
				if($page_now<1){
					$page_now=1;
				}
				$resultTotal = $this->db->fetchAll($query);
				$page_rec=10;
				$totalrec=count($resultTotal);
				$page=ceil($totalrec/$page_rec);
				if($page_now>$page){
					$page_now=$page;
				}
				if($page_now<=1){
					$before_page=1;
					if($page>1){
						$after_page=$page_now+1;
					}else{
						$after_page=1;
					}
				}else{
					$before_page=$page_now-1;
					if($page_now<$page){
						$after_page=$page_now+1;
					}else{
						$after_page=$page;
					}
				}
				$i=($page_now-1)*$page_rec<0?0:($page_now-1)*$page_rec;
				$query= "select * from ".$this->db->getPrefix()."tools,".$this->db->getPrefix()."tools_cate as tc WHERE tools_cateid=tools_cid limit ".$i.",".$page_rec;
				$result = $this->db->fetchAll($query);
				foreach($result as $value){
				?>
				<form id="updateToolForm<?=$value["tools_id"];?>" method="post" action="">
					<input type="hidden" name="action" value="submit_update_tools" />
					<input type="hidden" name="tools_id" value="<?=$value["tools_id"];?>" />
					<tr class="itemTools">
						<td><?=$value["tools_id"];?></td>
						<td>
							<select name="tools_cid" class="mdui-select">
							  <?php foreach($resultCateTotal as $val){?>
							  <option value="<?=$val["tools_cateid"];?>" <?php if($val["tools_cateid"]==$value["tools_cid"]){?>selected<?php }?>><?=$val["tools_catename"];?></option>
							  <?php }?>
							</select>
						</td>
						<td><?=$value["tools_name"];?></td>
						<td><?=$value["tools_dir"];?></td>
						<td>
							<a id="btnUpdateTool<?=$value["tools_id"];?>" class="btnUpdateTool" href="javascript:;">修改</a>
							<a href="<?=$url;?>?goto=del_tools&tools_id=<?=$value["tools_id"];?>">删除</a>
						</td>
					</tr>
				</form>
				<?php
				}
				?>
			</tbody>
		</table>
		<?php if(count($result)>0){?>
		<ul class="am-paginationTools blog-pagination" style="float:right;margin-right:20px;">
		  <?php if($page_now!=1){?>
			<li class="am-pagination-prev" style="float:left;"><a href="?page_now=1#tools">首页&nbsp;&nbsp;</a></li>
		  <?php }?>
		  <?php if($page_now>1){?>
			<li class="am-pagination-prev" style="float:left;"><a href="?page_now=<?=$before_page;?>#tools">&laquo;上一页</a></li>
		  <?php }?>
		  <?php if($page_now<$page){?>
			<li class="am-pagination-next" style="float:left;"><a class="next" href="?page_now=<?=$after_page;?>#tools">下一页&raquo;</a></li>
		  <?php }?>
		  <?php if($page_now!=$page){?>
			<li class="am-pagination-next" style="float:left;"><a href="?page_now=<?=$page;?>#tools">&nbsp;&nbsp;尾页</a></li>
		  <?php }?>
		</ul>
		<?php }?>
	</div>
</div>
<div id="store" class="mdui-p-a-2">
	<form method="get" action="">
		<div class="mdui-textfield">
		  <i class="mdui-icon material-icons">&#xe8b6;</i>
		  <label class="mdui-textfield-label">搜索</label>
		  <input class="mdui-textfield-input" name="searchwords" type="text" placeholder="输入关键词" />
		  <div class="mdui-textfield-helper">在小工具市场中搜索</div>
		</div>
	</form>
	<div class="<?php if(!isMobileBrowse()){?>mdui-row-xs-3 mdui-row-sm-4 mdui-row-md-5 mdui-row-lg-6 mdui-row-xl-7 <?php }?>mdui-grid-list"<?php if(!isMobileBrowse()){?> style="column-count: 5;"<?php }?>>
		<?php
		$queryLocalDir= "select tools_dir from ".$this->db->getPrefix()."tools";
		$resultLocalDir = $this->db->fetchAll($queryLocalDir);
		$dirs="";
		foreach($resultLocalDir as $dir){
			$dirs.="|".$dir["tools_dir"];
		}
		$dirs=substr($dirs,1);
		$dirs=explode("|",$dirs);
		$res=getServerTools(@$_GET["searchwords"]);
		$arr=json_decode($res,true);
		
		$page_now_app = isset($_GET['page_now_app']) ? intval($_GET['page_now_app']) : 1;
		if($page_now_app<1){
			$page_now_app=1;
		}
		$page_rec=10;
		$totalrecApp=count($arr);
		$pageApp=ceil($totalrecApp/$page_rec);
		if($page_now_app>$pageApp){
			$page_now_app=$pageApp;
		}
		if($page_now_app<=1){
			$before_page_app=1;
			if($pageApp>1){
				$after_page_app=$page_now_app+1;
			}else{
				$after_page_app=1;
			}
		}else{
			$before_page_app=$page_now_app-1;
			if($page_now_app<$pageApp){
				$after_page_app=$page_now_app+1;
			}else{
				$after_page_app=$pageApp;
			}
		}
		$arr = array_slice($arr, ($page_now_app-1)*$page_rec, $page_rec);
		$key=0;
		foreach($arr as $val){
		?>
		<div class="mdui-col"<?php if(!isMobileBrowse()){?> style="break-inside: avoid;width: 100%;"<?php }?>>
			<div class="mdui-grid-list" style="margin:1px;">
				<div class="mdui-card">
				  <div class="mdui-card-header">
					<a href="http://wpa.qq.com/msgrd?v=3&uin=<?=$val["tools_authorqq"];?>&site=qq&menu=yes" target="_blank" rel="nofollow" title="<?=$val["tools_author"];?>">
						<img class="mdui-card-header-avatar" src="https://q4.qlogo.cn/headimg_dl?dst_uin=<?=$val["tools_authorqq"];?>&spec=100" alt="<?=$val["tools_author"];?>"/>
					</a>
					<div class="mdui-card-header-title"><?=$val["tools_author"];?></div>
					<div class="mdui-card-header-subtitle"><?=$val["tools_authorinfo"];?></div>
				  </div>
				  <a href="http://joke.tongleer.com/tools.html?toolpage=<?=$val["tools_dir"];?>" target="_blank">
					  <div class="mdui-card-media">
						<img src="<?=$val["tools_picture"];?>" alt="<?=$val["tools_name"];?>"/>
						<div class="mdui-card-menu">
						  <button class="mdui-btn mdui-btn-icon mdui-text-color-white"><i class="mdui-icon material-icons">share</i></button>
						</div>
					  </div>
				  </a>
				  <div class="mdui-card-primary">
					<div class="mdui-card-primary-title"><?=$val["tools_name"];?></div>
					<div class="mdui-card-primary-subtitle"><?=$val["tools_info"];?></div>
				  </div>
				  <div class="mdui-card-content"><?=$val["tools_description"];?></div>
				  <div class="mdui-card-actions">
					<a class="mdui-btn mdui-ripple" href="<?=$val["tools_link"];?>" target="_blank" rel="nofollow">来源网站</a>
					<span id="isDownloadDiv<?=$val["tools_id"];?>">
						<?php
						$outindex=@file_get_contents(dirname(__FILE__)."/../module/".$val["tools_dir"]."/index.php");
						preg_match_all('/ \* @version (.*)/',$outindex,$version);
						$localversion=end(explode('.', trim(@$version[1][0])));
						$serverversion=end(explode('.', $val["tools_version"]));
						$hasUpdate=false;
						if($val["tools_ispay"]=="y"){
							?>
							<font color="red"><?=$val["tools_price"];?>元</font>
							<?php
							if(in_array($val["tools_dir"],$dirs)){
								echo "已安装";
								if($localversion<$serverversion){
									echo '<font color="red">有更新</font>';
								}
							}
						}else{
							if(in_array($val["tools_dir"],$dirs)){
								echo "已安装";
								if($localversion<$serverversion){
									$hasUpdate=true;
								}
							}
						}
						if(($val["tools_ispay"]=="n"&&!in_array($val["tools_dir"],$dirs))||($val["tools_ispay"]=="n"&&$hasUpdate)){
							?>
							<a href="javascript:;" id="downloadTool<?=$key;?>" class="downloadTool" target="_blank" data-id="<?=$val["tools_id"];?>" data-name="<?=$val["tools_name"];?>" data-dir="<?=$val["tools_dir"];?>" data-post="<?=$plug_url;?>/TleTools/ajax/downloadTool.php" data-url="http://joke.tongleer.com/usr/plugins/TleTools/page/tools/module/<?=$val["tools_dir"];?>.zip">
							<?php if($hasUpdate){echo "有更新";}else{echo "下载";}?>
							</a>
							<?php
						}
						?>
					</span>
				  </div>
				</div>
			</div>
		</div>
		<?php
		$key++;
		}
		?>
		<div id="cateDialog" class="mdui-dialog">
			<div class="mdui-dialog-content">
			  <div class="mdui-dialog-title">绑定本地分类</div>
			  <input type="hidden" id="data-post" value="" />
			  <input type="hidden" id="data-url" value="" />
			  <input type="hidden" id="data-name" value="" />
			  <input type="hidden" id="data-dir" value="" />
			  <input type="hidden" id="data-id" value="" />
			  <select id="data-cid" class="mdui-select">
				<option value="" selected>选择一个分类</option>
				<?php
				foreach($resultCateTotal as $value){
					?>
					<option value="<?=$value["tools_cateid"];?>"><?=$value["tools_catename"];?></option>
					<?php
					}
				?>
			  </select>
			</div>
			<div class="mdui-dialog-actions">
			  <button class="mdui-btn mdui-ripple" mdui-dialog-cancel>取消</button>
			  <button class="mdui-btn mdui-ripple" mdui-dialog-confirm>继续下载</button>
			</div>
		</div>
	</div>
	<?php if(count($arr)>0){?>
	<ul class="am-paginationApp blog-pagination" style="float:right;margin-right:20px;">
	  <?php if($page_now_app!=1){?>
		<li class="am-pagination-prev" style="float:left;"><a href="?searchwords=<?=@$_GET["searchwords"];?>&page_now_app=1#store">首页&nbsp;&nbsp;</a></li>
	  <?php }?>
	  <?php if($page_now_app>1){?>
		<li class="am-pagination-prev" style="float:left;"><a href="?searchwords=<?=@$_GET["searchwords"];?>&page_now_app=<?=$before_page_app;?>#store">&laquo;上一页</a></li>
	  <?php }?>
	  <?php if($page_now_app<$pageApp){?>
		<li class="am-pagination-next" style="float:left;"><a class="next" href="?searchwords=<?=@$_GET["searchwords"];?>&page_now_app=<?=$after_page_app;?>#store">下一页&raquo;</a></li>
	  <?php }?>
	  <?php if($page_now_app!=$pageApp){?>
		<li class="am-pagination-next" style="float:left;"><a href="?searchwords=<?=@$_GET["searchwords"];?>&page_now_app=<?=$pageApp;?>#store">&nbsp;&nbsp;尾页</a></li>
	  <?php }?>
	</ul>
	<?php
	}else{
		include "404.php";
	}
	?>
</div>
<script src="//cdnjs.loli.net/ajax/libs/mdui/0.4.2/js/mdui.min.js"></script>
<script>
$(function(){
	document.getElementById('btnStore').addEventListener('click',function(){
	});
	$("#toolsCateForm").submit(function(){
		if($("#tools_catename").val()==""){
			alert("请输入分类名称");
			return false;
		}
	});
	$("#toolsForm").submit(function(){
		if($("#tools_name").val()==""||$("#tools_dir").val()==""||$("#tools_cid").val()==""){
			alert("请填写完整");
			return false;
		}
	});
	
	$(".btnUpdateCate").each(function(){
		var id=$(this).attr("id");
		$("#"+id).click( function () {
			var updateCateFormId=id.replace("btnUpdateCate","");
			$("#updateCateForm"+updateCateFormId).submit();
		});
	});
	$(".btnUpdateTool").each(function(){
		var id=$(this).attr("id");
		$("#"+id).click( function () {
			var updateToolFormId=id.replace("btnUpdateTool","");
			$("#updateToolForm"+updateToolFormId).submit();
		});
	});
	
	$(".downloadTool").each(function(){
		var id=$(this).attr("id");
		$("#"+id).click( function () {
			cateinit.open();
			$("#data-post").val($(this).attr("data-post"));
			$("#data-url").val($(this).attr("data-url"));
			$("#data-name").val($(this).attr("data-name"));
			$("#data-dir").val($(this).attr("data-dir"));
			$("#data-id").val($(this).attr("data-id"));
		});
	});
	var cateinit = new mdui.Dialog("#cateDialog");
	var cateDialog=document.getElementById("cateDialog");
	cateDialog.addEventListener("confirm.mdui.dialog", function () {
		if($("#data-cid").val()==""){
			alert("需要先选择一个本地分类进行绑定");
			return;
		}
		$.post($("#data-post").val(),{action:"downloadTool",cid:$("#data-cid").val(),name:$("#data-name").val(),dir:$("#data-dir").val(),url:$("#data-url").val()},function(data){
			if(data==1){
				$("#isDownloadDiv"+$("#data-id").val()).html("已安装");
			}else if(data==0){
				alert("该工具包已损坏");
			}else{
				alert("安装未完成");
			}
		});
	});
});
</script>