jQuery(function($){
  pageSet();
  $(document).on("click", '#new', function() {
    var infoButton = $(this);
    var info = $('#info');
    var form = infoButton.parents('form');
    $.ajax({
      method: "POST",
      url: "./api/infoAdminScript.php",
      data: form.serialize()+"&proc=new",
      dataType: 'json',
      beforeSend: function () {
        if (jQuery.trim(info.val()) !== '') {
          if (confirm('登録します。よろしいですか？')) {
            infoButton.attr('disabled', 'disabled');
              return true;
            } else {
              return false;
            }
          } else {
            return false;
          }
      },
      success: function(data) {
        if (data.flg === 'OK') {
          if (data.html != '') {
            $('#infoList').html(data.html);
            pageSet();
            alert('登録しました。');
          } else {
            alert('リストの取得に失敗しました。');
          }
        } else {
          alert('登録に失敗しました。');
        }
        infoButton.attr('disabled', false);
      }
    });
  });

  $(document).on("click", '.edit', function() {
    var editButton = $(this);
      var editForm = editButton.parents('form');
      var editInfo = editButton.parents('tr').children('.td_infoAdminList_message').text();
      var dInfo = $('#dInfo');
      var id = editForm.find('input[name=infoId]').val();
      var dId = $('#dId');
      dInfo.val(editInfo);
      dId.val(id);
      dialog.dialog('open');
  });

//ダイアログの生成
  var dialog = $( "#dialog" );
  // ダイアログの設定
  dialog.dialog({
      autoOpen: false,
      modal: true,
      show: 'fade',
      hide: 'fade',
      position: 'center center',
      width: 700,
      buttons: {
        '変更': function() {
          var dInfo = $('#dInfo');
          var dForm = $('#dForm');
          var dId = $('#dId');
          var button = $('button[type="button"]');
            $.ajax({
              method: "POST",
              url: "./api/infoAdminScript.php",
              data: dForm.serialize()+"&proc=edit",
              dataType: 'json',
              beforeSend: function () {
                if (jQuery.trim(dInfo.val()) !== '') {
                  if (confirm('変更します。よろしいですか？')) {
                  button.attr('disabled', 'disabled');
                  dialog.dialog('close');
                    return true;
                  } else {
                    return false;
                  }
                } else {
                  return false;
                }
              },
              success: function(data) {
                var editForm = $('#'+data.id);
                if (data.flg === 'OK') {
                  if (data.message != '') {
                    editForm.children('.td_infoAdminList_message').html(data.message);
                    editForm.children('.td_infoAdminList_userName').html(data.name);
                    alert('変更しました。');
                  } else {
                    alert('リストの取得に失敗しました。');
                  }
                } else {
                  alert('変更に失敗しました。');
                }
                dInfo.val('');
                dId.val('');
                button.attr('disabled', false);
              }
            });
        },
        'キャンセル': function() {
          dialog.dialog('close');
          $('button[type="button"]').attr('disabled', false);
        }
      }
  });

  $(document).on("click", '.del', function() {
    var delButton = $(this);
    var delForm = delButton.parents('form');
    $.ajax({
      method: "POST",
      url: "./api/infoAdminScript.php",
      data: delForm.serialize()+"&proc=del",
      dataType: 'json',
      beforeSend: function () {
        if (confirm('削除します。よろしいですか？')) {
          delButton.attr('disabled', 'disabled');
          return true;
        } else {
          return false;
        }
      },
      success: function(data) {
        if (data.flg === 'OK') {
          if (data.html != '') {
            $('#infoList').html(data.html);
            pageSet();
            alert('削除しました。');
          } else {
            alert('リストの取得に失敗しました。');
          }
        } else {
          alert('削除に失敗しました。');
        }
        delButton.attr('disabled', false);
      }
    });
  });

  function pageSet() {
    var per = parseInt($('#current').val());
      $("div.holder").jPages({
        containerID : "infoList",// 対象となるまとまり
        perPage     : per,              // 1ページあたりのアイテム数
        startPage   : 1,              // 初期位置となるページ数
        previous    : "←前へ",       // back
        next        : "次へ→",       // next
//        delay       : 100,            // 表示速度
//        animation   : "fadeIn",       // アニメーション効果
//  	    scrollBrowse: true,           // マウススクロール設定
//  	    keyBrowse   : true,           // キースクロール設定
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
            $('span#total').text(totalItem);
            $('#min').text(min);
            $('#max').text(showItem);
        }         // ページ表示ごとに呼ばれる関数
      });
  }
});