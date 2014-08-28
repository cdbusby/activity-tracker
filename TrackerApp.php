<?php

// Include Strava PHP API
require 'external/StravaApi.php';

class TrackerApp {

    /**
     * Sets up the class and initialized StravaApi
     * @param array $creds
     * @param array $query
     */

    public function __construct( $creds, $query ) {

        // Set variables
        $this->clientId = $creds['clientId'];
        $this->clientSecret = $creds['clientSecret'];
        $this->accessToken = $creds['accessToken'];

        $this->query = $this->parseQuery( $query );

        $this->api = new StravaApi( $this->clientSecret, $this->clientId );

    }

    /**
     * Gets a list of activities (JSON) based on query string and filters map results
     * @param bool $filterMaps
     * @return string
     */

    public function listActivities( $filterMaps = true ) {

        $activities = $this->api->get( "athlete/activities", $this->accessToken, $this->query );

        if ( $filterMaps ) {
            $activities = $this->filterMaps( $activities );
        }

        return json_encode($activities);

    }

    /**
     * Filters out mapping from api object
     * @param object $obj
     * @return object
     */

    private function filterMaps( $obj ) {

        foreach ( $obj as &$item ) {
            unset( $item->map );
        }

        return $obj;

    }

    /**
     * Parses the query string into a usable array
     * @param array $query
     * @return array
     */

    private function parseQuery( $query ) {

        $params = array();

        foreach( $query as $key => $val ) {

            switch( $key ) {
                case "day":
                    $explode = explode( "-", $val );
                    $day = $explode[0];
                    $month = $explode[1];
                    $year = $explode[2];

                    $first_minute = mktime( 0, 0, 0, $month, $day, $year );
                    $last_minute = mktime( 23, 59, 0, $month, $day, $year );

                    $params['after'] = $first_minute;
                    $params['before'] = $last_minute;
                    break;

                case "month":
                    $explode = explode( "-", $val );
                    $month = $explode[0];
                    $year = $explode[1];

                    $first_minute = mktime( 0, 0, 0, $month, 1, $year );
                    $last_minute = mktime( 23, 59, 0, $month, date("t"), $year );

                    $params['after'] = $first_minute;
                    $params['before'] = $last_minute;
                    break;

                case "year":
                    $year = $val;

                    $first_minute = mktime( 0, 0, 0, 1, 1, $year );
                    $last_minute = mktime( 23, 59, 0, 12, date("t"), $year );

                    $params['after'] = $first_minute;
                    $params['before'] = $last_minute;
                    break;

                case "limit":
                    $params['per_page'] = $val;
                    break;
            }

        }

        return $params;

    }

}