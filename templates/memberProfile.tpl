{config_load file="common.conf"}
{include file=#headerTsalPath# title='T-SAL農園&nbsp;SNS'}

		<div class="frame_page">

			<div class="frame_profile">

				<h3 class="memberColor">{$userName}&nbsp;さん&nbsp;プロフィール</h3>

				<div class="frame_image_profile">
					<img src="{$userImage}" class="image_l" alt="{$userName}画像">
					<div class="image_name">{$userName}</div>
					<div>&nbsp;</div>
					<div class="login_time">前回ログイン</div>
					<div class="login_time">{$last}</div>

					<form action="./searchFriend.php"  class="button_applyFriend">
{if $friendFlg == 0}
						<input type="hidden" name="friendId" value="{$friendId}">
						<input id="invite" type="button" value="友達申請" class="button_m textColorRed">
{elseif $friendFlg == 1}
						<input type="submit" value="申請中" class="button_m textColorRed" disabled>
{elseif $friendFlg == 3}
						<input type="hidden" name="friendId" value="{$friendId}">
						<input id="app" type="submit" value="友達承認" class="button_m textColorRed">
{else}
						<input type="submit" value="友達" class="button_m textColorRed" disabled>
{/if}
					</form>

				</div>

				<div class="frame_profileList">
					<table class="table_profile">
						<tr>
							<td class="td_profile_label">ニックネーム</td>
							<td class="td_profile_value">{$userName}</td>
						</tr>

						<tr>
							<td class="td_profile_label">性別</td>
							<td class="td_profile_value">{$gender}</td>
						</tr>

						<tr>
							<td class="td_profile_label">誕生日</td>
							<td class="td_profile_value">{$birthday}</td>
						</tr>

						<tr>
							<td class="td_profile_label">都道府県</td>
							<td class="td_profile_value">{$place}</td>
						</tr>

						<tr>
							<td class="td_profile_label">自己紹介</td>
							<td class="td_profile_value">{$introduction}</td>
						</tr>

					</table>

				</div>

				<div>&nbsp;</div>
				<h3 class="memberColor">日記</h3>
{if $diaryHtml|@count == 0}
				<div class="noDiaryList">日記はありません。</div>
{else}
				<table class="table_memberDiaryList">
{foreach from=$diaryHtml item=var name=diary}
					<tr>
						<td class="td_memberDiaryListDate"><div>{$var.time}</div></td>
						<td class="td_memberDiaryListDiaryTitle">
							<div><a href="./friendDiary.php?friendId={$var.userId}&diaryId={$var.diaryId}">
{if $var.cCount == 0}
									{$var.title}
{else}
									{$var.title}({$var.cCount})
{/if}
							</a></div>
						</td>
					</tr>
{/foreach}
				</table>

				<p class="link_move"><a href="./friendDiaryList.php?friendId={$var.userId}">&rarr;&nbsp;もっと見る</a></p>
{/if}

			</div>

			<div class="frame_home_right">

{if $friendHtml|@count == 0}
				<h3 class="memberColor">友達リスト(0)</h3>
{else}
				<h3 class="memberColor">友達リスト({$friendHtml.1.myFriendCnt})</h3>
{/if}

				<div class="frame_home_right_format">
{if $friendHtml|@count == 0}
					<div class="noDiaryList">友達になろう。</div>
					<div>&nbsp;</div>
{else}
					<table class="table_friendList">
{foreach from=$friendHtml item=var name=friend}
{if ($smarty.foreach.friend.iteration + 2) % 3 == 0}
						<tr>
{/if}
							<td class="td_friendList">
								<img src="{$var.friendImage}" class="image_s" alt="{$var.friendName}画像">
								<div>{$var.friendName}</div>
							</td>
{if $smarty.foreach.friend.last}
{if $smarty.foreach.friend.total % 3 == 1}
								<td class="td_friendList">&nbsp;</td>
								<td class="td_friendList">&nbsp;</td>
{elseif $smarty.foreach.friend.total % 3 == 2}
								<td class="td_friendList">&nbsp;</td>
{/if}
{/if}
{if ($smarty.foreach.friend.iteration is div by 3) || $smarty.foreach.friend.last}
							</tr>
{/if}
{/foreach}
					</table>

					<p class="link_move"><a href="./friendList.php?friendId={$userId}">&rarr;&nbsp;もっと見る</a></p>
{/if}
				</div>

				<h3 class="memberColor">グループリスト({$html.1.groupCount})</h3>
				<div class="frame_home_right_format">

					<table class="table_friendList">
{foreach from=$html item=var name=option}
{if ($smarty.foreach.option.iteration + 2) % 3 == 0}
						<tr>
{/if}
							<td class="td_friendList">
								<img src="{$var.imagePath}" class="image_s" alt="{$var.groupName}画像">
								<div>{$var.groupName}</div>
							</td>
{if $smarty.foreach.option.last}
{if $smarty.foreach.option.total % 3 == 1}
								<td class="td_friendList">&nbsp;</td>
								<td class="td_friendList">&nbsp;</td>
{elseif $smarty.foreach.option.total % 3 == 2}
								<td class="td_friendList">&nbsp;</td>
{/if}
{/if}
{if ($smarty.foreach.option.iteration is div by 3) || $smarty.foreach.option.last}
							</tr>
{/if}
{/foreach}
					</table>

					<p class="link_move"><a href="./groupAdmin.php?userId={$userId}">&rarr;&nbsp;もっと見る</a></p>

				</div>

			</div>

		</div>
		
		<script language="javascript" type="text/javascript" src="js/profile.js"></script>
{include file=#footerTsalPath#}