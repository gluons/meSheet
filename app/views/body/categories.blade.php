@extends('layouts.body')

@section('breadcrumb')
				<li>{{ link_to('/', 'Home') }}</li>
				<li class="active">Categories</li>
@stop

@section('content')
			<div class="row">
				<div class="col-lg-12">
					<h2 class="page-header">Choose your subject categories</h2>
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
@stop