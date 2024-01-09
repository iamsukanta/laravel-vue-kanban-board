<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ColumnListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'created_at' => $this->created_at,
            'deleted_at' => $this->deleted_at,
            'cards' => count($this->cards)?CardResource::collection(
                $this->cards()
                                ->when($request->date, function ($q) use ($request) {
                                    $q->whereDate('created_at', $request->date);
                                })
                                ->when($request->has('status') && $request->status == 1, function ($q) use ($request) {
                                    $q->whereNull('deleted_at');
                                })
                                ->when($request->has('status') && !is_null($request->status) && $request->status == 0, function ($q) use ($request) {
                                    $q->whereNotNull('deleted_at');
                                })
                                ->orderBy('position')
                                ->get()
            ):[]
        ];
    }
}
