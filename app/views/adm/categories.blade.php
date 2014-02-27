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
			var loadCategories = function() {
				$("#categoryList").fadeOut(function () {
					$("#noCategory").fadeOut(function() {
						$("#loading").fadeIn(function() {
							$.getJSON("{{ url('/adm/categories/list') }}", function(response) {
								var $categoryList = $("#categoryList");
								$categoryList.empty();
								if(response.length > 0) {
									for(var i = 0; i < response.length; i++) {
										var $item = $("<a></a>").attr({
											id: "category" + response[i].id,
											href: "#"
										}).addClass("list-group-item");
										$item.html(response[i].name);
										$item.click(function(e) {
											e.preventDefault();
											$(this).parent().children().removeClass("active");
											$(this).addClass("active");
										});
										$categoryList.append($item);
									}
									$("#loading").fadeOut(function() {
										$("#categoryList").fadeIn();
									});
								} else {
									$("#loading").fadeOut(function() {
										$("#noCategory").fadeIn();
									});
								}
							});
						});
					});
				});
			};
			var confirmRemove = function(category, callback) {
				var result = false;
				$("#confirmModal button:contains('Yes')").off("click").click(function() {
					result = true;
					$(this).parents("#confirmModal").modal("hide");
				});
				$("#confirmModal .modal-body b").text(category);
				$("#confirmModal").modal("show").off("hidden.bs.modal").on("hidden.bs.modal", function() {
					callback(result);
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
				$("form").submit(function(e) {
					e.preventDefault();
				});
				$("#userMenu").hide();
				$("#categoryList").hide();
				loadCategories();
				$("#reloadButton").click(loadCategories);
				$("#addButton").click(function() {
					var $categoryList = $("#categoryList");
					var $editor = $("<a></a>").attr("id", "newCategoryEditor").addClass("list-group-item active");
					$categoryList.children().removeClass("active");
					$categoryList.find("#newCategoryEditor").remove();
					$editor.append($("<input></input>").attr({
						type: "text",
						title: "Press enter to add.<br>Press esc to cancel."
					}).tooltip({
						html: true
					}).addClass("form-control").keypress(function(e) {
						switch(e.keyCode) {
							case 13:
								e.preventDefault();
								if($(this).val().length > 0) {
									$.post("{{ url('/adm/categories/update') }}", {
										name: $(this).val()
									}, function(response) {
										if(response.success) {
											$("#feedback span").removeClass("error").text("Added");
											$("#feedback").removeClass("error").fadeIn({
												duration: 1500,
												complete: function() {
													$(this).delay(1500).fadeOut();
												}
											});
										} else {
											$("#feedback span").text(response.error_message).addClass("error");
											$("#feedback").addClass("error").fadeIn({
												duration: 1500,
												complete: function() {
													$(this).delay(1500).fadeOut();
												}
											});
										}
										loadCategories();
									}, "json");
								}
								break;
							case 27:
								$editor.remove();
								if($categoryList.children().length == 0) {
									$categoryList.fadeOut(function() {
										$("#noCategory").fadeIn();
									});
								}
								break;
						}
					}));
					$categoryList.append($editor);
					$("#noCategory").fadeOut(function() {
						$categoryList.fadeIn(function() {
							$editor.find("input").focus();
						});
					});
				});
				$("#editButton").click(function() {
					var $categoryList = $("#categoryList");
					var $selectedCategory = $categoryList.find(".active:not(:has(input))");
					$categoryList.find("a[id^='categoryEditor']").remove();
					$categoryList.find("a").show();
					if($selectedCategory.length != 0) {
						var editorId = "categoryEditor" + $selectedCategory.attr("id").replace (/[^\d.]/g, "");
						if($("#" + editorId).length == 0) {
							var $editor = $("<a></a>").attr("id", editorId).addClass("list-group-item active");
							$editor.append($("<input></input>").attr({
								type: "text",
								title: "Press enter to update.<br>Press esc to cancel."
							}).tooltip({
								html: true
							}).addClass("form-control").val($selectedCategory.text()).keypress(function(e) {
								switch(e.keyCode) {
									case 13:
										e.preventDefault();
										if($(this).val().length > 0) {
											$.post("{{ url('/adm/categories/update') }}", {
												id: $editor.attr("id").replace (/[^\d.]/g, ""),
												name: $(this).val()
											}, function(response) {
												if(response.success) {
													$("#feedback span").removeClass("error").text("Updated");
													$("#feedback").removeClass("error").fadeIn({
														duration: 1500,
														complete: function() {
															$(this).delay(1500).fadeOut();
														}
													});
												} else {
													$("#feedback span").text(response.error_message).addClass("error");
													$("#feedback").addClass("error").fadeIn({
														duration: 1500,
														complete: function() {
															$(this).delay(1500).fadeOut();
														}
													});
												}
												loadCategories();
											}, "json");
										}
										break;
									case 27:
										$editor.remove();
										$selectedCategory.show();
										break;
								}
							}));
							$selectedCategory.hide();
							$selectedCategory.after($editor);
							$editor.find("input").focus();
						}
					}
				});
				$("#removeButton").click(function() {
					var $categoryList = $("#categoryList");
					var $selectedCategory = $categoryList.find(".active:not(:has(input))");
					if($selectedCategory.length != 0) {
						confirmRemove($selectedCategory.text(), function(result) {
							if(result) {
								$.post("{{ url('/adm/categories/remove') }}", {
									id: $selectedCategory.attr("id").replace (/[^\d.]/g, "")
								}, function(response) {
									if(response.success) {
										$("#feedback span").removeClass("error").text("Removed");
										$("#feedback").removeClass("error").fadeIn({
											duration: 1500,
											complete: function() {
												$(this).delay(1500).fadeOut();
											}
										});
									} else {
										$("#feedback span").text(response.error_message).addClass("error");
										$("#feedback").addClass("error").fadeIn({
											duration: 1500,
											complete: function() {
												$(this).delay(1500).fadeOut();
											}
										});
									}
								}, "json");
								loadCategories();
							}
						});
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
			#feedback {
				position: absolute;
				top: 50%;
				left: 50%;
				width: 300px;
				margin-left: -150px;
				margin-top: -100px;
				padding: 20px;
				text-align: center;
				background-color: rgba(47, 164, 231, 0.5);
				border-radius: 5px;
				z-index: 10;
			}
			#feedback.error {
				background-color: rgba(255, 100, 100, 0.5);
			}
			#feedback span {
				color: #555555;
				font-size: 18px;
				word-wrap: break-word;
			}
			#feedback span.error {
				color: red;
			}
		</style>
    </head>
    <body>
		<div id="fb-root"></div>
		<div class="greyOut">
			<i class="fa fa-spinner fa-spin fa-5x" style="color: #1995DC;"></i>
		</div>
		<div id="feedback" style="display: none;">
			<span>
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
								<li><a href="{{ url('/adm/categories') }}">Manage categories</a></li>
								<li><a href="{{ url('/adm/subjects') }}">Manage subjects</a></li>
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
				</div>
			</div>
		</div>

		<div class="modal fade" role="dialog" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true" id="confirmModal" style="padding-top: 150px;">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title">Remove?</h4>
					</div>
					<div class="modal-body">
						Do you want to remove <b></b> category?
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">No</button>
						<button type="button" class="btn btn-danger">Yes</button>
					</div>
				</div>
			</div>
		</div>

		<div class="container" style="margin-bottom: 50px;">
			<ol class="breadcrumb">
				<li><a href="#">Management</a></li>
				<li class="active">Categories</li>
			</ol>

			<form role="form">
				<div class="row" style="margin-top: 30px; padding-left: 15px; padding-right: 15px;">
					<div class="col-lg-12" style="border-radius: 4px; background-color: rgba(47, 164, 231, 0.2); padding-top: 15px; padding-bottom: 15px;">
						<button type="button" id="addButton" class="btn btn-default">
							<i class="fa fa-plus-circle"></i>
							Add
						</button>
						<button type="button" id="editButton" class="btn btn-default">
							<i class="fa fa-edit"></i>
							Edit
						</button>
						<button type="button" id="removeButton" class="btn btn-danger">
							<i class="fa fa-minus-circle"></i>
							Remove
						</button>
						<button type="button" id="reloadButton" class="btn btn-default pull-right">
							<i class="fa fa-refresh"></i>
							Reload
						</button>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<h3>
							Categories:
						</h3>
						<div id="loading" style="font-size: 20px;">
							<i class="fa fa-refresh fa-spin"></i>
							Loading...
						</div>
						<div id="noCategory" class="panel panel-info" style="display: none;">
							<div class="panel-heading">
								<h3 class="panel-title">No category</h3>
							</div>
							<div class="panel-body">
								No category
							</div>
						</div>
						<div id="categoryList" class="list-group">
						</div>
					</div>
				</div>
			</form>
		</div>
    </body>
</html>
