<!doctype html>
<html>
<head>
<title>ACCESS DENIED!</title>
</head>
<body style="padding: 50px">

<h1>Access denied</h1>

<h3>{{ $error_msg }}</h3>

<button onclick="goBack()">Go Back</button>

<script>
	function goBack() {
		window.history.back();
	}
</script>

</body>
</html>
