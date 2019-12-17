<?php

namespace App\Notifications;

use App\Http\Models\AktivitasHarian;
use App\Http\Models\Users;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class Pengiriman extends Notification implements ShouldQueue
{
    use Queueable;
    protected $aktivitasHarian;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(AktivitasHarian $aktivitasHarian)
    {
        $this->aktivitasHarian = $aktivitasHarian;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    public function toDatabase($notifiable)
    {
        Carbon::setLocale('id');

        return [
            'id_aktivitas_harian'   => $this->aktivitasHarian->id,
            'id_aktivitas'          => $this->aktivitasHarian->id_aktivitas,
            'kode_aktivitas'        => $this->aktivitasHarian->aktivitas->kode_aktivitas,
            'nama'                  => $this->aktivitasHarian->aktivitas->nama,
            'asal_gudang'           => $this->aktivitasHarian->gudang->nama,
            'gudang_tujuan'         => $this->aktivitasHarian->gudangTujuan->nama,
            'waktu'                 => $this->aktivitasHarian->created_at->diffForHumans(),
            'created_at'            => $this->aktivitasHarian->created_at,
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'aktivitas_harian_id' => $this->aktivitasHarian->id,
        ];
    }
}
