<!DOCTYPE html>
<html lang='ru' dir='ltr'>
	<head>
		<meta charset="utf-8" />
		<meta name="robots" content="noindex,nofollow" />
		<meta http-equiv="X-UA-Compatible" content="IE=Edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Welcome to Mailer</title>
		<link rel="icon" href="favicon.ico" type="image/x-icon">
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">

		{#<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">#}
		{#<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">#}

		{{ stylesheet_link('css/font-awesome.css') }}
		{{ stylesheet_link('css/bootstrap/bootstrap.css') }}
		{{ stylesheet_link('css/bootstrap/bootstrap-theme.css') }}

		{#{{ stylesheet_link('css/bootswatch/default/bootstrap.css') }}#}
		{#{{ stylesheet_link('css/bootswatch/cyborg/bootstrap.css') }}#}

		{#<link rel="stylesheet" href="//bootswatch.com/cyborg/bootstrap.css">#}

		{#<link rel="stylesheet" href="//bootswatch.com/darkly/bootstrap.css">#}
		{#<link rel="stylesheet" href="//bootswatch.com/simplex/bootstrap.css">#}
		{#<link rel="stylesheet" href="//bootswatch.com/united/bootstrap.css">#}

		{{ stylesheet_link('css/style.css') }}



		<!-- Bootstrap core JavaScript
         ================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="/js/jquery/jquery-2.1.4.min.js"></script>
		<script src="/js/bootstrap/bootstrap.min.js"></script>
		{#<script src="//code.jquery.com/jquery-1.12.4.min.js"></script>#}
		{#<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>#}
	</head>
	<body>
		{{ content() }}
	</body>
</html>