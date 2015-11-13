(function($) {
  $.fn.stringCount = (function(options) {
    // 設定内容を入力値と比較して設定する
    options = $.extend({
        max : 500, // 最大入力文字数
        maxText : '#maxCount', // 最大入力文字数表示要素
        count : '#count', // カウントアップした値を表示する要素
        button : '#msgsend', // 投稿用ボタン
        style : '#countSpan', // 色を変える要素
        changeStyle : true // テキストカウントをするかどうか
    }, options);
    // 対象
    var target = this;

    // 初期値をセット
    if (options.changeStyle) {
      $(options.maxText).text(options.max);
      $(options.count).text(target.val().length);
    }
    // 対象でのキーイベントを確認したらスタート
    target.bind('keydown keyup keypress change', function() {
      // 入力された文字列の文字数を取得
      var thisLength = target.val().length;
      // 最大入力文字数を超過したらカウントアップを停止し送信ボタンをdisabledに
      if (options.max >= thisLength) {
        $(options.button).attr('disabled', false);
        countChange(options.count, thisLength);
        $(options.style).css({color:'#000000',fontWeight:'normal'});
      } else {
        $(options.button).attr('disabled', 'disabled');
        countChange(options.count, thisLength);
        $(options.style).css({color:'#ff0000',fontWeight:'normal'});
      }
    });
    // メソッドチェイン対応のためthisを返却する
    return (this);
  });

  // カウントを手動で変更
  $.fn.countReset = (function(option, count) {
    console.log(this);
    countChange(option, count);
  });

  // 文字カウント
  function countChange(elem, count) {
    console.log(count);
    $(elem).text(count);
  }
})(jQuery);
