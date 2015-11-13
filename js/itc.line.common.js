var lightBox = function() {
  $("a.z40[rel^='prettyPhoto']").prettyPhoto({animation_speed:'fast',slideshow:10000, hideflash: true});
}
var countReset = function(elem, count) {
  elem.countReset('#count', count);
}
$(window).load(function(){
  // スクロール要素の高さ
    var scrollHeight = document.getElementById("timeline").scrollHeight;
    jQuery.fx.off = true;
    $('.frame_timeLine_message').animate({ scrollTop: scrollHeight }, 'slow');
    jQuery.fx.off = false;
    lightBox();
});
var itc=itc || {};
itc.line={};
itc.line.common = {
  msgurl: "api/api_thread.php",
  topurl: "login.php",
  clkButton: null,
  init: function(){
    itc.line.common.startTimer();


    var msg = $('#msginput');
    msg.css('color', 'gray');
    var defaultvalue = document.getElementById("msginput").defaultValue;
    msg.focus(function() {
      if(msg.val() == defaultvalue){
      msg.val('');
      msg.css('color', 'black');
      countReset(msg, 0);
      }
    });
    msg.blur(function() {
      var str = msg.val();
      str = jQuery.trim(str);
      if(str == "") {
        msg.val(defaultvalue);
        msg.css('color', 'gray');
        countReset(msg, defaultvalue.length);
      }
    });

    $('textarea').stringCount();
//    $('.frameTimeLine').scroll(function() {
//      if ($(this).scrollTop() === 0) {
//        for ($i = 1; $i < 10; $i++) {
//          $('#cline').prepend( '<br/>' );
//        }
//        $(this).animate({
//            scrollTop: $('#' + $('#lastid').val()).offset().top});
//      }
//    });
    $("#msgsend").click(function () {
      itc.line.common.buttonEnable(false, $(this));
        //$('body').css('cursor', 'wait');
        $.ajax({
          url: itc.line.common.msgurl,
          method: 'POST',
          data: $('#msgform').serialize() + "&proc=add",
          dataType: 'json',
          success: itc.line.common.onDataReceived,
          error: itc.line.common.onErrReceived
        });
    });
//    $('.frame_timeLine_message').scroll(function() {
//      if ($(this).scrollTop() === 0) {
//        itc.line.common.buttonEnable(false, $(this));
//            $.ajax({
//              url: itc.line.common.msgurl,
//              method: 'POST',
//              data: $('#msgform').serialize() + "&proc=his",
//              dataType: 'json',
//              success: itc.line.common.onDataReceived,
//              error: itc.line.common.onErrReceived
//            });
//      }
//    });
    $("#msgupdate").click(function () {
      itc.line.common.buttonEnable(false, $(this));
      $.ajax({
        url: itc.line.common.msgurl,
        method: 'POST',
        data: $('#msgform').serialize() + "&proc=upd",
        dataType: 'json',
        success: itc.line.common.onDataReceived,
        error: itc.line.common.onErrReceived
      });
    });
    $("input.msgdel").click( itc.line.common.setDelButtonClick );
  },
  setDelButtonClick: function(){
    //$("input.msgdel").click(function () {
      itc.line.common.buttonEnable(false, $(this));
      $.ajax({
        url: itc.line.common.msgurl,
        method: 'POST',
        data: $('#msgform').serialize() +"&cid="+ $(this).attr('cid') + "&proc=del",
        dataType: 'json',
        success: itc.line.common.onDelReceived,
        error: itc.line.common.onErrReceived
      });
    //});
  },
  onDataReceived: function(data) {
    if(data.res=='NG'){
      top.location.href=itc.line.common.topurl;
      return false;
    }

    // 現在の縦スクロール位置
    var scrollPosition = document.getElementById("timeline").scrollTop;
    // スクロール要素の高さ
    var scrollHeight = document.getElementById("timeline").scrollHeight;

    if(data.html!=""){
      var defaultvalue = document.getElementById("msginput").defaultValue;
      if(data.res=='his'){
        $('#cline').html( data.html + $('#cline').html() );
      } else {
        $('#cline').append( data.html );
        if (data.res=='add') {
          $('#msginput').val(defaultvalue);
          $('#msginput').css('color', 'gray');
          $('#file').val('');
          $('#postPhotoName').val('');
          $('#preview').html('画像(2Mバイト以内のJPEG、PNG、GIF形式。)');
          $('.frame_timeLine_message').animate({ scrollTop: scrollHeight }, 'slow');
        } else {
          if ((scrollPosition) >= (scrollHeight - (scrollHeight * 0.06)) - 500) {
                $('.frame_timeLine_message').animate({ scrollTop: scrollHeight }, 'slow');
            }
        }
      }
      countReset($('#msginput'), defaultvalue.length);
      lightBox();
    }

    $("input.msgdel").click( itc.line.common.setDelButtonClick );
    itc.line.common.buttonEnable(true);
    if(data.res=='his'){
      if(data.firstid>0){
        $('#firstid').val(data.firstid);
      } else {
        $("#msghistory").attr('disabled', true);
      }
    } else {
      if(data.lastid>0){
        $('#lastid').val(data.lastid);
      }
    }
  },
  onErrReceived: function(data) {
    itc.line.common.buttonEnable(true);
  },
  onDelReceived: function(data) {
    if(data.res=='NG'){
      top.location.href=itc.line.common.topurl;
    }
    if( data.count == 1 ){
      $("input.msgdel[cid='"+ data.cid +"']").parent().remove();
    }
    itc.line.common.buttonEnable(true);
  },
  buttonEnable: function(isEnable, obj){
    if(isEnable){
      itc.line.common.clkButton.attr('disabled', false);
      itc.line.common.clkButton.removeAttr('disabled');
    } else {
      itc.line.common.clkButton=obj;
      obj.attr('disabled', true);
    }
  },
  startTimer: function(){
    var timer = setInterval(function(){
      itc.line.common.buttonEnable(false, $(this));
      $.ajax({
        url: itc.line.common.msgurl,
        method: 'POST',
        data: $('#msgform').serialize() + "&proc=upd",
        dataType: 'json',
        success: itc.line.common.onDataReceived,
        error: itc.line.common.onErrReceived
      });
    },30000);
    return timer;
  },
  stopTimer: function(timer){
    clearInterval(timer);
  },
//  go_bottom: function(selector){
//    $('.frameTimeLine').animate({scrollTop: $('#' + selector).offset().top},1000);
//    alert($('#' + selector).offset().top);
////    alert($('#' + selector).position().top);
////    var obj = $("#" + targetId);
////      if(obj.length == 0) return;
////      obj.scrollTop(obj*0*.scrollHeight);
//  }
};