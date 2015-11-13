<?php
class getHoliday {
  /**
   * googleAPIURL
   * @return string
   */
  private $getUrl = 'http://www.google.com/calendar/feeds/%s/public/full-noattendees?start-min=%s&start-max=%s&max-results=%d&alt=json';
  /**
   * 日本の祝日
   * @return string
   */
  private $getAPICalendar = 'outid3el0qkcrsuf89fltf7a4qbacgt9@import.calendar.google.com'; // 'japanese@holiday.calendar.google.com'

//   /**
//    * 引数に指定された日付の休日を取得する
//    * @param unknown_type $today
//    */
//   public function getHoliday($today = '') {
//     if ($today === '') {
//       $today = date('Y-m-d');
//     }
//     $holidays_url = sprintf(
//         $this->getUrl ,
//         $this->getAPICalendar ,
//         $today ,  // 取得開始日
//         '2013-09-30' ,  // 取得終了日
//         50              // 最大取得数
//     );
//     if ( $results = file_get_contents($holidays_url) ) {
//       $results = json_decode($results, true);
//       $holidays = array();
//       if ($results['feed']['entry']) {
//         foreach ($results['feed']['entry'] as $val ) {
//           $date  = $val['gd$when'][0]['startTime'];
//           $title = $val['title']['$t'];
//           $holidays[$date] = $title;
//         }
//       } else {
//         $holidays = false;
//       }
//       return $holidays;
//     }
//   }

  /**
   * 引数に指定された年の祝日一覧を取得する
   * @param unknown_type $year
   */
  public function getHolidays($year = '') {
    if ($year === '') {
      $year = date(Y);
    }
    $holidays_url = sprintf(
        $this->getUrl ,
        $this->getAPICalendar,
        $year.'-01-01' ,  // 取得開始日
        $year.'-12-31' ,  // 取得終了日
        50              // 最大取得数
    );
    if ( $results = file_get_contents($holidays_url) ) {
      $results = json_decode($results, true);
      $holidays = array();
      foreach ($results['feed']['entry'] as $val ) {
        $date  = $val['gd$when'][0]['startTime'];
        $title = $val['title']['$t'];
        $holidays[$date] = $title;
      }
      ksort($holidays);
      return $holidays;
    }
  }
}