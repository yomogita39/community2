{config_load file="common.conf"}
{include file=#headerTsalPath# title='T-SAL農園&nbsp;SNS'}

		<div class="frame_page">

			<div class="frame_home_left">

				<div class="frame_image">
					<img src="{$userImage}" class="image_l" alt="{$userName}画像">
					<div class="image_name">{$userName}</div>
				</div>

			</div>

			<div class="frame_groupHome">
				<div style="margin-bottom:10px"><a href="./myDiaryList.php">一覧へ</a></div>
				<h3>日記</h3>

				<div class="input_createDiary_caption">
					<span class="textColorRed">&nbsp;(※)</span>
					<span>は必須入力になります。</span>
				</div>

				<form action="createDiary.php" method="post" enctype="multipart/form-data">
					<table class="table_createDiary">
						<tr>
							<td class="td_createDiaryTitle">タイトル<span class="textColorRed">&nbsp;(※)</span></td>
							<td class="td_createDiaryValue">
								<input type="text" name="title" class="input_diaryTitle" maxlength="30" value="{$cTitle}">
							</td>
						</tr>
						<tr>
							<td class="td_createDiaryTitle top">本文<span class="textColorRed">&nbsp;(※)</span></td>
							<td class="td_createDiaryValue">
								<textarea name="message" class="textarea_diaryMessage">{$cBody}</textarea>
								<div id="countSpan" class="right">入力文字数：<span id="count"></span>/<span id="maxCount"></span></div>
							</td>
						</tr>
						<tr>
							<td class="td_createDiaryTitle">公開範囲<span class="textColorRed">&nbsp;(※)</span></td>
							<td class="td_createDiaryValue">
								<select name="publicState" class="select_diaryPublicState">
									<option value="0" selected="selected">全員に公開</option>
									<option value="1">友達まで公開</option>
									<option value="2">公開しない</option>
								</select>
							</td>
						</tr>
						<tr class="fallback">
							<td colspan="2">
								<div id="preview">画像(2Mバイト以内のJPEG、PNG、GIF形式。)</div>
								<div id="boxs" class="dropzone-custom"><span>ここにドロップ</span></div>
								<div id="preview_area" class="dropzone-custom" style="display:none"></div>
							</td>
						</tr>
						<tr class="fallback" style="display:none;">
							<td class="td_createDiaryTitle">写真1</td>
							<td class="td_createDiaryValue">
								<input type="file" size="40" name="file[]" enctype="multipart/form-data" class="input_diaryPhoto">
								<span class="textColorRed">&nbsp;{$caution1Message}</span>
							</td>
						</tr>
						<tr class="fallback" style="display:none;">
							<td class="td_createDiaryTitle">写真2</td>
							<td class="td_createDiaryValue">
								<input type="file" size="40" name="file[]" enctype="multipart/form-data" class="input_diaryPhoto">
								<span class="textColorRed">&nbsp;{$caution2Message}</span>
							</td>
						</tr>
						<tr class="fallback" style="display:none;">
							<td class="td_createDiaryTitle">写真3</td>
							<td class="td_createDiaryValue">
								<input type="file" size="40" name="file[]" enctype="multipart/form-data" class="input_diaryPhoto">
								<span class="textColorRed">&nbsp;{$caution3Message}</span>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="right">
								<input type="button" id="reg" value="作成" class="button_s textColorRed position_createButton_diary">
							</td>
						</tr>
					</table>
					<input type="hidden" value="trueCreate" name="create" id="create">
				</form>

			</div>

		</div>
		<link rel="stylesheet" href="./css/dropzone.css" type="text/css" />
		<script src="js/dropzone-amd-module.js"></script>
		<script src="js/dropzone.js"></script>
		<script src='js/jquery.upload-1.0.2.js'></script>
		<script src='js/jquery.stringCount.js'></script>
		<script src='js/createDiary.js'></script>

{include file=#footerTsalPath#}