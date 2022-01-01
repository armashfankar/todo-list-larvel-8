<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDisputeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $dispute = [
            'order_reference_number' => $this->order_reference_number,
            'user_reference_number' => $this->user_reference_number,
            'transporter_reference_number' => $this->transporter_reference_number,
            'dispute_type' => $this->dispute_type,
            'status' => $this->status,
            'description' => $this->description
        ];

        if ((!empty($this->order)) && $this->order->count()) {
            $dispute['order'] = [
                'order_reference_number' => $this->order->order_reference_number,
                'description' => $this->order->description,
                'schedule' => $this->order->schedule,
                'scheduled_at' => $this->order->scheduled_at,
                'city' => $this->order->city,
                'status' => $this->order->status
            ];
        } else {
            $dispute['order'] = null;
        }

        if ((!empty($this->user)) && $this->user->count()) {
            $dispute['user'] = [
                'user_reference_number' => $this->user->user_reference_number,
                'name' => $this->user->name,
                'mobile_number' => $this->user->mobile_number,
                'email' => $this->user->email
            ];
        } else {
            $dispute['user'] = null;
        }

        if ((!empty($this->transporter)) && $this->transporter->count()) {
            $dispute['transporter'] = [
                'transporter_reference_number' => $this->transporter->transporter_reference_number,
                'name' => $this->transporter->name,
                'mobile_number' => $this->transporter->mobile_number,
                'email' => $this->transporter->email,
                'city' => $this->transporter->city
            ];
        } else {
            $dispute['transporter'] = null;
        }
        
        if ((!empty($this->order->address)) && $this->order->address->count()) {
            $dispute['address'] = [
                'order_address_reference_number' => $this->order->address->order_address_reference_number,
                'pick_up_address_type' => $this->order->address->pick_up_address_type,
                'pick_up_address' => $this->order->address->pick_up_address,
                'pick_up_latitude' => $this->order->address->pick_up_latitude,
                'pick_up_longitude' => $this->order->address->pick_up_longitude,
                'pick_up_contact_name' => $this->order->address->pick_up_contact_name,
                'pick_up_contact_number' => $this->order->address->pick_up_contact_number,
                'drop_up_address_type' => $this->order->address->drop_up_address_type,
                'drop_up_address' => $this->order->address->drop_up_address,
                'drop_up_latitude' => $this->order->address->drop_up_latitude,
                'drop_up_longitude' => $this->order->address->drop_up_longitude,
                'drop_up_contact_name' => $this->order->address->drop_up_contact_name,
                'drop_up_contact_number' => $this->order->address->drop_up_contact_number,
                'distance' => $this->order->address->distance
            ];
        } else {
            $dispute['address'] = null;
        }

        return $dispute;
    }
}
