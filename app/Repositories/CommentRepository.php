<?php

namespace App\Repositories;

use App\Models\Comment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * コメントに関する処理を実行するレポジトリ
 * @version 1.0.0
 * @author S.Yamamoto
 */
class CommentRepository
{
    public function getAllComments($id)
    {
        return Comment::where('recruitment_id', $id)
            ->orderBy('created_at', 'desc')->with('user')->get();
    }

    public function getCommentById($id)
    {
        return Comment::with('user')->find($id);
    }

    public function createComment($data)
    {
        return DB::transaction(function () use ($data) {
            return Comment::create($data);
        });
    }

    public function updateComment($id, $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $comment = Comment::find($id);
            if ($comment) {
                $comment->update($data);
                return $comment;
            }
            return null;
        });
    }

    public function deleteComment($id)
    {
        return DB::transaction(function () use ($id) {
            return Comment::destroy($id);
        });
    }
}
