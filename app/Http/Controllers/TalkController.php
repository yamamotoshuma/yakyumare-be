<?php

namespace App\Http\Controllers;

use App\Services\ApplicationService;
use App\Repositories\ApplicationRepository;
use App\Services\TalkService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * トーク情報に関する処理を実行するコントローラー
 * @version 1.0.0
 * @author S.Yamamoto
 */
class TalkController extends Controller
{
    protected $talkService;

    public function __construct(TalkService $talkService)
    {
        $this->talkService = $talkService;
    }

    /**
     * ユーザーに紐づくトーク一覧を取得する
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $talks = $this->talkService->getAllTalks();
        return response()->json($talks, 200);
    }
}

