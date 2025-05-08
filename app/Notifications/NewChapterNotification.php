<?php

namespace App\Notifications;

use App\Models\Chapter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class NewChapterNotification extends Notification
{
    use Queueable;

    protected $chapter;
    protected $novel;

    public function __construct(Chapter $chapter)
    {
        $this->chapter = $chapter;
        $this->novel = $chapter->novel;
        
        // Thêm log khi tạo notification
        Log::info('Creating notification for chapter:', [
            'chapter_id' => $chapter->id,
            'novel_id' => $chapter->novel_id
        ]);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; // Chỉ sử dụng channel database
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        // Thêm log khi tạo dữ liệu thông báo
        Log::info('Converting notification to array for user:', ['user_id' => $notifiable->id]);
        
        return [
            'novel_id' => $this->novel->id,
            'novel_title' => $this->novel->title,
            'chapter_id' => $this->chapter->id,
            'chapter_title' => $this->chapter->title,
            'chapter_number' => $this->chapter->chapter_number,
            'author_name' => $this->novel->user->name,
        ];
    }
}