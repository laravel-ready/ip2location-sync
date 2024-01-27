<?php

namespace DragAndPublish\Ip2locationSync\Jobs;

use Exception;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use DragAndPublish\Ip2locationSync\Models\Ip2LocationSync;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class Ip2LocationDownloadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $downloadUrl = 'https://www.ip2location.com/download/';

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $token = Config::get('ip2location-sync.token');

        Log::info('IP2Location token: ' . $token);

        if (empty($token)) {
            throw new Exception('IP2Location token is not set.');
        }

        $dbCode = 'DB3LITEBIN';

        // empty = IPv4
        // IPV6 = IPv6
        $ipType = Config::get('ip2location-sync.ip_type', 'IPV6') === 'IPV6' ? 'IPV6' : '';

        $this->downloadUrl = "{$this->downloadUrl}?token={$token}&file={$dbCode}{$ipType}";
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $fileName = Carbon::now()->format('Y_m') . '/ip2location_sync.zip';

        if (Storage::disk('ip2location_sync')->exists($fileName)) {
            if (Ip2LocationSync::where('file_path', $fileName)->exists()) {
                return;
            }

            Ip2LocationSync::create([
                'file_path' => $fileName,
                'sync_status' => false
            ]);

            return;
        }

        $client = new Client();
        $response = $client->get($this->downloadUrl);
        $fileContent = $response->getBody()->getContents();

        Storage::disk('ip2location_sync')->put($fileName, $fileContent);

        Ip2LocationSync::create([
            'file_path' => $fileName,
            'sync_status' => false
        ]);
    }
}
