<?php

namespace App\Http\Controllers;

use App\Services\ApplicationService;
use App\Repositories\ApplicationRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * 応募情報に関する処理を実行するコントローラー
 * @version 1.0.0
 * @author S.Yamamoto
 */
class ApplicationController extends Controller
{
    protected $applicationService;

    public function __construct(ApplicationService $applicationService)
    {
        $this->applicationService = $applicationService;
    }

    /**
     * 応募情報の登録を実行する
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'recruitment_id' => 'required',
            'recruited_user_id' => 'required',
            'apply_user_id' => 'required',
        ]);
        $application = $this->applicationService->createApplication($data);

        return response()->json($application, 201);
    }

    /**
     * 応募の承認・否認を実行する
     * @param \Illuminate\Http\Request $request
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'id' => 'required',
            'approval' => 'required',
        ]);
        $this->applicationService->updateApproval($data);

        return response()->json(null, 204);
    }
}

