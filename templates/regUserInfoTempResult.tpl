{config_load file="common.conf"}

{include file=#headerPath# title='T-SAL農園&nbsp;SNS'}

	<div class="display">
		<div><img src="./templates/image/top_title.png" alt="タイトル画像"></div>

		<div class="frame_userReg">
{if flg == 0}
			<div class="frame_userReg_title">入会&ndash;受付完了</div>

			<div class="frame_input_userReg">

				<div class="input_userReg_caption textColorRed">
					<span>○仮登録が完了しました。</span>
					<div>&nbsp;SNSサイト管理人が登録を確認しますので、しばらくお待ち下さい。確認後、登録したメールアドレスに、本登録用URLを記載したメールをお送りしますので、そちらから本登録を行って下さい。</div>
				</div>
				<p>&nbsp;</p>
				<form action="./login.php">
					<p><input type="submit" value="ログイン画面へ" class="button_right_l textColorRed"></p>
				</form>
{else}
			<div class="frame_userReg_title">入会&ndash;受付エラー</div>

			<div class="frame_input_userReg">

				<div class="input_userReg_caption textColorRed">
					<span>○申し訳ありません。</span>
					<div>&nbsp;仮登録の際に、エラーが発生しました。<br>大変お手数ですが、再度入力し直してください。</div>
				</div>
				<p>&nbsp;</p>
				<form action="./registration.php">
					<p><input type="submit" value="再入力" class="button_right_l textColorRed"></p>
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