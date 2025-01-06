<?php
namespace App\Services;

use App\Repositories\NoticeRepository;
use Illuminate\Support\Facades\Auth;

/**
 * お知らせに関する処理を実行するサービス
 * @version 1.0.0
 * @author S.Yamamoto
 */
class NoticeService
{
    protected $noticeRepository;

    public function __construct(NoticeRepository $noticeRepository)
    {
        $this->noticeRepository = $noticeRepository;
    }

    /**
     * お知らせの一覧取得を実行する
     * @param mixed $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllPublished($perPage = 10)
    {
        return $this->noticeRepository->getAllPublished($perPage);
    }

    /**
     * お知らせの取得を実行する
     * @param mixed $id
     * @return \App\Models\Notice|\App\Models\Notice[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function find($id)
    {
        return $this->noticeRepository->find($id);
    }

    /**
     * お知らせの登録を実行する
     * @param array $data
     * @throws \Exception
     * @return mixed
     */
    public function create(array $data)
    {
        $user = Auth::user();
        if ($user && $user->isAdmin()) {
            return $this->noticeRepository->create($data);
        }
        throw new \Exception('Unauthorized');
    }

    /**
     * お知らせの更新を実行する
     * @param mixed $id
     * @param array $data
     * @throws \Exception
     * @return mixed
     */
    public function update($id, array $data)
    {
        $user = Auth::user();
        if ($user && $user->isAdmin()) {
            return $this->noticeRepository->update($id, $data);
        }
        throw new \Exception('Unauthorized');
    }

    /**
     * お知らせの削除を実行する
     * @param mixed $id
     * @throws \Exception
     * @return mixed
     */
    public function delete($id)
    {
        $user = Auth::user();
        if ($user && $user->isAdmin()) {
            return $this->noticeRepository->delete($id);
        }
        throw new \Exception('Unauthorized');
    }
}
