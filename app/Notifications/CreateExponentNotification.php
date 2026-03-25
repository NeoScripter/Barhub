<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class CreateExponentNotification extends Notification // implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected string $email) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Доступ к личному кабинету экпонента')
            ->greeting('Здравствуйте.')
            ->line('Рады сообщить вам, что вам предоставлен доступ к личному кабинету экпонента.')
            ->action('Регистрация', url('/register'))
            ->line('Данное сообщение сгенерировано автоматически. Пожалуйста, не отвечайте на него')
            ->salutation(new HtmlString('<p>С уважением, <br>Barhub</p>'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
