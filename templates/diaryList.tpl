{config_load file="common.conf"}
{include file=#headerTsalPath# title='T-SAL農園&nbsp;SNS'}

		<div class="frame_page">

			<div class="frame_friendList">

				<h3 class="h3_list_l">日記リスト</h3>
				
				<p class="list_count_l">
					<span id="total"></span><span>件中&nbsp;</span><span id="min"></span><span>～</span><span id="max"></span><span>件を表示</span>
				</p>

{foreach from=$html item=var name=option}
{if $smarty.foreach.option.first}
				<table class="table_diaryList">
					<tbody id="itemContainer">
{/if}
						<tr>
							<td class="td_diaryListDate">{$var.time}</td>
							<td class="td_diaryListUserName">
								<a href="./profile.php?userId={$var.userId}">{$var.userName}</a>
							</td>
							<td class="td_diaryListDairyTitle">
								<a href="./friendDiary.php?friendId={$var.userId}&diaryId={$var.diaryId}">
{if $var.cCount == 0}
									{$var.title}
{else}
									{$var.title}({$var.cCount})
{/if}
								</a>
							</td>
						</tr>
{if $smarty.foreach.option.last}
					</tbody>
				</table>
{/if}
{/foreach}

				<div class="list_count_l holder">
				</div>

			</div>

		</div>

		<input type="hidden" id="total" name="total" value={$smarty.foreach.option.total}>
		<input type="hidden" id="current" name="current" value=10>
		<input type="hidden" id="size" name="size" value=10>

{include file=#footerTsalPath#}