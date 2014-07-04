<?php

$app = require("lib/base.php");
$app->set( "DEBUG", array_key_exists( "minify", $_GET )? 0 : 1);
$app->set( "AUTOLOAD", "../lib/" );

$app->set( "SCRIPTS.UI", "scripts/" );
$app->set( "SCRIPTS.test-alias", "test-alias-1.js,test-alias-2.js" );

$app->route("GET /index.php/scripts/@script", function(
	$req, $args
) {
	echo \Web\Compiler::instance("SCRIPTS")->render( $args["script"] );
});

// Automatic handler, uses Compiler instance named after path parameter
// with a bit of caching
$app->route("GET /index.php/autoscripts/@SCRIPTS", '\Web\Compiler::handler', 1000*60);

$app->set( "STYLES.UI", "styles/" );
$app->set( "STYLES.test-alias", "test-style-1.css,test-style-2.css" );

$app->route("GET /index.php/styles/@STYLES", '\Web\Compiler::handler');

$app->route("GET /", function() { ?><html>
	<head>
		<title>FatFree Compiler test page</title>
		<link rel="stylesheet" href="index.php/styles/test-alias.css"/>
		<link rel="stylesheet" href="index.php/styles/test-style.css"/>
	</head>
	<body>
		<h2>Tests</h2>
			<h3>CSS compiling</h3>
				<p id="test-style-1" class="test-style">Style 1: </p>
				<p id="test-style-2" class="test-style">Style 2: </p>
			<h3>Javascript compiling</h3>
			<pre id="log"></pre>
			<script src="index.php/scripts/test-alias.js"></script>
			<script src="index.php/scripts/test-template.js"></script>
	</body>
</html><?php });

$app->run();

