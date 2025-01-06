<?php

namespace App\Services;

use App\Repositories\RecruitmentTypeRepository;

/**
 * 募集種別に関する処理を実行するサービス
 * @version 1.0.0
 * @author S.Yamamoto
 */
class RecruitmentTypeService
{
    protected $recruitmentTypeRepository;

    public function __construct(RecruitmentTypeRepository $recruitmentTypeRepository)
    {
        $this->recruitmentTypeRepository = $recruitmentTypeRepository;
    }

    /**
     * 募集種別の一覧取得を実行する
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllRecruitmentTypes()
    {
        return $this->recruitmentTypeRepository->getAllRecruitmentTypes();
    }

    /**
     * 募集種別の取得を実行する
     * @param mixed $id
     * @return \App\Models\RecruitmentType|\App\Models\RecruitmentType[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function getRecruitmentTypeById($id)
    {
        return $this->recruitmentTypeRepository->getRecruitmentTypeById($id);
    }

    /**
     * 募集種別の登録を実行する
     * @param mixed $data
     * @return mixed
     */
    public function createRecruitmentType($data)
    {
        return $this->recruitmentTypeRepository->createRecruitmentType($data);
    }

    /**
     * 募集種別の更新を実行する
     * @param mixed $id
     * @param mixed $data
     * @return mixed
     */
    public function updateRecruitmentType($id, $data)
    {
        return $this->recruitmentTypeRepository->updateRecruitmentType($id, $data);
    }

    /**
     * 募集種別の削除を実行する
     * @param mixed $id
     * @return mixed
     */
    public function deleteRecruitmentType($id)
    {
        return $this->recruitmentTypeRepository->deleteRecruitmentType($id);
    }
}

