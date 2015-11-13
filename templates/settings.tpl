{config_load file="common.conf"}
{include file=#headerTsalPath# title='T-SAL農園&nbsp;SNS'}

		<div class="frame_page">

			<div class="frame_settings">

				<h3>設定</h3>

				<ul>
{if $flg == 1}
					<li>メールアドレス変更<div>&nbsp;</div><div>&nbsp;</div></li>
{else}
					<li><a href="./setting.php?setting=1">メールアドレス変更</a><br/>
					ログインアドレスの変更<div>&nbsp;</div></li>
{/if}

{if $flg == 3}
					<li>携帯アドレス登録<div>&nbsp;</div><div>&nbsp;</div></li>
{else}
					<li><a href="./setting.php?setting=3">携帯アドレス登録</a><br/>
					メール投稿アドレスの登録<div>&nbsp;</div></li>
{/if}

{if $flg == 2}
					<li>パスワード変更<div>&nbsp;</div><div>&nbsp;</div></li>
{else}
					<li><a href="./setting.php?setting=2">パスワード変更</a><br/>
					ログインパスワードの変更<div>&nbsp;</div></li>
{/if}
				</ul>

{if $flg == 1}
				<div class="frame_settingsValue">
					<form action="">
						<table class="table_changeMail">
							<tr>
								<td class="td_changeMailLabel">メールアドレス</td>
								<td class="td_changeMailValue"><input type="text" name="mail" class="input_changeMail" maxlength="50"></td>
							</tr>
							<tr>
								<td class="td_changeMailLabel">メールアドレス(再確認)</td>
								<td class="td_changeMailValue"><input type="text" name="remail" class="input_changeMail" maxlength="50"></td>
							</tr>
							<tr>
								<td class="td_changeMailLabel">確認キーワード</td>
								<td class="td_changeMailValue">
									<img id="siimage" src="./securimage/securimage_show.php" alt="CAPTCHA Image">
									<a tabindex="-1" id="refresh" style="border-style: none;" href="#" onclick="return false">
										<img src="./securimage/images/refresh.png" alt="Reload Image" border="0" align="bottom">
									</a>
									<div>&nbsp;</div>
									<div class="textColorRed">上記に表示されているキーワードを入力して下さい。</div>
									<input type="text" name="keyword" class="input_changeMail" maxlength="20">
								</td>
							</tr>
							<tr>
								<td colspan="2" class="right">
									<input type="button" value="変更" class="button_s textColorRed position_createButton_diary">
								</td>
							</tr>
						</table>
					</form>
				</div>
{elseif $flg == 2}
				<div class="frame_settingsValue">
					<form action="">
						<table class="table_changeMail">
							<tr>
								<td class="td_changeMailLabel">現在のパスワード</td>
								<td class="td_changeMailValue">
									<input type="password" name="oldPassword" class="input_changeMail" maxlength="20">
								</td>
							</tr>
							<tr>
								<td class="td_changeMailLabel">新規パスワード</td>
								<td class="td_changeMailValue">
									<input type="password" name="newPassword" class="input_changeMail" maxlength="20">
								</td>
							</tr>
							<tr>
								<td class="td_changeMailLabel">新規パスワード(再確認)</td>
								<td class="td_changeMailValue">
									<input type="password" name="newRepassword" class="input_changeMail" maxlength="20">
								</td>
							</tr>
							<tr>
								<td colspan="2" class="right">
									<input type="button" value="変更" class="button_s textColorRed position_createButton_diary">
								</td>
							</tr>
						</table>
					</form>
				</div>
{elseif $flg == 3}
				<div class="frame_settingsValue">
				<table style="margin-bottom:15px">
					<tbody>
						<tr>
							<td colspan="3">
								現在メール投稿可能な項目と送信先アドレス
							</td>
						</tr>
						<tr>
							<td style="width:200px;">
								自分の農業日誌内の通信欄
							</td>
							<td colspan="2">
								:&nbsp;sns-farmdiarysystem@t-sal.net&nbsp;<a href="http://www.t-sal.net/community/qrcode/farm_diary_mail.gif" rel="prettyPhoto" title="自分の農業日誌通信欄用QRコード" class="z40">QRコード</a>
							</td>
						</tr>
						<tr>
							<td style="width:200px;">
								日記
							</td>
							<td colspan="2">
								:&nbsp;sns-diarysystem@t-sal.net&nbsp;<a href="http://www.t-sal.net/community/qrcode/diary_mail.gif" rel="prettyPhoto" title="日記用QRコード" class="z40">QRコード</a>
							</td>
						</tr>
						<tr>
							<td style="width:200px;">
								メール投稿公開範囲の設定
							</td>
							<td style="">
								:&nbsp;<select name="publicState" class="select_diaryPublicState" id="publicState">
									<option value="0" {$public}>全員に公開</option>
									<option value="1" {$protected}>友達まで公開</option>
									<option value="2" {$private}>公開しない</option>
								</select>
							</td>
							<td>
								&nbsp;<span id="changePublicStateSuccess"></span>
							</td>
						</tr>
					</tbody>
				</table>
					<form action="">
						<table class="table_changeMail">
							<tr>
								<td class="td_changeMailLabel">携帯アドレス</td>
								<td class="td_changeMailValue"><input type="text" name="mail" class="input_changeMail" maxlength="50" value="{$phoneAddress}"></td>
							</tr>
							<tr>
								<td class="td_changeMailLabel">携帯アドレス(再確認)</td>
								<td class="td_changeMailValue"><input type="text" name="remail" class="input_changeMail" maxlength="50"></td>
							</tr>
							<tr>
								<td class="td_changeMailLabel">確認キーワード</td>
								<td class="td_changeMailValue">
									<img id="siimage" src="./securimage/securimage_show.php" alt="CAPTCHA Image">
									<a tabindex="-1" id="refresh" style="border-style: none;" href="#" onclick="return false">
										<img src="./securimage/images/refresh.png" alt="Reload Image" border="0" align="bottom">
									</a>
									<div>&nbsp;</div>
									<div class="textColorRed">上記に表示されているキーワードを入力して下さい。</div>
									<input type="text" name="keyword" class="input_changeMail" maxlength="20">
								</td>
							</tr>
							<tr>
								<td colspan="2" class="right">
									<input type="button" value="変更" class="button_s textColorRed position_createButton_diary">
								</td>
							</tr>
						</table>
					</form>
				</div>
{/if}
			</div>

		</div>

		<link rel="stylesheet" href="./css/prettyPhoto.css" />
		<script src="js/jquery.prettyPhoto.js"></script>
		<script src="js/lightbox.js"></script>
		<script src="js/setting.js?var=20131018"></script>
		<input type="hidden" value="{$flg}" id="flg">

{include file=#footerTsalPath#}