jQuery(function($){
  /**
   * 画面遷移時に非同期処理を停止
   */
  $("body").bind("ajaxSend", function(c, xhr) {
    $( window ).bind( 'beforeunload', function() {
      xhr.abort();
    });
  });
  /**
   * 24節気表示ようダイアログの生成
   */
  // ダイアログの生成
  var dialog = $( "#dialog" );
  // ダイアログの設定
  dialog.dialog({
      autoOpen: false,
      modal: true,
      show: 'fade',
      hide: 'fade',
      position: 'center center',
      width: 500
  });
  //ダイアログをオープン
  $('#thisYear').click(function() {
     dialog.dialog('open');
  });
  /**
   * 右クリック時にコピーライト表示
   */
  $(document).on('contextmenu', function(e) {
    alert('Copyright(C) 2013 ISB TOHOKU');
    // 通常の右クリックメニューを抑制
    return false;
  });

  /**
   * 圃場状態の非同期での取得
   */
  $(document).ready(function() {
    var fieldState = $('#fieldState');
    var name = 'fieldState=fieldState';
    $.ajax({
      async: true,
      cache: true,
      type:   "POST",
      url:    location.href,
      data:   name,
      dataType: 'html',
      success: function(html) {
        if (html !== '') {
          fieldState.hide().append(html);
          var img = new Image();
          img.src = $('img.image_farm').attr('src');
          img.onload = function () {
            $('#loading').remove();
            fieldState.fadeTo('slow', 1);
            $("a.z40[rel^='prettyPhoto']").prettyPhoto({animation_speed:'fast',slideshow:10000, hideflash: true});
          }
        } else {
          window.console.log('失敗');
        }
      }
    });
  });
});