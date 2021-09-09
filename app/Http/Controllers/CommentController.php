<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentStoreRequest;
use App\Models\Episode;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse|Responsable
     */
    public function index(Episode $episode)
    {
        return $this->success([
            'count' => $episode->comments_count,
            'comments' => $episode->comments,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse|Responsable
     */
    public function store(CommentStoreRequest $request, Episode $episode)
    {
        $episode->comments()->create([
            'parent_id' => $request->comment,
            'comment' => $request->text,
            'user_id' => Auth::id(),
        ]);

        return $this->success(null, 201);
    }
}
