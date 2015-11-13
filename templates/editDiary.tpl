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

				<h3>日記編集</h3>

				<div class="input_createDiary_caption">
					<span class="textColorRed">&nbsp;(※)</span>
					<span>は必須入力になります。</span><span class="textColorRed">&nbsp;{$cautionMessage}</span>
				</div>

				<form action="editDiary.php" method="post" enctype="multipart/form-data">
					<table class="table_createDiary">
						<tr>
							<td class="td_createDiaryTitle">タイトル<span class="textColorRed">&nbsp;(※)</span></td>
							<td class="td_createDiaryValue">
								<input type="text" name="title" class="input_diaryTitle" maxlength="30" value="{$title}">
							</td>
						</tr>
						<tr>
							<td class="td_createDiaryTitle top">本文<span class="textColorRed">&nbsp;(※)</span></td>
							<td class="td_createDiaryValue">
								<textarea name="message" class="textarea_diaryMessage">{$message}</textarea>
							</td>
						</tr>
						<tr>
							<td class="td_createDiaryTitle">公開範囲<span class="textColorRed">&nbsp;(※)</span></td>
							<td class="td_createDiaryValue">
								<select name="publicState" class="select_diaryPublicState">
{if $state == 0}
									<option value="0" selected="selected">全員に公開</option>
									<option value="1">友達まで公開</option>
									<option value="2">公開しない</option>
{elseif $state == 1}
									<option value="0">全員に公開</option>
									<option value="1" selected="selected">友達まで公開</option>
									<option value="2">公開しない</option>
{else}
									<option value="0">全員に公開</option>
									<option value="1">友達まで公開</option>
									<option value="2" selected="selected">公開しない</option>
{/if}
								</select>
							</td>
						</tr>
						<tr>
							<td class="td_createDiaryTitle">写真1</td>
							<td class="td_createDiaryValue">
{if !empty($photo1)}
								<div class="position_editPhoto"><img src="{$photo1}" class="image_l" alt="画像1"></div>
{/if}
								<div class="position_editPhoto">
									<input type="file" size="40" name="photo1" enctype="multipart/form-data" class="input_diaryPhoto">
									<span class="textColorRed">&nbsp;{$caution1Message}</span></div>
								<div><input type="checkbox" name="delete1" value="1">画像を削除する。</div>
							</td>
						</tr>
						<tr>
							<td class="td_createDiaryTitle">写真2</td>
							<td class="td_createDiaryValue">
{if !empty($photo2)}
								<div class="position_editPhoto"><img src="{$photo2}" class="image_l" alt="画像2"></div>
{/if}
								<div class="position_editPhoto">
									<input type="file" size="40" name="photo2" enctype="multipart/form-data" class="input_diaryPhoto">
									<span class="textColorRed">&nbsp;{$caution2Message}</span></div>
								<div><input type="checkbox" name="delete2" value="2">画像を削除する。</div>
							</td>
						</tr>
						<tr>
							<td class="td_createDiaryTitle">写真3</td>
							<td class="td_createDiaryValue">
{if !empty($photo3)}
								<div class="position_editPhoto"><img src="{$photo3}" class="image_l" alt="画像3"></div>
{/if}
								<div class="position_editPhoto">
									<input type="file" size="40" name="photo3" enctype="multipart/form-data" class="input_diaryPhoto">
									<span class="textColorRed">&nbsp;{$caution3Message}</span></div>
								<div><input type="checkbox" name="delete3" value="3">画像を削除する。</div>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="right">
								<input type="submit" value="更新" class="button_s textColorRed position_createButton_diary">
								<input type="hidden" value="regEdit" name="proc">
								<input type="hidden" value="{$diaryId}" name="diaryId">
							</td>
						</tr>
					</table>

				</form>

			</div>
			<div>&nbsp;</div>

		</div>

{include file=#footerTsalPath#}