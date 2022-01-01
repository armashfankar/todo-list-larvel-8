<?php

namespace App\Http\Controllers\V1;

use App\Exceptions\ValidationFailedException;
use App\Http\Controllers\Controller;
use App\Http\Resources\ToDoResource;
use App\Repositories\ToDoRepository;
use App\Traits\ResponseCodeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ToDoController extends Controller
{
    use ResponseCodeTrait;

    protected $todo_repository;

    /**
     * ToDOController constructor.
     * @param ToDoRepository $todo_repository
     * 
     */
    public function __construct(ToDoRepository $todo_repository)
    {
        $this->todo_repository = $todo_repository;
        
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationFailedException
     */
    public function index(Request $request)
    {
        $request_data = $request->all();
    
        $rules = [
            'user_reference_number' => 'required'
        ];

        $this->validate($request, $rules);
        
        $todos = $this->todo_repository->all($request_data);
        
        $response = $this->getResponseCode(1);
        if (!empty($todos)) {
            $response['data']['todos'] = ToDoResource::collection($todos);
        } else {
            $response = $this->getResponseCode(104);
        }

        return response($response);
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
            'user_reference_number' => 'required',
            'title' => 'required|max:100',
            'body' => 'required',
            'due_date' => 'sometimes|required|date|date_format:Y-m-d',
            'attachment' => 'sometimes|required|url'
        ];

        $this->validate($request, $rules);

        $todo_params = [
            'user_reference_number' => $request_data['user_reference_number'],
            'title' => $request_data['title'],
            'body' => $request_data['body'],            
            'due_date' => ($request->has('due_date')) ? $request_data['due_date'] : null,
            'attachment' => ($request->has('attachment')) ? $request_data['attachment'] : null,
        ];

        $todo = $this->todo_repository->create($todo_params);

        $response = $this->getResponseCode(1);
        if (!empty($todo)) {
            $response['data']['todo'] = new ToDoResource($todo);
        } else {
            $response = $this->getResponseCode(102);
        }

        return response($response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $order_reference_number
     * @return JsonResponse
     * @throws ValidationFailedException
     */
    public function update(Request $request, $todo_reference_number)
    {
        $request_data = $request->all();
        
        $todo = $this->todo_repository->findByReferenceNumber($todo_reference_number);
        if(empty($todo)){
            
            $response = $this->getResponseCode(108);
            if (!empty($message)) {
                $response['message'] = $message;
            }

            return response($response);
        }
        
        $update_params = [];
        if ($request->has('status')) {
            $update_params['status'] = $request_data['status'];
        }
        if ($request->has('title')) {
            $update_params['title'] = $request_data['title'];
        }
        if ($request->has('body')) {
            $update_params['body'] = $request_data['body'];
        }
        if ($request->has('due_date')) {
            $update_params['due_date'] = $request_data['due_date'];
        }
        if ($request->has('attachment')) {
            $update_params['attachment'] = $request_data['attachment'];
        }

        $update = $this->todo_repository->update($todo_reference_number,$update_params);
        
        $response = $this->getResponseCode(1);
        if (!empty($update)) {
            $response['data']['todo'] = new ToDoResource($update);
        } else {
            $response = $this->getResponseCode(104);
        }

        return response($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $todo_reference_number
     * @return JsonResponse
     */
    public function destroy($todo_reference_number)
    {
        $message = '';

        $todo = $this->todo_repository->findByReferenceNumber($todo_reference_number);
        if(empty($todo)){   
            $response = $this->getResponseCode(108);
            if (!empty($message)) {
                $response['message'] = $message;
            }
            return response($response);
        }
        
        $todo = $this->todo_repository->delete($todo_reference_number);

        $response = $this->getResponseCode(1);
        if (!empty($todo) && empty($message)) {
            $message = "Deleted Succesfully!";
            $response['message'] = $message;
        } else {
            $response = $this->getResponseCode(102);
            $response['message'] = $message;
        }

        return response($response);
    }
}
