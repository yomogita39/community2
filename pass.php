<html>
<head>
</head>
<body>
<form action="./pass.php" method="post" id="loginForm">

	<input type="text" name="password">
	<input type="submit" value="送信">
	<input type="reset" value="リセット">

</form>
</body>
</html>
<?PHP
$pass = $_POST['password'];
if(isset($pass) && strlen($pass)>0 ){
		print(hash('SHA256', $pass));
}
?>