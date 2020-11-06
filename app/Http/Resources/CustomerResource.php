<?php

namespace App\Http\Resources;

use App\Models\Customer;

/**
 * Class CustomerResource
 *
 * @package App\Http\Resources
 *
 * @property integer    $id
 * @property string     $name
 * @property string     $email
 * @property string     $city
 * @property string     $last_name
 * @property integer    $country_id
 * @property string     $telephone
 * @property string     $street
 *
 * @property int        $postcode
 * @property int        $house
 * @property string     $hourly_rate
 * @property string     $contact
 * @property int        $status
 * @property int        $tenant_id
 * @property int        $custom_id
 * @property \Carbon\Carbon|null    $created_at
 */
class CustomerResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (empty($this->resource)) {
            return null;
        }

        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'contact'       => $this->contact,
            'email'         => $this->email,
            'city'          => $this->city,
            'country_id'    => $this->country_id,
            'telephone'     => $this->telephone,
            'street'        => $this->street,
            'house'         => $this->house,
            'postcode'      => $this->postcode,
            'hourly_rate'   => (float) $this->hourly_rate,
            'status'        => $this->getCustomerStatus(),
            'tenant_id'     => $this->tenant_id,
            'custom_id'     => $this->custom_id,
            'created_at'    => (string) $this->created_at,
            'is_archived'   => $this->status == Customer::STATUS['archived'],
        ];
    }

    /**
     * @return false|int|string
     */
    public function getCustomerStatus()
    {
        return array_search($this->status, \App\Models\Customer::STATUS);
    }

}
