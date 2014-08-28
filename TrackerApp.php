<?php

require 'external/StravaApi.php';

class TrackerApp {

  const MAXRESULTS = 200;

  public function __construct($creds, $query) {

    $this->clientId = $creds['clientId'];
    $this->clientSecret = $creds['clientSecret'];
    $this->accessToken = $creds['accessToken'];

    $this->query = $query;
    $this->parsedQuery = $this->parseQuery($query);

    $this->api = new StravaApi($this->clientSecret, $this->clientId);

  }

  private function parseQuery($query) {

    if (!array_key_exists("limit", $query)) {
      $query["limit"] = self::MAXRESULTS;
    }

    $params = array();

    foreach ($query as $key => $val) {

      switch ($key) {
        case "day":
          $explode = explode("-", $val);
          $day = $explode[0];
          $month = $explode[1];
          $year = $explode[2];

          $first_minute = mktime(0, 0, 0, $month, $day, $year);
          $last_minute = mktime(23, 59, 0, $month, $day, $year);

          $params['after'] = $first_minute;
          $params['before'] = $last_minute;
          break;

        case "month":
          $explode = explode("-", $val);
          $month = $explode[0];
          $year = $explode[1];

          $first_minute = mktime(0, 0, 0, $month, 1, $year);
          $last_minute = mktime(23, 59, 0, $month, date("t"), $year);

          $params['after'] = $first_minute;
          $params['before'] = $last_minute;
          break;

        case "year":
          $year = $val;

          $first_minute = mktime(0, 0, 0, 1, 1, $year);
          $last_minute = mktime(23, 59, 0, 12, date("t"), $year);

          $params['after'] = $first_minute;
          $params['before'] = $last_minute;
          break;

        case "limit":
          if ($val > self::MAXRESULTS) {
            $val = self::MAXRESULTS;;
          }
          $params['per_page'] = $val;
          break;
      }

    }

    return $params;

  }

  public function listActivities($filterMaps = TRUE) {

    $activities = $this->api->get("athlete/activities", $this->accessToken, $this->parsedQuery);

    if ($filterMaps) {
      $activities = $this->filterMaps($activities);
    }

    if (array_key_exists("year", $this->query)) {
      $activities = $this->groupByMonth($activities);
    }

    return json_encode($activities);

  }

  private function filterMaps($obj) {

    foreach ($obj as &$item) {
      unset($item->map);
    }

    return $obj;

  }

  private function groupByMonth($obj) {

    $array = array();

    foreach ($obj as &$item) {

      $date = explode("-", $item->start_date);
      $month = $date[1];

      $array[$month][] = $item;

    }

    return $array;

  }

}