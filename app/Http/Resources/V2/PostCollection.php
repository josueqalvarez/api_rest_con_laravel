<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PostCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'organization' => 'Platzi',
                'author' => 'Josue Q. Alvarez',
                ], 
            'type' => 'collection',
        ];
    }
}
