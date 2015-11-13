<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link rel="shortcut icon" href="./favicon.ico">
<link rel="stylesheet" href="./css/style_tsal.css?20131017=20131017" type="text/css" />
<link rel="stylesheet" href="./css/common.css" type="text/css" />
<link rel="stylesheet" href="./css/ui-lightness/jquery.ui.all.css" type="text/css" />
<link rel="stylesheet" href="./css/prettyPhoto.css" />
<link rel="stylesheet" href="./css/basic.css" type="text/css" />
<link rel="stylesheet" href="./css/dropzone.css" type="text/css" />
<script src='http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js'></script>
<script src='http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js'></script>
<script src="js/jquery.prettyPhoto.js"></script>
<script src="js/invitePost.js"></script>
<script src='js/itc.line.common.js?20131015=20131015'></script>
<script src='js/dropUpload.js?20131015=20131015'></script>
<script src="js/dropzone-amd-module.js"></script>
<script src="js/dropzone.js?20131015=20131015"></script>
<script src="js/jquery.stringCount.js?20131015=20131015"></script>
<script src='js/jquery.upload-1.0.2.js'></script>

<title>{$title|default:'T-SAL農園&nbsp;SNS'}</title>
</head>
<body>

	<div class="display_home">
		<div class="headTop">

			<img src="./templates/image/logo.png" alt="ロゴ画像">

			<form action="./home.php" class="headButton1">
				<input type="submit" value="ホーム" class="headButton" onmouseover="this.style.backgroundColor='#819ff7'" onmouseout="this.style.backgroundColor='#ffffff'">
			</form>

			<form action="./searchFriend.php" class="headButton2">
				<input type="submit" value="友達検索" class="headButton" onmouseover="this.style.backgroundColor='#819ff7'" onmouseout="this.style.backgroundColor='#ffffff'">
			</form>

			<form action="./setting.php" class="headButton3">
				<input type="submit" value="設定" class="headButton" onmouseover="this.style.backgroundColor='#819ff7'" onmouseout="this.style.backgroundColor='#ffffff'">
			</form>

			<form action="./logout.php" class="headButton4">
				<input type="button" value="ログアウト" id="logoutButton" class="headButton" onmouseover="this.style.backgroundColor='#819ff7'" onmouseout="this.style.backgroundColor='#ffffff'">
			</form>

{if $authority != 0}
			<form action="./admin.php" class="headButton5">
				<input type="submit" value="管理画面" class="headButton" onmouseover="this.style.backgroundColor='#819ff7'" onmouseout="this.style.backgroundColor='#ffffff'">
			</form>
{/if}

		</div>

		<div class="headBottom">

			<div class="headMenuNews">
				<table class="headMenuNewsTb">
					<tr>
						<td>
							<form action="./applyFriendList.php" class="headMenuNewsPosition">
{if 1 <= $newFriendCount && $newFriendCount <= 9}
								<span class="headMenuCount1" id="headFriend">{$newFriendCount}</span>
{elseif 10 <= $newFriendCount && $newFriendCount <= 99}
								<span class="headMenuCount2" id="headFriend">{$newFriendCount}</span>
{elseif 100 <= $newFriendCount && $newFriendCount <= 999}
								<span class="headMenuCount3" id="headFriend">{$newFriendCount}</span>
{else}
					<span id="headFriend"></span>
{/if}
								<input type="submit" value="" id="friend" class="{$friendClass}" {$friendDisabled}>
							</form>
						</td>

						<td>
							<form action="./inviteList.php" class="headMenuNewsPosition">
{if 1 <= $newGroupCount && $newGroupCount <= 9}
								<span class="headMenuCount1" id="headGroup">{$newGroupCount}</span>
{elseif 10 <= $newGroupCount && $newGroupCount <= 99}
								<span class="headMenuCount2" id="headGroup">{$newGroupCount}</span>
{elseif 100 <= $newGroupCount && $newGroupCount <= 999}
								<span class="headMenuCount3" id="headGroup">{$newGroupCount}</span>
{else}
					<span id="headGroup"></span>
{/if}
								<input type="submit" value="" id="post" class="{$class}" {$disabled}>
							</form>
						</td>

						<td>
							<form action="./unreadList.php" class="headMenuNewsPosition">
{if 1 <= $unreadCount && $unreadCount <= 9}
								<span class="headMenuCount1" id="headMail">{$unreadCount}</span>
{elseif 10 <= $unreadCount && $unreadCount <= 99}
								<span class="headMenuCount2" id="headMail">{$unreadCount}</span>
{elseif 100 <= $unreadCount && $unreadCount <= 999}
								<span class="headMenuCount3" id="headMail">{$unreadCount}</span>
{else}
					<span id="headMail"></span>
{/if}
								<input type="submit" value="" id="mail" class="{$mailClass}" {$mailDisabled}>
							</form>
						</td>
					</tr>
				</table>

			</div>

			<table class="headMenu">
				<tr>
					<td>
						<form action="./friendList.php">
							<input type="submit" value="友達" class="headMenuStyle" onmouseover="this.style.backgroundColor='#819ff7'" onmouseout="this.style.backgroundColor='#92d050'">
						</form>
					</td>
					<td>
						<form action="./groupAdmin.php">
							<input type="submit" value="グループ" class="headMenuStyle" onmouseover="this.style.backgroundColor='#819ff7'" onmouseout="this.style.backgroundColor='#92d050'">
						</form>
					</td>
					<td>
						<form action="./myDiaryList.php">
							<input type="submit" value="日記" class="headMenuStyle" onmouseover="this.style.backgroundColor='#819ff7'" onmouseout="this.style.backgroundColor='#92d050'">
						</form>
					</td>
					<td>
						<form action="./workEdit.php">
							<input type="submit" value="農業日誌" class="headMenuStyle" onmouseover="this.style.backgroundColor='#819ff7'" onmouseout="this.style.backgroundColor='#92d050'">
						</form>
					</td>
					<td>
						<form action="./management.php">
							<input type="submit" value="経営日誌" class="headMenuStyle" onmouseover="this.style.backgroundColor='#819ff7'" onmouseout="this.style.backgroundColor='#92d050'">
						</form>
					</td>
				</tr>
			</table>

		</div>