<?php

namespace App\Console\Commands;

use App\Models\PendingRegistration;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:delete-expired-pending-users')]
#[Description('Menghapus akun pending yang telah melewati masa verifikasi')]
class DeleteExpiredPendingUsers extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $deleted = PendingRegistration::where('expired_at', '<=', now())->delete();

        if ($deleted > 0) {
            $this->info("Berhasil menghapus {$deleted} akun pending yang telah kedaluwarsa.");
        } else {
            $this->info("Tidak ada akun pending yang perlu dihapus.");
        }

        return Command::SUCCESS;
    }
}