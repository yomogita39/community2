$(window).load(function() {
  pageSet();
  appSet();
  function pageSet() {
    var per = parseInt($('#current').val());
    $("div#holder").jPages({
      containerID : "admin",// 対象となるまとまり
      perPage     : per,              // 1ページあたりのアイテム数
      startPage   : 1,              // 初期位置となるページ数
      previous    : "←前へ",       // back
      next        : "次へ→",       // next
//    	        delay       : 100,            // 表示速度
//    	        animation   : "fadeIn",       // アニメーション効果
//    	  	    scrollBrowse: true,           // マウススクロール設定
//    	  	    keyBrowse   : true,           // キースクロール設定
      callback    : function (pages, items) {
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
//              $('span#total').text(totalItem);
        $('#min').text(min);
        $('#max').text(showItem);
      }         // ページ表示ごとに呼ばれる関数
    });
  }
  function appSet() {
    var per = parseInt($('#current').val());
    $("div#appHolder").jPages({
      containerID : "app",// 対象となるまとまり
      perPage     : per,              // 1ページあたりのアイテム数
      startPage   : 1,              // 初期位置となるページ数
      previous    : "←前へ",       // back
      next        : "次へ→",       // next
//    		        delay       : 100,            // 表示速度
//    		        animation   : "fadeIn",       // アニメーション効果
//    		  	    scrollBrowse: true,           // マウススクロール設定
//    		  	    keyBrowse   : true,           // キースクロール設定
      callback    : function (pages, items) {
        var size = parseInt($('#size').val());
        var totalItem = parseInt($('input#appTotal').val());
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
//             $('span#total').text(totalItem);
        $('#appMin').text(min);
        $('#appMax').text(showItem);
      }         // ページ表示ごとに呼ばれる関数
    });
  }
});

jQuery(function($){
  $('#admin').each(function() {
    $(document).on("click", '.textColorRed', function() {
      var button = $(this);
      var form = button.parents('form');
      var div1 = form.find('input[name="divisionNo1"]');
      var div2 = form.find('input[name="divisionNo2"]');
      var div3 = form.find('input[name="divisionNo3"]');
      var div4 = form.find('input[name="divisionNo4"]');
      $.ajax({
        method: "POST",
        url: "regUserAdmin.php",
        data: form.serialize()+"&proc=reco",
        dataType: 'json',
        beforeSend: function () {
          var con = confirm('承認します。よろしいですか？');
          if (con) {
            $('.button_s').attr('disabled', 'disabled');
            div1.val(jQuery.trim(div1.val()));
            div2.val(jQuery.trim(div2.val()));
            div3.val(jQuery.trim(div3.val()));
            div4.val(jQuery.trim(div4.val()));
          }
          return con;
//            } else {
//              alert('パスワードは半角英数6文字以上です。');
//              return false;
//            }
        },
        success: function(data) {
          if (data.flg === 'OK') {
            alert(data.message);
            window.location = 'regUserAdmin.php';
          } else {
            alert(data.message);
          }
        }
      });
    });
  });
});