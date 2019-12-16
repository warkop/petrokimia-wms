<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class Pengiriman extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        return [
            'id_aktivitas_harian'   => $notifiable->id,
            'id_aktivitas'          => $notifiable->id_aktivitas,
            'kode_aktivitas'        => $notifiable->aktivitas->kode_aktivitas,
            'nama'                  => $notifiable->aktivitas->nama,
            'asal_gudang'           => $notifiable->gudang->nama,
            'gudang_tujuan'         => $notifiable->gudangTujuan->nama,
            'waktu'                 => $notifiable->created_at->diffForHumans(),
            'created_at'            => $notifiable->created_at,
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
