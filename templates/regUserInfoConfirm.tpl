{config_load file="common.conf"}

{include file=#headerPath# title='T-SAL農園&nbsp;SNS'}

	<div class="display">
		<div><img src="./templates/image/top_title.png" alt="タイトル画像"></div>

		<div class="frame_userReg">
			<div class="frame_userReg_title">入会-入力内容の確認</div>

			<div class="frame_input_userReg">

				<div class="input_userReg_caption textColorRed">○以下の内容で登録してもよろしいですか？</div>

				<form action="./regmail/result.php" method="post">
					<table class="input_userReg_table">
						<tr>
							<td class="input_userReg_label">メールアドレス</td>
							<td class="input_confirm_td">{$showId}</td>
						</tr>
						
						<tr>
							<td class="input_userReg_label">名前</td>
							<td class="input_confirm_td">{$realName}</td>
						</tr>
						
						<tr>
							<td class="input_userReg_label">ニックネーム</td>
							<td class="input_confirm_td">{$showName}</td>
						</tr>
						
						<tr>
							<td class="input_userReg_label">性別</td>
							<td class="input_confirm_td">{$gender}</td>
						</tr>
						
						<tr>
							<td class="input_userReg_label">誕生日</td>
							<td class="input_confirm_td">{$birthday}</td>
						</tr>
						
						<tr>
							<td class="input_userReg_label">都道府県</td>
							<td class="input_confirm_td">{$place}</td>
						</tr>

						<tr>
							<td class="input_userReg_label">パスワード</td>
							<td class="input_confirm_td">{$showPw}</td>
						</tr>

					</table>

					<input type="hidden" name="userId" value="{$userId}">
					<input type="hidden" name="userName" value="{$userName}">
					<input type="hidden" name="userPass" value="{$userPass}">
					<input type="hidden" name="realName" value="{$realName}">
					<input type="hidden" name="gender" value="{$gender}">
					<input type="hidden" name="birthday" value="{$birthday}">
					<input type="hidden" name="place" value="{$place}">
					
					<input type="submit" value="送信" class="button_right_s">
				</form>

				<form action="./registration.php">
					<input type="submit" value="再入力" class="button_left_s">
				</form>
				
				<div class="textColorRed">
					<div>(注意)</div>
					<div>携帯電話のメールアドレスを登録した方は、受信設定にてドメイン「t-sal.net」を許可して下さい。</div>
					<div>パソコンからのメールを拒否している場合、入会の本登録が完了できません。</div>
				</div>

				<div class="triangle">&nbsp;</div>
				<div class="triangle2">&nbsp;</div>
				<div class="triangle3">&nbsp;</div>

			</div>
			
			<div class="height_50">&nbsp;</div>

		</div>

		<div class="footer">&nbsp;</div>
	</div>

{include file=#footerPath#}