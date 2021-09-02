<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentStoreRequest;
use App\Models\Episode;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Episode $episode)
    {
        return response()->json([
            'count' => $episode->comments_count,
            'comments' => $episode->comments,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CommentStoreRequest $request, Episode $episode)
    {
        $episode->comments()->create([
            'parent_id' => $request->comment,
            'comment' => $request->text,
            'user_id' => Auth::id(),
        ]);

        return response()->json(null, 201);
    }
}
