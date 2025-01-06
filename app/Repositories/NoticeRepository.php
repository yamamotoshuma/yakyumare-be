<?php

namespace App\Repositories;

use App\Models\Notice;
use Illuminate\Support\Facades\DB;

/**
 * お知らせに関する処理を実行するレポジトリ
 * @version 1.0.0
 * @author S.Yamamoto
 */
class NoticeRepository
{
    public function getAllPublished($perPage = 10)
    {
        return Notice::where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function find($id)
    {
        return Notice::findOrFail($id);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            return Notice::create($data);
        });
    }

    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $notice = Notice::findOrFail($id);
            $notice->update($data);
            return $notice;
        });
    }

    public function delete($id)
    {
        return DB::transaction(function () use ($id) {
            Notice::destroy($id);
        });
    }
}
