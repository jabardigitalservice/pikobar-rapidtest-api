<?php

namespace App\Notifications;

use App\Channels\SmsChannel;
use App\Channels\WhatsappChannel;
use App\Entities\RdtEvent;
use App\Entities\RdtInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CheckinThankYou extends Notification
{
    use Queueable;

    /**
     * @var \App\Entities\RdtEvent
     */
    public $rdtEvent;

    /**
     * @var \App\Entities\RdtInvitation
     */
    public $rdtInvitation;

    /**
     * Create a new notification instance.
     *
     * @param \App\Entities\RdtEvent $rdtEvent
     * @param \App\Entities\RdtInvitation $rdtInvitation
     */
    public function __construct(RdtEvent $rdtEvent, RdtInvitation $rdtInvitation)
    {
        $this->rdtEvent = $rdtEvent;
        $this->rdtInvitation = $rdtInvitation;
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

    public function toSms($notifiable)
    {
        return "Yth. {$notifiable->name}. Terima kasih menghadiri Tes Masif COVID19 PIKOBAR.
            Nomor Pendaftaran: {$notifiable->registration_code}.
            Datang: {$this->rdtInvitation->attended_at->setTimezone('Asia/Jakarta')}.
            Info: s.id/tesmasif";
    }

    public function toWhatsapp($notifiable)
    {
        return <<<EOT
*Yth. {$notifiable->name}*

Terima kasih telah menghadiri Tes Masif COVID-19 PIKOBAR Provinsi Jawa Barat.

Nomor Pendaftaran: *{$notifiable->registration_code}*
Tanggal/Waktu: *{$this->rdtInvitation->attended_at->setTimezone('Asia/Jakarta')}*
Tempat: *{$this->rdtEvent->event_location}*

*Hasil tes akan diberitahukan melalui SMS, Whatsapp, dan Website.*
Buka https://tesmasif.pikobar.jabarprov.go.id dan masukkan Nomor Pendaftaran.

Selama menunggu hasil tes, Anda diharapkan tetap memperbanyak aktivitas di rumah
EOT .
        <<<EOT
 menggunakan masker jika berpergian, menjaga jarak dan protokol kesehatan,
EOT .
        <<<EOT
 serta melakukan pola hidup bersih dan sehat (PHBS).

Hatur nuhun.
EOT;
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
