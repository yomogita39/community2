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
					<div class="frame_home_left_bottom">
						<h3>友達の日誌</h3>
						<div class="frame_farm">
							<form id="perForm">
								<table class="table_farmState">
									<tr>
										<th class="">許可された他のユーザの<br>日誌にアクセスします</th>
									</tr>
									<tr>
										<td class="center">
											<select name="perDiary" id="perDiary">
												{$pDiaryOption}
											</select>
										</td>
									</tr>
								</table>
							</form>
						</div>
					</div>
				</div>
					<div class="frame_groupHome">
						<h3>経営日誌&nbsp;&ndash;&nbsp;{$cDate}({$week})</h3>
						<div><a href="#" id="accessButton" onclick="return false;" style="{$style}">日誌へのアクセス権限を設定する</a></div>
						<div>&nbsp;</div>
						<form id='costForm' enctype="multipart/form-data">
							<table class="table_editProfile" style="width:600px; margin:0 auto 0 auto;">
								<tbody id="costHtml">
									{$costHtml}
								</tbody>
							</table>
							<table class="table_editProfile" style="width:600px; margin:0 auto 0 auto;">
								<tbody>
									<tr>
										<td colspan="4" class="right">
											<a href="#costTbl" onclick="return false;" style="" id="costOpen" class="totalToggle" title="今月の経費一覧">今月の経費一覧</a>
											<input type="button" id="costButton" value="保存" class="button_s textColorRed position_createButton_diary">
										</td>
									</tr>
								</tbody>
							</table>
						</form>
						<div  id="costTbl" style=" display:none;">
							<table class="table_editProfile" style="width:700px; margin:0 auto 0 auto;">
								<tbody id="costTotal">
									{$costTotal}
								</tbody>
							</table>
						</div>
						<div>&nbsp;</div>
						<form id='workerForm' enctype="multipart/form-data">
							<table class="table_editProfile" style="width:600px; margin:0 auto 0 auto;">
								<tbody id="workHtml">
									{$workHtml}
								</tbody>
							</table>
							<table class="table_editProfile" style="width:600px; margin:0 auto 0 auto;">
								<tbody>
									<tr>
										<td colspan="4" class="right">
											<a href="#workTbl" onclick="return false;" style="" id="workOpen" class="totalToggle" title="今月の作業時間一覧">今月の作業時間一覧</a>
											<input type="button" id="workButton" value="保存" class="button_s textColorRed position_createButton_diary">
										</td>
									</tr>
								</tbody>
							</table>
						</form>
						<div  id="workTbl" style=" display:none;">
							<table class="table_editProfile" style="width:700px; margin:0 auto 0 auto;">
								<tbody id="workTotal">
									{$workTotal}
								</tbody>
							</table>
						</div>
						<div>&nbsp;</div>
						<form id='harvestForm' enctype="multipart/form-data">
							<table class="table_editProfile" style="width:600px; margin:0 auto 0 auto;">
								<tbody id="harvestHtml">
									
									
									{$harvestHtml}
								</tbody>
							</table>
							<table class="table_editProfile" style="width:600px; margin:0 auto 0 auto;">
								<tbody>
									
									
									<tr>
										<td colspan="4" class="right">
											<a href="#harvestTbl" onclick="return false;" style="" id="harvestOpen" class="totalToggle" title="今月の収穫一覧">今月の収穫一覧</a>
											<input type="button" id="harvestButton" value="保存" class="button_s textColorRed position_createButton_diary" style="">
										</td>
									</tr>
								</tbody>
							</table>
						</form>
						<div  id="harvestTbl" style=" display:none;">
							<table class="table_editProfile" style="width:700px; margin:0 auto 0 auto;">
								<tbody id="harvestTotal">
									{$harvestTotal}
								</tbody>
							</table>
						</div>
						<div>&nbsp;</div>

						<input type="hidden" id="proc"  value="management">

					</div>

					<div class="" id="accessDialog" style="display:none" title="アクセス権限の追加">
						<form id='accessForm' enctype="multipart/form-data">
							<table class="">
								<tbody class="" id="accessMember">
									{$accessHtml}
								</tbody>
							</table>
							<table class="">
								<tbody id="access">
									<tr>
										<td>
											新たにアクセス権限を付与する<br />
											<select name="friendSelect" id="friendSelect">{$friendOption}</select>
										</td>
									</tr>
								</tbody>
							</table>
						</form>
					</div>


			</div>
		<input type="text" id="dummy" value="" style="display:none;">
		<input type="hidden" name="date" id="date" value="{$date}">

		<link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/start/jquery-ui.css" rel="stylesheet" />
		<script src="js/management.js"></script>

{include file=#footerTsalPath#}