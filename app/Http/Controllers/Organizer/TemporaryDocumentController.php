<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TemporaryDocumentController extends Controller
{
    public function store(Request $request)
    {
        $organizer = Auth::user();
        $temporary_document_path = "storage/users/organizers/$organizer->id/temp_docs";

        foreach($request->file('documents') as $document) {
            $doc_name = $document->getClientOriginalName();
            $document->move($temporary_document_path, $doc_name);

            $path = $temporary_document_path . DIRECTORY_SEPARATOR . $doc_name;
        }

        return ['url' => asset($path)];
    }

    public function destroy(Request $request)
    {
        $organizer = Auth::user();

        if($request->has('code')) { //! the file being deleted is already attached, exists in the events folder
            $event = $this->getEvent($request->code);
            $document_path = "public/events/".$event->id."/documents";
        } else { //! file being deleted is still in temps, exists in the users/organizers folder
            $document_path = "public/users/organizers/$organizer->id/temp_docs";
        }

        Storage::delete("$document_path/$request->name");

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
