<?php
$config = array(
	'appId' => Config::get('facebook.id'),
	'secret' => Config::get('facebook.secret'),
	'allowSignedRequest' => false
);
$facebook = new Facebook($config);
$loginUrl = $facebook->getLoginUrl(array(
	'scope' => 'email,user_groups',
	'redirect_uri' => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
	'display' => 'page'
));
$error = "";
if(Input::has('error')) {
	$error .= "Error: " . Input::get('error');
}
if(Input::has('error_description')) {
	$error .= '<br>';
	$error .= "Error Description: " . Input::get('error_description');
}
if(Input::has('error_reason')) {
	$error .= '<br>';
	$error .= "Error Reason: " . Input::get('error_reason');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Test</title>
		<script src="{{ asset("js/jquery-2.0.3.min.js") }}"></script>
		<script>
			$('<script></script' + '>').attr('src', '//connect.facebook.net/th-TH/all.js').insertBefore('script:eq(0)');
			window.fbAsyncInit = function() {
				FB.init({
					appId: '{{ $facebook->getAppId() }}',
					status: true,
					cookie: true
				});
				var $link = $('<a></a>');
				FB.getLoginStatus(function(response) {
					switch(response.status) {
						case 'connected':
							window.location.href = "{{ url('/test/view') }}";
							break;
						case 'not_authorized':
						default :
							$link.attr('href', '{{ $loginUrl }}');
							$link.text('Login');
							break;
					}
				});
				$link.appendTo($('body'));
			};
		</script>
		@unless (empty($error))
		<script>
			$(document).ready(function() {
				$('<div></div>').css({color: 'red', marginBottom: '5px'}).html('{{ $error }}').insertAfter($('#fb-root'));
			});
		</script>
		@endunless
    </head>
    <body>
		<div id="fb-root"></div>
	</body>
</html>
