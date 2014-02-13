<!DOCTYPE html>
<html>
    <head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Forbidden</title>
		<script src="{{ asset('js/jquery-2.0.3.min.js') }}"></script>
		<script src="{{ asset('js/bootstrap.min.js') }}"></script>
		<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
		<script>
			$(document).ready(function() {
				$("a[href=\"#\"]").click(function(e) {
					e.preventDefault();
				});
			});
		</script>
		<style type="text/css">
			body {
				padding-top: 170px;
			}
		</style>
    </head>
    <body>
		<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
			<div class="container">
				<div class="navbar-header">
					<a class="navbar-brand" href="{{ url('/') }}">meSheet</a>
				</div>
			</div>
		</nav>

		<div class="container">
			<div class="jumbotron">
				<h1>You are not eligible.</h1>
				<p>You are not IT KMITL student.</p>
			</div>
		</div>
    </body>
</html>
