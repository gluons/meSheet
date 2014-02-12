@extends('layouts.master')

@section('head')
		<style type="text/css">		
			body {
				padding-top: 70px;
			}
			#college-years img {
				transition: all 0.5s ease 0s;
			}
			#college-years > div {
				margin-top: 5pt;
				margin-bottom: 5pt;
			}
		</style>
		<script>
			$(document).ready(function() {
				$("#college-years a[href^=\"#\"]").click(function(e) {
					e.preventDefault();
				});
				$("#college-years a").click(function() {
					// Show categories
				});
				$("#college-years img").mouseover(function() {
					$(this).removeClass("img-circle");
					$(this).addClass("img-rounded");
				});
				$("#college-years img").mouseout(function() {
					$(this).removeClass("img-rounded");
					$(this).addClass("img-circle");
				});
			});
		</script>
@stop

@section('breadcrumb')
	@include('body.index')
@stop

@section('content')
	@include('body.index')
@stop