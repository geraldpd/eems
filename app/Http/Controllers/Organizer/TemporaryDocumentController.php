<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\EventServices;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TemporaryDocumentController extends Controller
{

    public function retrieve()
    {
        return (new EventServices)->getTemporaryDocs();
    }

    public function store(Request $request)
    {
        $organizer = Auth::user();
        $temporary_document_path = "users/organizers/$organizer->id/temp_docs";

        $document_path = '';
        foreach($request->file('documents') as $document) {

            if (! Storage::disk('s3')->exists($temporary_document_path)){
                Storage::disk('s3')->makeDirectory($temporary_document_path);
            }

            $document_path = $document->storeAs(
                $temporary_document_path, $document->getClientOriginalName(), 's3'
            );
        }

        return ['document_path' => encrypt($document_path)];
    }

    public function destroy(Request $request)
    {
        $organizer = Auth::user();

        if($request->has('code')) { //! the file being deleted is already attached, exists in the events folder
            $event = $this->getEvent($request->code);
            $document_path = "events/".$event->id."/documents";
        } else { //! file being deleted is still in temps, exists in the users/organizers folder
            $document_path = "users/organizers/$organizer->id/temp_docs";
        }

        Storage::disk('s3')->delete("$document_path/$request->name");

        return "$document_path/$request->name";
    }


    /**
     * Retrieve the event resource using the provided code
     *
     * @param  $code
     * @return App\Model\Event
     */
    private function getEvent($code)
    {
        try {
            $event = Event::whereCode($code)->firstOrFail();
        }
        catch(ModelNotFoundException $e){
            abort(404);
        }

        return $event;
    }


}
