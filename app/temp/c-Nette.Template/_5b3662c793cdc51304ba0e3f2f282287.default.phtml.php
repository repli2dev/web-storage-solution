<?php //netteCache[01]000218a:2:{s:4:"time";s:21:"0.89740300 1278699014";s:9:"callbacks";a:1:{i:0;a:3:{i:0;a:2:{i:0;s:5:"Cache";i:1;s:9:"checkFile";}i:1;s:63:"/home/Weby/Ostatni/osw/www/app/templates/Homepage/default.phtml";i:2;i:1278008476;}}}?><?php
// file …/templates/Homepage/default.phtml
//

$_cb = LatteMacros::initRuntime($template, NULL, '0d901b4cff'); unset($_extends);


//
// block content
//
if (!function_exists($_cb->blocks['content'][] = '_cbbb16b53eec2_content')) { function _cbbb16b53eec2_content($_args) { extract($_args)
?>

<div id="header">
	<h1>It works!</h1>

	<h2>Congratulations on your first Nette Framework powered page.</h2>
</div>

<div>
	<p><?php echo TemplateHelpers::escapeHtml($message) ?></p>

	<a href="http://nette.org" title="Nette Framework - The most innovative PHP framework"><img
	src="<?php echo TemplateHelpers::escapeHtml($basePath) ?>/images/nette-powered2.gif" width="80" height="15" alt="Nette Framework powered"></a>
</div>

<style>
	body {
		margin: 0;
		padding: 0;
	}

	div {
		padding: .2em 1em;
	}

	#header {
		background: #EEE;
		border-bottom: 1px #DDD solid;
	}

	h1 {
		color: #0056ad;
		font-size: 30px;
	}

	h2 {
		color: gray;
		font-size: 20px;
	}

	img {
		border: none;
	}
</style><?php
}}

//
// end of blocks
//

if ($_cb->extends) { ob_start(); }

if (SnippetHelper::$outputAllowed) {
if (!$_cb->extends) { call_user_func(reset($_cb->blocks['content']), get_defined_vars()); }  
}

if ($_cb->extends) { ob_end_clean(); LatteMacros::includeTemplate($_cb->extends, get_defined_vars(), $template)->render(); }
