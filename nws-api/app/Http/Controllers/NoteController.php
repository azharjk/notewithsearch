<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

use App\Http\Resources\NoteResource;

class NoteController extends Controller
{
    protected function __validateStoreNote($data)
    {
        $validator = $this->__validate($data, [
            'title' => 'required|max:60',
            'content' => ''
        ]);

        return $validator;
    }

    // FIXME: Maybe convert this to Throw Error
    protected function responseNotNoteFound()
    {
        return Response::json([
            'message' => 'Note you are looking for is not found'
        ], 404);
    }

    public function index(Request $request)
    {
        // FIXME: Handle for a lot of notes (Pagination)
        $notes = $request->user()->notes;

        return NoteResource::collection($notes);
    }

    public function show(Request $request, $id)
    {
        $note = $request->user()
            ->notes()
            ->where('id', $id)
            ->first();

        if (! $note) {
            return $this->responseNotNoteFound();
        }

        return new NoteResource($note);
    }

    public function store(Request $request)
    {
        $validator = $this->__validateStoreNote($request->all());

        $validated = $validator->validated();

        $note = $request->user()
            ->notes()
            ->create($validated);

        return new NoteResource($note);
    }

    public function update(Request $request, $id)
    {
        $exists = $request->user()
            ->notes()
            ->where('id', $id)
            ->exists();

        if (! $exists) {
            return $this->responseNotNoteFound();
        }

        $validator = $this->__validateStoreNote($request->all());

        $validated = $validator->validated();

        $success = $request->user()
            ->notes()
            ->where('id', $id)
            ->update($validated);

        // The case just id not found
        if (! $success) {
            return Response::json([
                'message' => 'There is some error when perform update'
            ], 500);
        }

        $note = $request->user()
            ->notes()
            ->where('id', $id)
            ->first();

        return new NoteResource($note);
    }
}
