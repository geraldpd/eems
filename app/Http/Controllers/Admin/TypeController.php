<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Type;
use Illuminate\Http\Request;

use App\Http\Requests\Type\{
    StoreRequest,
    UpdateRequest
};


class TypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $types = Type::all();
        return view('admin.types.index', compact('types'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.types.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        Type::create($request->validated());

        return redirect()->route('admin.types.index')->with('message', 'Event Type Successfully Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\type  $type
     * @return \Illuminate\Http\Response
     */
    public function show(type $type)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\type  $type
     * @return \Illuminate\Http\Response
     */
    public function edit(type $type)
    {
        return view('admin.types.edit', compact('type'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\type  $type
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, type $type)
    {
        $data = $request->validated();

        if(! $request->has('is_active')) {
            $data['is_active'] = 0;
        }

        $type->update($data);

        return redirect()->route('admin.types.index')->with('message', $type->name.' Successfully Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\type  $type
     * @return \Illuminate\Http\Response
     */
    public function destroy(type $type)
    {

        if($type->events->count()) {
            return response()->json([
                'result' => 'fail',
                'message' => 'existing_event_relationship'
            ]);
        }

        $type->delete();

        return redirect()->back()->with('message', $type->name.' Successfully Deleted');
    }
}
