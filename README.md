activity-tracker
================

Activity tracking to be used with the Strava V3 PHP API.

Usage
----------------

Download the Strava PHP API: https://github.com/iamstuartwilson/strava

Specify the include path in your TrackerApp file:

```php
require 'StravaApi.php';
```

In your own file, initialize the TrackerApp with your Strava app details:

```php
$creds = array(
	'clientId' => 1234,
	'clientSecret' => 'abc123',
	'accessToken' => 'abc123'
	);

$TrackerApp = new TrackerApp( $creds, $_GET );
```

You can setup your Strava app here: http://www.strava.com/developers

Call the list activities function from your file:

```php
$activities = $TrackerApp->listActivities();
```