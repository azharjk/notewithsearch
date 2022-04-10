<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Resources\NoteResource;

class NoteController extends Controller
{
    public function store(Request $request)
    {
        $validator = $this->__validate($request->all(), [
            'title' => 'required|max:60'
        ]);

        $validated = $validator->validated();

        $note = $request->user()
            ->notes()
            ->create($validated);

        return new NoteResource($note);
    }
}
