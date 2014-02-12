@extends('layouts.master')

@section('head')
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
		</style>
		<script>
			$(document).ready(function() {
				$("#categories a[href^=\"#\"]").click(function(e) {
					e.preventDefault();
				});
				$("ol.breadcrumb a[href]").click(function(e) {
					e.preventDefault();
					var url = $(this).attr("href");
					// Go to home
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
@stop

@section('breadcrumb')
	@include('body.categories')
@stop

@section('content')
	@include('body.categories')
@stop