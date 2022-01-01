<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Exceptions\ValidationFailedException;
use App\Http\Resources\ReminderResource;
use App\Repositories\ToDoRepository;
use App\Repositories\ReminderRepository;
use App\Traits\ResponseCodeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReminderController extends Controller
{

    use ResponseCodeTrait;

    protected $reminder_repository;
    protected $todo_repository;

    /**
     * ReminderController constructor.
     * @param ReminderRepository $reminder_repository
     * @param ToDoRepository $todo_repository
     * 
     */
    public function __construct(ReminderRepository $reminder_repository,
                                ToDoRepository $todo_repository)
    {
        $this->reminder_repository = $reminder_repository;
        $this->todo_repository = $todo_repository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationFailedException
     */
    public function store(Request $request)
    {
        $request_data = $request->all();

        $rules = [
            'todo_reference_number' => 'required|exists:to_dos,todo_reference_number',
            'user_reference_number' => 'required|exists:users,user_reference_number',
            'remind_in' => 'required|integer',
            'type' => 'required|in:day,week'
        ];

        $this->validate($request, $rules);
        
        $reminder_params = [
            'todo_reference_number' => $request_data['todo_reference_number'],
            'user_reference_number' => $request_data['user_reference_number'],
            'remind_in' => $request_data['remind_in'],
            'type' => $request_data['type']
        ];
        
        $reminder = $this->reminder_repository->create($reminder_params);

        $this->reminder_repository->schedule($reminder);
        
        $response = $this->getResponseCode(1);
        if (!empty($reminder)) {
            $response['data']['reminder'] = new ReminderResource($reminder);
        } else {
            $response = $this->getResponseCode(102);
        }

        return response($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $reminder_reference_number
     * @return \Illuminate\Http\Response
     */
    public function destroy($reminder_reference_number)
    {
        $message = '';

        $reminder = $this->reminder_repository->findByReferenceNumber($reminder_reference_number);
        if(empty($reminder)){   
            $response = $this->getResponseCode(108);
            if (!empty($message)) {
                $response['message'] = $message;
            }
            return response($response);
        }
        
        $reminder = $this->reminder_repository->delete($reminder_reference_number);

        $response = $this->getResponseCode(1);
        if (!empty($reminder) && empty($message)) {
            $message = "Deleted Succesfully!";
            $response['message'] = $message;
        } else {
            $response = $this->getResponseCode(102);
            $response['message'] = $message;
        }

        return response($response);
    }
}
