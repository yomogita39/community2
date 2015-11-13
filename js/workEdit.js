document.open();
document.write('<script type="text/javascript" src="./js/import/diaryCommon.js"></script>');
document.close();
var lightBox = function() {
  $("a.z40[rel^='prettyPhoto']").prettyPhoto({animation_speed:'fast',slideshow:10000, hideflash: true});
}
$(document).ready(function(){
  lightBox();
});
jQuery(function($){
  // ルーチンワーク追加ダイアログの生成
  var rDialog = $( "#routineDialog" );
  // ダイアログの設定
  rDialog.dialog({
    autoOpen: false,
    modal: true,
    show: 'fade',
    hide: 'fade',
    position: 'center center',
    width: 300,
    buttons: {
      '追加': function() {
        var routineAdd = $('#routineAdd');
        if (routineAdd.val() !== '') {
          rDialog.dialog('close');
          $.ajax({
            method:   "POST",
            url:    "workEdit.php",
            data:   $("#routineForm").serialize()+'&perDiary='+$('#perDiary').val()+'&proc=routineAdd&date='+$('#date').val(),
            dataType: 'json',
            beforeSend: function() {
              if(jQuery.trim(routineAdd.val()) !== '') {
                return true;
              } else {
                return false;
              }
            },
            success: function (data) {
              if (data.flg === 'OK') {
                routineAdd.val('');
                $('#workHtml').html(data.html)
                alert('追加しました。');
              } else {
                alert('失敗しました。');
              }
            }
          });
        }
      },
      'キャンセル': function() {
        rDialog.dialog('close');
      }
    }
  });
  // ビハインドワーク追加ダイアログの生成
  var bDialog = $( "#behindDialog" );
  // ダイアログの設定
  bDialog.dialog({
    autoOpen: false,
    modal: true,
    show: 'fade',
    hide: 'fade',
    position: 'center center',
    width: 300,
    buttons: {
      '追加': function() {
        var behindAdd = $('#behindAdd');
        if (behindAdd.val() !== '') {
          bDialog.dialog('close');
          $.ajax({
            method:   "POST",
            url:    "workEdit.php",
            data:   $("#behindForm").serialize()+'&perDiary='+$('#perDiary').val()+'&proc=behindAdd&date='+$('#date').val(),
            dataType: 'json',
            beforeSend: function() {
              if(jQuery.trim(behindAdd.val()) !== '') {
                return true;
              } else {
                return false;
              }
            },
            success: function (data) {
              if (data.flg === 'OK') {
                behindAdd.val('');
                $('#workHtml').html(data.html)
                alert('追加しました。');
              } else {
                alert('失敗しました。');
              }
            }
          });
        }
      },
      'キャンセル': function() {
        bDialog.dialog('close');
      }
    }
  });

  $(document).on('click','#behind', function() {
    bDialog.dialog('open');
  });
  $(document).on('click','#routine', function() {
    rDialog.dialog('open');
  });
  $(document).on('click','.up', function() {
    var up = $(this);
    $('#prioritySave').show();
//    var upId = up.parent().children('input[name="behindId[]"]').val();
//    var editForm = $("#editForm");
    var move = up.parent().prev();
    var td = up.parent().parent();
    var moveto = up.parent().parent().prev().children('.move');
    var tdto = up.parent().parent().prev();
    move.remove();
    moveto.remove();
    td.prepend(moveto);
    tdto.prepend(move);
  });
  $(document).on('click','.down', function() {
      var down = $(this);
      $('#prioritySave').show();
//	    var upId = up.parent().children('input[name="behindId[]"]').val();
//	    var editForm = $("#editForm");
      var move = down.parent().prev().prev();
      var td = down.parent().parent();
      var moveto = down.parent().parent().next().children('.move');
      var tdto = down.parent().parent().next();
      move.remove();
      moveto.remove();
      td.prepend(moveto);
      tdto.prepend(move);
  });
  $(document).on('click','#prioritySave', function() {
    var save = $(this);
    save.attr('disabled', 'disabled');
    var editForm = $("#editForm");
    $.ajax({
      method:   "POST",
      url:    "workEdit.php",
      data:   editForm.serialize() + '&proc=priority',
      dataType: 'json',
      success: function (data) {
        save.attr('disabled', false);
        if (data.flg === 'OK') {
          $('#prioritySave').hide();
          alert('保存しました。')
        } else {
          alert('失敗しました。');
        }
      }
    });
  });
  $(document).on('click', '#workButton', function() {
    var button = $(this);
    var editForm = $("#editForm");
    $.ajax({
      method:   "POST",
      url:    "workEdit.php",
      data:   editForm.serialize()+'&proc=workPre',
      dataType: 'json',
      beforeSend: function() {
        if($("#editForm :checked").length > 0) {
          if (confirm('作業を完了します。')) {
            button.attr('disabled', 'disabled');
            return true;
          } else {
            return false;
          }
        } else {
          return false;
        }
      },
      success: function (data) {
        button.attr('disabled', false);
        if (data.flg === 'OK') {
          alert('お疲れさまでした');
          window.location = 'workEdit.php?'+ '&date=' +
          $('#date').val() + '&perDiary=' + data.perUserId;
        } else {
          alert('失敗しました。');
        }
      }
    });
  });
  $(document).on('click', '#planButton', function() {
      var button = $(this);
      var form = $("#workPlan");
      $.ajax({
        method:   "POST",
        url:    "workEdit.php",
        data:   form.serialize()+'&proc=planAdd'+'&date='+$('#date').val(),
        dataType: 'json',
        beforeSend: function() {
          if($("#plan").val() !== '' || $('#performance').val() !== '') {
            if (confirm('保存します。')) {
              button.attr('disabled', 'disabled');
              $('#plan').val(jQuery.trim($('#plan').val()));
              $('#performance').val(jQuery.trim($('#performance').val()));
              return true;
            } else {
              return false;
            }
          } else {
            return false;
          }
        },
        success: function (data) {
          button.attr('disabled', false);
          if (data.flg === 'OK') {
            alert('保存しました');
          } else {
            alert('失敗しました。');
          }
        }
      });
    });
  $(document).on('click','#msgsend', function() {
    var button = $(this);
    var form = $('form');
    var text = $("#commText");
    $.ajax({
        method:   "POST",
        url:    "workEdit.php",
        data:   form.serialize()+'&proc=farmMessageAdd',
        dataType: 'json',
        beforeSend: function() {
          if(jQuery.trim(text.val()) !== '' || jQuery.trim($('#postPhotoName').val()) !== '') {
            button.attr('disabled', 'disabled');
            return true;
          } else {
            return false;
          }
        },
        success: function (data) {
          button.attr('disabled', false);
          if (data.flg === 'OK') {
            text.val('');
            $('#commHtml').html(data.html);
            $('#preview').html('画像(2Mバイト以内のJPEG、PNG、GIF形式。)');
            lightBox();
          } else {
            $('#preview').html('画像(2Mバイト以内のJPEG、PNG、GIF形式。)');
            alert('失敗しました。');
          }
        }
      });
  });
  $(document).on('click','#harvestButton', function() {
    var harvest = $(this);
    var form = $("form");
    $.ajax({
      method:   "POST",
      url:    "workEdit.php",
      data:   form.serialize()+'&proc=harvestAdd',
      dataType: 'json',
      beforeSend: function() {
        $('input[type = "text"]').each(function() {
          if (jQuery.trim($(this).val()) === '') {
            return false;
          }
          //$(this).val(jQuery.trim($(this).val()));
        });
        harvest.attr('disabled', 'disabled');
        return true;
      },
      success: function (data) {
        harvest.attr('disabled', false);
        if (data.flg === 'OK') {
          $('#harvestHtml').html(data.html);
          $('#harvestTotal').html(data.tableHtml);
          alert('保存しました');
        } else {
          alert('失敗しました。');
        }
      }
    });
  });

  $(document).on('click','#workDel', function() {
    var del = $(this);
    var editForm = $("#editForm");
    $.ajax({
      method:   "POST",
      url:    "workEdit.php",
      data:   editForm.serialize()+'&proc=workDel',
      dataType: 'json',
      beforeSend: function() {
        if($("#editForm :checked").length > 0) {
          if (confirm('作業を削除します')) {
            del.attr('disabled', 'disabled');
            return true;
          } else {
            return false;
          }
        } else {
          return false;
        }
      },
      success: function (data) {
        del.attr('disabled', false);
        if (data.flg === 'OK') {
          alert('削除しました。');
          window.location = 'workEdit.php?'+ '&date=' + $('#date').val()
                              + '&perDiary=' + data.perUserId;
        } else {
          alert('失敗しました。');
        }
      }
    });
  });
  $(document).on('change','#selectName', function() {
    var name = $('#selectName').val();
    if (name !== '') {
      $('#crops').val(name);
    }
  });
  $(document).on('change','#selectDestination', function() {
    var destination = $('#selectDestination').val();
    if (destination !== '') {
      $('#destination').val(destination);
    }
  });
  $(document).on('click','.tableTogle', function() {
    switch ($(this).text()) {
      case '今月の収穫一覧':
      case '今月の経費一覧':
      case '今月の作業時間一覧':$(this).text('閉じる');$('#'+$(this).attr('id')).slideDown();
      default :$(this).text($(this).attr('title'));$('#'+$(this).attr('id')).slideUp();
    }
      $("#totalTbl").slideDown();
    });
  $(document).on('click','#close', function() {
      $("#totalTbl").slideUp();
    });
});