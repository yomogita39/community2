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

<!--				<h3>日記の作成</h3>
				<form action="./createDiary.php" method="post" class="position_button_createDiary">
					<input type="submit" value="日記を作成する" class="button_long">
				</form>
-->
				<div style="margin-bottom:10px"><a href="./createDiary.php">新規作成</a></div>

				<h3>自分の日記</h3>

{foreach from=$html item=var name=option}
{if $smarty.foreach.option.first}
				<p class="list_count_m">
					{$html|@count}件中&nbsp;<span id="min"></span><span>～</span><span id="max"></span>件を表示
				</p>

				<table class="table_myDiaryList">
					<tbody id="itemContainer">
{/if}
						<tr>
							<td class="td_myDiaryListDate">{$var.time}</td>
							<td class="td_myDiaryListTitle">
								<a href="./myDiary.php?diaryId={$var.diaryId}&year={$var.year}&month={$var.month}">
{if $var.count == 0}
									{$var.title}
{else}
									{$var.title}({$var.count})
{/if}
								</a>
							</td>
						</tr>
{if $smarty.foreach.option.last}
					</tbody>
				</table>
{/if}
{/foreach}

				<div class="list_count_m holder">
				</div>

			</div>

		</div>

		<input type="hidden" id="total" name="total" value={$smarty.foreach.option.total}>
		<input type="hidden" id="current" name="current" value=10>
		<input type="hidden" id="size" name="size" value=10>

{include file=#footerTsalPath#}