<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $organizer = User::find(Auth::user()->id);
        $organizer->load('organization');

        //dd($organizer->organization->supporting_documents_path);
        return view('organizer.profile.index', compact('organizer'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'firstname' => ['required'],
            'lastname' => ['required'],
            'mobile_number' => ['required', 'digits:11', 'regex:/(09)[0-9]{9}/', 'numeric'],
            'password' => ['nullable', 'confirmed'],
            'address' => ['required'],
            'supporting_documents' => ['nullable', 'array', 'max:3']
        ]);

        $user = Auth::user();

        if ($request->has('supporting_documents')) {
            $existingDocuments = count($user->organization?->supporting_documents ?? []);
            $documentCount = count($request->file('supporting_documents')) + $existingDocuments;

            if ($documentCount > 3) {
                return redirect()->back()->with('message', 'Error! You can only have 3 supporting documents at a time.');
            }
        }

        DB::beginTransaction();
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->mobile_number = $request->mobile_number;
        $user->address = $request->address;

        $user->organization->name = $request->organization_name;
        $user->organization->department = $request->department;
        $user->organization->save();

        if ($request->password && $request->password_confirmation) {
            $user->password =  Hash::make($request->password);
        }

        if ($request->has('profile_picture')) {
            $path = $request->file('profile_picture')->store(
                "users/organizers/$user->id/",
                's3'
            );

            $user->profile_picture = [
                'filename' => basename($path),
                'path' => Storage::disk('s3')->url($path)
            ];
        }

        if ($request->has('logo')) {
            $path = $request->file('logo')->store(
                "users/organizers/$user->id/logo",
                's3'
            );

            $user->organization->logo = [
                'filename' => basename($path),
                'path' => Storage::disk('s3')->url($path)
            ];
        }

        if ($request->has('supporting_documents')) {
            $supporting_documents = [];

            foreach ($request->file('supporting_documents') as $supporting_document) {
                $name = $supporting_document->getClientOriginalName();

                $path = $supporting_document->storeAs(
                    "users/organizers/$user->id/supporting_documents",
                    $name,
                    's3'
                );

                $supporting_documents[] = [
                    'filename' => $name,
                    'path' => Storage::disk('s3')->url($path)
                ];
            }

            $user->organization->supporting_documents = $user->organization->supporting_documents
                ? array_merge($user->organization->supporting_documents, $supporting_documents)
                : $supporting_documents;
        }

        $user->organization->save();
        $user->save();

        DB::commit();

        return redirect()->route('organizer.profile.index')->with('message', 'Profile Successfully Updated');
    }
}
