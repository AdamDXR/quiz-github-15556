<?php

namespace App\Notifications;

use App\Models\Periksa;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BuktiPembayaranDiunggahNotification extends Notification
{
    use Queueable;

    protected $periksa;

    /**
     * Create a new notification instance.
     */
    public function __construct(Periksa $periksa)
    {
        $this->periksa = $periksa;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $pasienNama = $this->periksa->daftarPoli->pasien->nama ?? $this->periksa->daftarPoli->pasien->name ?? 'Pasien';
        $poli = $this->periksa->daftarPoli->jadwalPeriksa->dokter->poli->nama_poli ?? 'Poliklinik';
        $biaya = number_format($this->periksa->biaya_periksa, 0, ',', '.');

        return [
            'title' => 'Bukti Pembayaran Diunggah',
            'message' => "Pasien {$pasienNama} ({$poli}) telah mengunggah bukti pembayaran tagihan sebesar Rp {$biaya}.",
            'periksa_id' => $this->periksa->id,
            'link' => route('riwayat-pasien.index'),
            'type' => 'bukti_pembayaran_diunggah',
            'icon' => 'fa-receipt',
            'color' => 'indigo',
        ];
    }
}
