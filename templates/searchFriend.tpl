{config_load file="common.conf"}
{include file=#headerTsalPath# title='T-SAL農園&nbsp;SNS'}

		<div class="frame_page">

			<div class="frame_inputFriendInfo">

				<h3>友達検索</h3>

				<form action="searchFriend.php" method="post">

					<table class="table_inputFriendInfo">
						<tr class="tr_editProfile">
							<td class="td_editProfile_label">ニックネーム</td>
							<td class="td_editProfile_label">
								<input type="text" name="userName" class="input_editProfile" value="{$inUserName}" maxlength="20">
							</td>
						</tr>

						<tr class="tr_editProfile">
							<td class="td_editProfile_label">性別</td>
							<td class="td_editProfile_label">
								<select name="gender" class="select_gender">
									<option value="0" selected="selected"></option>
									<option value="男性" {$man}>男性</option>
									<option value="女性" {$woman}>女性</option>
								</select>
							</td>
						</tr>

						<tr class="tr_editProfile">
							<td class="td_editProfile_label">都道府県</td>
							<td class="td_editProfile_label">
								<select name="place" class="select_gender">
									<option value="" selected="selected"></option>
									<option value="北海道" {$hokkaido}>北海道</option>
									<option value="青森県" {$aomori}>青森県</option>
									<option value="岩手県" {$iwate}>岩手県</option>
									<option value="宮城県" {$miyagi}>宮城県</option>
									<option value="秋田県" {$akita}>秋田県</option>
									<option value="山形県" {$yamagata}>山形県</option>
									<option value="福島県" {$fukushima}>福島県</option>
									<option value="茨城県" {$ibaraki}>茨城県</option>
									<option value="栃木県" {$totigi}>栃木県</option>
									<option value="群馬県" {$gunma}>群馬県</option>
									<option value="埼玉県" {$saitama}>埼玉県</option>
									<option value="千葉県" {$chiba}>千葉県</option>
									<option value="東京都" {$toukyo}>東京都</option>
									<option value="神奈川県" {$kanagawa}>神奈川県</option>
									<option value="新潟県" {$niigata}>新潟県</option>
									<option value="富山県" {$toyama}>富山県</option>
									<option value="石川県" {$ishikawa}>石川県</option>
									<option value="福井県" {$fukui}>福井県</option>
									<option value="山梨県" {$yamanashi}>山梨県</option>
									<option value="長野県" {$nagano}>長野県</option>
									<option value="岐阜県" {$gifu}>岐阜県</option>
									<option value="静岡県" {$shizuoka}>静岡県</option>
									<option value="愛知県" {$aichi}>愛知県</option>
									<option value="三重県" {$mie}>三重県</option>
									<option value="滋賀県" {$shiga}>滋賀県</option>
									<option value="京都府" {$kyouto}>京都府</option>
									<option value="大阪府" {$osaka}>大阪府</option>
									<option value="兵庫県" {$hyougo}>兵庫県</option>
									<option value="奈良県" {$nara}>奈良県</option>
									<option value="和歌山県" {$wakayama}>和歌山県</option>
									<option value="鳥取県" {$tottori}>鳥取県</option>
									<option value="島根県" {$shimane}>島根県</option>
									<option value="岡山県" {$okayama}>岡山県</option>
									<option value="広島県" {$hiroshima}>広島県</option>
									<option value="山口県" {$yamaguchi}>山口県</option>
									<option value="徳島県" {$tokushima}>徳島県</option>
									<option value="香川県" {$kagawa}>香川県</option>
									<option value="愛媛県" {$ehime}>愛媛県</option>
									<option value="高知県" {$kochi}>高知県</option>
									<option value="福岡県" {$fukuoka}>福岡県</option>
									<option value="佐賀県" {$saga}>佐賀県</option>
									<option value="長崎県" {$nagasaki}>長崎県</option>
									<option value="熊本県" {$kumamoto}>熊本県</option>
									<option value="大分県" {$oita}>大分県</option>
									<option value="宮崎県" {$miyazaki}>宮崎県</option>
									<option value="鹿児島県" {$kagoshima}>鹿児島県</option>
									<option value="沖縄県" {$okinawa}>沖縄県</option>
								</select>
							</td>
						</tr>

						<tr class="tr_editProfile">
							<td class="td_editProfile_label">自己紹介</td>
							<td class="td_editProfile_label">
								<input type="text" name="intro" id="intro" class="input_editProfile" maxlength="20" value="{$inIntro}">
							</td>
						</tr>

						<tr>
							<td colspan="2" class="td_button_editProfile">
								<input type="submit" value="検索" class="button_s">
								<input type="hidden" value="search" id="proc" name="proc">
							</td>
						</tr>
					</table>

				</form>

			</div>

{if $flg}
			<div class="frame_selectResult">

				<h3>検索結果</h3>

				<div class="frame_selectResultList">

					<table class="table_selectResultCount">
						<tr>
							<td class="textWeightBold">該当人数：{$html|@count}人</td>
{if $html|@count == 0}
							<td>&nbsp;</td>
{else}
							<td class="right">{$html|@count}人中&nbsp;<span id="min"></span>～<span id="max"></span>人を表示</td>
							<input type="hidden" id="total" name="total" value="{$html|@count}">
{/if}
						</tr>
					</table>

{foreach from=$html item=var name=option}
{if $smarty.foreach.option.total > 0}
{if $smarty.foreach.option.first}
					<table class="table_selectResultList">
						<tbody	id="itemContainer">
{/if}
							<tr>
								<td rowspan="2" class="td_selectResultListImage">
									<img src="{$var.userImage}" class="image_s" alt="{$var.userName}画像">
								</td>
								<td class="td_selectResultListLabel">ニックネーム</td>
								<td class="td_selectResultListMessage">{$var.userName}</td>
								<td rowspan="2" class="td_inviteButton">
									<form action="./searchFriend.php" method="post">
										<input type="hidden" name="userId" value="{$var.userId}">
										<input type="submit" value="申請" class="button_s textColorRed">
									</form>
								</td>
							</tr>
							<tr>
								<td class="td_selectResultListLabel">自己紹介</td>
								<td class="td_selectResultListMessage">{$var.introduction}</td>
							</tr>

							<tr>
								<td colspan="4">&nbsp;</td>
							</tr>

{if $smarty.foreach.option.last}
						</tbody>
					</table>
{/if}
{/if}
{/foreach}
{if $html|@count != 0}
					<div class="right">
						<div class="holder"></div>
					</div>
{/if}

				</div>

			</div>
{/if}

		</div>

		<input type="hidden" id="current" name="current" value=15>
		<input type="hidden" id="size" name="size" value=5>

{include file=#footerTsalPath#}