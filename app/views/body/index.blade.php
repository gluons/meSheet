@extends('layouts.body')

@section('breadcrumb')
				<li class="active">Home</li>
@stop

@section('content')
			<div class="row">
				<div class="col-lg-12">
					<h2 class="page-header">Choose your college years</h2>
				</div>
			</div>

			<div id="college-years" class="row">
				<div class="col-lg-6 col-sm-6">
					<a href="#freshman">
						<img class="img-circle img-responsive center-block" src="http://placehold.it/300&text=Freshman">
					</a>
				</div>
				<div class="col-lg-6 col-sm-6">
					<a href="#sophomore">
						<img class="img-circle img-responsive center-block" src="http://placehold.it/300&text=Sophomore">
					</a>
				</div>
				<div class="col-lg-6 col-sm-6">
					<a href="#junior">
						<img class="img-circle img-responsive center-block" src="http://placehold.it/300&text=Junior">
					</a>
				</div>
				<div class="col-lg-6 col-sm-6">
					<a href="#senior">
						<img class="img-circle img-responsive center-block" src="http://placehold.it/300&text=Senior">
					</a>
				</div>
			</div>
@stop