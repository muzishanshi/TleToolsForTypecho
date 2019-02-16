<?php
/**
 * TleTools - Typecho同乐工具集插件，一个可添加本地、网络工具，并支持开发者提交收费、免费工具到工具市场和设置首页后“相当于”模板使用，随意从工具和分类中切换的工具集插件。
 * @package TleTools For Typecho
 * @author 二呆
 * @version 1.0.1
 * @link http://www.tongleer.com/
 * @date 2018-12-24
 */
require_once("libs/PluginOption.php");
require_once("libs/ZipFolder.php");

class TleTools_Plugin implements Typecho_Plugin_Interface{
    // 激活插件
    public static function activate(){
		$db = Typecho_Db::get();
		//创建数据表
		self::createTableToolsCate($db);
		self::createTableTools($db);
		//判断目录权限，并将插件文件写入主题目录
		self::funWriteThemePage($db,'page_tletools.php');
		self::funWriteThemePage($db,'tools/config/function.php');
		self::funWriteThemePage($db,'tools/include/admin.php');
		self::funWriteThemePage($db,'tools/include/404.php');
		self::funWriteThemePage($db,'tools/include/header.php');
		self::funWriteThemePage($db,'tools/include/footer.php');
		//如果数据表没有添加页面就插入
		self::funWriteDataPage($db,'同乐工具集','tletools','page_tletools.php','publish');
		unset($db);
        return _t('插件已经激活，需先配置插件信息！');
    }

    // 禁用插件
    public static function deactivate(){
		//删除页面模板
		$db = Typecho_Db::get();
		$queryTheme= $db->select('value')->from('table.options')->where('name = ?', 'theme'); 
		$rowTheme = $db->fetchRow($queryTheme);
		@unlink(dirname(__FILE__).'/../../themes/'.$rowTheme['value'].'/page_tletools.php');
		@unlink(dirname(__FILE__).'/../../themes/'.$rowTheme['value'].'/tools/config/function.php');
		@unlink(dirname(__FILE__).'/../../themes/'.$rowTheme['value'].'/tools/include/admin.php');
		@unlink(dirname(__FILE__).'/../../themes/'.$rowTheme['value'].'/tools/include/404.php');
		@unlink(dirname(__FILE__).'/../../themes/'.$rowTheme['value'].'/tools/include/header.php');
		@unlink(dirname(__FILE__).'/../../themes/'.$rowTheme['value'].'/tools/include/footer.php');
		unset($db);
        return _t('插件已被禁用');
    }

    // 插件配置面板
    public static function config(Typecho_Widget_Helper_Form $form){
		$db = Typecho_Db::get();
		$tools = new pluginOptions;
		
		echo "<style>
		body{background-color:#F5F5F5}@media screen and (min-device-width:1024px){::-webkit-scrollbar-track{background-color:rgba(255,255,255,0)}::-webkit-scrollbar{width:6px;background-color:rgba(255,255,255,0)}::-webkit-scrollbar-thumb{border-radius:3px;background-color:rgba(193,193,193,1)}}.typecho-head-nav{}#typecho-nav-list .focus .parent a,#typecho-nav-list .parent a:hover,#typecho-nav-list .root:hover .parent a{background:RGBA(255,255,255,0);}#typecho-nav-list{display:block}.typecho-head-nav .operate a{border:0;color:rgba(255,255,255,.6)}.typecho-head-nav .operate a:focus,.typecho-head-nav .operate a:hover{color:rgba(255,255,255,.8);background-color:#673AB7;outline:0}.body.container{min-width:100%!important;padding:0}.row{margin:0}.col-mb-12{padding:0!important}.typecho-page-title{height:100px;padding:10px 40px 20px 40px;background-color:#673AB7;color:#FFF;font-size:24px}.typecho-option-tabs{padding:0;margin:0;height:60px;background-color:#512DA8;margin-bottom:40px!important;padding-left:25px}.typecho-option-tabs li{margin:0;border:none;float:left;position:relative;display:block;text-align:center;font-weight:500;font-size:14px;text-transform:uppercase}.typecho-option-tabs a{height:auto;border:0;color:rgba(255,255,255,.6);background-color:rgba(255,255,255,0)!important;padding:17px 24px}.typecho-option-tabs a:hover{color:rgba(255,255,255,.8)}.message{background-color:#673AB7!important;color:#fff}.success{background-color:#673AB7;color:#fff}.current{background-color:#FFF;height:4px;padding:0!important;bottom:0}.current a{color:#FFF}input[type=text],textarea{border:none;border-bottom:1px solid rgba(0,0,0,.6);outline:0;border-radius:0}.typecho-option span{margin-right:0}.typecho-option-submit{position:fixed;right:32px;bottom:32px}.typecho-option-submit button{float:right;background:#00BCD4;box-shadow:0 2px 2px 0 rgba(0,0,0,.14),0 3px 1px -2px rgba(0,0,0,.2),0 1px 5px 0 rgba(0,0,0,.12);color:#FFF}.typecho-page-main .typecho-option textarea{height:149px}.typecho-option label.typecho-label{font-weight:500;margin-bottom:20px;margin-top:10px;font-size:16px;padding-bottom:5px;border-bottom:1px solid rgba(0,0,0,.2)}#use-intro{box-shadow:0 2px 2px 0 rgba(0,0,0,.14),0 3px 1px -2px rgba(0,0,0,.2),0 1px 5px 0 rgba(0,0,0,.12);background-color:#fff;margin:8px;padding:8px;padding-left:20px;margin-bottom:40px}.typecho-foot{padding:16px 40px;color:#9e9e9e;margin-top:80px}button,form{display:none}
		</style>";

		echo '<link rel="stylesheet" href="//cdnjs.loli.net/ajax/libs/mdui/0.4.2/css/mdui.min.css">' . 
		'<script src="//cdnjs.loli.net/ajax/libs/mdui/0.4.2/js/mdui.min.js"></script>';
		echo "<script>mdui.JQ(function () { mdui.JQ('form').eq(0).attr('action', mdui.JQ('form').eq(1).attr('action')); });</script>";

		echo '<form action="" method="post" enctype="application/x-www-form-urlencoded" style="display: block!important">
		<div class="mdui-panel" mdui-panel>
		  <div class="mdui-panel-item mdui-panel-item-open">
			<div class="mdui-panel-item-header">介绍</div>
			<div class="mdui-panel-item-body">';

		$version=file_get_contents('https://www.tongleer.com/api/interface/TleTools.php?action=update&version=1');
		echo '<p style="font-size:14px;">
			<span style="display: block; margin-bottom: 10px; margin-top: 10px; font-size: 16px;">感谢您使用TleTools插件</span>
			<span style="margin-bottom:10px;display:block">请关注 <a href="http://www.tongleer.com" target="_blank" style="color:#3384da;font-weight:bold;text-decoration:underline">同乐儿</a> 公众号以获得<span style="color:#df3827;font-weight:bold;">最新版本支持</span></span>
			<a href="http://doc.tongleer.com" >帮助支持&开发文档</a> &nbsp;
			<a href="http://mail.qq.com/cgi-bin/qm_share?t=qm_mailme&email=diamond0422@qq.com" target="_blank">建议反馈&工具提交(含收费和免费工具提交)</a> &nbsp;
			<a href="http://club.tongleer.com" >交流论坛</a> &nbsp;<br><br>';
		echo '版本检查：'.$version;
		echo '</p></div></div>';
		
		$tools->radio("初始化数据",array("y"=>"是","n"=>"否"),"inittools","是否初始化工具到数据库");
		
		$tools->textarea("友情链接","friendlinks","用于在页面底部添加友情链接。");
		
		$tools->textarea("网络工具","nettools","1、用于前台展示来自网络的小工具，需要按格式输入网络工具的各项信息；<br />2、本地工具配置请前往<font color='red'>独立页面</font>进行设置。");
		
		echo '</div>
			<button class="mdui-btn mdui-btn-raised mdui-ripple mdui-color-deep-purple-accent">保存</button>
		</form>';
		
		$inittools = new Typecho_Widget_Helper_Form_Element_Radio('inittools', array(
            'y'=>_t('是'),
            'n'=>_t('否')
        ), 'n', _t('初始化数据'), _t("是否初始化工具到数据库，注：其中作者信息请勿修改）"));
        $form->addInput($inittools->addRule('enum', _t(''), array('y', 'n')));
		
		$friendlinksvalue='
			[{
				"name":"同乐儿",
				"link":"http://www.tongleer.com",
				"qq":"2293338477"
			}]
		';
		$friendlinks = new Typecho_Widget_Helper_Form_Element_Textarea('friendlinks', array("value"), $friendlinksvalue, _t('友情链接'));
        $form->addInput($friendlinks);
		
		$nettoolsvalue='
			[{
				"name":"帮你百度",
				"link":"http://api.tongleer.com/baidu/"
			},{
				"name":"百度语音",
				"link":"https://developer.baidu.com/vcast"
			},
			{
				"name":"微博图床",
				"link":"http://joke.tongleer.com/images.html"
			},
			{
				"name":"万年历",
				"link":"http://www.tongleer.com/calendar.html"
			},
			{
				"name":"字数统计",
				"link":"http://www.tongleer.com/countword.html"
			},
			{
				"name":"PhotoShop",
				"link":"http://www.tongleer.com/api/weixin/ps.html"
			},
			{
				"name":"音乐搜索器",
				"link":"http://api.tongleer.com/music/"
			}]
		';
		$nettools = new Typecho_Widget_Helper_Form_Element_Textarea('nettools', array("value"), $nettoolsvalue, _t('网络工具'));
        $form->addInput($nettools);
		
		$inittools = @isset($_POST['inittools']) ? addslashes(trim($_POST['inittools'])) : '';
		if($inittools=='y'){
			//解压并初始化数据（请勿修改作者信息）
			self::funWriteDataTools($db,"生活常用","weather","天气预报","二呆","http://www.tongleer.com","查询当天及未来天气的基本情况，以及查询支持的城市列表。提供天气信息包括：当前时间、当前气温、当前湿度，天气情况、污染指数、风向、风速、日出日落时间以及未来十天天气状况等。","https://ws3.sinaimg.cn/large/ecabade5ly1fy5gjqhzs0j2028028wea.jpg","天气早知道","diamond@tongleer.com","2293338477","一个好人","1.0.1");
			self::funWriteDataTools($db,"生活常用","phone","手机号查询","二呆","http://www.tongleer.com","仅支持国内的手机号码查询，具体城市可根据支持的城市列表API来查询。可以通过城市名称进行查询。","https://ws3.sinaimg.cn/large/ecabade5ly1fy5glh527zj2028028gle.jpg","一眼辨识归属地","diamond@tongleer.com","2293338477","一个好人","1.0.1");
			self::funWriteDataTools($db,"生活常用","postcode","邮编查询","二呆","http://www.tongleer.com","邮编查地名，地名查邮编，随心所欲。","https://ws3.sinaimg.cn/large/ecabade5ly1fy6a9kg87wj2028028mwz.jpg","邮编轻松查","diamond@tongleer.com","2293338477","一个好人","1.0.1");
			self::funWriteDataTools($db,"生活常用","ip","IP地址","二呆","http://www.tongleer.com","根据IP地址查询对应的省市区信息","https://ws3.sinaimg.cn/large/ecabade5ly1fy6ki2rf0tj2028028dfm.jpg","IP地址随心查","diamond@tongleer.com","2293338477","一个好人","1.0.1");
			self::funWriteDataTools($db,"生活常用","idcard","身份证查询","二呆","http://www.tongleer.com","查询身份证的基本信息，并非身份证鉴别。","https://ws3.sinaimg.cn/large/ecabade5ly1fy77lgbzpwj2028028q2q.jpg","简单的身份证查询工具","diamond@tongleer.com","2293338477","一个好人","1.0.1");
			self::funWriteDataTools($db,"站长工具","icpweight","网站备案权重查询","二呆","http://www.tongleer.com","基于爱站的网站备案及权重查询方式。","https://ws3.sinaimg.cn/large/ecabade5ly1fy7e95qm8hj202i02ijrc.jpg","备案权重一起查才方便","diamond@tongleer.com","2293338477","一个好人","1.0.1");
			self::funWriteDataTools($db,"站长工具","qrcode","二维码扫描生成器","二呆","http://www.tongleer.com","二维码扫描生成器是一款简单实用的扫描解析二维码以及生成二维码的小工具。","https://ws3.sinaimg.cn/large/ecabade5ly1fy7n4evhfmj20sg0sgaa6.jpg","简约实用的二维码扫描生成工具","diamond@tongleer.com","2293338477","一个好人","1.0.1");
			self::funWriteDataTools($db,"站长工具","stqconvert","字体转换","二呆","http://www.tongleer.com","可以在简体、繁体、火星文之间相互转换的工具","https://ws3.sinaimg.cn/large/ecabade5ly1fy7ue8vabaj20e80e8aa8.jpg","简体、繁体、火星文转换","diamond@tongleer.com","2293338477","一个好人","1.0.1");
			self::funWriteDataTools($db,"站长工具","convutf8","UTF8转中文","二呆","http://www.tongleer.com","可以在UTF8、中文之间相互转换的工具","https://ws3.sinaimg.cn/large/ecabade5ly1fy7uldjre9j20e80e8wf5.jpg","UTF8和中文互相转换","diamond@tongleer.com","2293338477","一个好人","1.0.1");
			self::funWriteDataTools($db,"站长工具","html2js","html转js","二呆","http://www.tongleer.com","可以在html、js之间相互转换的工具","https://ws3.sinaimg.cn/large/ecabade5ly1fy7ut7d5egj20e80e8dft.jpg","html和js互相转换","diamond@tongleer.com","2293338477","一个好人","1.0.1");
		}
    }

    // 个人用户配置面板
    public static function personalConfig(Typecho_Widget_Helper_Form $form){
    }

    // 获得插件配置信息
    public static function getConfig(){
        return Typecho_Widget::widget('Widget_Options')->plugin('TleTools');
    }
	
	/*公共方法：将页面写入数据库*/
	public static function funWriteDataPage($db,$title,$slug,$template,$status="hidden"){
		date_default_timezone_set('Asia/Shanghai');
		$query= $db->select('slug')->from('table.contents')->where('template = ?', $template); 
		$row = $db->fetchRow($query);
		if(count($row)==0){
			$contents = array(
				'title'      =>  $title,
				'slug'      =>  $slug,
				'created'   =>  time(),
				'text'=>  '<!--markdown-->',
				'password'  =>  '',
				'authorId'     =>  Typecho_Cookie::get('__typecho_uid'),
				'template'     =>  $template,
				'type'     =>  'page',
				'status'     =>  $status,
			);
			$insert = $db->insert('table.contents')->rows($contents);
			$insertId = $db->query($insert);
			$slug=$contents['slug'];
		}else{
			$slug=$row['slug'];
		}
	}
	
	/*公共方法：将页面写入主题目录*/
	public static function funWriteThemePage($db,$filename){
		$queryTheme= $db->select('value')->from('table.options')->where('name = ?', 'theme'); 
		$rowTheme = $db->fetchRow($queryTheme);
		$path=dirname(__FILE__).'/../../themes/'.$rowTheme['value'];
		if(!is_dir($path."/tools/config/")){mkdir ($path."/tools/config/", 0777, true );}
		if(!is_dir($path."/tools/include/")){mkdir ($path."/tools/include/", 0777, true );}
		if(!is_writable($path)){
			Typecho_Widget::widget('Widget_Notice')->set(_t('主题目录不可写，请更改目录权限。'.__TYPECHO_THEME_DIR__.'/'.$rowTheme['value']), 'success');
		}
		if(!file_exists($path."/".$filename)){
			$regfile = @fopen(dirname(__FILE__)."/page/".$filename, "r") or die("不能读取".$filename."文件");
			$regtext=fread($regfile,filesize(dirname(__FILE__)."/page/".$filename));
			fclose($regfile);
			$regpage = fopen($path."/".$filename, "w") or die("不能写入".$filename."文件");
			fwrite($regpage, $regtext);
			fclose($regpage);
		}
	}
	
	/*创建工具分类数据表*/
	public static function createTableToolsCate($db){
		$prefix = $db->getPrefix();
		$db->query('CREATE TABLE IF NOT EXISTS `'.$prefix.'tools_cate` (
		  `tools_cateid` bigint(20) NOT NULL AUTO_INCREMENT,
		  `tools_catename` varchar(20) DEFAULT NULL COMMENT "分类名称",
		  PRIMARY KEY (`tools_cateid`)
		) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;');
	}
	
	/*创建工具数据表*/
	public static function createTableTools($db){
		$prefix = $db->getPrefix();
		$db->query('CREATE TABLE IF NOT EXISTS `'.$prefix.'tools` (
		  `tools_id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `tools_cid` bigint(20) DEFAULT NULL,
		  `tools_name` varchar(64) DEFAULT NULL,
		  `tools_dir` varchar(32) DEFAULT NULL,
		  `tools_author` varchar(20) DEFAULT NULL,
		  `tools_link` varchar(255) DEFAULT NULL,
		  `tools_description` varchar(255) DEFAULT NULL,
		  `tools_picture` varchar(255) DEFAULT NULL,
		  `tools_ispay` enum("y","n") DEFAULT "n",
		  `tools_price` int(11) DEFAULT 0,
		  `tools_info` varchar(32) DEFAULT NULL,
		  `tools_authoremail` varchar(32) DEFAULT NULL,
		  `tools_authorqq` bigint(20) DEFAULT NULL,
		  `tools_authorinfo` varchar(32) DEFAULT "n",
		  `tools_version` varchar(20) DEFAULT 0,
		  PRIMARY KEY (`tools_id`)
		) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;');
	}
	
	/*输出友情链接*/
	public static function printLinks(){
		$options = Typecho_Widget::widget('Widget_Options');
		$option=$options->plugin('TleTools');
		$links="";
		$friendlinks=json_decode($option->friendlinks,true);
		if(count($friendlinks)>0&&$friendlinks[0]["name"]!=""){
			$links.='<div><marquee direction="up" behavior="scroll" scrollamount="1" scrolldelay="10" loop="-1" onMouseOver="this.stop()" onMouseOut="this.start()" width="100%" height="30" style="text-align:center;">友情链接：';
			foreach($friendlinks as $value){
				$qq=$value["qq"]!=""?$value["qq"]:"0";
				$links.='<a href=javascript:open("https://wpa.qq.com/msgrd?v=3&uin='.$qq.'&site=qq&menu=yes");><img src="https://q1.qlogo.cn/g?b=qq&nk='.$qq.'&s=100" width="16" /></a><a href="'.$value["link"].'" target="_blank" title="'.$value["name"].'" rel="friend">'.$value["name"].'</a>&nbsp;';
			}
			$links.='</marquee></div>';
		}
		echo $links;
	}
	
	/*解压并初始化数据*/
	public static function funWriteDataTools($db,$tools_catename,$tools_dir,$tools_name,$tools_author,$tools_link,$tools_description,$tools_picture,$tools_info,$tools_authoremail,$tools_authorqq,$tools_authorinfo,$tools_version){
		$prefix = $db->getPrefix();
		$queryTheme= $db->select('value')->from('table.options')->where('name = ?', 'theme'); 
		$rowTheme = $db->fetchRow($queryTheme);
		$zip = new ZipFolder;
		$zip->unzip(dirname(__FILE__)."/page/tools/module/".$tools_dir.".zip",dirname(__FILE__)."/../../themes/".$rowTheme['value']."/tools/module/");
		$queryCate= $db->select()->from('table.tools_cate')->where("tools_catename = ?",$tools_catename); 
		$rowCate = $db->fetchRow($queryCate);
		if(count($rowCate)==0){
			$insertCate=array(
				'tools_catename' => $tools_catename
			);
			$insert = $db->insert('table.tools_cate')->rows($insertCate);
			$cateId = $db->query($insert);
		}else{
			$cateId = $rowCate["tools_cateid"];
		}
		$insertTool=array(
			'tools_cid' => $cateId,
			'tools_name' => $tools_name,
			'tools_dir' => $tools_dir,
			'tools_author' => $tools_author,
			'tools_link' => $tools_link,
			'tools_description' => $tools_description,
			'tools_picture' => $tools_picture,
			'tools_info' => $tools_info,
			'tools_authoremail' => $tools_authoremail,
			'tools_authorqq' => $tools_authorqq,
			'tools_authorinfo' => $tools_authorinfo,
			'tools_version' => $tools_version
		);
		$insert = $db->insert('table.tools')->rows($insertTool);
		$insertToolId = $db->query($insert);
	}
}