// 日誌に使用する共通スクリプト
document.open();
document.write('<script type="text/javascript" src="./js/import/diaryCommon.js"></script>');
document.close();
jQuery(function($) {
  var autocomplete = function () {
    $( ".auto" ).autocomplete({
      source: function(req, resp) {
          // 現在フォーカスのあたっている要素のidを取得する
          var name = document.activeElement.name;
          $.ajax({
                method:   "POST",
                url:    "management.php",
                data:   "proc="+name+"&param="+req.term+"&perDiary="+$('#perDiary').val(),
                dataType: 'json',
                beforeSend: function() {
                  if(jQuery.trim(req.term) !== '') {
                    return true;
                  } else {
                    return false;
                  }
                },
                success: function (data) {
                  resp(data);
                }
              });
        },
        select: function (ev, ui) {
          $(this).blur().val(ui.item.value).focus();
          return false;
        }
      });
  }
  autocomplete();
  // 保存、投稿ボタンの制御
  $(document).on('click', '.button_s', function() {
    // クリックされたボタン
      var button = $(this);
      // クリックされたボタンの祖先要素となるform
      var form = button.parents("form");
      // management.phpに非同期で送信
      $.ajax({
        method:   "POST",
        url:    "management.php",
        data:   form.serialize()+'&proc='+button.attr("id")+"&date="+$("#date").val()+"&perDiary="+$('#perDiary').val(),
        dataType: 'json',
        beforeSend: function() {
          if (confirm(button.val() + 'します')) {
            button.attr('disabled', 'disabled');
            return true;
          } else {
            return false;
          }
        },
        success: function (data) {
          button.attr('disabled', false);
          if (data.flg === 'OK') {
            alert(button.val() + 'しました');
            form.find('#'+data.proc+"Html").html(data.html);
            $('#'+data.proc+"Total").html(data.total);
            autocomplete();
          } else {
            alert(data.message);
          }
        },
        error: function (data) {
          button.attr('disabled', false);
        }
      });
    });
  // 一覧表示、非表示切替
  $(document).on('click','.totalToggle', function() {
    // クリックされた要素
    var total = $(this);
    // hrefに記述してある表示するべきテーブル
    var id = total.attr('href');
    switch ($(this).text()) {
      case '今月の収穫一覧':
      case '今月の経費一覧':
      case '今月の作業時間一覧':total.text('閉じる'); $(id).slideDown(); break;
      default :total.text(total.attr('title')); $(id).slideUp(); break;
    }
  });
});
