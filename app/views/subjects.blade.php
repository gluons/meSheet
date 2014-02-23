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
@if (isset($topicId))
			var topicId = {{ $topicId }};
@endif
@if (isset($requestId))
			var requestId = {{ $requestId }};
@endif
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
						window.location.href = "{{ url('/logout') }}?next=" + window.location.href;
					});
				});
				$("#userMenu").hide();
				$("#newFileForm").hide();
				$("#newRequestForm").hide();
				$("a[data-toggle='tab']").on("show.bs.tab", function() {
					$("#fileAccordion .collapse").each(function() {
						if ($(this).hasClass("in")) {
							$(this).collapse("hide");
						}
					});
					$("#requestAccordion .collapse").each(function() {
						if ($(this).hasClass("in")) {
							$(this).collapse("hide");
						}
					});
				});
				$("#listTab a[href='#fileList']").on("shown.bs.tab", function() {
					History.pushState(null, document.title, "{{ url('/' . $year . '/' . $category . '/' . $subjectId) }}");
				});
				$("#listTab a[href='#requestList']").on("shown.bs.tab", function() {
					History.pushState(null, document.title, "{{ url('/' . $year . '/' . $category . '/' . $subjectId) }}/request");
				});
				// New file
				$("#newFileButton").click(function() {
					History.pushState(null, document.title, "{{ url('/' . $year . '/' . $category . '/' . $subjectId) }}");
					$("#fileAccordion .collapse").each(function() {
						if ($(this).hasClass("in")) {
							$(this).collapse("hide");
						}
					});
					$("#fileListContainer").fadeOut(function() {
						$("#newFileForm").fadeIn();
					});
				});
				$("#newFileSubmitButton").click(function() {
					$(".greyOut").fadeIn();
				});
				$("#newFileCancelButton").click(function() {
					$("#newFileForm").fadeOut(function() {
						$("#newFileForm")[0].reset();
						$("#fileListContainer").fadeIn();
					});
				});
				// New request
				$("#newRequestButton").click(function() {
					History.pushState(null, document.title, "{{ url('/' . $year . '/' . $category . '/' . $subjectId) }}/request");
					$("#requestAccordion .collapse").each(function() {
						if ($(this).hasClass("in")) {
							$(this).collapse("hide");
						}
					});
					$("#requestListContainer").fadeOut(function() {
						$("#newRequestForm").fadeIn();
					});
				});
				$("#newRequestSubmitButton").click(function() {
					$(".greyOut").fadeIn();
				});
				$("#newRequestCancelButton").click(function() {
					$("#newRequestForm").fadeOut(function() {
						$("#newRequestForm")[0].reset();
						$("#requestListContainer").fadeIn();
					});
				});
				$.get("{{ asset('template/topics.html') }}", function(response) {
					$("#noFile").hide();
					$response = $(response);
					for(var i = 1; i <= 3; i++) {
						$item = $response.clone();
						$item.find(".panel-title a").attr("href", "#fileCollapse" + i).attr("data-url", "{{ url('/' . $year . '/' . $category . '/' . $subjectId) }}/topic/" + i);
						$item.find(".panel-title a > span:first").text("Title " + i);
						$item.find(".panel-title span.badge").text({{ FacebookHelper::getTotalCount('https://www.facebook.com/') }});
						$item.find(".panel-collapse").attr("id", "fileCollapse" + i);
						$item.find(".panel-collapse #fileDescription").text("Description " + i);
						$item.find(".panel-collapse #fileSize").text("Size " + i);
						$item.find(".panel-collapse #fileUploadTime").text("Upload time " + i);
						$item.find(".panel-collapse #fileAuthor").attr("href", "https://www.facebook.com/").text("Author " + i);
						$item.find(".panel-collapse #fileDownload").attr("href", "{{ url('/download/' . $year . '/' . $category . '/' . $subjectId) }}/" + i);
						$item.find(".collapse").on("shown.bs.collapse", function() {
							History.pushState(null, document.title, $(this).parent(".panel").find(".panel-title a").attr("data-url"));
						});
						$item.find(".collapse").on("hide.bs.collapse", function() {
							History.pushState(null, document.title, "{{ url('/' . $year . '/' . $category . '/' . $subjectId) }}");
						});
@if (isset($topicId))
						if(i == topicId) {
							$item.find(".collapse").addClass("in");
						}
@endif
						$("#fileAccordion").append($item);
					}
				}, "html");
@if (isset($requestId) || (isset($isRequest) && $isRequest))
				$("#listTab a[href='#requestList']").tab("show");
@endif
				$.get("{{ asset('template/requests.html') }}", function(response) {
					$("#noRequest").hide();
					$response = $(response);
					for(var i = 1; i <= 3; i++) {
						$item = $response.clone();
						$item.find(".panel-title a").attr("href", "#requestCollapse" + i).attr("data-url", "{{ url('/' . $year . '/' . $category . '/' . $subjectId) }}/request/" + i).text("Title " + i);
						$item.find(".panel-collapse").attr("id", "requestCollapse" + i);
						$item.find(".panel-body").text("Message " + i);
						$item.find(".collapse").on("shown.bs.collapse", function() {
							History.pushState(null, document.title, $(this).parent(".panel").find(".panel-title a").attr("data-url"));
						});
						$item.find(".collapse").on("hide.bs.collapse", function() {
							History.pushState(null, document.title, "{{ url('/' . $year . '/' . $category . '/' . $subjectId) }}/request");
						});
@if (isset($requestId))
						if(i == requestId) {
							$item.find(".collapse").addClass("in");
						}
@endif
						$("#requestAccordion").append($item);
					}
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
				margin-bottom: 50px;
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
						<div id="fileListContainer">
							<div class="panel-group" id="fileAccordion">
								<div id="noFile" class="panel panel-info">
									<div class="panel-heading">
										<h4 class="panel-title">
											No file
										</h4>
									</div>
									<div class="panel-body">
										No file
									</div>
								</div>
							</div>

							<button type="button" id="newFileButton" class="btn btn-primary">
								<i class="fa fa-upload"></i>
								Upload File
							</button>
						</div>

						<form role="form" id="newFileForm">
							<div class="form-group">
								<label for="newFileTitle">Title</label>
								<input type="text" class="form-control" id="newFileTitle" placeholder="Enter title">
							</div>
							<div class="form-group">
								<label for="newFileDescription">Description</label>
								<textarea class="form-control" rows="3" id="newFileDescription" placeholder="Enter description" style="resize: vertical;"></textarea>
							</div>
							<div class="form-group">
								<label for="newFileFile">File</label>
								<input type="file" id="newFileFile">
							</div>
							<button type="button" id="newFileSubmitButton" class="btn btn-primary">
								<i class="fa fa-upload"></i>
								Upload New File
							</button>
							<button type="button" id="newFileCancelButton" class="btn btn-default">
								<i class="fa fa-ban"></i>
								Cancel
							</button>
						</form>
					</div>
				</div>
				<div class="tab-pane fade" id="requestList">
					<div class="listBox">
						<div id="requestListContainer">
							<div class="panel-group" id="requestAccordion">
								<div id="noRequest" class="panel panel-info">
									<div class="panel-heading">
										<h4 class="panel-title">
											No request
										</h4>
									</div>
									<div class="panel-body">
										No request
									</div>
								</div>
							</div>

							<button type="button" id="newRequestButton" class="btn btn-primary">
								<span class="glyphicon glyphicon-file"></span>
								Request File
							</button>
						</div>

						<form role="form" id="newRequestForm">
							<div class="form-group">
								<label for="newRequestTitle">Title</label>
								<input type="text" class="form-control" id="newRequestTitle" placeholder="Enter title">
							</div>
							<div class="form-group">
								<label for="newRequestDescription">Description</label>
								<textarea class="form-control" rows="3" id="newRequestDescription" placeholder="Enter description" style="resize: vertical;"></textarea>
							</div>
							<button type="button" id="newRequestSubmitButton" class="btn btn-primary">
								<span class="glyphicon glyphicon-file"></span>
								New Request
							</button>
							<button type="button" id="newRequestCancelButton" class="btn btn-default">
								<i class="fa fa-ban"></i>
								Cancel
							</button>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			window.___gcfg = {lang: 'th'};

			(function() {
			  var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
			  po.src = 'https://apis.google.com/js/platform.js';
			  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
			})();
		</script>
    </body>
</html>
