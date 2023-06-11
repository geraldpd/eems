<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrganizationController extends Controller
{

    public function getDocuments(Request $request)
    {
        //
    }

    public function storeDocuments(Request $request)
    {
        //dd($request->all());
    }

    public function deleteDocuments(Request $request, $supporting_document)
    {
        $organization = $request->user()->organization;

        if (!$organization->supporting_documents) {
            return response()->json([
                'result' => 'fail',
                'message' => 'no supporting document found'
            ]);
        }

        $deleteDocument = collect($organization->supporting_documents)->firstWhere('filename', $supporting_document);

        if (!$deleteDocument) {
            return response()->json([
                'result' => 'fail',
                'message' => 'no supporting document found'
            ]);
        }

        $supporting_documents = collect($organization->supporting_documents)->filter(function ($document) use ($deleteDocument) {
            return $document['filename'] != $deleteDocument['filename'];
        })->all();

        Storage::disk('s3')->delete('users/organizers/' . $request->user()->id . '/supporting_documents/' . $deleteDocument['filename']);

        $organization->supporting_documents = $supporting_documents;
        $organization->save();

        return response()->json([
            'result' => 'success'
        ]);
    }
}
