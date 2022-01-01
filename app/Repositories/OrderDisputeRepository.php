<?php

namespace App\Repositories;

use App\Helpers\UtilHelper as Util;
use App\Models\Order;
use App\Models\OrderDispute;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class OrderDisputeRepository
{
    protected $model;
    protected $order_repository;


    public function __construct(OrderDispute $model, Order $order_repository)
    {
        $this->model = $model;
        $this->order_repository = $order_repository;

    }

    /**
     * @param $data
     * @return OrderDispute
    */
    public function create($data)
    {
        return $this->model->create($data);
    }

    /**
     * @param $dispute_type
     * @return OrderDispute
    */
    public function adminDisputeList($type,$disputes_array=Null)
    {
        $disputes = $this->model->where('dispute_type',$type);
        if(!empty($disputes_array)){
            $disputes = $disputes->whereIn('order_reference_number',$disputes_array);
        }
        $disputes = $disputes->get();
        
        return $disputes;
    }

    /**
     * @param $order_reference_number
     * @param $params
     * @return mixed
    */
    public function update($order_reference_number, $params)
    {
        $dispute = $this->model->where('order_reference_number', $order_reference_number)->first();

        if (!empty($params)) {
            $dispute->update($params);
        }

        return $dispute;
    }

    /**
     * @param $param
     * @return Array
    */
    public function filterDisputes($params)
    {
        $dispute_list = [];
        $disputes = $this->model;

        if (!empty($params['dispute_type'])) {
            $disputes = $disputes->where('dispute_type',$params['dispute_type']);
        }
        
        if (!empty($params['city'])) {
            $city = $params['city'];
            $disputes = $disputes->whereHas('order', function ($q) use ($city) {
                $q->where('city', $city);
            });
        }

        $disputes = $disputes->get();
        
        if($disputes->count()){
            $dispute_list = $disputes->pluck('order_reference_number')->toArray();
        }
        
        return $dispute_list;
    }
}
