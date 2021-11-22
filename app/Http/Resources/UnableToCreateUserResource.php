<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UnableToCreateUserResource extends JsonResource
{
    protected \Throwable $e;

    public function __construct(\Throwable $e)
    {
        $this->e = $e;
    }
    
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'message' => $this->e->getMessage()
        ];
    }
}
