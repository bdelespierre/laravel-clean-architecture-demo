<?php

namespace App\Http\Resources;

use App\Domain\Interfaces\UserEntity;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCreatedResource extends JsonResource
{
    protected UserEntity $user;

    public function __construct($user)
    {
        $this->user = $user;
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
            'name' => $this->user->getName(),
            'email' => $this->user->getEmail()
        ];
    }
}
