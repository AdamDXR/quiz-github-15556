<?php

namespace App\Notifications;

use App\Models\Periksa;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PembayaranDiverifikasiNotification extends Notification
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
        $poli = $this->periksa->daftarPoli->jadwalPeriksa->dokter->poli->nama_poli ?? 'Poliklinik';
        $biaya = number_format($this->periksa->biaya_periksa, 0, ',', '.');

        return [
            'title' => 'Pembayaran Terverifikasi (Lunas)',
            'message' => "Pembayaran tagihan pemeriksaan Anda di {$poli} sebesar Rp {$biaya} telah diverifikasi dan berstatus LUNAS.",
            'periksa_id' => $this->periksa->id,
            'link' => route('pasien.pembayaran.index'),
            'type' => 'pembayaran_diverifikasi',
            'icon' => 'fa-circle-check',
            'color' => 'emerald',
        ];
    }
}
