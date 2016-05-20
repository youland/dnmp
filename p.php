<?php
$runtime= new runtime;
$runtime->start();
class runtime
{ 
    var $StartTime = 0; 
    var $StopTime = 0; 
    function get_microtime() 
    { 
        list($usec, $sec) = explode(' ', microtime()); 
        return ((float)$usec + (float)$sec); 
    } 
    function start() 
    { 
        $this->StartTime = $this->get_microtime(); 
    } 
    function stop() 
    { 
        $this->StopTime = $this->get_microtime(); 
    } 
    function spent() 
    { 
        return round(($this->StopTime - $this->StartTime) * 1000, 1); 
    } 
}

	header("content-Type: text/html; charset=utf-8");
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
	ob_start();
     
    $mysqlReShow = "none";
    $mailReShow = "none";
    $funReShow = "none";
    $opReShow = "none";
    $sysReShow = "none";
     
    define("YES", "<span class='resYes'>YES</span>");
    define("NO", "<span class='resNo'>NO</span>");
    define("ICON", "<span class='icon'>2</span>&nbsp;");
    $phpSelf = $_SERVER[PHP_SELF] ? $_SERVER[PHP_SELF] : $_SERVER[SCRIPT_NAME];
    define("PHPSELF", preg_replace("/(.{0,}?\/+)/", "", $phpSelf));
     
    if ($_GET['act'] == "phpinfo")
    {
        phpinfo();
        exit();
    }
    elseif($_GET['act'] == "Function")
    {
$arr = get_defined_functions();
Function php() {
}
echo "<pre>";
Echo "这里显示系统所支持的所有函数,和自定义函数\n";
print_r($arr);
echo "</pre>";
        exit();
    }
    elseif($_POST['act'] == "CONNECT")
    {
        $mysqlReShow = "show";
        $mysqlRe = "MYSQL连接测试结果：";
        $mysqlRe .= (false !== @mysql_connect($_POST['mysqlHost'], $_POST['mysqlUser'], $_POST['mysqlPassword']))?"MYSQL服务器连接正常, ":
        "MYSQL服务器连接失败, ";
        $mysqlRe .= "数据库 <b>".$_POST['mysqlDb']."</b> ";
        $mysqlRe .= (false != @mysql_select_db($_POST['mysqlDb']))?"连接正常":
        "连接失败";
    }
    elseif($_POST['act'] == "SENDMAIL")
    {
        $mailReShow = "show";
        $mailRe = "MAIL邮件发送测试结果：发送";
        $mailRe .= (false !== @mail($_POST["mailReceiver"], "MAIL SERVER TEST", "This is a test email."))?"完成":"失败";
    }
    elseif($_POST['act'] == "FUNCTION_CHECK")
    {
        $funReShow = "show";
        $funRe = "函数 <b>".$_POST['funName']."</b> 支持状况检测结果：".isfun($_POST['funName']);
    }
    elseif($_POST['act'] == "CONFIGURATION_CHECK")
    {
        $opReShow = "show";
        $opRe = "配置参数 <b>".$_POST['opName']."</b> 检测结果：".getcon($_POST['opName']);
    }
     
     
    // 系统参数
     
     
    switch (PHP_OS)
    {
        case "Linux":
        $sysReShow = (false !== ($sysInfo = sys_linux()))?"show":"none";
        break;
        default:
        break;
    }
     
/*========================================================================*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PHP探针</title>
<style type="text/css">
<!--
body, div, p, ul, form, h1 {
	margin:0px;
	padding:0px;
}
body {
	background:#252724;
}
div, a, input {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	color:#7D795E;
}
div {
	margin-left:auto;
	margin-right:auto;
	width:720px;
}
input {
	border: 1px solid #414340;
	background:#353734;
}
a, #t3 a.arrow, #f1 a.arrow {
	text-decoration:none;
	color:#978F78;
}
a:hover {
	text-decoration:underline;
}
a.arrow {
	font-family:Webdings, sans-serif;
	color:#343525;
	font-size:10px;
}
a.arrow:hover {
	color:#ff0000;
	text-decoration:none;
}
.resYes {
	font-size: 9px;
	font-weight: bold;
	color: #33CC00;
}
.resNo {
	font-size: 9px;
	font-weight: bold;
	color: #CC3300;
}
.myButton {
	font-size:10px;
	font-weight:bold;
	background:#3D3F3E;
	border:1px solid #4A4C49;
	border-right-color:#2D2F2C;
	border-bottom-color:#2D2F2C;
	color:#978F78;
}
.bar {
	border:1px solid #2D2F2C;
	background:#6C6754;
	height:8px;
	font-size:2px;
}
.bar li {
	background:#979179;
	height:8px;
	font-size:2px;
	list-style-type:none;
}
table {
	clear:both;
	background:#2D2F2C;
	border:3px solid #41433E;
	margin-bottom:10px;
}
td, th {
	padding:4px;
	background:#363835;
}
th {
	background:#7E7860;
	color:#343525;
	text-align:left;
}
th span {
	font-family:Webdings, sans-serif;
	font-weight:normal;
	padding-right:4px;
}
th p {
	float:right;
	line-height:10px;
	text-align:right;
}
th a {
	color:#343525;
}
h1 {
	color:#009900;
	font-size:35px;
	width:300px;
	float:left;
}
h1 b {
	color:#cc3300;
	font-size:50px;
	font-family: Webdings, sans-serif;
	font-weight:normal;
}
h1 span {
	font-size:10px;
	padding-left:10px;
	color:#7D795E;
}
#t12 {
	float:right;
	text-align:right;
	padding:15px 0px 30px 0px;
}
#t12 a {
	line-height:18px;
	color:#7D795E;
}
#t3 td {
	line-height:30px;
	height:30px;
	text-align:center;
	background:#3D3F3E;
	border:1px solid #4A4C49;
	border-right:none;
	border-bottom:none;
}
#t3 th, #t3 th a {
	font-weight:normal;
}
#m4 td {
	text-align:center;
}
.th2 th, .th3 {
	background:#232522;
	text-align:center;
	color:#7D795E;
	font-weight:normal;
}
.th3 {
	font-weight:bold;
	text-align:left;
}
#footer table {
	clear:none;
}
#footer td {
	text-align:center;
	padding:1px 3px;
	font-size:9px;
}
#footer a {
	font-size:9px;
}
#f1 {
	text-align:right;
	padding:15px;
}
#f2 {
	float:left;
	border:1px solid #dddddd;
}
#f2 td {
	background:#FF6600;
}
#f2 a {
	color:#ffffff;
}
#f3 {
	border: 1px solid #888888;
	float:right;
}
#f3 a {
	color:#222222;
}
#f31 {
	background:#2359B1;
	color:#FFFFFF;
}
#f32 {
	background:#dddddd;
}
-->
</style>
</head>
<body>
<form method="post" action="<?=PHPSELF."#bottom"?>">
  <div>
    <!-- =============================================================
页头 
============================================================= -->
    <a name="top"></a>
    <table width="100%" border="0" cellspacing="1" cellpadding="0" id="t3">
      <tr>
        <td><a href="#sec1">服务器特征</a></td>
        <td><a href="#sec2">PHP基本特征</a></td>
        <td><a href="#sec3">PHP组件支持状况</a></td>
        <td><a href="#sec4">自定义检测</a></td>
        <td><a href="<?=PHPSELF?>" class="t211">刷新</a></td>
        <td><a href="#bottom" class="arrow">66</a></td>
      </tr>
    </table>
    <!-- =============================================================
服务器特性 
============================================================= -->
    <table width="100%" border="0" cellspacing="1" cellpadding="0">
      <tr>
        <th colspan="2"><p> <a href="#top" class="arrow">5</a> <br />
            <a href="#bottom" class="arrow">6</a> </p>
          <span>8</span>服务器特性 <a name="sec1" id="sec1"></a> </th>
      </tr>
      <?if("show"==$sysReShow){?>
      <tr>
        <td>CPU个数</td>
        <td><?=$sysInfo['cpu']['num']?></td>
      </tr>
      <tr>
        <td>CPU型号</td>
        <td><?=$sysInfo['cpu']['model']?></td>
      </tr>
      <tr>
        <td>CPU频率</td>
        <td><?=$sysInfo['cpu']['mhz']?></td>
      </tr>
      <tr>
        <td>CPU二级缓存</td>
        <td><?=$sysInfo['cpu']['cache']?></td>
      </tr>
      <tr>
        <td>系统Bogomips</td>
        <td><?=$sysInfo['cpu']['bogomips']?></td>
      </tr>
      <?}?>
      <tr>
        <td>服务器时间</td>
        <td><?=date("Y年n月j日 H:i:s")?>
          &nbsp;北京时间：
          <?=gmdate("Y年n月j日 H:i:s",time()+8*3600)?></td>
      </tr>
      <?if("show"==$sysReShow){?>
      <tr>
        <td>服务器运行时间</td>
        <td><?=$sysInfo['uptime']?></td>
      </tr>
      <?}?>
      <tr>
        <td>服务器域名/IP地址</td>
        <td><?=$_SERVER['SERVER_NAME']?>
          (
          <?=@gethostbyname($_SERVER['SERVER_NAME'])?>
          )</td>
      </tr>
      <tr>
        <td>服务器操作系统
          <?$os = explode(" ", php_uname());?></td>
        <td><?=$os[0];?>
          &nbsp;内核版本：
          <?=$os[2]?></td>
      </tr>
      <tr>
        <td>主机名称</td>
        <td><?=$os[1];?></td>
      </tr>
      <tr>
        <td>服务器解译引擎</td>
        <td><?=$_SERVER['SERVER_SOFTWARE']?></td>
      </tr>
      <tr>
        <td>Web服务端口</td>
        <td><?=$_SERVER['SERVER_PORT']?></td>
      </tr>
      <tr>
        <td>服务器语言</td>
        <td><?php echo getenv("HTTP_ACCEPT_LANGUAGE");?></td>
      </tr>
      <tr>
        <td>服务器管理员</td>
        <td><a href="mailto:<?=$_SERVER['SERVER_ADMIN']?>">
          <?=$_SERVER['SERVER_ADMIN']?>
          </a></td>
      </tr>
      <tr>
        <td>本文件路径</td>
        <td><?=$_SERVER['DOCUMENT_ROOT']. "<br />".$_SERVER['$PATH_INFO']?></td>
      </tr>
      <tr>
        <td>目前还有空余空间&nbsp;diskfreespace</td>
        <td><?=round((@disk_free_space(".")/(1024*1024)),2)?>
          M</td>
      </tr>
      <?if("show"==$sysReShow){?>
      <tr>
        <td>内存使用状况</td>
        <td> 物理内存：共
          <?=$sysInfo['memTotal']?>
          M, 已使用
          <?=$sysInfo['memUsed']?>
          M, 空闲
          <?=$sysInfo['memFree']?>
          M, 使用率
          <?=$sysInfo['memPercent']?>
          %
          <?=bar($sysInfo['memPercent'])?>
          Cache化内存为
          <?=$sysInfo['memCached']?>
          M, 真实内存使用率为
          <?=$sysInfo['memRealPercent']?>
          %
          <?=bar($sysInfo['memRealPercent'])?>
          SWAP区：共
          <?=$sysInfo['swapTotal']?>
          M, 已使用
          <?=$sysInfo['swapUsed']?>
          M, 空闲
          <?=$sysInfo['swapFree']?>
          M, 使用率
          <?=$sysInfo['swapPercent']?>
          %
          <?=bar($sysInfo['swapPercent'])?></td>
      </tr>
            <tr>
        <td>系统平均负载</td>
        <td><?=$sysInfo['loadAvg']?></td>
      </tr>
      <tr>
        <td>网络</td>
        <td><table width="100%"><?php
if (false === ($str = @file("/proc/net/dev"))) return false;
for($i=2;$i<count($str);$i++){
        preg_match_all( "/([^\s]+):[\s]{0,}(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)/", $str[$i], $info );
        echo "<tr><td>".$info[1][0].":</td><td>已接收:".round($info[2][0]/1024/1024, 2)."MB</td><td>已发送:".round($info[10][0]/1024/1024, 2)."MB</td></tr>";
}
?></table></td>
      </tr>
      <?}?>
    </table>
    <!-- ============================================================= 
PHP基本特性 
============================================================== -->
    <table width="100%" cellpadding="0" cellspacing="1" border="0">
      <tr>
        <th colspan="2"><p> <a href="#top" class="arrow">5</a> <br />
            <a href="#bottom" class="arrow">6</a> </p>
          <span>8</span>PHP基本特性 <a name="sec2" id="sec2"></a> </th>
      </tr>
      <tr>
        <td width="49%">PHP运行方式</td>
        <td width="51%"><?=strtoupper(php_sapi_name())?></td>
      </tr>
      <tr>
        <td>PHP版本</td>
        <td><?=PHP_VERSION?></td>
      </tr>
      <tr>
        <td>Zend版本</td>
        <td><?php echo zend_version();?></td>
      </tr>
      <tr>
        <td>Mysql版本</td>
        <td><?php if(function_exists("mysql_get_server_info")) {
	$curr_mysql_version = @mysql_get_server_info();
	$m = @mysql_get_client_info();
	$s = @mysql_get_client_info()?"$m&nbsp;<a href=\"javascript:ShowHide('sqlcl');\" title=\"点击此处查看说明\">说明</a>
      <p id=\"sqlcl\" class=\"notice\" style=\"display:none;\">
    此处显示的是客户端的Mysql版本，不要误会是服务器端的,大部分正确的连接版本, 都是连接上数据库后才会显示</p>":"未安装";
	}
	else {
		$s = "<span class='resNo'>NO</span>";
	}
	echo $curr_mysql_version?$curr_mysql_version:"$s";?></td>
      </tr>
      <tr>
        <td>SQLite版本</td>
        <td><?php
				if(function_exists(sqlite_libversion)) {
	$sqlitelibversion = @sqlite_libversion();
	}
	else {
		$sqlitelibversion = "";
	}
	echo $sqlitelibversion?$sqlitelibversion:NO;?></td>
      </tr>
      <tr>
        <td>GD Library版本</td>
        <td><?php
				if(function_exists(gd_info)) {
				@$gdInfo=@gd_info();
	$gdInfo_a = @$gdInfo["GD Version"];
	}
	else {
		$gdInfo_a = "";
	}
	echo $gdInfo_a?$gdInfo_a:NO;?></td>
      </tr>
      <tr>
        <td>运行于安全模式</td>
        <td><?=getcon("safe_mode")?></td>
      </tr>
      <tr>
        <td>支持ZEND编译运行</td>
        <td><?=(get_cfg_var("zend_optimizer.optimization_level")||get_cfg_var("zend_extension_manager.optimizer_ts")||get_cfg_var("zend_extension_ts")) ?YES:NO?></td>
      </tr>
      <tr>
        <td>允许使用URL打开文件&nbsp;allow_url_fopen</td>
        <td><?=getcon("allow_url_fopen")?></td>
      </tr>
      <tr>
        <td>允许动态加载链接库&nbsp;enable_dl</td>
        <td><?=getcon("enable_dl")?></td>
      </tr>
      <tr>
        <td>COOKIE支持</td>
        <td><?=isset($HTTP_COOKIE_VARS)?YES:NO;?></td>
      </tr>
      <tr>
        <td>显示错误信息&nbsp;display_errors</td>
        <td><?=getcon("display_errors")?></td>
      </tr>
      <tr>
        <td>自动定义全局变量&nbsp;register_globals</td>
        <td><?=getcon("register_globals")?></td>
      </tr>
      <tr>
        <td>程序最多允许使用内存量&nbsp;memory_limit</td>
        <td><?=getcon("memory_limit")?></td>
      </tr>
      <tr>
        <td>POST最大字节数&nbsp;post_max_size</td>
        <td><?=getcon("post_max_size")?></td>
      </tr>
      <tr>
        <td>允许最大上传文件&nbsp;upload_max_filesize</td>
        <td><?=getcon("upload_max_filesize")?></td>
      </tr>
      <tr>
        <td>程序最长运行时间&nbsp;max_execution_time</td>
        <td><?=getcon("max_execution_time")?>
          秒</td>
      </tr>
      <tr>
        <td>magic_quotes_gpc</td>
        <td><?=(1===get_magic_quotes_gpc())?YES:NO?></td>
      </tr>
      <tr>
        <td>magic_quotes_runtime</td>
        <td><?=(1===get_magic_quotes_runtime())?YES:NO?></td>
      </tr>
      <tr>
        <td>被禁用的函数&nbsp;disable_functions</td>
        <td><?=(""==($disFuns=get_cfg_var("disable_functions")))?"无":str_replace(",","<br />",$disFuns)?></td>
      </tr>
      <tr>
        <td>PHP信息&nbsp;PHPINFO</td>
        <td><?=(false!==eregi("phpinfo",$disFuns))?NO:"<a href='$phpSelf?act=phpinfo' target='_blank' class='static'>PHPINFO</a>"?></td>
      </tr>
      <tr>
        <td>默认支持函数</td>
        <td><?=(false!==eregi("function",$disFuns))?NO:"<a href='$phpSelf?act=Function' target='_blank' class='static'>Function</a>"?></td>
      </tr>
    </table>
    <!-- ============================================================= 
PHP组件支持 
============================================================== -->
    <table width="100%" cellpadding="0" cellspacing="1" border="0">
      <tr>
        <th colspan="4"><p> <a href="#top" class="arrow">5</a> <br />
            <a href="#bottom" class="arrow">6</a> </p>
          <span>8</span>PHP组件支持 <a name="sec3" id="sec3"></a> </th>
      </tr>
      <tr>
        <td width="38%">拼写检查 ASpell Library</td>
        <td width="12%"><?=isfun("aspell_check_raw")?></td>
        <td width="38%">高精度数学运算 BCMath</td>
        <td width="12%"><?=isfun("bcadd")?></td>
      </tr>
      <tr>
        <td>历法运算 Calendar</td>
        <td><?=isfun("cal_days_in_month")?></td>
        <td>DBA数据库</td>
        <td><?=isfun("dba_close")?></td>
      </tr>
      <tr>
        <td>dBase数据库</td>
        <td><?=isfun("dbase_close")?></td>
        <td>DBM数据库</td>
        <td><?=isfun("dbmclose")?></td>
      </tr>
      <tr>
        <td>FDF表单资料格式</td>
        <td><?=isfun("fdf_get_ap")?></td>
        <td>FilePro数据库</td>
        <td><?=isfun("filepro_fieldcount")?></td>
      </tr>
      <tr>
        <td>Hyperwave数据库</td>
        <td><?=isfun("hw_close")?></td>
        <td>图形处理 GD Library</td>
        <td><?=isfun("gd_info")?></td>
      </tr>
      <tr>
        <td>IMAP电子邮件系统</td>
        <td><?=isfun("imap_close")?></td>
        <td>Informix数据库</td>
        <td><?=isfun("ifx_close")?></td>
      </tr>
      <tr>
        <td>LDAP目录协议</td>
        <td><?=isfun("ldap_close")?></td>
        <td>MCrypt加密处理</td>
        <td><?=isfun("mcrypt_cbc")?></td>
      </tr>
      <tr>
        <td>哈稀计算 MHash</td>
        <td><?=isfun("mhash_count")?></td>
        <td>mSQL数据库</td>
        <td><?=isfun("msql_close")?></td>
      </tr>
      <tr>
        <td>SQL Server数据库</td>
        <td><?=isfun("mssql_close")?></td>
        <td>MySQL数据库</td>
        <td><?=isfun("mysql_close")?></td>
      </tr>
      <tr>
        <td>SyBase数据库</td>
        <td><?=isfun("sybase_close")?></td>
        <td>Yellow Page系统</td>
        <td><?=isfun("yp_match")?></td>
      </tr>
      <tr>
        <td>Oracle数据库</td>
        <td><?=isfun("ora_close")?></td>
        <td>Oracle 8 数据库</td>
        <td><?=isfun("OCILogOff")?></td>
      </tr>
      <tr>
        <td>PREL相容语法 PCRE</td>
        <td><?=isfun("preg_match")?></td>
        <td>PDF文档支持</td>
        <td><?=isfun("pdf_close")?></td>
      </tr>
      <tr>
        <td>Postgre SQL数据库</td>
        <td><?=isfun("pg_close")?></td>
        <td>SNMP网络管理协议</td>
        <td><?=isfun("snmpget")?></td>
      </tr>
      <tr>
        <td>VMailMgr邮件处理</td>
        <td><?=isfun("vm_adduser")?></td>
        <td>WDDX支持</td>
        <td><?=isfun("wddx_add_vars")?></td>
      </tr>
      <tr>
        <td>压缩文件支持(Zlib)</td>
        <td><?=isfun("gzclose")?></td>
        <td>XML解析</td>
        <td><?=isfun("xml_set_object")?></td>
      </tr>
      <tr>
        <td>FTP</td>
        <td><?=isfun("ftp_login")?></td>
        <td>ODBC数据库连接</td>
        <td><?=isfun("odbc_close")?></td>
      </tr>
      <tr>
        <td>Session支持</td>
        <td><?=isfun("session_start")?></td>
        <td>Socket支持</td>
        <td><?=isfun("socket_accept")?></td>
      </tr>
      <tr>
        <td>浮点型数据显示的有效位数(precision)</td>
        <td><?=getcon("precision")?></td>
        <td>socket超时时间(default_socket_timeout)</td>
        <td><?=getcon("default_socket_timeout")?>
          秒</td>
      </tr>
      <tr>
        <td>"&lt;?...?&gt;"短标签(short_open_tag)</td>
        <td><?=getcon("short_open_tag")?></td>
        <td>指定包含文件目录(include_path)</td>
        <td><?=getcon("include_path")?></td>
      </tr>
      <tr>
        <td>忽略重复错误信息(ignore_repeated_errors)</td>
        <td><?=getcon("ignore_repeated_errors")?></td>
        <td>忽略重复的错误源(ignore_repeated_source)</td>
        <td><?=getcon("ignore_repeated_source")?></td>
      </tr>
      <tr>
        <td>报告内存泄漏(report_memleaks)</td>
        <td><?=getcon("report_memleaks")?></td>
        <td>声明argv和argc变量(register_argc_argv)</td>
        <td><?=getcon("register_argc_argv")?></td>
      </tr>
      <tr>
        <td>历法运算函数库</td>
        <td><?=isfun("JDToGregorian")?></td>
        <td>Iconv编码转换</td>
        <td><?=isfun("iconv")?></td>
      </tr>
      <tr>
        <td>mbstring</td>
        <td><?=isfun("mb_eregi")?></td>
        <td>SQLite 数据库</td>
        <td><?=isfun("sqlite_close"); if(isfun("sqlite_close") == '支持'){echo ",版本为: ".@sqlite_libversion();}?></td>
      </tr>
      <tr>
        <td>SMTP支持</td>
        <td><?=get_cfg_var("SMTP")?YES:NO;?></td>
        <td>SMTP地址</td>
        <td><?=get_cfg_var("SMTP")?get_cfg_var("SMTP"):NO;?></td>
      </tr>
	  <tr>
        <td colspan="4" class="e"><b>已编译模块检测</b><br />
		<?php 
		$able=get_loaded_extensions();
		foreach ($able as $key=>$value) {
			if ($key!=0 && $key%12==0) {
				echo '<br />';
			}
			echo "$value&nbsp;&nbsp;";
		}
		?>
		</td>
      </tr>
    </table>
    <!-- ============================================================= 
自定义检测 
============================================================== -->
    <?php
    $isMysql = (false !== function_exists("mysql_query"))?"":" disabled";
    $isMail = (false !== function_exists("mail"))?"":" disabled";
?>
    <table width="100%" border="0" cellspacing="1" cellpadding="0">
      <tr>
        <th colspan="4"><p> <a href="#top" class="arrow">5</a> <br />
            <a href="#bottom" class="arrow">6</a> </p>
          <span>8</span>自定义检测 <a name="sec4" id="sec4"></a> </th>
      </tr>
      <tr>
        <th colspan="4" class="th3">MYSQL连接测试</th>
      </tr>
      <tr>
        <td>MYSQL服务器</td>
        <td><input type="text" name="mysqlHost" value="localhost" <?=$isMysql?> /></td>
        <td> MYSQL用户名 </td>
        <td><input type="text" name="mysqlUser" <?=$isMysql?> /></td>
      </tr>
      <tr>
        <td> MYSQL用户密码 </td>
        <td><input type="text" name="mysqlPassword" <?=$isMysql?> /></td>
        <td> MYSQL数据库名称 </td>
        <td><input type="text" name="mysqlDb" />
          &nbsp;
          <input type="submit" class="myButton" value="CONNECT" <?=$isMysql?>  name="act" /></td>
      </tr>
      <?php if("show"==$mysqlReShow){?>
      <tr>
        <td colspan="4"><?=$mysqlRe?></td>
      </tr>
      <?}?>
      <tr>
        <th colspan="4" class="th3">MAIL邮件发送测试</th>
      </tr>
      <tr>
        <td>收信地址</td>
        <td colspan="3"><input type="text" name="mailReceiver" size="50" <?=$isMail?> />
          &nbsp;
          <input type="submit" class="myButton" value="SENDMAIL" <?=$isMail?>  name="act" /></td>
      </tr>
      <?php if("show"==$mailReShow){?>
      <tr>
        <td colspan="4"><?=$mailRe?></td>
      </tr>
      <?}?>
      <tr>
        <th colspan="4" class="th3">函数支持状况</th>
      </tr>
      <tr>
        <td>函数名称</td>
        <td colspan="3"><input type="text" name="funName" size="50" />
          &nbsp;
          <input type="submit" class="myButton" value="FUNCTION_CHECK" name="act" /></td>
        <?php if("show"==$funReShow){?>
      <tr>
        <td colspan="4"><?=$funRe?></td>
      </tr>
      <?}?>
      </tr>
      
      <tr>
        <th colspan="4" class="th3">PHP配置参数状况</th>
      </tr>
      <tr>
        <td>参数名称</td>
        <td colspan="3"><input type="text" name="opName" size="40" />
          &nbsp;
          <input type="submit" class="myButton" value="CONFIGURATION_CHECK" name="act" /></td>
      </tr>
      <?php if("show"==$opReShow){?>
      <tr>
        <td colspan="4"><?=$opRe?></td>
      </tr>
      <?}?>
    </table>
    <!-- ============================================================= 
页脚  
============================================================== -->
    <div id="footer">
      <p id="f1"> <br/>
        <?php
$runtime->stop();
echo "页面执行时间: ".$runtime->spent()." 毫秒";
?>
        <br/>
        <a name="bottom"></a> <a href="#top" class="arrow">55</a> </p>
    </div>
  </div>
</form>
</body>
</html>
<?php
/*=============================================================
    函数库
=============================================================*/
/*-------------------------------------------------------------------------------------------------------------
    检测函数支持
--------------------------------------------------------------------------------------------------------------*/
    function isfun($funName)
    {
        return (false !== function_exists($funName))?YES:NO;
    }
/*-------------------------------------------------------------------------------------------------------------
    检测PHP设置参数
--------------------------------------------------------------------------------------------------------------*/
    function getcon($varName)
    {
        switch($res = get_cfg_var($varName))
        {
            case 0:
            return NO;
            break;
            case 1:
            return YES;
            break;
            default:
            return $res;
            break;
        }
         
    }

/*-------------------------------------------------------------------------------------------------------------
    比例条
--------------------------------------------------------------------------------------------------------------*/
    function bar($percent)
    {
    ?>
<ul class="bar">
  <li style="width:<?=$percent?>%">&nbsp;</li>
</ul>
<?php
    }
/*-------------------------------------------------------------------------------------------------------------
    系统参数探测 LINUX
--------------------------------------------------------------------------------------------------------------*/
    function sys_linux()
    {
        // CPU
        if (false === ($str = @file("/proc/cpuinfo"))) return false;
        $str = implode("", $str);
		@preg_match_all("/model\s+name\s{0,}\:+\s{0,}([\w\s\)\(\@.-]+)([\r\n]+)/s", $str, $model);		
        @preg_match_all("/cpu\s+MHz\s{0,}\:+\s{0,}([\d\.]+)[\r\n]+/", $str, $mhz);
        @preg_match_all("/cache\s+size\s{0,}\:+\s{0,}([\d\.]+\s{0,}[A-Z]+[\r\n]+)/", $str, $cache);
        @preg_match_all("/bogomips\s{0,}\:+\s{0,}([\d\.]+)[\r\n]+/", $str, $bogomips);
        if (false !== is_array($model[1]))
            {
            $res['cpu']['num'] = sizeof($model[1]);
            for($i = 0; $i < $res['cpu']['num']; $i++)
            {
				$res['cpu']['model'][] = $model[1][$i];
				$res['cpu']['mhz'][] = $mhz[1][$i];
				$res['cpu']['cache'][] = $cache[1][$i];
				$res['cpu']['bogomips'][] = $bogomips[1][$i];
            }
			if (false !== is_array($res['cpu']['model'])) $res['cpu']['model'] = implode("<br />", $res['cpu']['model']);
			if (false !== is_array($res['cpu']['mhz'])) $res['cpu']['mhz'] = implode("<br />", $res['cpu']['mhz']);
			if (false !== is_array($res['cpu']['cache'])) $res['cpu']['cache'] = implode("<br />", $res['cpu']['cache']);
			if (false !== is_array($res['cpu']['bogomips'])) $res['cpu']['bogomips'] = implode("<br />", $res['cpu']['bogomips']);
            }

        // NETWORK

        // UPTIME
        if (false === ($str = @file("/proc/uptime"))) return false;
        $str = explode(" ", implode("", $str));
        $str = trim($str[0]);
        $min = $str / 60;
        $hours = $min / 60;
        $days = floor($hours / 24);
        $hours = floor($hours - ($days * 24));
        $min = floor($min - ($days * 60 * 24) - ($hours * 60));
        if ($days !== 0) $res['uptime'] = $days."天";
        if ($hours !== 0) $res['uptime'] .= $hours."小时";
        $res['uptime'] .= $min."分钟";
         
        // MEMORY
        if (false === ($str = @file("/proc/meminfo"))) return false;
        $str = implode("", $str);
        preg_match_all("/MemTotal\s{0,}\:+\s{0,}([\d\.]+).+?MemFree\s{0,}\:+\s{0,}([\d\.]+).+?Cached\s{0,}\:+\s{0,}([\d\.]+).+?SwapTotal\s{0,}\:+\s{0,}([\d\.]+).+?SwapFree\s{0,}\:+\s{0,}([\d\.]+)/s", $str, $buf);

        $res['memTotal'] = round($buf[1][0]/1024, 2);
        $res['memFree'] = round($buf[2][0]/1024, 2);
		$res['memCached'] = round($buf[3][0]/1024, 2);
        $res['memUsed'] = ($res['memTotal']-$res['memFree']);
        $res['memPercent'] = (floatval($res['memTotal'])!=0)?round($res['memUsed']/$res['memTotal']*100,2):0;
        $res['memRealUsed'] = ($res['memTotal'] - $res['memFree'] - $res['memCached']);
		$res['memRealPercent'] = (floatval($res['memTotal'])!=0)?round($res['memRealUsed']/$res['memTotal']*100,2):0;
		
        $res['swapTotal'] = round($buf[4][0]/1024, 2);
        $res['swapFree'] = round($buf[5][0]/1024, 2);
        $res['swapUsed'] = ($res['swapTotal']-$res['swapFree']);
        $res['swapPercent'] = (floatval($res['swapTotal'])!=0)?round($res['swapUsed']/$res['swapTotal']*100,2):0;

        // LOAD AVG
        if (false === ($str = @file("/proc/loadavg"))) return false;
        $str = explode(" ", implode("", $str));
        $str = array_chunk($str, 4);
        $res['loadAvg'] = implode(" ", $str[0]);
		 
        return $res;
    }
?>