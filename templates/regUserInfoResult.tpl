{config_load file="common.conf"}

{include file=#headerPath# title='T-SAL農園&nbsp;SNS'}

	<div class="display">
		<div><img src="./templates/image/top_title.png" alt="タイトル画像"></div>

		<div class="frame_userReg">
{if $flg == 0}
			<div class="frame_userReg_title">入会&ndash;本登録完了</div>

			<div class="frame_input_userReg">

				<div class="input_userReg_caption textColorRed">
					<span>●本登録が完了しました。</span>
					<div>&nbsp;今後は、登録したユーザID(メールアドレス)、パスワードにてログインし、ご利用下さい。</div>
				</div>
				<p>&nbsp;</p>
				<form action="./login.php">
					<p><input type="submit" value="ログイン画面へ" class="button_right_l textColorRed"></p>
				</form>
{elseif $flg == 1}
			<div class="frame_userReg_title">入会&ndash;本登録エラー</div>

			<div class="frame_input_userReg">

				<div class="input_userReg_caption textColorRed">
					<span>●申し訳ありません。</span>
					<div>
						<span>&nbsp;本登録の際に、エラーが発生しました。</span>
						<div>大変お手数ですが、下記の「お問い合わせボタン」よりお問い合わせ下さい。</div>
						<div>※ボタン押下で、メールが送信されます。</div>
					</div>
				</div>
				<p>&nbsp;</p>
				<form action="./registration.php">
					<p><input type="button" id="inquiry" name="inquiry" value="お問い合わせ" class="button_right_l textColorRed"></p>
					<input type="hidden" id="userMail" name="userMail" value="{$userMail}">
				</form>
{elseif $flg == 2}
			<div class="frame_userReg_title">メールアドレス変更&ndash;登録完了</div>

			<div class="frame_input_userReg">

				<div class="input_userReg_caption textColorRed">
					<span>●登録が完了しました。</span>
					<div>&nbsp;今後は、登録したユーザID(メールアドレス)にてログインし、ご利用下さい。</div>
				</div>
				<p>&nbsp;</p>
				<form action="./login.php">
					<p><input type="submit" value="ログイン画面へ" class="button_right_l textColorRed"></p>
				</form>
{else}
			<div class="frame_userReg_title">入会&ndash;登録エラー</div>

			<div class="frame_input_userReg">

				<div class="input_userReg_caption textColorRed">
					<span>●申し訳ありません。</span>
					<div>
						<span>&nbsp;有効期限が過ぎています。</span>
						<div>大変お手数ですが、再度ユーザ登録をし直してください。</div>
					</div>
				</div>
				<p>&nbsp;</p>
				<form action="./registration.php">
					<p><input type="submit" value="ユーザ登録" class="button_right_l textColorRed"></p>
				</form>
{/if}

				<div class="triangle">&nbsp;</div>
				<div class="triangle2">&nbsp;</div>
				<div class="triangle3">&nbsp;</div>

			</div>
			
			<div class="height_50">&nbsp;</div>

		</div>

		<div class="footer">&nbsp;</div>
	</div>

{include file=#footerPath#}