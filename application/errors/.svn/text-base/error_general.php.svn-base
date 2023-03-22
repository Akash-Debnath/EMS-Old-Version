
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
if (!function_exists('base_url')) {
	function base_url($atRoot=FALSE, $atCore=FALSE, $parse=FALSE){
		if (isset($_SERVER['HTTP_HOST'])) {
			$http = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
			$hostname = $_SERVER['HTTP_HOST'];
			$dir =  str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

			$core = preg_split('@/@', str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath(dirname(__FILE__))), NULL, PREG_SPLIT_NO_EMPTY);
			$core = $core[0];

			$tmplt = $atRoot ? ($atCore ? "%s://%s/%s/" : "%s://%s/") : ($atCore ? "%s://%s/%s/" : "%s://%s%s");
			$end = $atRoot ? ($atCore ? $core : $hostname) : ($atCore ? $core : $dir);
			$base_url = sprintf( $tmplt, $http, $hostname, $end );
		}
		else $base_url = 'http://localhost/';

		if ($parse) {
			$base_url = parse_url($base_url);
			if (isset($base_url['path'])) if ($base_url['path'] == '/') $base_url['path'] = '';
		}

		return $base_url;
	}
}
?><!DOCTYPE html>

<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>404 Page Not Found - EMS</title>

	<link href="https://a1.muscache.com/airbnb/static/packages/common_o2.1-f8a73ee0d4378e3442aa7772f248998e.css" media="all" rel="stylesheet" type="text/css">

	<link rel="shortcut icon" type="image/x-icon" href="https://a1.muscache.com/airbnb/static/logotype_favicon_pretzel-114df7f43fae7dd6dbc4ab074d934da5.ico">

</head>

<body>
<header class="page-container page-container-responsive space-top-4">
	<a href="/">
      <span class="screen-reader-only">
        QuoteMX
      </span>
	</a>
</header>

<div class="page-container page-container-responsive">
	<div class="row space-top-8 space-8 row-table">
		<div class="col-5 col-middle">
			<h1 class="text-jumbo text-ginormous">Oops!</h1>
			<h2>We can't seem to find the page you're looking for.</h2>
			<h6>Error code: 404</h6>
			<ul class="list-unstyled">
				<li>This page will be redirected to <a href="<?php echo base_url(); ?>">Home Page</a> within <span id="SecondsUntilReload"></span> seconds.</li>
			</ul>
		</div>
		<div class="col-5 col-middle text-center">
			<img src="https://a0.muscache.com/airbnb/static/error_pages/404-Airbnb_final-d652ff855b1335dd3eedc3baa8dc8b69.gif" width="313" height="428" alt="Girl has dropped her ice cream.">
		</div>
	</div>
</div>
</body>
</html>

<script type="text/javascript">
	var homePage = "<?php echo base_url(); ?>"
	var REFRESH_TIMEOUT = 10;
	var _reloadSecondsCounter = 1;
	refreshInterval = window.setInterval(CheckReloadTime, 1000);

	function CheckReloadTime() {
		_reloadSecondsCounter++;
		var oPanel = document.getElementById("SecondsUntilReload");
		if (oPanel){
			oPanel.innerHTML = (REFRESH_TIMEOUT - _reloadSecondsCounter) + "";
		}
		if (_reloadSecondsCounter >= REFRESH_TIMEOUT) {
			document.location.href = homePage;
		}
	}
</script>