jQuery(function($){
  $('#invite').click(function() {
    var button = $(this);
    var appry = $(".button_applyFriend");
    button.attr("disabled", "disabled");
    $.ajax({
      async: false,
      type: "POST",
      url: "searchFriend.php",
      data: appry.serialize()+"&proc=profile",
      success: function(data){
        if (data === 'OK') {
          appry.html('<input type="submit" value="申請中" class="button_m textColorRed" disabled>');
        } else {
          appry.html('<input type="submit" value="申請失敗" class="button_m textColorRed" disabled>');
        }
      }
    });
  });
  $('#app').click(function() {
      var button = $(this);
      var appry = $(".button_applyFriend");
      button.attr("disabled", "disabled");
      $.ajax({
        async: false,
        type: "POST",
        url: "applyFriendList.php",
        data: appry.serialize()+"&proc=profile",
        success: function(data){
          if (data === 'OK') {
            appry.html('<input type="submit" value="友達" class="button_m textColorRed" disabled>');
          } else {
            appry.html('<input type="submit" value="承認失敗" class="button_m textColorRed" disabled>');
          }
        }
      });
    });
});