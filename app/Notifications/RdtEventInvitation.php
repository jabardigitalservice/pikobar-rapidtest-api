<?php

namespace App\Notifications;

use App\Channels\SmsChannel;
use App\Channels\WhatsappChannel;
use App\Entities\RdtEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RdtEventInvitation extends Notification
{
    use Queueable;

    public $rdtEvent;

    /**
     * Create a new notification instance.
     *
     * @param RdtEvent $rdtEvent
     */
    public function __construct(RdtEvent $rdtEvent)
    {
        $this->rdtEvent = $rdtEvent;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [WhatsappChannel::class, SmsChannel::class];
    }

    public function toWhatsapp($notifiable)
    {
        $hostName = $this->rdtEvent->host_name;
        $name     = $notifiable->name;

        $message  = 'Yth. ' . $name . ' Sampurasun, Anda diundang untuk melakukan Tes COVID-19 oleh ';
        $message .= $hostName . ' Silakan buka tautan https://s.id/tesmasif2 dan masukkan Nomor Pendaftaran berikut: ';
        $message .= $notifiable->registration_code . ' untuk melihat undangan. Hatur nuhun';

        return $message;
    }

    public function toSms($notifiable)
    {
        $message  = 'Sampurasun. Anda diundang Tes COVID-19 ';
        $message .= $this->rdtEvent->host_name . '. Buka tautan s.id/tesmasif1 dan input nomor: ';
        $message .= $notifiable->registration_code . ' untuk melihat undangan.';

        return $message;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
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
            //
        ];
    }
}
