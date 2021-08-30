<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'code' => $this->code,
            'qrcode' => $this->qrcode,
            'organizer_id' => $this->organizer_id,
            'category_id' => $this->category_id,
            'evaluation_id' => $this->evaluation_id,
            'evaluation_questions' => json_decode($this->evaluation_questions, true),
            'name' => $this->name,
            'type' => $this->type,
            'description' => $this->description,
            'location' => $this->location,
            'venue' => $this->venue,
            'online' => $this->online,
            'documents' => $this->documents,
            'schedule_start' => $this->schedule_start,
            'schedule_end' => $this->schedule_end,
            'status' => $this->status,
        ];
    }
}
