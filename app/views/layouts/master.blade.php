<?php
$config = array(
	'appId' => Config::get('facebook.id'),
	'secret' => Config::get('facebook.secret'),
	'allowSignedRequest' => false
);
$facebook = new Facebook($config);
$loginUrl = $facebook->getLoginUrl(array(
	'scope' => 'email,user_groups',
	'display' => 'page'
));
$isLoggedIn = false;
try {
	$me = $facebook->api('/me');
	$isLoggedIn = true;
} catch (FacebookApiException $e) {
	$isLoggedIn = false;
}
if($isLoggedIn) {
	$groups = $facebook->api('/me/groups')['data'];
	$isEligible = false;
	foreach ($groups as $group) {
		if($group['id'] == "162895923753285") {
			$isEligible = true;
			break;
		}
	}
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>meSheet - The IT KMITL document organizer for sharing</title>
		<script src="{{ asset('js/jquery-2.0.3.min.js') }}"></script>
		<script src="{{ asset('js/jquery.history.js') }}"></script>
		<script src="{{ asset('js/bootstrap.min.js') }}"></script>
		<script>
			$("<script></script" + ">").attr("src", "//connect.facebook.net/th-TH/all.js").insertAfter("script:last");
			window.fbAsyncInit = function() {
				FB.init({
					appId: "{{ $facebook->getAppId() }}",
					status: true,
					cookie: true
				});
				FB.getLoginStatus(function(response) {
					switch(response.status) {
						case "connected":
							$(".greyOut").fadeOut();
							FB.api("/me", function(response) {
								$("#name").text(response.name);
								$("#userMenu").fadeIn();
							});
@if ($isLoggedIn)
@unless ($isEligible)
							$("#notificationModal").modal({
								show: true
							}).on("hidden.bs.modal", function() {
								FB.logout(function() {
									window.location.reload();
								});
							});
@endunless
@endif

							break;
						case "not_authorized":
						default:
							$("#loginButton").wrap($("<a></a>").attr("href", "{{ $loginUrl }}"));
							$("#loginModal").modal({
								backdrop: "static",
								keyboard: false,
								show: true
							});
							break;
					}
				});
			};
			$(document).ready(function() {
				$("a[href=\"#\"]").click(function(e) {
					e.preventDefault();
				});
				$("#logoutButton").click(function() {
					FB.logout(function() {
						window.location.reload();
					});
				});
				$("#userMenu").hide();
			});
		</script>
		<style type="text/css">
			.greyOut {
				background-color: #555555;
				background-repeat: no-repeat;
				background-position: center;
				opacity: 0.5;
				height: 100%;
				width: 100%;
				position: fixed;
				top: 0px;
				left: 0px;
				z-index: 1040;
			}
		</style>
		<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
@yield('head')
    </head>
    <body>
		<div id="fb-root"></div>
		<div class="greyOut"></div>
		<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="#">meSheet</a>
				</div>
				<div class="collapse navbar-collapse" id="bs-navbar-collapse-1">
					<ul class="nav navbar-nav navbar-right" id="userMenu">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span id="name"></span> <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a id="logoutButton" href="#">Logout</a></li>
							</ul>
						</li>
					</ul>
				</div>
			</div>
		</nav>

		<div class="modal fade" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true" id="loginModal" style="padding-top: 150px;">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">Login</h4>
					</div>
					<div class="modal-body">
						<p><img src="{{ asset("img/login.png") }}" class="img-responsive" id="loginButton" style="margin: 0 auto;"></p>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
		
		<div class="modal fade" role="dialog" aria-labelledby="notificationModalLabel" aria-hidden="true" id="notificationModal" style="padding-top: 150px;">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title">Notification</h4>
					</div>
					<div class="modal-body">
						<p>You are not eligible.</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->

		<div class="container">
			<ol class="breadcrumb" style="margin-bottom: 0;">
@yield('breadcrumb')
			</ol>

@yield('content')

			<hr>

			<footer>
				<div class="row">
					<div class="col-lg-12">
						<p>{{ StringHelper::prettifyText('Power by IT KMITL') }}</p>
					</div>
				</div>
			</footer>

		</div>
    </body>
</html>
