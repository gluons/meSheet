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
		<script src="//code.jquery.com/ui/1.10.4/jquery-ui.min.js"></script>
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
@if ($errorMsg != null)

								$("#errorMsg").effect({
									effect: "pulsate",
									times: 3,
									duration: 1500,
									complete: function() {
										$(this).delay(1500).fadeOut();
									}
								});
@endif
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
			// Get topic
			var getTopic = function() {
				$fileAccordion = $("#fileAccordion")
				$fileAccordion.fadeOut(function() {
					$fileAccordion.children("div:gt(0)").remove();
					$("#noFile").hide();
					$("#fileLoading").fadeIn(function() {
						$.getJSON("{{ url('/topics/' . $subjectId) }}", function(response) {
							$.get("{{ asset('template/topic.html') }}", function(topic) {
								var $topic = $(topic);
								for(var i = 0; i < response.length; i++) {
									$item = $topic.clone();
									$item.find(".panel-title a").attr("href", "#fileCollapse" + response[i].id).attr("data-url", "{{ url('/' . $year . '/' . $category . '/' . $subjectId) }}/topic/" + response[i].id);
									$item.find(".panel-title a > span:first").html(response[i].title);
									$.getJSON("https://graph.facebook.com/fql?q=" + "SELECT total_count FROM link_stat WHERE url= '" + $item.find(".panel-title a").attr("data-url") + "'", function(fbResponse) {
										if(fbResponse.data.length != 0) {
											$item.find(".panel-title span.badge").text(fbResponse.data[0].total_count);
										} else {
											$item.find(".panel-title span.badge").text("0");
										}
									});
									$item.find(".panel-collapse").attr("id", "fileCollapse" + response[i].id);
									$item.find(".panel-collapse #fileDescription").html(response[i].description);
									$item.find(".panel-collapse #fileSize").text(response[i].filesize);
									$item.find(".panel-collapse #fileUploadTime").text(response[i].created_at.date);
									$item.find(".panel-collapse #fileAuthor").attr("href", response[i].author_url).text(response[i].author);
									$item.find(".panel-collapse #filePopularity > iframe").prop("src", "//www.facebook.com/plugins/like.php?href=" + $item.find(".panel-title a").attr("data-url") + "&amp;width&amp;layout=button_count&amp;action=like&amp;show_faces=false&amp;share=false&amp;height=21&amp;appId=716966921722728");
									$item.find(".panel-collapse #filePopularity > div").attr("data-href", $item.find(".panel-title a").attr("data-url"));
									$item.find(".panel-collapse #fileDownload").attr("href", "{{ url('/download/' . $year . '/' . $category . '/' . $subjectId) }}/" + response[i].id);
									$item.find(".collapse").on("shown.bs.collapse", function() {
										History.pushState(null, document.title, $(this).parent(".panel").find(".panel-title a").attr("data-url"));
									});
									$item.find(".collapse").on("hide.bs.collapse", function() {
										History.pushState(null, document.title, "{{ url('/' . $year . '/' . $category . '/' . $subjectId) }}");
									});
@if (isset($topicId))
									if(response[i].id == topicId) {
										$item.find(".collapse").addClass("in");
									}
@endif
									$fileAccordion.append($item);
								}
								$("#fileLoading").fadeOut(function () {
									if(response.length > 0) {
										$fileAccordion.fadeIn();
									} else {
										$("#noFile").show(function() {
											$fileAccordion.slideDown();
										})
									}
								});
							}, "html");
						});
					});
				});
			};
			// Get request
			var getRequest = function() {
				$requestAccordion = $("#requestAccordion");
				$requestAccordion.fadeOut(function() {
					$requestAccordion.children("div:gt(0)").remove();
					$("#noRequest").hide();
					$("#requestLoading").fadeIn(function() {
						$.getJSON("{{ url('/requests/' . $subjectId) }}", function(response) {
							$.get("{{ asset('template/request.html') }}", function(request) {
								var $request = $(request);
								for(var i = 0; i < response.length; i++) {
									$item = $request.clone();
									$item.find(".panel-title a").attr("href", "#requestCollapse" + response[i].id).attr("data-url", "{{ url('/' . $year . '/' . $category . '/' . $subjectId) }}/request/" + response[i].id).html(response[i].title);
									$item.find(".panel-collapse").attr("id", "requestCollapse" + response[i].id);
									$item.find(".panel-collapse #requestAuthor").attr("href", response[i].author_url).text(response[i].author);
									$item.find(".panel-collapse #requestMessage").html(response[i].message);
									$item.find(".collapse").on("shown.bs.collapse", function() {
										History.pushState(null, document.title, $(this).parent(".panel").find(".panel-title a").attr("data-url"));
									});
									$item.find(".collapse").on("hide.bs.collapse", function() {
										History.pushState(null, document.title, "{{ url('/' . $year . '/' . $category . '/' . $subjectId) }}/request");
									});
@if (isset($requestId))
									if(response[i].id == requestId) {
										$item.find(".collapse").addClass("in");
									}
@endif
									$requestAccordion.append($item);
								}
								$("#requestLoading").fadeOut(function () {
									if(response.length > 0) {
										$requestAccordion.fadeIn();
									} else {
										$("#noRequest").show(function() {
											$requestAccordion.slideDown();
										})
									}
								});
							}, "html");
						});
					});
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
					$("#newFileForm").hide(function() {
						$("#newFileForm")[0].reset();
						$("#fileListContainer").show();
					});
					$("#newRequestForm").hide(function() {
						$("#newRequestForm")[0].reset();
						$("#requestListContainer").show();
					});
				});
				$("#listTab a[href='#fileList']").on("shown.bs.tab", function() {
					History.pushState(null, document.title, "{{ url('/' . $year . '/' . $category . '/' . $subjectId) }}");
				});
				$("#listTab a[href='#fileList']").click(function() {
					getTopic();
				})
				$("#listTab a[href='#requestList']").on("shown.bs.tab", function() {
					if(!/.+\/request\/\d$/.test(window.location.href)) {
						History.pushState(null, document.title, "{{ url('/' . $year . '/' . $category . '/' . $subjectId) }}/request");
					}
				});
				$("#listTab a[href='#requestList']").click(function() {
					getRequest();
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
				$("#newFileForm").submit(function() {
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
				$("#newRequestForm").submit(function(e) {
					e.preventDefault();
					$(".greyOut").fadeIn(function() {
						$.post("{{ url('/' . $year . '/' . $category . '/' . $subjectId) }}/request", $("#newRequestForm").serializeArray(), function(response) {
							if(response.success == true) {
								$(".greyOut").fadeOut(function() {
									$("#newRequestForm").fadeOut(function() {
										$("#newRequestForm")[0].reset();
										$("#requestListContainer").fadeIn(function() {
											getRequest();
										});
									});
								});
							} else {
								$(".greyOut").fadeOut(function() {
									$("#errorMsg span").text("Fail to add new request.");
									$("#errorMsg").effect({
										effect: "pulsate",
										times: 3,
										duration: 1500,
										complete: function() {
											$(this).delay(1500).fadeOut();
										}
									});
									$("#newRequestForm").fadeOut(function() {
										$("#newRequestForm")[0].reset();
										$("#requestListContainer").fadeIn(function() {
											getRequest();
										});
									});
								})
							}
						}, "json");
					});
				});
				$("#newRequestCancelButton").click(function() {
					$("#newRequestForm").fadeOut(function() {
						$("#newRequestForm")[0].reset();
						$("#requestListContainer").fadeIn();
					});
				});

				getTopic();
				getRequest();

@if (isset($requestId) || (isset($isRequest) && $isRequest))
				$("#listTab a[href='#requestList']").tab("show");
@endif
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
			#errorMsg {
				position: absolute;
				top: 50%;
				left: 50%;
				width: 300px;
				margin-left: -150px;
				margin-top: -100px;
				padding: 20px;
				text-align: center;
				background-color: rgba(255, 100, 100, 0.5);
				border-radius: 5px;
				z-index: 10;
			}
			#errorMsg span {
				color: black;
				font-size: 18px;
				word-wrap: break-word;
			}
		</style>
    </head>
    <body>
		<div id="fb-root"></div>
		<div class="greyOut">
			<i class="fa fa-spinner fa-spin fa-5x" style="color: #1995DC;"></i>
		</div>
		<div id="errorMsg" style="display: none;">
			<span style="color: red;">
@if ($errorMsg != null)
				{{ $errorMsg }}

@endif
			</span>
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
@if ($isRoot)
								<li><a href="{{ url('/adm/categories') }}">Manage categories</a></li>
								<li><a href="{{ url('/adm/subjects') }}">Manage subjects</a></li>
@endif
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
							<div id="fileLoading" style="margin-bottom: 20px;">
								<i class="fa fa-refresh fa-spin"></i>
								Loading...
							</div>
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

						<form role="form" name="newFileForm" id="newFileForm" action="{{ url('/' . $year . '/' . $category . '/' . $subjectId) }}/topic" method="post" enctype="multipart/form-data">
							<div class="form-group">
								<label for="newFileTitle">Title</label>
								<input type="text" class="form-control" name="newFileTitle" id="newFileTitle" placeholder="Enter title" required="yes">
							</div>
							<div class="form-group">
								<label for="newFileDescription">Description</label>
								<textarea class="form-control" rows="3" name="newFileDescription" id="newFileDescription" placeholder="Enter description" required="yes" style="resize: vertical;"></textarea>
							</div>
							<div class="form-group">
								<label for="newFileFile">File</label>
								<input type="file" name="newFileFile" id="newFileFile" required="yes">
							</div>
							<button type="submit" id="newFileSubmitButton" class="btn btn-primary">
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
							<div id="requestLoading" style="margin-bottom: 20px;">
								<i class="fa fa-refresh fa-spin"></i>
								Loading...
							</div>
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

						<form role="form" name="newRequestForm" id="newRequestForm">
							<div class="form-group">
								<label for="newRequestTitle">Title</label>
								<input type="text" class="form-control" name="newRequestTitle" id="newRequestTitle" placeholder="Enter title" required="yes">
							</div>
							<div class="form-group">
								<label for="newRequestDescription">Message</label>
								<textarea class="form-control" rows="3" name="newRequestMessage" id="newRequestMessage" placeholder="Enter message" required="yes" style="resize: vertical;"></textarea>
							</div>
							<button type="submit" id="newRequestSubmitButton" class="btn btn-primary">
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
