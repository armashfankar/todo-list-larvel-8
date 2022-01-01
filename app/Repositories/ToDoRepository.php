<?php

namespace App\Repositories;

use App\Helpers\UtilHelper as Util;
use App\Models\ToDo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class ToDoRepository
{
    protected $model;

    public function __construct(ToDo $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        $data['todo_reference_number'] = $this->generateReferenceNumber();

        return $this->model->create($data);
    }

    /**
     * @param $params
     * @param bool $first
     * @return Order[]|Collection
     */
    public function all($params, $first = false)
    {
        if (!empty($params['page'])) {
            $limit = $params['limit'];
            $skip = ($params['page'] - 1) * $limit;
        }

        $todos = $this->model->where('user_reference_number', $params['user_reference_number']);

        if (!empty($params['status'])) {
            switch (strtolower($params['status'])) {
                case 'complete':
                    $todos = $todos->where('status', 'complete');
                    break;

                case 'incomplete':
                    $todos = $todos->where('status', 'incomplete');
                    break;
            }
        } else {
            $todos = $todos->whereIn('status', ['complete','incomplete']);
        }

        if (isset($limit) && isset($skip)) {
            $todos = $todos->take($limit)->skip($skip);
        }

        $todos = $todos->orderBy('due_date', 'ASC')
            ->with('reminder', 'user');

        if ($first) {
            $todos = $todos->first();
        } else {
            $todos = $todos->get();
        }

        return $todos;
    }

    /**
     * @param $order_reference_number
     * @param $params
     * @return mixed
     */
    public function update($todo_reference_number, $params)
    {
        $todo = $this->model->where('todo_reference_number', $todo_reference_number)->first();

        if (!empty($params)) {
            $todo->update($params);
        }

        return $todo;
    }

    /**
     * @param $todo_reference_number
     * @return mixed
     */
    public function delete($todo_reference_number)
    {
        return $this->model->where('todo_reference_number', $todo_reference_number)->delete();
    }

    /**
     * @return string
     */
    private function generateReferenceNumber()
    {
        return 'TOD' . Util::generateString(true);
    }


    /**
     * @param $todo_reference_number
     * @return mixed
     */
    public function findByReferenceNumber($todo_reference_number)
    {
        return $this->model->where('todo_reference_number', $todo_reference_number)->first();
    }
}
