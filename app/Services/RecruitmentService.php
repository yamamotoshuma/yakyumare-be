<?php

namespace App\Services;

use App\Repositories\RecruitmentRepository;

/**
 * 募集に関する処理を実行するサービス
 * @version 1.0.0
 * @author S.Yamamoto
 */
class RecruitmentService
{
    protected $recruitmentRepository;

    public function __construct(RecruitmentRepository $recruitmentRepository)
    {
        $this->recruitmentRepository = $recruitmentRepository;
    }

    /**
     * 募集情報の一覧取得を実行する
     * @param array $filters
     * @param mixed $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllRecruitments(array $filters, $perPage = 20)
    {
        return $this->recruitmentRepository->getAllRecruitments($filters, $perPage);
    }

    /**
     * 募集の取得を実行する
     * @param mixed $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function getRecruitmentById($id)
    {
        return $this->recruitmentRepository->getRecruitmentById($id);
    }

    /**
     * 募集の登録を実行する
     * @param mixed $data
     * @return mixed
     */
    public function createRecruitment($data)
    {

        return $this->recruitmentRepository->createRecruitment($data);
    }

    /**
     * 募集の更新を実行する
     * @param mixed $id
     * @param mixed $data
     * @return mixed
     */
    public function updateRecruitment($id, $data)
    {
        return $this->recruitmentRepository->updateRecruitment($id, $data);
    }

    /**
     * 募集終了処理
     * @param mixed $id
     * @return mixed
     */
    public function closeRecruitment($id)
    {
        $updateData = ['active' => 0];
        return $this->recruitmentRepository->updateRecruitment($id, $updateData);
        // エラーハンドリングを追加
    }

    /**
     * 募集の削除を実行する
     * @param mixed $id
     * @return mixed
     */
    public function deleteRecruitment($id)
    {
        return $this->recruitmentRepository->deleteRecruitment($id);
    }
}

