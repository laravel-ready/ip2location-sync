<?php

namespace DragAndPublish\Ip2locationSync\Jobs;

use ZipArchive;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
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

        DB::connection('ip2location')
            ->table('ip2location_database_tmp')
            ->truncate()
            

        echo $csvFiles[0];

        // get first csv file
        // $csvFilePath = $csvFiles[0];

        // $file = fopen($csvFilePath, 'r');

        // $rows = [];

        // while (($data = fgetcsv($file, 1000, ',')) !== false) {
        //     $rows[] = [
        //         'ip_from' => $data[0],
        //         'ip_to' => $data[1],
        //         'country_code' => $data[2],
        //         'city_name' => $data[5],
        //     ];
        // }

        // fclose($file);

        ray(count($rows));
    }
}
