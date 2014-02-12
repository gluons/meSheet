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
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Someone User <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li><a id="logout" href="#">Logout</a></li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
</nav>

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