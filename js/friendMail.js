jQuery(function($){
  var timer = setInterval(function(){
    $.ajax({
      method: "POST",
      url: "invitePost.php",
      data: "proc=unreadCount",
      success: function (html) {
        if (html != '') {
          $('#friendList').html(html);
        }
      }
    });
    $.ajax({
        method: "POST",
        url: "invitePost.php",
        data: "proc=groupUnread",
        success: function (html) {
          if (html != '') {
            $('#groupList').html(html);
          }
        }
      });
  },30000);
  var timer = setInterval(function(){
      $.ajax({
        method: "POST",
        url: "invitePost.php",
        data: "proc=unreadCount",
        success: function (html) {
          if (html != '') {
            $('#friendList').html(html);
          }
        }
      });
    },30000);

  // 文字カウント
  $('textarea').stringCount({
    max : 126, // 最大入力文字数
    button : '#mutterButton' // 投稿用ボタン
  });

  $('#mutterButton').click(function () {
    var button = $(this);
    var ms = $('#message');
    if ($('table').hasClass('table_mutterList')) {
      var flg = true;
      var mutter = 'mutter';
    } else {
      var flg = false;
      var mutter = 'mutter2';
    }
    $.ajax({
      method: "POST",
      url: "mutter.php",
      data: $('form').serialize() +"&proc=" + mutter,
      beforeSend: function () {

        if (jQuery.trim($('#message').val()) !== '' && $('#public').val() !== '') {
          button.attr('disabled', 'disabled');
          return true;
        } else {
          return false;
        }

      },
        success: function (html) {
          if (html !== 'NG') {
            button.attr('disabled', false);
            $('#itemContainer').html(html);
            ms.val('');
            $('textarea').countReset('#count', 0);
            var per = parseInt($('#current').val());
            $("div.holder").jPages({
              containerID : "itemContainer",// 対象となるまとまり
              perPage     : per,              // 1ページあたりのアイテム数
              startPage   : 1,              // 初期位置となるページ数
              previous    : "←前へ",       // back
              next        : "次へ→",       // next
              delay       : 100,            // 表示速度
              animation   : "fadeIn",       // アニメーション効果
//        	    scrollBrowse: true,           // マウススクロール設定
//        	    keyBrowse   : true,           // キースクロール設定
              callback    : pageSet         // ページ表示ごとに呼ばれる関数
            });
//            window.location = "home.php";
          }
        }
      });
  });
  function pageSet(pages, items) {

      var size = parseInt($('#size').val());
      var totalItem = parseInt($('input#total').val());
      var totalPage = pages.count;
      var showPage = pages.current;
      var showItem = 0;
      var min = (showPage * size) - (size - 1);

      if (totalPage === showPage) {
        if (totalItem % size === 0) {
          showItem = ((showPage) * size);
        } else {
          showItem = ((showPage - 1) * size) + (totalItem % size);
        }
      } else {
        showItem = showPage * size;
      }
      $('span#total').text(totalItem);
      $('#min').text(min);
      $('#max').text(showItem);
    }
});