<?php

namespace App\Http\Resources;

use App\Models\Permission;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Class ActionResource
 *
 * @package App\Http\Resources
 */
class SettingsResource extends BaseResource
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
            'currency_id'   => $this->currency_id,
            'font_id'       => $this->font_id,
            'creator'       => $this->creator,
            'author'        => $this->author,
            'title'         => $this->title,
            'subject'       => $this->subject,
            'keywords'      => $this->keywords,
            'logo'          => $this->logo,
            'filename'      => $this->filename,
            'fee'           => $this->fee,
            'date_format'   => $this->date_format,
            'money_format'  => (array) $this->money_format,
            'email'         => $this->email,
            'phone'         => $this->phone,
            'postcode'      => $this->postcode,
            'street'        => $this->street,
            'web'           => $this->web,
            'city'          => $this->city,
            'bill_settings' => $this->bill_settings,
        ];
    }
}
