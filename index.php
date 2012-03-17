<?php
require_once "TinyFram.php";

// APP SET UP
$app = new TinyFram("0.1", "testApp");
$app->appConfig("views/");
$app->pathIgnore(array('Some/route/you/want/to/ignore'));
// APP SET UP

// APP ROUTING
$urls = array(
		"/"=>"index",
		"/test"=>"lolwut"

);

$app->setRoutes($urls);

function index($url) {
		global $app;
		$app->render("index");
};

function lolwut($url) {
		global $app;
		$app->render("lolwut");
};

// APP ROUTING


// APP CALLBACKS
function notFound($urlPath) {
	global $app;
	$app->render("notfound");
}
// APP CALLBACKS
?>
