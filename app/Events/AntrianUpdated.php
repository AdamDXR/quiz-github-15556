<?php

namespace App\Events;

use App\Models\DaftarPoli;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AntrianUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $id_jadwal;
    public $no_antrian;
    public $status;
    public $nama_poli;
    public $nama_dokter;
    public $id_pasien;

    /**
     * Create a new event instance.
     */
    public function __construct(DaftarPoli $daftarPoli)
    {
        $daftarPoli->loadMissing(['jadwalPeriksa.dokter.poli', 'pasien']);

        $this->id_jadwal = $daftarPoli->id_jadwal;
        $this->no_antrian = $daftarPoli->no_antrian;
        $this->status = $daftarPoli->status;
        $this->nama_poli = $daftarPoli->jadwalPeriksa->dokter->poli->nama_poli ?? '';
        $this->nama_dokter = $daftarPoli->jadwalPeriksa->dokter->nama ?? $daftarPoli->jadwalPeriksa->dokter->name ?? '';
        $this->id_pasien = $daftarPoli->id_pasien;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('antrian-channel'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'AntrianUpdated';
    }
}
