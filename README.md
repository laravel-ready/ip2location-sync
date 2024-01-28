# ip2location-sync
Ip2Location database sync package for Laravel apps. This package is designed for `DB3LITECSV` and it requires a database to store the data.

## Differences between `docker-ip2location-mysql` and this package

- The official docker image [docker-ip2location-mysql](https://github.com/ip2location/docker-ip2location-mysql) provides a database with the data but there is no custom sync option. This package provides a custom sync option.
- It is not designed for complex use cases. If you need more features, please use the official docker image.
- Sometimes the official docker image causes a quota exceeded error. This may break your application. This package uses a cache to prevent this error.

## Installation

```bash
composer require drag-and-publish/ip2location-sync
```

## Usage

Run required jobs in the console.

```php
<?php

namespace App\Console;

use DragAndPublish\Ip2locationSync\Jobs\Ip2LocationDownloadJob;
use DragAndPublish\Ip2locationSync\Jobs\Ip2LocationSyncJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(Ip2LocationDownloadJob::class)->dailyAt('00:00');
        $schedule->job(Ip2LocationSyncJob::class)->dailyAt('00:00');
    }
}
```

### Environment Variables

Define your environment variables in the `.env` file.

```yml
# ip2location
IP2LOCATION_TOKEN={YOUR_TOKEN}
IP2LOCATION_IP_TYPE=IPV6
IP2LOCATION_MYSQL_PORT=1010
IP2LOCATION_MYSQL_HOST=127.0.0.1
IP2LOCATION_MYSQL_DBNAME=ip2location_database
IP2LOCATION_MYSQL_USERNAME=admin
IP2LOCATION_MYSQL_PASSWORD=secret
```