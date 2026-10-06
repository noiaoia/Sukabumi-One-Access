<?php

namespace App\Console\Commands;

use App\Models\Admin\Layanan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CheckLayananHealth extends Command
{
    protected $signature = 'layanan:check-health';
    protected $description = 'Cek status HTTP seluruh layanan iframe aktif secara paralel, simpan hasil ke cache';

    public function handle(): void
    {
        $layananList = Layanan::where('jenis', 'iframe')
            ->where('status', 'aktif')
            ->whereNotNull('iframe_url')
            ->get(['id', 'iframe_url']);

        if ($layananList->isEmpty()) {
            $this->info('Tidak ada layanan iframe aktif untuk dicek.');
            return;
        }

        // Request paralel, timeout pendek, satu URL mati tidak memblokir yang lain
        $responses = Http::pool(fn ($pool) => $layananList->map(
            fn ($layanan) => $pool->as((string) $layanan->id)->timeout(4)->get($layanan->iframe_url)
        )->all());

        $checkedAt = now()->toIso8601String();

        foreach ($layananList as $layanan) {
            $response = $responses[(string) $layanan->id] ?? null;
            $ok = $response && !($response instanceof \Throwable) && $response->successful();

            Cache::put("layanan_health:{$layanan->id}", [
                'ok'         => $ok,
                'checked_at' => $checkedAt,
            ], now()->addMinutes(10));
        }

        $this->info("Selesai mengecek {$layananList->count()} layanan.");
    }
}