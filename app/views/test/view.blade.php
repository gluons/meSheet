<?php
$config = array(
	'appId' => Config::get('facebook.id'),
	'secret' => Config::get('facebook.secret'),
	'allowSignedRequest' => false
);
$facebook = new Facebook($config);
$me = $facebook->api('/me');
$groups = $facebook->api('/me/groups')['data'];
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title></title>
	</head>
	<body>
		<p>Id: {{ $me['id'] }}</p>
		<p>Name: <a href="https://www.facebook.com/profile.php?id={{ $me['id'] }}" target="_blank">{{ $me['name'] }}</a></p>
		<p>Email: <a href="mailto:{{ $me['email'] }}" target="_blank">{{ $me['email'] }}</a></p>
		<p>Username: {{ $me['username'] }}</p>
		<p>Groups: </p>
		<ul>
			@foreach ($groups as $group)
			<li>{{ $group['name'] }} ({{ $group['id'] }})</li>
			@endforeach
		</ul>
	</body>
</html>
