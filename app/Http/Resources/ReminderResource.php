<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use Storage;

class ReminderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $reminder = [
            'reminder_reference_number' => $this->reminder_reference_number,
            'todo_reference_number' => $this->todo_reference_number,
            'user_reference_number' => $this->user_reference_number,
            'remind_in' => $this->remind_in,
            'type' => $this->type,
            'status' => $this->status,
            'is_email_sent' => $this->is_email_sent,
            'created_at' => $this->created_at->format('Y-m-d H:i:s')
        ];


        if ((!empty($this->todo)) && $this->todo->count()) {
            $reminder['todo'] = [
                'todo_reference_number' => $this->todo->todo_reference_number,
                'title' => $this->title,
                'body' => $this->body,
                'due_date' => $this->due_date,
                'attachment' => $this->attachment,
                'status' => $this->status,
                'is_archived' => $this->is_archived,
                'is_reminder_set' => $this->is_reminder_set
            ];
        } else {
            $reminder['todo'] = null;
        }

        if ((!empty($this->user)) && $this->user->count()) {
            $reminder['user'] = [
                'user_reference_number' => $this->user->user_reference_number,
                'name' => $this->user->name,
                'email' => $this->user->email
            ];
        } else {
            $reminder['user'] = null;
        }

        return $reminder;
    }
}
