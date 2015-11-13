{config_load file="common.conf"}
{include file=#headerTsalPath# title='T-SAL農園&nbsp;SNS'}

		<div class="frame_page">

			<div class="frame_home_left">

				<div class="frame_image">
					<img src="{$userImage}" class="image_l" alt="{$userName}画像">
					<div class="image_name">{$userName}</div>
					<div>&nbsp;</div>
					<div class="login_time">前回ログイン</div>
					<div class="login_time">{$lastLoginTime}</div>
				</div>
				
				<div class="frame_home_left_bottom">
					<div class="center"><a href="profileEdit.php">プロフィール編集</a></div>
					<h3>暦</h3>
					<div class="frame_farm">
						<table class="table_farmState">
							<tr>
								<td class="td_homeCalendarLabel">日付</td>
								<td class="td_homeCalendarfarmStateValue">{$smarty.now|date_format:"%m月%d日"}({$week})<br>{$qreki.rokuyou}
{if !empty($holiday)}
<br>
{$holiday}
{/if}
</td>
							</tr>
							<tr>
								<td class="td_homeCalendarLabel">旧暦</td>
								<td class="td_homeCalendarfarmStateValue">{$qreki.year}年{$qreki.month}月{$qreki.day}日</td>
							</tr>
{if !empty($season)}
							<tr>
								<td class="td_homeCalendarLabel">二十四節気</td>
								<td class="td_homeCalendarfarmStateValue">{$season.name}&nbsp;{$season.time}</td>
							</tr>
{/if}
{if !empty($seasonCount)}
							<tr>
								<td class="td_homeCalendarLabel">二十四節気</td>
								<td class="td_homeCalendarfarmStateValue">{$seasonCount.name}まで&nbsp;あと{$seasonCount.count}日</td>
							</tr>
{/if}
						</table>
						<div><a href="#" onclick="return false" id="thisYear">今年度の二十四節気</a></div>
					</div>
					<div>&nbsp;</div>
					
					<div id="dialog" title="二十四節気" style="display:none">
						<div class="position_table_season24">
							<table class="table_season24">
								<tr>
									<th class="th_season24">名称</th>
									<th class="th_season24">日時</th>
								</tr>

{foreach from=$seasonHtml item=var name=season}
{if $smarty.foreach.season.iteration < 13}
								<tr>
									<td class="td_season24_name">{$var.name}</td>
									<td class="td_season24_date">{$var.date}&nbsp;{$var.time}</td>
								</tr>
{/if}
{if $smarty.foreach.season.iteration == 12}
							</table>
{/if}
{if $smarty.foreach.season.iteration == 13}
							<table class="table_season24 position_table_season24_half">
								<tr>
									<th class="th_season24">名称</th>
									<th class="th_season24">日時</th>
								</tr>
{/if}

{if 13 <= $smarty.foreach.season.iteration}
								<tr>
									<td class="td_season24_name">{$var.name}</td>
									<td class="td_season24_date">{$var.date}&nbsp;{$var.time}</td>
								</tr>
{/if}
{/foreach}
							</table>

						</div>
					</div>

{if !empty($fieldState)}
					<h3>圃場状態</h3>
					<div class="frame_farm">
						<a href="{$fieldState}" rel="prettyPhoto" title="" class="z40">
						<img src="{$fieldState}" class="image_farm" alt="圃場画像">
						</a>
						<table class="table_farmState">
							<tr>
								<td colspan="2">{$regTime}</td>
							</tr>
							<tr>
								<td class="td_farmStateLabel">気温(℃)</td>
								<td class="td_farmStateValue">{$tmp}</td>
							</tr>
							<tr>
								<td class="td_farmStateLabel">気圧(hPa)</td>
								<td class="td_farmStateValue">{$prss}</td>
							</tr>
							<tr>
								<td class="td_farmStateLabel">湿度(%)</td>
								<td class="td_farmStateValue">{$rhum}</td>
							</tr>
							<tr>
								<td class="td_farmStateLabel">日照(lux)</td>
								<td class="td_farmStateValue">{$sun}</td>
							</tr>
						</table>
					</div>
{/if}
				</div>

			</div>

			<div class="frame_home_center">

				<h3>インフォメーション</h3>
				<div class="information">
{foreach from=$info item=var name=info}
{if $smarty.foreach.info.first}
					<div>{$var.time}</div>
					<ul class="ul_information">
{/if}
						<li class="li_information">{$var.message}</li>
{if $smarty.foreach.info.last}
					</ul>
{/if}
{/foreach}
				</div>
				<h3>今日の予定</h3>
				<div class="information">
					<ul class="ul_information">
{if !empty($plan)}
						<li class="li_information">{$plan}</li>
{else}
						<li class="li_information">予定は特にありません。よい一日をお過ごしください。</li>
{/if}
					</ul>
				</div>

				<h3>ひとり言</h3>
				<div class="frame_home_center_format">
					<form>
						<textarea id="message" name="message" class="textarea_mutter"></textarea>

						<table class="table_public">
							<tr>
								<td class="center">公開範囲</td>
								<td>
									<select id="public" name="public" class="select_public">
										<option value="0" selected="selected">全員に公開</option>
										<option value="1">友達まで公開</option>
										<option value="2">公開しない</option>
									</select>
								</td>
								<td class="right"><input type="button" id="mutterButton" value="投稿" class="button_s textColorRed"></td>
							</tr>
						</table>
					</form>

{if $mutterHtml|@count != 0}
					<table class="table_mutterList">
					<tbody id="itemContainer">
{foreach from=$mutterHtml item=var name=mutter}
						<tr class="tr_mutterList">
							<td class="td_mutterListImage">
								<a href="./profile.php?userId={$var.userId}"><img src="{$var.userImage}" class="image_s" alt="{$var.userName}画像"></a>
							</td>
							<td class="td_mutterListMessage">
								<div>{$var.time}&nbsp;<a href="./profile.php?userId={$var.userId}">{$var.userName}</a></div>
								<div>&nbsp;</div>
								<div>{$var.message}</div>
							</td>
						</tr>
{/foreach}
					</tbody>
					</table>

					<p class="link_move"><a href="mutterList.php">&rarr;&nbsp;もっと見る</a></p>
{else}
					<div>&nbsp;</div>
{/if}
				</div>

				<h3>日記</h3>
{if $diaryHtml|@count == 0}
				<div class="frame_home_center_format"><a href="./createDiary.php">日記を作成しよう。</a></div>
{else}
				<div class="frame_home_center_format">
					<table class="table_homeDiaryList">
{foreach from=$diaryHtml item=var name=diary}
						<tr>
							<td class="td_homeDiaryListDate">{$var.time}</td>
							<td class="td_homeDiaryListUserName">
								<a href="./profile.php?userId={$var.userId}">{$var.userName}</a>
							</td>
							<td class="td_homeDiaryListTitle">
								<a href="./friendDiary.php?friendId={$var.userId}&diaryId={$var.diaryId}">
{if $var.cCount == 0}
									{$var.title}
{else}
									{$var.title}({$var.cCount})
{/if}
								</a>
							</td>
						</tr>
{/foreach}
					</table>
					<p class="link_move"><a href="./diaryList.php">&rarr;&nbsp;もっと見る</a></p>

				</div>
{/if}

			</div>

			<div class="frame_home_right">

				<h3>友達リスト({$friendHtml.1.myFriendCnt})</h3>
				<div class="frame_home_right_format">
					<table class="table_friendList">
					<tbody id="friendList">
{foreach from=$friendHtml item=var name=friend}
{if ($smarty.foreach.friend.iteration + 2) % 3 == 0}
						<tr>
{/if}
							<td class="td_friendList">
								<a href="./friendTimeLine.php?friendId={$var.friendId}" class="position_homeUnreadCount">
									<img src="{$var.friendImage}" alt="{$var.friendName}画像" class="image_s">
{if 1 <= $var.friendUnread && $var.friendUnread <= 9}
	              					<span class="homeUnreadCount1">{$var.friendUnread}</span>
{elseif 10 <= $var.friendUnread && $var.friendUnread <= 99}
	              					<span class="homeUnreadCount2">{$var.friendUnread}</span>
{elseif 100 <= $var.friendUnread && $var.friendUnread <= 999}
	              					<span class="homeUnreadCount3">{$var.friendUnread}</span>
{/if}
								</a>
								<div><a href="./friendTimeLine.php?friendId={$var.friendId}">{$var.friendName}({$var.friendCnt})</a></div>
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
					</tbody>
					</table>
					<p class="link_move"><a href="./friendList.php">&rarr;&nbsp;もっと見る</a></p>
				</div>

				<h3>グループリスト({$html.1.groupCount})</h3>
				<div class="frame_home_right_format">

					<table class="table_friendList">
					<tbody id="groupList">
{foreach from=$html item=var name=option}
{if ($smarty.foreach.option.iteration + 2) % 3 == 0}
						<tr>
{/if}
							<td class="td_friendList">
								<a href="./timeLine.php?groupId={$var.groupId}" class="position_homeUnreadCount">
									<img src="{$var.imagePath}" alt="{$var.groupName}画像" class="image_s">
{if 1 <= $var.groupUnread && $var.groupUnread <= 9}
	              					<span class="homeUnreadCount1">{$var.groupUnread}</span>
{elseif 10 <= $var.groupUnread && $var.groupUnread <= 99}
	              					<span class="homeUnreadCount2">{$var.groupUnread}</span>
{elseif 100 <= $var.groupUnread && $var.groupUnread <= 999}
	              					<span class="homeUnreadCount3">{$var.groupUnread}</span>
{/if}
								</a>
								<div><a href="./timeLine.php?groupId={$var.groupId}">{$var.groupName}({$var.count})</a></div>
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
					</tbody>
					</table>

					<p class="link_move"><a href="./groupAdmin.php">&rarr;&nbsp;もっと見る</a></p>

				</div>
				<h3>リンク集</h3>
				<div class="frame_home_right_format">

					<table class="table_friendList">
						<tbody id="groupList">
							<tr>
								<td>
									<a href="http://t-sal.net/">東北スマートアグリカルチャー研究会</a>
								</td>
							</tr>
						</tbody>
					</table>
				</div>

			</div>

		</div>
		
		<link rel="stylesheet" href="./css/prettyPhoto.css" />
		<link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/start/jquery-ui.css" rel="stylesheet">
		<script language="javascript" src="js/jquery.prettyPhoto.js" type="text/javascript"></script>
		<script language="javascript" src="js/lightbox.js" type="text/javascript"></script>
		<script language="javascript" src="js/friendMail.js" type="text/javascript"></script>
		<script language="javascript" src="js/homeScript.js" type="text/javascript"></script>
{include file=#footerTsalPath#}