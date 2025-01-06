<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RecruitmentTypeService;

/**
 * 募集種別に関する処理を実行するコントローラー
 * @version 1.0.0
 * @author S.Yamamoto
 */

class RecruitmentTypeController extends Controller
{
    //
    protected $recruitmentTypeService;

    public function __construct(RecruitmentTypeService $recruitmentTypeService)
    {
        $this->recruitmentTypeService = $recruitmentTypeService;
    }

    /**
     * 募集種別を取得する
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $recruitmentTypes = $this->recruitmentTypeService->getAllRecruitmentTypes();
        return response()->json($recruitmentTypes);
    }
}
