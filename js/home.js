jQuery(function($){
    var per = parseInt($('#current').val());
    $("div.holder").jPages({
      containerID : "itemContainer",// 対象となるまとまり
      perPage     : per,              // 1ページあたりのアイテム数
      startPage   : 1,              // 初期位置となるページ数
      previous    : "←前へ",       // back
      next        : "次へ→",       // next
      delay       : 100,            // 表示速度
      animation   : "fadeIn",       // アニメーション効果
//	    scrollBrowse: true,           // マウススクロール設定
//	    keyBrowse   : true,           // キースクロール設定
      callback    : pageSet         // ページ表示ごとに呼ばれる関数
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