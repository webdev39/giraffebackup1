<?php

namespace App\Http\Resources;

/**
 * Class AttachmentResource
 *
 * @package App\Http\Resources
 *
 * @property int                    $id
 * @property bool                   $is_image
 * @property int                    $name
 * @property string                 $path
 * @property int                    $size
 * @property \Carbon\Carbon|null    $created_at
 */
class AttachmentResource extends BaseResource
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

        $downloadUrl = route('attachment.download', md5($this->path));
        $path = $this->path ? url($this->path) : null;
        if(!$this->is_image) {
            $path = $downloadUrl;
        }
        
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'path'          => $path,
            'size'          => $this->size,
            'is_image'      => (boolean) $this->is_image,
            'created_at'    => (string) $this->created_at,
            'download_url'  => $downloadUrl,
            'comment'       => $this->comment,
        ];
    }
}