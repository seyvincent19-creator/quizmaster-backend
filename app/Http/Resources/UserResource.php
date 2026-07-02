<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'class_id' => $this->class_id,
            'class_name' => $this->schoolClass?->name,
            'year_of_study' => $this->schoolClass?->year_of_study,
            'generation' => $this->schoolClass?->generation,
            'department_id' => $this->schoolClass?->department_id,
            'department_name' => $this->schoolClass?->department?->name,
            'email' => $this->email,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
