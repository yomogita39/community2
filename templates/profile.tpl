{config_load file="common.conf"}

{include file=#headerTsalPath# title='T-SAL農園&nbsp;SNS'}

		<div class="frame_page">

			<div class="frame_profile">

				<h3>マイプロフィール</h3>

				<div class="frame_image_profile">
					<img src="{$userImage}" class="image_l" alt="{$userName}画像">
					<div class="image_name">{$userName}</div>
					<div>&nbsp;</div>
					<div class="login_time">前回ログイン</div>
					<div class="login_time">{$last}</div>
				</div>

				<div class="frame_profileList">
					<table class="table_profile">
						<tr>
							<td class="td_profile_label">名前(非公開)</td>
							<td class="td_profile_value">{$realName}</td>
						</tr>
					
						<tr>
							<td class="td_profile_label">ニックネーム</td>
							<td class="td_profile_value">{$userName}</td>
						</tr>

						<tr>
							<td class="td_profile_label">性別</td>
							<td class="td_profile_value">{$gender}</td>
						</tr>

						<tr>
							<td class="td_profile_label">誕生日</td>
							<td class="td_profile_value">{$birthday}</td>
						</tr>

						<tr>
							<td class="td_profile_label">都道府県</td>
							<td class="td_profile_value">{$place}</td>
						</tr>

						<tr>
							<td class="td_profile_label">自己紹介</td>
							<td class="td_profile_value">{$introduction}</td>
						</tr>

						<tr>
							<td colspan="2" class="td_button_editProfile">
								<form action="./profileEdit.php">
									<input type="submit" value="編集" class="button_s" {$style}>
								</form>
							</td>
						</tr>
					</table>

				</div>

			</div>

		</div>

		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
		<script type="text/javascript" src="js/jquery.upload-1.0.2.js"></script>
		<script language="javascript" type="text/javascript" src="js/profileScript.js"></script>
		<script language="javascript" type="text/javascript" src="js/profileEdit.js"></script>

{include file=#footerTsalPath#}