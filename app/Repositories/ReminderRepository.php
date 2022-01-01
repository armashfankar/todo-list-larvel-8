<?php

namespace App\Repositories;

use App\Helpers\UtilHelper as Util;
use App\Models\ToDo;
use App\Models\Reminder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use App\Jobs\SendReminderJob;

class ReminderRepository
{
    protected $model;

    public function __construct(Reminder $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        $data['reminder_reference_number'] = $this->generateReferenceNumber();
                
        return $this->model->create($data);
    }

    /**
     * @param $reminder_reference_number
     * @param $params
     * @return mixed
     */
    public function update($reminder_reference_number, $params)
    {
        $reminder = $this->model->where('reminder_reference_number', $reminder_reference_number)->first();

        if (!empty($params)) {
            $reminder->update($params);
        }

        return $reminder;
    }

    /**
     * @param $reminder_reference_number
     * @return mixed
     */
    public function delete($reminder_reference_number)
    {
        return $this->model->where('reminder_reference_number', $reminder_reference_number)->delete();
    }

    /**
     * @return string
     */
    private function generateReferenceNumber()
    {
        return 'REM' . Util::generateString(true);
    }


    /**
     * @param $reminder_reference_number
     * @return mixed
     */
    public function findByReferenceNumber($reminder_reference_number)
    {
        return $this->model->where('reminder_reference_number', $reminder_reference_number)->first();
    }

    /**
     * @param $reminder
     * @return mixed
     */
    public function schedule($reminder)
    {
        //calcuate delay in hours
        $hours = $this->calculateDelay($reminder);

        $reminder['email'] = $reminder->user->email;
        dispatch(new SendReminderJob($reminder))->delay(Carbon::now()->addHours($hours));
    }

    /**
     * @param $reminder
     * @return $hour
     */
    public function calculateDelay($reminder)
    {
        $hours = 0;

        if($reminder->type == 'day')
        {
            $hours = $reminder->remind_in * 24;
        
        }else{
            $days = $reminder->remind_in * 7;
            $hours = $days * 24;
        }

        return $hours;
    }
    
}
