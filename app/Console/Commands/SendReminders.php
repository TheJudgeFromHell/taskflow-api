<?php

namespace App\Console\Commands;

use App\Task;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendReminders extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Send task reminders to users';

    public function handle()
    {
        $tasks = Task::where('reminder_at', '<=', Carbon::now())
                     ->where('reminder_sent', false)
                     ->get();
        
        foreach ($tasks as $task) {
            // Здесь можно отправить email или уведомление
            $this->info("Reminder for task: {$task->title} (user: {$task->user_id})");
            
            $task->reminder_sent = true;
            $task->save();
        }
        
        $this->info("Sent {$tasks->count()} reminders");
    }
}