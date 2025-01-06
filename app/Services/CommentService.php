<?php

namespace App\Services;

use App\Repositories\CommentRepository;

/**
 * コメントに関する処理を実行するサービス
 * @version 1.0.0
 * @author S.Yamamoto
 */
class CommentService
{
    protected $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    /**
     * 募集IDに紐づくコメントの一覧取得を実行する
     * @param mixed $id
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAllComments($id)
    {
        return $this->commentRepository->getAllComments($id);
    }

    /**
     * コメントIDに紐づくコメントの取得を実行する
     * @param mixed $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function getCommentById($id)
    {
        return $this->commentRepository->getCommentById($id);
    }

    /**
     * コメントの登録を実行する
     * @param mixed $data
     * @return mixed
     */
    public function createComment($data)
    {
        return $this->commentRepository->createComment($data);
    }

    /**
     * コメントの更新を実行する
     * @param mixed $id
     * @param mixed $data
     * @return mixed
     */
    public function updateComment($id, $data)
    {
        return $this->commentRepository->updateComment($id, $data);
    }

    /**
     * コメントの削除を実行する
     * @param mixed $id
     * @return mixed
     */
    public function deleteComment($id)
    {
        return $this->commentRepository->deleteComment($id);
    }
}

