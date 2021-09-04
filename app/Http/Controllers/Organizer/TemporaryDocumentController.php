<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
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
        $temporary_document_path = "storage/users/organizers/$organizer->id/temp_docs";

        return File::delete(public_path("$temporary_document_path/$request->name"));
    }
}
