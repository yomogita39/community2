jQuery(function($){
//    $.ajax({
//      method: "POST",
//      url: "invitePost.php",
//      data: "proc=groupPost",
//      success: getPost
//    });
  $(window).load(function() {
    $.ajax({
          method: "POST",
          url: "invitePost.php",
          data: "proc=mailPost",
          success: getMail
        });
  });

  var timer = setInterval(function(){
    $.ajax({
      method: "POST",
      url: "invitePost.php",
      data: "proc=groupPost",
      success: getPost
    });
  },30000);
  var friendTimer = setInterval(function(){
        $.ajax({
            method: "POST",
            url: "invitePost.php",
            data: "proc=friendPost",
            success: getFriend
          });
      },30000);
  var mailTimer = setInterval(function(){
      $.ajax({
          method: "POST",
          url: "invitePost.php",
          data: "proc=mailPost",
          success: getMail
        });
    },30000);
  function getPost (data) {
    var post = $('#post');
    if (data !== '0') {
      post.removeClass('headMenuPostImageGray');
      post.addClass('headMenuPostImage');
      post.attr('disabled', false);
      post.attr('style', 'visibility:visible;');
      if (data != 'OK') {
        var hGroup = $('#headGroup');
        hGroup.toggleClass("headMenuCount1", data > 0 && 9 >= data)
          .toggleClass("headMenuCount2", data >= 10 && 99 >= data)
          .toggleClass("headMenuCount3", data >= 100 && 999 >= data);
        hGroup.html(data);
      }
//      clearInterval(timer);
    } else {
      if (post.hasClass('headMenuPostImage')) {
        post.removeClass('headMenuPostImage');
        post.addClass('headMenuPostImageGray');
        post.attr('disabled', true);
        post.attr('style', 'visibility:visible;');
      }
    }
  }

  function getFriend (data) {
      var friend= $('#friend');
      if (data !== '0') {
        friend.removeClass('headMenuFriendImageGray');
        friend.addClass('headMenuFriendImage');
        friend.attr('disabled', false);
        friend.attr('style', 'visibility:visible;');
        if (data != 'OK') {
          var hFriend = $('#headFriend');
          hFriend.toggleClass("headMenuCount1", data > 0 && 9 >= data)
            .toggleClass("headMenuCount2", data >= 10 && 99 >= data)
            .toggleClass("headMenuCount3", data >= 100 && 999 >= data);
          hFriend.html(data);
        }
//        clearInterval(friendTimer);
      } else {
        if (friend.hasClass('headMenuFriendImage')) {
          friend.removeClass('headMenuFriendImage');
          friend.addClass('headMenuFriendImageGray');
          friend.attr('disabled', true);
          friend.attr('style', 'visibility:visible;');
        }
      }
    }
  function getMail (data) {
      var mail= $('#mail');
      var hMail = $('#headMail');
      if (data !== '0') {
        mail.removeClass('headMenuMailImageGray');
        mail.addClass('headMenuMailImage');
        mail.attr('disabled', false);
        mail.attr('style', 'visibility:visible;');
        if (data != 'OK') {
          hMail.toggleClass("headMenuCount1", data > 0 && 9 >= data)
            .toggleClass("headMenuCount2", data >= 10 && 99 >= data)
            .toggleClass("headMenuCount3", data >= 100 && 999 >= data);
          hMail.html(data);
        }
//        clearInterval(friendTimer);
      } else {
          mail.removeClass('headMenuMailImage');
          mail.addClass('headMenuMailImageGray');
          mail.attr('disabled', true);
          mail.attr('style', 'visibility:visible;');
      }
    }
  // ログアウト押下で確認ダイアログ
  $(document).on('click', '#logoutButton', function() {
    if (confirm('ログアウトします。よろしいですか？')) {
      window.location = 'logout.php';
    }
  });
});