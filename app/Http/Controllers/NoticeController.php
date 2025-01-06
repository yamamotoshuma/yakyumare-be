<?php

namespace App\Http\Controllers;

use App\Services\NoticeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * お知らせに関する処理を実行するコントローラー
 * @version 1.0.0
 * @author S.Yamamoto
 */
class NoticeController extends Controller
{
    protected $noticeService;

    public function __construct(NoticeService $noticeService)
    {
        $this->noticeService = $noticeService;
    }

    /**
     * ページングされたお知らせ一覧を取得する
     * @param \Illuminate\Http\Request $request ページ
     * @return mixed|\Illuminate\Http\JsonResponse お知らせ一覧
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 2);
        $notices = $this->noticeService->getAllPublished($perPage);
        return response()->json($notices);
    }

    /**
     * IDに紐づくお知らせを取得する
     * @param mixed $id お知らせID
     * @return mixed|\Illuminate\Http\JsonResponse お知らせ
     */
    public function show($id)
    {
        $notice = $this->noticeService->find($id);
        return response()->json($notice);
    }

    /**
     * お知らせの登録を実行する
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $notice = $this->noticeService->create($request->all());
            return response()->json($notice, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * お知らせの更新を実行する
     * @param \Illuminate\Http\Request $request
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $notice = $this->noticeService->update($id, $request->all());
            return response()->json($notice);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * お知らせの削除を実行する
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $this->noticeService->delete($id);
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }
}
