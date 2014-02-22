<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>meSheet - The IT KMITL document organizer for sharing</title>
		<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
		<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
		<script src="{{ asset('js/jquery-2.0.3.min.js') }}"></script>
		<script src="{{ asset('js/jquery.history.js') }}"></script>
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
								$.get("{{ url('/login') }}");
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
				$("a[data-toggle='tab']").on("show.bs.tab", function() {
					$("#requestAccordion .collapse").each(function() {
						if ($(this).hasClass("in")) {
							$(this).collapse("hide");
						}
					});
					$("#fileAccordion .collapse").each(function() {
						if ($(this).hasClass("in")) {
							$(this).collapse("hide");
						}
					});
				});
			});
		</script>
		<style type="text/css">
			body {
				padding-top: 70px;
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
				left: 50%;
			}
			.listBox {
				margin-top: 20px;
				margin-left: 3%;
				margin-right: 3%;
			}
		</style>
    </head>
    <body>
		<div id="fb-root"></div>
		<script>(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return;
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=716966921722728";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));</script>
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
			<ol class="breadcrumb">
				<li>{{ link_to('/', 'Home') }}</li>
				<li>{{ link_to('/' . $year, ucwords($year)) }}</li>
				<li>{{ link_to('/' . $year . '/' . $category, ucwords($category)) }}</li>
				<li class="active">{{ ucwords($subject) }}</li>
			</ol>

			<ul class="nav nav-tabs" id="listTab">
				<li class="active"><a href="#fileList" data-toggle="tab">File List</a></li>
				<li><a href="#requestList" data-toggle="tab">Request List</a></li>
			</ul>

			<div class="tab-content">
				<div class="tab-pane fade in active" id="fileList">
					<div class="listBox">
						<div class="panel-group" id="fileAccordion">
@if (count($fileList) > 0)
@foreach ($fileList as $file)
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#fileAccordion" href="#fileCollapse{{ $file->id }}">
											{{ $file->title }}
											<span class="badge">{{ $file->like_count }}</span>
										</a>
									</h4>
								</div>
								<div id="fileCollapse1" class="panel-collapse collapse">
									<div class="panel-body">
										<div>
											Description: {{ $file->description }}
										</div>
										<div>
											Size: {{ $file->filesize }}
										</div>
										<div>
											Upload time: {{ $file->created_at }}
										</div>
										<div>
											Author: {{ $file->author_id }}
										</div>
										<div>
											<div class="fb-like" data-href="{{ $file->url }}" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
										</div>
										<div>
											<a href="{{ $file->filepath }}" class="btn btn-success btn-sm" role="button">
												<span class="glyphicon glyphicon-download-alt"></span>
												Download
											</a>
										</div>
									</div>
								</div>
							</div>
@endforeach
@else
							<div class="panel panel-info">
								<div class="panel-heading">
									<h4 class="panel-title">
										No file
									</h4>
								</div>
									<div class="panel-body">
										No file
									</div>
							</div>
@endif
						</div>
						
						<button type="button" class="btn btn-primary">
							<i class="fa fa-upload"></i>
							Upload File
						</button>
					</div>
				</div>
				<div class="tab-pane fade" id="requestList">
					<div class="listBox">
						<div class="panel-group" id="requestAccordion">
@if ($requestList->count() > 0)
@foreach ($requestList as $request)
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#requestAccordion" href="#requestCollapse{{ $request->id }}">
											{{ $request->title }}
										</a>
									</h4>
								</div>
								<div id="requestCollapse1" class="panel-collapse collapse">
									<div class="panel-body">
										{{ $request->message }}
									</div>
								</div>
							</div>
@endforeach
@else
							<div class="panel panel-info">
								<div class="panel-heading">
									<h4 class="panel-title">
										No request
									</h4>
								</div>
									<div class="panel-body">
										No request
									</div>
							</div>
@endif
						</div>
						
						<button type="button" class="btn btn-primary">
							<span class="glyphicon glyphicon-file"></span>
							Request File
						</button>
					</div>
				</div>
			</div>
		</div>
    </body>
</html>
