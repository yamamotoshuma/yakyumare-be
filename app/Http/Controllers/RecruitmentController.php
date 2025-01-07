<?php

namespace App\Http\Controllers;

use App\Services\RecruitmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * 募集情報に関する処理を実行するコントローラー
 * @version 1.0.0
 * @author S.Yamamoto
 */
class RecruitmentController extends Controller
{
    protected $recruitmentService;

    public function __construct(RecruitmentService $recruitmentService)
    {
        $this->recruitmentService = $recruitmentService;
    }

    /**
     * 任意の指定された募集情報を取得する
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $filters = $request->only(['type_id', 'start_date', 'end_date', 'prefecture', 'city', 'active_only', 'sort', 'order','user_id','apply_user_id']);
        $perPage = $request->input('per_page', 20);
        $recruitments = $this->recruitmentService->getAllRecruitments($filters, $perPage);
        return response()->json($recruitments, 200);
    }

    /**
     * 募集IDに紐づく募集情報を取得する
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $recruitment = $this->recruitmentService->getRecruitmentById($id);
        if ($recruitment) {
            return response()->json($recruitment, 200);
        }
        return response()->json(['message' => '募集情報が存在しませんでした。'], 404);
    }

    /**
     * 募集の登録を実行する
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type_id' => 'required|exists:recruitment_types,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'event_date' => 'required|date|after:today',
            'deadline' => 'required|date|after:today',
            'prefecture' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'place' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
        ]);
        // 相関チェック: 開催日が期限より後かを確認
        Validator::make($data, [
            'event_date' => function ($attribute, $value, $fail) use ($data) {
                if (isset($data['deadline']) && $value <= $data['deadline']) {
                    $fail('開催日は募集期限より後の日付を設定してください。');
                }
            },
        ])->validate();

        $recruitment = $this->recruitmentService->createRecruitment($data);
        return response()->json($recruitment, 201);
    }

    /**
     * 募集の更新を実行する
     * @param \Illuminate\Http\Request $request
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type_id' => 'required|exists:recruitment_types,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'event_date' => 'required|date|after:today',
            'deadline' => 'required|date|after:today',
            'prefecture' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'place' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
        ]);
        $recruitment = $this->recruitmentService->updateRecruitment($id, $data);
        if ($recruitment) {
            return response()->json($recruitment, 200);
        }
        return response()->json(['message' => '募集情報が存在しませんでした。'], 404);
    }

    /**
     * 募集の終了を実行する
     * @param \Illuminate\Http\Request $request
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function closeRecruitment(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:recruitments,id',
        ]);
        $recruitment =$this->recruitmentService->closeRecruitment($data['id']);

        if ($recruitment) {
            return response()->json($recruitment, 204);
        }
        return response()->json(['message' => '募集情報が存在しませんでした。'], 404);
    }

    /**
     * 募集の削除を実行する
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $result = $this->recruitmentService->deleteRecruitment($id);
        if ($result) {
            return response()->json(['message' => 'Recruitment deleted successfully'],204);
        }
        return response()->json(['message' => 'Recruitment not found'], 404);
    }
}

