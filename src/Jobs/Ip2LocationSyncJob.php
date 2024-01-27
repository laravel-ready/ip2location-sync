<?php

namespace DragAndPublish\Ip2locationSync\Jobs;

use ZipArchive;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use DragAndPublish\Ip2locationSync\Models\Ip2LocationSync;

class Ip2LocationSyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $lastFile = Ip2LocationSync::where('sync_status', false)->latest()->first();

        if (!$lastFile) {
            return;
        }

        // check file exists
        $filePath = $lastFile->file_path;

        if (!Storage::disk('ip2location_sync')->exists($filePath)) {
            $lastFile->delete();

            return;
        }

        $fileFolder = storage_path('app/private/ip2location_sync/' . dirname($filePath));
        $csvFilesPattern = "{$fileFolder}/*.CSV";

        $csvFiles = glob($csvFilesPattern);

        // extract zip file
        if (count($csvFiles) === 0) {
            $zip = new ZipArchive();

            if ($zip->open(Storage::disk('ip2location_sync')->path($filePath)) === true) {
                $zip->extractTo($fileFolder);
                $zip->close();
            }
        }

        // check csv files
        $csvFiles = glob($csvFilesPattern);

        if (count($csvFiles) === 0) {
            return;
        }

        try {
            DB::connection('ip2location')->statement('DROP TABLE IF EXISTS ip2location_database_tmp');
        } catch (\Exception $e) {
            throw new \Exception('Error dropping ip2location_database_tmp table: ' . $e->getMessage());
        }

        try {
            Schema::connection('ip2location')->create('ip2location_database_tmp', function (Blueprint $table) {
                $table->id();

                $table->decimal('ip_from', 39, 0);
                $table->decimal('ip_to', 39, 0);
                $table->string('country_code', 2);
                $table->string('country_name', 64);
                $table->string('region_name', 128);
                $table->string('city_name', 128);
                $table->double('latitude')->nullable(true)->default(null);
                $table->double('longitude')->nullable(true)->default(null);
                $table->string('zip_code', 30);
                $table->string('time_zone', 8);

                $table->timestamps();
            });
        } catch (\Exception $e) {
            throw new \Exception('Error creating ip2location_database_tmp table: ' . $e->getMessage());
        }
    }
}
