<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class CreateExponentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected string $email, protected string $company, protected string $exhibition) {}

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
            ->greeting('Добрый день!')
            ->line('Вас приветствует команда bar hub 👋')
            ->line("Рады подтвердить участие компании {$this->company} в выставке {$this->exhibition}.")
            ->line('Для продолжения работы и заполнения информации о вашем стенде, пожалуйста, перейдите по ссылке и зарегистрируйтесь в личном кабинете экспонента:')
            ->action('Регистрация', url('/register'))
            ->line('Если у вас возникнут вопросы по работе с кабинетом — мы всегда на связи и готовы помочь.')
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
