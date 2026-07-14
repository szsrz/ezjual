<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderReceived extends Notification
{
    use Queueable;

    public function __construct(
        public Order $order,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Pesanan baru diterima'))
            ->line(__('Produk: :product', ['product' => $this->order->product->name]))
            ->line(__('Pelanggan: :name', ['name' => $this->order->customer_name]))
            ->line(__('Telefon: :phone', ['phone' => $this->order->customer_phone]))
            ->line(__('Alamat: :address', ['address' => $this->order->customer_address]))
            ->line(__('Kuantiti: :quantity', ['quantity' => $this->order->quantity]))
            ->when($this->order->notes, fn (MailMessage $mail) => $mail->line(__('Nota: :notes', ['notes' => $this->order->notes])))
            ->action(__('Lihat Pesanan'), route('orders.index'));
    }
}
