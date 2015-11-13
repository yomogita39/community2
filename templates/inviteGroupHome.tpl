{config_load file="common.conf"}
{include file=#headerTsalPath# title='T-SAL農園&nbsp;SNS'}

		<div class="frame_page">

			<div class="frame_home_left">

				<div class="frame_image">
					<img src="{$groupImage}" class="image_l" alt="グループ画像">
					<div class="image_name">{$groupName}</div>
				</div>

				<div class="position_imageArrow">
					<img src="./templates/image/arrow.png" alt="矢印画像">

					<form action="./inviteGroupHome.php" method="post">
						<input type="hidden" name="groupId" value={$groupId}>
						<input type="submit" value="参加" class="button_big">
					</form>
				</div>

			</div>

			<div class="frame_groupHome">

				<h3>メンバーリスト({$html|@count})</h3>

				<p class="list_count_m">
					<span id="total"></span><span>人中&nbsp;</span><span id="min"></span><span>～</span><span id="max"></span><span>人を表示</span>
				</p>
	                                
				<table class="list_table_m">
					 <tbody id="itemContainer">
					
{foreach from=$html item=var name=option}
{if $smarty.foreach.option.iteration % 5 == 1}
						<tr>
{/if}
							<td class="list_td_m">
								<img src="{$var.userImage}" class="image_m" alt="{$var.userName}画像">
								<div class="list_td_userName">{$var.userName}</div>
							</td>

{if $smarty.foreach.option.last}
{section name=count start=0 loop=5}
{if $smarty.foreach.option.total % 5 != 0 && $smarty.section.count.index < (5-($smarty.foreach.option.total % 5))}
							<td class="list_td_m">&nbsp;</td>
{/if}
{/section}
{/if}
{if ($smarty.foreach.option.iteration is div by 5) || $smarty.foreach.option.last}
						</tr>
{/if}
{/foreach}
					</tbody>
				</table>
				<input type="hidden" id="total" name="total" value={$smarty.foreach.option.total}>

				<div class="list_count_m">
					<div class="holder"></div>
				</div>

			</div>

		</div>
		<input type="hidden" id="current" name="current" value=2>
		<input type="hidden" id="size" name="size" value=10>

{include file=#footerTsalPath#}