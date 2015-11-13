{config_load file="common.conf"}
{include file=#headerTsalPath# title='T-SAL農園&nbsp;SNS'}

		<div class="frame_page">

			<div class="frame_home_left">

				<div class="frame_image">
					<img src="{$userImage}" class="image_l" alt="{$userName}画像">
					<div class="image_name">{$userName}</div>
				</div>

				<div>&nbsp;</div>
				<div>&nbsp;</div>
				<div>
					{$calendar}
				</div>

			</div>

			<div class="frame_groupHome">

				<h3>農業日誌&nbsp;&ndash;&nbsp;{$cDate}({$week})</h3>
				<div>&nbsp;</div>
				<form action="./farmDiary.php" method="post">
					<table class="table_farmDiary">
						<tr>
							<th class="th_farmDiary">予定</th>
							<th class="th_farmDiary">作業内容</th>
						</tr>
						<tr>
							<td class="td_farmDiaryValue">
								<textarea id="schedule" name="schedule" class="textarea_work">{$workS}</textarea>
							</td>
							<td class="td_farmDiaryValue">
								<textarea id="work" name="work" class="textarea_work">{$work}</textarea>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="right">
								<input type="button" id="workButton" value="保存" class="button_s textColorRed position_createButton_diary">
								<input type="hidden" value="{$date}" name="date" id="date">
							</td>
						</tr>
					</table>
				</form>

			</div>

		</div>
		<script language="javascript" type="text/javascript" src="js/farmDiary.js"></script>
{include file=#footerTsalPath#}