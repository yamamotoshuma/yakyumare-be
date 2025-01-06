<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CommentService;
use Illuminate\Support\Facades\Log;

/**
 * コメントに関する処理を実行するコントローラー
 * @version 1.0.0
 * @author S.Yamamoto
 */

class CommentController extends Controller
{
    //
    protected $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * 募集に紐づくコメント一覧を取得する
     * @param \Illuminate\Http\Request $request 募集情報のID
     * @return mixed|\Illuminate\Http\JsonResponse コメント一覧
     */
    public function index(Request $request)
    {
        $id = $request->recruitment_id;
        $comments = $this->commentService->getAllComments($id);
        return response()->json($comments);
    }

    /**
     * コメントの登録を実行する
     * @param \Illuminate\Http\Request $request コメント情報
     * @return mixed|\Illuminate\Http\JsonResponse 成功
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'recruitment_id' => 'required|exists:recruitments,id',
            'content' => 'required|string',
        ]);

        $comment = $this->commentService->createComment($data);
        return response()->json($comment, 201);
    }
}
