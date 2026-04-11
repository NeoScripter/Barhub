<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $token)
    {
        //
    }

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
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Сброс пароля')
            ->greeting('Добрый день!')
            ->line('Вы получили это письмо, потому что был запрошен сброс пароля для вашего аккаунта в системе bar hub.')
            ->action('Ссылка для сброса пароля', $url)
            ->line('Ссылка действует в течение 60 минут.')
            ->line('Если вы не запрашивали сброс пароля — просто проигнорируйте это письмо, никаких действий не требуется.')
            ->line('Если возникнут вопросы — мы всегда на связи 🤝')
            ->salutation(new HtmlString('<p>С уважением, <br>команда bar hub</p>'));
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
