jQuery(function($){
  pageSet();
  $('#userSearch').click(function() {
    var search = $(this);
    var searchText = $('#search');
    var searchForm = $('#searchForm');

    $.ajax({
      method: "POST",
      url: "userAdmin.php",
      data: searchForm.serialize()+"&proc=searchUser",
      dataType: 'json',
      beforeSend: function () {
        if (jQuery.trim(searchText.val()) !== '') {
          search.attr('disabled', 'disabled');
          return true;
        } else {
          return false;
        }
      },
      success: function(data) {
        if (data.flg === 'OK') {
          if (data.html != '') {
            search.attr('disabled', false);
            $('#admin').html(data.html);
            pageSet()
          } else {
            search.attr('disabled', false);
            alert('該当するユーザはいませんでした。');
          }
        } else {
          search.attr('disabled', false);
          alert('絞り込めませんでした。');
        }
      }
    });
  });

  $('#clear').click(function() {
    var clear = $(this);
    var keyWord = $('#search');
    keyWord.val('');
    clear.attr('disabled', 'disabled');
    $.ajax({
        method: "POST",
        url: "userAdmin.php",
        data: "proc=clear",
        dataType: 'json',
        success: function(data) {
          if (data.flg === 'OK') {
            if (data.html != '') {
              $('#admin').html(data.html);
              pageSet()
            } else {
              alert('エラーが発生しました。');
            }
          } else {
            alert('エラーが発生しました。');
          }
          clear.attr('disabled', false);
        }
      });
  });

  $('#admin').each(function() {
    $(document).on("click", '.textColorRed', function() {
        var button = $(this);
        var form = button.parents('form');
        var optionSelect = form.find('option[selected="selected"]');
        var option = form.find('option:selected');
        var pass = form.find('input[name="pass"]');
        var div1 = form.find('input[name="divisionNo1"]');
        var div2 = form.find('input[name="divisionNo2"]');
        var div3 = form.find('input[name="divisionNo3"]');
        var div4 = form.find('input[name="divisionNo4"]');
        $.ajax({
          method: "POST",
          url: "userAdmin.php",
          data: form.serialize()+"&proc=editUser",
          dataType: 'json',
          beforeSend: function () {
//            if (option.text() !== optionSelect.text() || jQuery.trim(pass.val()) !== '' ) {
              var con = confirm('pass:' + pass.val() +
                          '\n区画番号:' + div1.val() + '-' + div2.val() + '-' + div3.val() + '-' + div4.val() +
                          '\n管理者権限:' + option.text() +
                          '\n変更します。よろしいですか？');
              if (con) {
                $('.button_s').attr('disabled', 'disabled');
                pass.val(jQuery.trim(pass.val()));
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
              $('#' + data.id).html(data.html);
              $('.button_s').attr('disabled', false);
              alert('変更しました。');
            } else {
              if (data.message) {
                $('.button_s').attr('disabled', false);
                alert(data.message);
              } else {
                $('.button_s').attr('disabled', false);
                alert('変更に失敗しました。');
              }
            }
          }
        });
      });
  });

  function pageSet() {
      var per = parseInt($('#current').val());
        $("div.holder").jPages({
          containerID : "admin",// 対象となるまとまり
          perPage     : per,              // 1ページあたりのアイテム数
          startPage   : 1,              // 初期位置となるページ数
          previous    : "←前へ",       // back
          next        : "次へ→",       // next
//	        delay       : 100,            // 表示速度
//	        animation   : "fadeIn",       // アニメーション効果
//	  	    scrollBrowse: true,           // マウススクロール設定
//	  	    keyBrowse   : true,           // キースクロール設定
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
              $('span.total').text(totalItem);
              $('#min').text(min);
              $('#max').text(showItem);
          }         // ページ表示ごとに呼ばれる関数
        });
    }

});