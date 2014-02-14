<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>meSheet - The IT KMITL document organizer for sharing</title>
		<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
		<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
		<script src="{{ asset('js/jquery-2.0.3.min.js') }}"></script>
		<script src="{{ asset('js/bootstrap.min.js') }}"></script>
		<script src="//connect.facebook.net/th_TH/all.js"></script>
		<script>
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
							break;
						case "not_authorized":
						default:
							$("#loginButton").css({
								cursor: "pointer"
							}).click(function() {
								FB.login(function(response) {
									window.location.href = "{{ url('/newuser') }}"
								}, {
									scope: "email,user_groups"
								});
							});
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
						window.location.href = "{{ url('/logout') }}";
					});
				});
				$("#userMenu").hide();
				$("#categories a[href^=\"#\"]").click(function(e) {
					e.preventDefault();
					var category = $(this).attr("href").replace("#", "");
					window.location.href = "{{ url('/' . $year) }}/" + category;
				});
				$("#categories img").mouseover(function() {
					$(this).removeClass("img-circle");
					$(this).addClass("img-rounded");
				});
				$("#categories img").mouseout(function() {
					$(this).removeClass("img-rounded");
					$(this).addClass("img-circle");
				});
			});
		</script>
		<style type="text/css">
			body {
				padding-top: 70px;
			}
			#categories img {
				transition: all 0.5s ease 0s;
			}
			#categories > div {
				margin-top: 5pt;
				margin-bottom: 5pt;
			}
			.greyOut {
				position: fixed;
				top: 0px;
				left: 0px;
				height: 100%;
				width: 100%;
				background-color: #555555;
				background-repeat: no-repeat;
				background-position: center;
				text-align: center;
				opacity: 0.5;
				z-index: 1040;
			}
			.greyOut i {
				position: absolute;
				top: 40%;
			}
		</style>
	</head>
	<body>
		<div id="fb-root"></div>
		<div class="greyOut">
			<i class="fa fa-spinner fa-spin fa-5x" style="color: #1995DC;"></i>
		</div>
		<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="{{ url('/') }}">meSheet</a>
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

		<div class="container">
			<ol class="breadcrumb" style="margin-bottom: 0;">
				<li>{{ link_to('/', 'Home') }}</li>
				<li class="active">{{ ucwords($year) }}</li>
			</ol>

			<div class="row">
				<div class="col-lg-12">
					<h2 class="page-header">Choose your subject category</h2>
				</div>
			</div>

			<div id="categories" class="row">
				<div class="col-lg-6 col-sm-6">
					<a href="#programming">
						<img class="img-circle img-responsive center-block" src="http://placehold.it/300&text=Programming">
					</a>
				</div>
				<div class="col-lg-6 col-sm-6">
					<a href="#network">
						<img class="img-circle img-responsive center-block" src="http://placehold.it/300&text=Network">
					</a>
				</div>
				<div class="col-lg-6 col-sm-6">
					<a href="#multimedia">
						<img class="img-circle img-responsive center-block" src="http://placehold.it/300&text=Multimedia">
					</a>
				</div>
				<div class="col-lg-6 col-sm-6">
					<a href="#business">
						<img class="img-circle img-responsive center-block" src="http://placehold.it/300&text=Business">
					</a>
				</div>
				<div class="col-lg-6 col-sm-6">
					<a href="#other">
						<img class="img-circle img-responsive center-block" src="http://placehold.it/300&text=Other">
					</a>
				</div>
			</div>

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