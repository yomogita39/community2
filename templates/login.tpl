{config_load file="common.conf"}
{include file=#headerPath# title='T-SAL農園&nbsp;SNS'}

	<div class="frame_login_image">

		<div class="frame_login">
			<div class="frame_login_title">ログイン</div>

			<form action="./login.php" method="post" id="loginForm">

				<table class="frame_login_table">
					<tr>
						<td class="right">
							<div class="inputLabelColor">メールアドレス</div></td>
						<td>
							<input type="text" name="userId" class="input_login" maxlength="255">
						</td>
					</tr>
					<tr>
						<td class="right">
							<div class="inputLabelColor">パスワード</div></td>
						<td>
							<input type="password" name="password" class="input_login" maxlength="255">
						</td>
					</tr>
				</table>

				<input type="button" id="loginButton" value="　　　入園　　　" class="btn btn-custom textColorRed position_button_login">

			</form>

			<form action="./registration.php">
				<input type="submit" class="btn btn-custom position_button_userReg" value="無料新規登録"/>
			</form>

			<div class="position_link_changePass">
				 <a href="#" onclick="return false" id="forgetPass" class="link_login">※パスワードを忘れた方はこちら</a>
			</div>
			<div id="dialog" title="パスワード再発行" style="display:none">
				<p>「送信」で仮パスワードをご入力<br>
				いただいたメールアドレスに送信します。</p>
				<p>登録メールアドレス <input type="text" name="mAddress" id="mAddress" style="width:300px" maxlength="255"></p>
			</div>
			
		</div>
		
		<div class="position_image_isbLogo">
			<a href="http://www.isb.co.jp/itc" target="_blank"><img src="./templates/image/ITC_logo.jpg" alt="ISB東北" class="image_isbLogo"></a>
		</div>

	</div>
	<link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/start/jquery-ui.css" rel="stylesheet">
	<script type="text/javascript" src="js/forgetsPass.js"></script>
	<script type="text/javascript" src="js/login.js"></script>
	<script src="./js/importBootstrap.js?20131015=20131015"></script>

{include file=#footerPath#}