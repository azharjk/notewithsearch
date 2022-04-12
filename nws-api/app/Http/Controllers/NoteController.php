<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

use App\Http\Resources\NoteResource;

class NoteController extends Controller
{
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
            return Response::json([
                'message' => 'Note you are looking for is not found'
            ], 404);
        }

        return new NoteResource($note);
    }

    public function store(Request $request)
    {
        $validator = $this->__validate($request->all(), [
            'title' => 'required|max:60',
            'content' => ''
        ]);

        $validated = $validator->validated();

        $note = $request->user()
            ->notes()
            ->create($validated);

        return new NoteResource($note);
    }
}
