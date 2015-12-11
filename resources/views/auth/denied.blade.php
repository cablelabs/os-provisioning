<!doctype html>
<html>
<head>
<title>ACCESS DENIED!</title>
</head>
<body style="padding: 50px">

<h1>Access denied</h1>

@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif

<br><br>
<button onclick="goBack()">Go Back</button>

<br><br>
<form action="{{Request::root()}}">
    <input type="submit" value="Login">
</form>

<script>
	function goBack() {
		window.history.back();
	}
</script>

</body>
</html>
