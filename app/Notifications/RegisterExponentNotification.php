<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class RegisterExponentNotification extends Notification // implements ShouldQueue
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
            ->line("Рады сообщить вам, что вам предоставлен доступ к личному кабинету эскпонента {$this->company} на выставке {$this->exhibition}.")
            ->action('Регистрация', url('/register'))
            ->line('В личном кабинете вы можете:')
            ->line(new HtmlString('<ul style="margin:15; padding-left:20px;">
                <li>отслеживать и выполнять задачи по подготовке к выставке</li>
                <li>редактировать информацию о компании для публикации на сайте и в приложении</li>
                <li>загружать материалы и получать необходимые инструкции</li>
                <li>заказывать дополнительные услуги и сервисы</li>
                <li>следить за статусами отправленных данных и заявок</li>
                <li>ознакомиться с программой выставки и расписанием лекториев</li>
            </ul>'))
            ->line('Если у вас возникнут вопросы или потребуется помощь — наша команда всегда на связи 🤝')
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
