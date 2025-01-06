<?php

namespace App\Repositories;

use App\Models\RecruitmentType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 募集種別に関する処理を実行するレポジトリ
 * @version 1.0.0
 * @author S.Yamamoto
 */
class RecruitmentTypeRepository
{
    public function getAllRecruitmentTypes()
    {
        return RecruitmentType::all();
    }

    public function getRecruitmentTypeById($id)
    {
        return RecruitmentType::find($id);
    }

    public function createRecruitmentType($data)
    {
        return DB::transaction(function () use ($data) {
            return RecruitmentType::create($data);
        });
    }

    public function updateRecruitmentType($id, $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $recruitmentType = RecruitmentType::find($id);
            if ($recruitmentType) {
                $recruitmentType->update($data);
                return $recruitmentType;
            }
            return null;
        });
    }

    public function deleteRecruitmentType($id)
    {
        return DB::transaction(function () use ($id) {
            return RecruitmentType::destroy($id);
        });
    }
}
