<?php

namespace App\Http\Controllers\V1;

use App\Exceptions\ValidationFailedException;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Resources\AdminOrderResource;
use App\Http\Resources\OrderDisputeResource;
use App\Repositories\OrderRepository;
use App\Repositories\OrderDisputeRepository;
use App\Traits\ResponseCodeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Mail\Transport\Transport;
use Illuminate\Support\Facades\Config;

class DisputeController extends Controller
{
    use ResponseCodeTrait;

    protected $order_repository;
    protected $transporter_repository;

    /**
     * DisputeController constructor.
     * @param OrderRepository $order_repository
     * @param OrderDisputeRepository $dispute_repository
     * 
     * 
     */
    public function __construct(OrderRepository $order_repository, OrderDisputeRepository $dispute_repository)
    {
        $this->order_repository = $order_repository;
        $this->dispute_repository = $dispute_repository;
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
            'order_reference_number' => 'required',
            'user_reference_number' => 'required',
            'dispute_type' => 'required',
            'description' => 'sometimes|required|max:500',
        ];

        $this->validate($request_data, $rules);

        $dispute_params = [
            'order_reference_number' => $request_data['order_reference_number'],
            'user_reference_number' => $request_data['user_reference_number'],
            'dispute_type' => $request_data['dispute_type'],
            'description' => ($request->has('description')) ? $request_data['description'] : null
        ];

        $order_dispute = $this->dispute_repository->create($dispute_params);
        
        $order = Null;
        if(!empty($order_dispute))
        {
            $order = $this->order_repository->findByReferenceNumberDispute($order_dispute->order_reference_number);
        }

        $response = $this->getResponseCode(1);
        if (!empty($order)) {
            $response['data']['order'] = OrderResource::collection($order);
        } else {
            $response = $this->getResponseCode(102);
        }

        return $this->response($response);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getDisputeListForAdmin(Request $request)
    {
        $request_data = $request->all();
    
        $dispute_types = Config::get('constants.dispute_types');
        
        $response = $this->getResponseCode(1);

        foreach($dispute_types as $type)
        {
            $disputes = $this->dispute_repository->adminDisputeList($type);
            $response['data']['disputes'][$type] = OrderDisputeResource::collection($disputes);
        }

        return $this->response($response);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationFailedException
     */
    public function updateDisputeDetails(Request $request)
    {
        $request_data = $request->all();

        $rules = [
            'order_reference_number' => 'required|exists:order_disputes,order_reference_number',
        ];

        $this->validate($request_data, $rules);

        $order_update_params = [];
        if ($request->has('status')) {
            $order_update_params['status'] = $request_data['status'];
        }
        if ($request->has('description')) {
            $order_update_params['description'] = $request_data['description'];
        }

        $dispute = $this->dispute_repository->update($request_data['order_reference_number'], $order_update_params);
        
        $response = $this->getResponseCode(1);
        if (!empty($dispute)) {
            $response['data']['dispute'] = new OrderDisputeResource($dispute);
        } else {
            $response = $this->getResponseCode(102);
            if (!empty($message)) {
                $response['message'] = $message;
            }
        }

        return $this->response($response);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationFailedException
     */
    public function filterDispute(Request $request)
    {
        $request_data = $request->all();
            
        $filtered_disputes = $this->dispute_repository->filterDisputes($request_data);
        
        $response = $this->getResponseCode(1);

        $dispute_types = Config::get('constants.dispute_types');
        
        $response = $this->getResponseCode(1);

        foreach($dispute_types as $type)
        {
            $disputes = $this->dispute_repository->adminDisputeList($type,$filtered_disputes);
            $response['data']['disputes'][$type] = OrderDisputeResource::collection($disputes);
        }

        return $this->response($response);
    }
}
