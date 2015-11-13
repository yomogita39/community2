jQuery(function($){
  $(document).on("click", '#workButton', function() {
    var workButton = $(this);
    var work = $('#work');
    var schedule = $('#schedule');
    var date = $('#date').val();
    var calendar = $('.trcalendar');
    var chil = calendar.find('a[href*="'+date+'"]');
    $.ajax({
          method: "POST",
          url: "./api/farmDiaryScript.php",
          data: $('form').serialize()+"&proc=work",
          dataType: 'json',
          beforeSend: function () {
            if (jQuery.trim(work.val()) !== '' || jQuery.trim(schedule.val()) !== '') {
              workButton.attr('disabled', 'disabled');
              return true;
            } else {
              return false;
            }
          },
          success: function(data) {
            if (data.flg === 'OK') {
              workButton.attr('disabled', false);
              chil.parent().addClass('color_schedule');
            } else {
              workButton.attr('disabled', false);
              alert('保存に失敗しました。');
            }

          }
        });
  });
});