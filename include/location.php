<?php
function genderCheck($gender) {
  switch ($gender) {
    case '男性':$sex = 'man';break;
    case '女性':$sex = 'woman';break;
  }
  return $sex;
}
function placeCheck($place) {
  switch ($place) {
    case '北海道':$location = 'hokkaido';break;
    case '青森県':$location = 'aomori';break;
    case '岩手県':$location = 'iwate';break;
    case '宮城県':$location = 'miyagi';break;
    case '秋田県':$location = 'akita';break;
    case '山形県':$location = 'yamagata';break;
    case '福島県':$location = 'fukushima';break;
    case '茨城県':$location = 'ibaraki';break;
    case '栃木県':$location = 'totigi';break;
    case '群馬県':;$location ='gunma' ;break;
    case '埼玉県':$location = 'saitama';break;
    case '千葉県':$location = 'chiba';break;
    case '東京都':$location = 'toukyo';break;
    case '神奈川県':$location = 'kanagawa';break;
    case '新潟県':$location = 'niigata';break;
    case '富山県':$location = 'toyama';break;
    case '石川県':$location = 'ishikawa';break;
    case '福井県':$location = 'fukui';break;
    case '山梨県':$location = 'yamanashi';break;
    case '長野県':$location = 'nagano';break;
    case '岐阜県':$location = 'gifu';break;
    case '静岡県':$location = 'shizuoka';break;
    case '愛知県':$location = 'aichi';break;
    case '三重県':$location = 'mie';break;
    case '滋賀県':$location = 'shiga';break;
    case '京都府':$location = 'kyouto';break;
    case '大阪府':$location = 'osaka';break;
    case '兵庫県':$location = 'hyougo';break;
    case '奈良県':$location = 'nara';break;
    case '和歌山県':$location = 'wakayama';break;
    case '鳥取県':$location = 'tottori';break;
    case '島根県':$location = 'shimane';break;
    case '岡山県':$location = 'okayama';break;
    case '広島県':$location = 'hiroshima';break;
    case '山口県':$location = 'yamaguchi';break;
    case '徳島県':$location = 'tokushima';break;
    case '香川県':$location = 'kagawa';break;
    case '愛媛県':$location = 'ehime';break;
    case '高知県':$location = 'kochi';break;
    case '福岡県':$location = 'fukuoka';break;
    case '佐賀県':$location = 'saga';break;
    case '長崎県':$location = 'nagasaki';break;
    case '熊本県':$location = 'kumamoto';break;
    case '大分県':$location = 'oita';break;
    case '宮崎県':$location = 'miyazaki';break;
    case '鹿児島県':$location = 'kagoshima';break;
    case '沖縄県':$location = 'okinawa';break;
  }
  return $location;
}

function forYear($year) {
  $now = date(Y);
  $i = 1900;
  $html = "";
  while ($now > $i) {
    if ($year) {
      if ($i == $year) {
        $html .= '<option value="'.$i.'" selected="selected">'.$i.'</option>';
      } else {
        $html .= '<option value="'.$i.'">'.$i.'</option>';
      }
    } else {
//       if ($i == 1970) {
//         $html .= '<option value="'.$i.'" selected="selected">'.$i.'</option>';
//       } else {
        $html .= '<option value="'.$i.'">'.$i.'</option>';
//       }
    }
    $i++;
  }
  return $html;
}
function forMonth($month) {

  switch (intval($month)) {
    case 1:$birthdayMonth = 'month1';break;
    case 2:$birthdayMonth = 'month2';break;
    case 3:$birthdayMonth = 'month3';break;
    case 4:$birthdayMonth = 'month4';break;
    case 5:$birthdayMonth = 'month5';break;
    case 6:$birthdayMonth = 'month6';break;
    case 7:$birthdayMonth = 'month7';break;
    case 8:$birthdayMonth = 'month8';break;
    case 9:$birthdayMonth = 'month9';break;
    case 10:$birthdayMonth = 'month10';break;
    case 11:$birthdayMonth = 'month11';break;
    case 12:$birthdayMonth = 'month12';break;
  }
  return $birthdayMonth;
}

function forDays($day) {
  switch (intval($day)) {
    case 1:$birthdayDays = 'day1';break;
    case 2:$birthdayDays = 'day2';break;
    case 3:$birthdayDays = 'day3';break;
    case 4:$birthdayDays = 'day4';break;
    case 5:$birthdayDays = 'day5';break;
    case 6:$birthdayDays = 'day6';break;
    case 7:$birthdayDays = 'day7';break;
    case 8:$birthdayDays = 'day8';break;
    case 9:$birthdayDays = 'day9';break;
    case 10:$birthdayDays = 'day10';break;
    case 11:$birthdayDays = 'day11';break;
    case 12:$birthdayDays = 'day12';break;
    case 13:$birthdayDays = 'day13';break;
    case 14:$birthdayDays = 'day14';break;
    case 15:$birthdayDays = 'day15';break;
    case 16:$birthdayDays = 'day16';break;
    case 17:$birthdayDays = 'day17';break;
    case 18:$birthdayDays = 'day18';break;
    case 19:$birthdayDays = 'day19';break;
    case 20:$birthdayDays = 'day20';break;
    case 21:$birthdayDays = 'day21';break;
    case 22:$birthdayDays = 'day22';break;
    case 23:$birthdayDays = 'day23';break;
    case 24:$birthdayDays = 'day24';break;
    case 25:$birthdayDays = 'day25';break;
    case 26:$birthdayDays = 'day26';break;
    case 27:$birthdayDays = 'day27';break;
    case 28:$birthdayDays = 'day28';break;
    case 29:$birthdayDays = 'day29';break;
    case 30:$birthdayDays = 'day30';break;
    case 31:$birthdayDays = 'day31';break;
  }
  return $birthdayDays;
}
?>