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
					{$calender}
				</div>

			</div>

			<div class="frame_groupHome">
				<div style="margin-bottom:10px"><a href="./friendDiaryList.php?friendId={$friendId}&diaryId={$diaryId}&year={$year}&month={$month}&day={$day}">一覧へ</a></div>

				<h3 class="memberColor">{$cYear}<br>「{$title}」</h3>

				<table class="table_createDiary">
					<tr>
						<td colspan="3" class="myDiaryPublicState_caption">公開範囲：{$pState}</td>
					</tr>
{if $photo|@count > 0}
					<tr>
{/if}
{foreach from=$photo item=var name=photo}
						<td class="td_createDiaryValue center">
							<a href="{$var}" rel="prettyPhoto[01]" title="" class="z40">
								<img src="{$var}" class="image_l" alt="画像{$smarty.foreach.photo.iteration}"/>
							</a>
						</td>
{/foreach}
{if $photo|@count == 2}
						<td class="td_createDiaryValue center">&nbsp;</td>
{elseif $photo|@count == 1}
						<td class="td_createDiaryValue center">&nbsp;</td>
						<td class="td_createDiaryValue center">&nbsp;</td>
{/if}
{if $photo|@count > 0}
					</tr>
{/if}
					<tr>
						<td class="td_createDiaryValue top" colspan="3">{$message}</td>
					</tr>
				</table>
				<div>&nbsp;</div>

{foreach from=$html item=var name=option}
				<table class="table_diaryComment">
					<tr>
						<td rowspan="2" class="td_diaryCommentNo">{$smarty.foreach.option.iteration}:</td>
						<td class="td_diaryCommentUserName"><a href="./profile.php?userId={$var.userId}">{$var.userName}</a></td>
						<td class="td_diaryCommentDate">{$var.regTime}</td>
					</tr>
					<tr>
						<td colspan="2" class="td_diaryCommentMessage top">
							<div class="diaryCommentMessageValue">{$var.message}</div>
						</td>
					</tr>
				</table>
{/foreach}

{if $html|@count != 0}
				<p>&nbsp;</p>
{/if}
				<h3 class="memberColor">コメントを書く</h3>

				<form action="friendDiary.php?friendId={$friendId}&diaryId={$diaryId}" method="post">
					<table class="table_createDiary">
						<tr>
							<td class="td_createDiaryTitle top">コメント</td>
							<td class="td_createDiaryValue">
								<textarea name="message" class="textarea_diaryMessage"></textarea>
								<div id="countSpan" class="right" style="margin-right:10px;">入力文字数：<span id="count"></span>/<span id="maxCount"></span></div>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="right">
								{$system}
								<input type="submit" value="投稿" class="button_s textColorRed position_createButton_diary">
								<input type="hidden" value="comment" name="send">
							</td>
						</tr>
					</table>

				</form>

			</div>
			<div>&nbsp;</div>

		</div>

		<link rel="stylesheet" href="./css/prettyPhoto.css" />
		<script src="js/jquery.prettyPhoto.js"></script>
		<script src="js/lightbox.js"></script>
		<script src="js/myDiary.js"></script>

{include file=#footerTsalPath#}