<?php //netteCache[01]000210a:2:{s:4:"time";s:21:"0.10502000 1278771776";s:9:"callbacks";a:1:{i:0;a:3:{i:0;a:2:{i:0;s:5:"Cache";i:1;s:9:"checkFile";}i:1;s:55:"/home/Weby/Ostatni/osw/www/app/templates//@layout.phtml";i:2;i:1278771756;}}}?><?php
// file …/templates//@layout.phtml
//

$_cb = LatteMacros::initRuntime($template, NULL, 'de79d382a8'); unset($_extends);


//
// block content
//
if (!function_exists($_cb->blocks['content'][] = '_cbb74422ec55c_content')) { function _cbb74422ec55c_content($_args) { extract($_args)
?>
				
<?php
}}

//
// end of blocks
//

if ($_cb->extends) { ob_start(); }

if (SnippetHelper::$outputAllowed) {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<meta name="description" content="<?php echo TemplateHelpers::escapeHtml($web->description) ?>">
	<meta name="keywords" content="<?php echo TemplateHelpers::escapeHtml($web->keywords) ?>"><?php if (isset($web->robots)): ?>
	<meta name="robots" content="<?php echo TemplateHelpers::escapeHtml($web->robots) ?>">
<?php endif ?>

	<title><?php if (isset($pageTitle)): echo TemplateHelpers::escapeHtml($pageTitle) ?> | <?php endif ;echo TemplateHelpers::escapeHtml($web->defaultTitle) ?></title>

	<link rel="stylesheet" href="/css/blueprint/screen.css"   type="text/css" media="screen, projection">
	<link rel="stylesheet" href="/css/blueprint/print.css" type="text/css" media="print">
	<!--[if IE]><link rel="stylesheet" href="/css/blueprint/ie.css" type="text/css" media="screen, projection"><![endif]-->

	<link rel="stylesheet" media="screen,projection,tv" href="<?php echo TemplateHelpers::escapeHtml($basePath) ?>/css/screen.css" type="text/css">

	<link rel="stylesheet" href="http://jquery-ui.googlecode.com/svn/tags/1.7.2/themes/blitzer/ui.all.css" type="text/css" />
	<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
	<script type="text/javascript" src="http://jquery-ui.googlecode.com/svn/tags/1.7.2/ui/minified/ui.core.min.js"></script>
	<script type="text/javascript" src="http://jquery-ui.googlecode.com/svn/tags/1.7.2/ui/minified/ui.slider.min.js"></script>
	<script type="text/javascript" src="http://jquery-ui.googlecode.com/svn/tags/1.7.2/ui/minified/ui.datepicker.min.js"></script>
	<script type="text/javascript" src="http://jquery-ui.googlecode.com/svn/tags/1.7.2/ui/minified/i18n/ui.datepicker-<?php echo TemplateHelpers::escapeHtml($variable->lang) ?>.min.js"></script>
	<script type="text/javascript" src="<?php echo TemplateHelpers::escapeHtml($basePath) ?>/js/timepicker-<?php echo TemplateHelpers::escapeHtml($variable->lang) ?>.js"></script>
	<?php $unique_id = uniqid("") ?>
	<script type="text/javascript">
		  <!-- <![CDATA[
		    $(document).ready(function()
		    {
		      $('input.datetimepicker').datepicker(
		      {
			duration: '',
			changeMonth: true,
			changeYear: true,
			yearRange: '2007:2020',
			showTime: true,
			time24h: true,
			currentText: 'Today',
			closeText: 'OK'
		      });
		    });
		  //]]> -->
	</script>
</head>

<body>
	<div class="container">
		<hr />
			<h1><?php echo TemplateHelpers::escapeHtml($web->defaultTitle) ?></h1>
		<hr />

		<div id="menu" class="large">
			<?php if (!$presenter->canSee("users") && !$presenter->canSee("files")): ?><a <?php try { $presenter->link("Users:login"); } catch (InvalidLinkException $e) {}; if ($presenter->getLastCreatedRequestFlag("current")): ?>class="highlight"<?php endif ?> href="/">Úvod</a><?php endif ?>


			<?php if ($presenter->canSee("files")): ?><a <?php try { $presenter->link("Files:*"); } catch (InvalidLinkException $e) {}; if ($presenter->getLastCreatedRequestFlag("current")): ?>class="highlight"<?php endif ?> href="<?php echo TemplateHelpers::escapeHtml($presenter->link("Files:")) ?>">Files</a><?php endif ?>

			<?php if ($presenter->canSee("users")): ?><a <?php try { $presenter->link("Users:*"); } catch (InvalidLinkException $e) {}; if ($presenter->getLastCreatedRequestFlag("current")): ?>class="highlight"<?php endif ?> href="<?php echo TemplateHelpers::escapeHtml($presenter->link("Users:")) ?>">Users</a><?php endif ?>

			<?php if ($presenter->canSee("users") || $presenter->canSee("files")): ?><a <?php try { $presenter->link("Users:password"); } catch (InvalidLinkException $e) {}; if ($presenter->getLastCreatedRequestFlag("current")): ?>class="highlight"<?php endif ?> href="<?php echo TemplateHelpers::escapeHtml($presenter->link("Users:password")) ?>">Change password</a><?php endif ?>

			<?php if ($presenter->canSee("users") || $presenter->canSee("files")): ?><a href="<?php echo TemplateHelpers::escapeHtml($presenter->link("Users:logout")) ?>">Logout</a><?php endif ?>

		</div>
		<hr class="space clear">

		<div class="span-24">
<?php if (!$_cb->extends) { call_user_func(reset($_cb->blocks['content']), get_defined_vars()); } ?>
		</div>
		<div class="clear" id="footer">
			&copy; 2010 Jan Drabek
		</div>
		<hr />
	</div>

</body>
</html>
<?php
}

if ($_cb->extends) { ob_end_clean(); LatteMacros::includeTemplate($_cb->extends, get_defined_vars(), $template)->render(); }
