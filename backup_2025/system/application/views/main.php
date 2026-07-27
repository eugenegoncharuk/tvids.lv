<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
<meta name="google-site-verification" content="LUrMbdzpnbW9eZF1C20RjT7HjxS64KLoFWPZ3sPUtjU" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="language" content="ru" />
<meta name="author" content="DESIGNSTUDIO" />
<meta name="publisher" content="DESIGNSTUDIO" />
<meta name="copyright" content="DESIGNSTUDIO" />
<meta name="page-type" content="" />
<meta name="page-topic" content="" />
<meta name="audience" content="all" />
<meta name="description" content="Gultas veļas pārdošana,продажа постельного белья" />
<meta name="keywords" content="Gultas veļa,gulta,šūt,šūvēja,satīns,ткань,постельное белье,ткань,продажа,интернет магазин,сатин,бязь,ночная рубашка" />
<meta name="Expires" content="never" />

<title>SIA "TVIDS" Gultas veļa/Постельное белье</title>
<base href="<?php echo base_url()?>system/www/">
<link rel="stylesheet" href="css/base.css" type="text/css" media="screen, projection" />
<link rel="stylesheet" href="css/ie.css" type="text/css" media="screen, projection" />
<link href="tree/simpletree.css" type="text/css" rel="stylesheet" />
<link rel="stylesheet" href="fancybox/jquery.fancybox.css" type="text/css" media="screen">
<!--[if IE 6]>
<style type="text/css">
	html body {
		width:expression(document.documentElement.clientWidth < 1000? "1000px" : "auto");
	}

	#item_block {
		zoom: 1;
		display: inline;

	}

	#item {
		zoom: 1;
		display: inline;
	}

	#cart {
		margin-right: 30px;
	}

</style>
<![endif]-->

<script language="JavaScript">
<!--
	function onLoad()
	{

		var viewportwidth;
		var viewportheight;

		// the more standards compliant browsers (mozilla/netscape/opera/IE7) use window.innerWidth and window.innerHeight
		if (typeof window.innerWidth != 'undefined')
		{
		  viewportwidth = window.innerWidth,
		  viewportheight = window.innerHeight
		}
		// IE6 in standards compliant mode (i.e. with a valid doctype as the first line in the document)
		else if (typeof document.documentElement != 'undefined'
		 && typeof document.documentElement.clientWidth !=
		 'undefined' && document.documentElement.clientWidth != 0)
		{
		   viewportwidth = document.documentElement.clientWidth,
		   viewportheight = document.documentElement.clientHeight
		}
		// older versions of IE
		else
		{
		   viewportwidth = document.getElementsByTagName('body')[0].clientWidth,
		   viewportheight = document.getElementsByTagName('body')[0].clientHeight
		}

		viewportheight = viewportheight - 289 - 179;

		d = document.getElementById('main');
		if(d.offsetHeight){
			divHeight=d.offsetHeight;
		}
		else if(d.style.pixelHeight){
			divHeight=d.style.pixelHeight;
		}

		if (divHeight < viewportheight){
			document.getElementById('main').style.height=viewportheight+'px';
		}
	}
// -->
</script>

<script type="text/javascript" src="tree/simpletreemenu.js"></script>

<script type="text/javascript" src="fancybox/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="fancybox/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="fancybox/jquery.fancybox-1.2.1.pack.js"></script>
<!-- Adding JS for gallery show -->
<script type="text/javascript">
	$(document).ready(function() {
		/* This is basic - uses default settings */
		$("#table_images a").fancybox();
		/* Using custom settings */
		$("a#inline").fancybox({
			'hideOnContentClick': true
			});
		$("a.group").fancybox({
			'zoomSpeedIn': 300,
			'zoomSpeedOut': 300,
			'overlayShow': false
		});
	});
</script>

</head>
<body onLoad="javascript: onLoad();" onResize="javascript: onLoad();">
<div id="block">
	<div id="header" style="background-image: url(./img/header_bg.jpg);">
		<a href="<?=$this->config->site_url()?>web">
		    <div id="nav" style="background-image: url(./img/header1_<?=$this->lang->get_current_lang();?>.jpg);"></div>
		</a>
	    <div id="content" style="background-image: url(./img/header_bg.jpg);"></div>
	</div>
	<div id="header_right" style="background-image: url(./img/header2_<?=$this->lang->get_current_lang();?>.jpg);">
		<p> <? if ($this->lang->get_current_lang()=='ru') { ?>
				<a href="<?=base_url()?>index.php/lv/web/show/{pid}">latviski</a>
			<? } else { ?>
				<a href="<?=base_url()?>index.php/ru/web/show/{pid}">по-русски</a></p>
			<? } ?>
	</div>

	<div id="main">
	    <div id="nav">
			<div id="menu_text">
				<script type="text/javascript" src="<?php echo base_url()?>system/www/tree/simpletreemenu.js">
				/***********************************************
				* Simple Tree Menu -� Dynamic Drive DHTML code library (www.dynamicdrive.com)
				* This notice MUST stay intact for legal use
				* Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code
				***********************************************/
				</script>
					<ul id="treemenu1" class="treeview">
						{menu_text2}
						{menu_text}
					</ul>
				<script type="text/javascript">
					ddtreemenu.createTree("treemenu1", true)
				</script>
			</div>
	    </div>
	    <div id="content" name="main_content">{text}
			<?php if (@$pagination!="") { ?>
			<div id="pagination"><?=$pagination?></div>
			<?php } ?>
		</div>
	</div>
	<div id="main_right">
		<div id="login_info">
			<form name="loginform" id="loginform" action="<?=$this->config->site_url()?>web/login/{pid}" method="post">
			<? if (@$login_error!=''){ ?>
				<div id="error">{login_error}</div>
			<? } ?>
			<table border="0" cellspacing="2" cellpadding="0" width="170">
				<?php if ($this->session->userdata('web_user_id')!=""){ ?>
					<tr><td colspan="2" align="center">{hello} <?=$this->session->userdata('web_user_name')?> !
						<br><a href="<?=$this->config->site_url()?>web/logout">{logoff}</a>
					</td></tr>
				<? } else { ?>
				  <tr>
					<td>{main_menu_login}:</td>
					<td align="center"><input id="login" name="login" type="text"/></td>
				  </tr>
				  <tr>
					<td>{main_menu_pass}:</td>
					<td align="center"><input id="password" name="password" type="password" /></td>
				  </tr>
				  <tr>
					<td></td>
					<td align="center"><?php if (!$this->session->userdata('web_user_id')){ ?>
						<a href="javascript:document.loginform.submit()" class="login">Login</a>
						<? } ?>
					</td>
				  </tr>
				<tr>
					<td nowrap="nowrap" align="left" colspan="2">
						<?php if (!$this->session->userdata('web_user_id')){ ?>
						<br>
						<a href="<?=$this->config->site_url()?>web/view_reg_login" class="reg">{main_menu_register}</a>
						<br>
						<a href="<?=$this->config->site_url()?>web/forgot_password" class="lost_pass">{main_menu_lost}!</a>
						<? } ?>
					</td>
					<td></td>
				</tr>
				<? } ?>
			</table>
			</form>
		</div>

		<div id="cart_block">
			<div id="cart">
				<p id="count">{main_menu_products}:
				<? if (@$this->session->userdata('web_user_itemscount')!=''){
					echo @$this->session->userdata('web_user_itemscount');
				} else { echo '-'; } ?>
				</p>
				<p id="total">
		        {main_menu_total}:
				<? if (@$this->session->userdata('web_user_itemssum')>0){
					echo number_format(@$this->session->userdata('web_user_itemssum'), 2, '.', '');
					} else { echo '-'; } ?>
				</p>
			</div>
			<a id="view_cart_link" href="<?=$this->config->site_url()?>web/view_cart">{main_menu_shopcart}</a>
		</div>
	</div>

	<div id="bottom" style="background-image: url(./img/bottom_bg.jpg);">
	    <div id="nav" style="background-image: url(./img/bottom1.jpg);">
			<div id="bottom_contacts">
				+371 29558938<br>
				tvids(at)inbox.lv
			</div>
	    </div>
	    <div id="content" style="background-image: url(./img/bottom_bg.jpg);">
	    </div>
	</div>
	<div id="bottom_right" style="background-image: url(./img/bottom_bg.jpg);">
			<div id="bottom_copyright">
				Copyright &copy; 2009 SIA "TVIDS"<br>
				Developed by <a href="http://www.designstudio.lv" target="_blank">Designstudio</a>
			</div>
	</div>
</div>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("UA-8804751-2");
pageTracker._trackPageview();
</script>
</body>
</html>

