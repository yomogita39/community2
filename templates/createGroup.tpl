{config_load file="common.conf"}
{include file=#headerTsalPath# title='T-SAL農園&nbsp;SNS'}

		<div class="frame_page">

			<div class="frame_profile">

				<h3>グループ作成</h3>

				<div class="input_editProfile_caption">
					<span>○グループ情報を入力して下さい。</span>
					<span class="textColorRed">&nbsp;(※)</span>
					<span>は必須入力になります。</span>
				</div>

				<div class="frame_editProfile">

					<form action="./createGroup.php" method="post" enctype="multipart/form-data">
						<table class="table_createGroup">

							<tr>
								<td class="td_createGroup_label">グループ名<span class="textColorRed">&nbsp;(※)</span></td>
								<td class="td_createGroup_value"><input type="text" name="groupName" class="input_groupInfo" maxlength="20"></td>
							</tr>

							<tr>
								<td class="td_createGroup_label">グループ画像</td>
								<td class="td_createGroup_value">
									<div>2Mバイト以内のJEPG、PNG、GIF形式</div>
									<input type="file" name="groupImage" class="input_groupInfo">
								</td>
							</tr>
						</table>

						<input type="submit" value="作成" class="button_m textColorRed position_createButton">
						<input type="reset" value="クリア" class="button_m position_clearButton">
					</form>

				</div>

			</div>

		</div>

{include file=#footerTsalPath#}