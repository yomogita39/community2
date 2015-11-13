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
						<h3>農業日誌&nbsp;&ndash;&nbsp;{$cDate}({$week})</h3>
						<div><a href="#" id="accessButton" onclick="return false;" style="{$style}">日誌へのアクセス権限を設定する</a></div>
						<div>&nbsp;</div>
						<form id='editForm' enctype="multipart/form-data">
							<table class="table_editProfile" style="width:600px; margin:0 auto 0 auto;">
								<tbody id="workHtml">

									{$html}

								</tbody>
							</table>
						</form>
						<form id="workPlan">
							<table class="table_farmDiary" style="width:600px; margin:0 auto 0 auto;">
								<tbody>
									<tr>
										<th class="th_farmDiary">予定入力</th>
										<th class="th_farmDiary">実績入力</th>
									</tr>
									<tr>
										<td class="td_farmDiaryValue">
											<textarea maxlength="500" id="plan" name="plan" class="textarea_work">{$plan}</textarea>
										</td>
										<td class="td_farmDiaryValue">
											<textarea maxlength="500" id="performance" name="performance" class="textarea_work">{$performance}</textarea>
										</td>
									</tr>
									<tr>
										<td colspan="2" class="right">
											<input type="button" id="planButton" value="保存" class="button_s textColorRed position_createButton_diary">
										</td>
									</tr>
								</tbody>
							</table>
						</form>

						<div>&nbsp;</div>
						<table class="table_editProfile" style="width:600px; margin:0 auto 0 auto;">
							<tbody id="commHtml">
								{$commHtml}
							</tbody>
						</table>
						<form>
							<table class="table_editProfile" style="width:600px; margin:0 auto 0 auto;">
								<tbody>
									<tr>
										<td colspan="4" class="center">
											<textarea name="commText" id="commText" class="textarea_mutter td_farmDiaryValue" style="width:510px; margin:0 auto 0 auto;"></textarea>
										</td>
									</tr>
									<tr>
										<td>
											<div id="fallback">
												<br>
												<div id="preview">画像(2Mバイト以内のJPEG、PNG、GIF形式。)</div>
											</div>
											
											<div id="boxs" class="dropzone-custom">ここにドロップ</div>
											<div id="preview_area" class="dropzone-custom" style="display:none"></div>
										</td>
									</tr>
									<tr>
										<td colspan="4" class="right">
											<input type="button" id="msgsend" value="投稿" class="button_s textColorRed position_createButton_diary">
										</td>
									</tr>
									
								</tbody>
							</table>
						</form>
						<input type="hidden" id="proc"  value="farm">

					</div>

					<div class="" id="routineDialog" style="display:none" title="作業の追加">
						<form id='routineForm' enctype="multipart/form-data">
							<table class="">
								<tr class="tr_editProfile">
									<th class="th_farmDiary"> ルーチンワーク </th>
								</tr>
								<tr>
									<td class="td_farmDiaryValue">
										<input type="text" id="routineAdd" name="add" class="input_editProfile">
										<input type="text" class="dammy" style="display:none;">
									</td>
								</tr>
							</table>
						</form>
					</div>
					<div class="" id="behindDialog" style="display:none" title="作業の追加">
						<form id='behindForm' enctype="multipart/form-data">
							<table class="">
								<tr class="tr_editProfile">
									<th class="th_farmDiary">ビハインドワーク</th>
								</tr>
								<tr>
									<td class="td_farmDiaryValue">
										<input type="text" id="behindAdd" name="add" class="input_editProfile">
										<input type="text" class="dammy" style="display:none;">
									</td>
								</tr>
							</table>
						</form>
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
		<link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/start/jquery-ui.css" rel="stylesheet">
		<link rel="stylesheet" href="css/magic.min.css">
		<link rel="stylesheet" href="./css/dropzone.css" type="text/css" />
		<link rel="stylesheet" href="./css/prettyPhoto.css" />
		<script src="js/jquery.prettyPhoto.js"></script>
		<script src="js/dropUpload.js"></script>
		<script src="js/dropzone-amd-module.js"></script>
		<script src="js/dropzone.js"></script>
		<script src="js/workEdit.js"></script>
		<script src='js/jquery.upload-1.0.2.js'></script>
{include file=#footerTsalPath#}