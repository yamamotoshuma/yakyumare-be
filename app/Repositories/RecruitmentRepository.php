<?php

namespace App\Repositories;

use App\Models\Recruitment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 募集に関する処理を実行するレポジトリ
 * @version 1.0.0
 * @author S.Yamamoto
 */
class RecruitmentRepository
{
    public function getAllRecruitments(array $filters, $perPage = 20)
    {
        $query = Recruitment::with('user');

        $this->applyFilters($query, $filters);
        $this->applySorting($query, $filters);

        return $query->paginate($perPage);
    }


    public function getRecruitmentById($id)
    {
        return Recruitment::with('user',)->with('type')->With('applications')->find($id);
    }

    public function createRecruitment($data)
    {
        return DB::transaction(function () use ($data) {
            // 連番IDの生成
            $lastRecruitment = Recruitment::withTrashed()->orderBy('id', 'desc')->first();
            $nextId = $lastRecruitment ? intval(substr($lastRecruitment->id, 3)) + 1 : 1; // YKMの後の数字を取得
            $customId = 'YKM' . str_pad($nextId, 9, '0', STR_PAD_LEFT); // YKM0000000001形式
            $data['id'] = $customId;

            return Recruitment::create($data);
        });
    }

    public function updateRecruitment($id, $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $recruitment = Recruitment::find($id);
            if ($recruitment) {
                $recruitment->update($data);
                return $recruitment;
            }
            return null;
        });
    }

    public function deleteRecruitment($id)
    {
        return DB::transaction(function () use ($id) {
            return Recruitment::destroy($id);
        });
    }

    private function applyFilters($query, $filters)
    {
        $filterMethods = [
            'type_id' => fn($query, $value) => $query->where('type_id', $value),
            'start_date' => fn($query, $value) => $query->whereDate('event_date', '>=', $value),
            'end_date' => fn($query, $value) => $query->whereDate('event_date', '<=', $value),
            'prefecture' => fn($query, $value) => $query->where('prefecture', 'LIKE', $value . '%'),
            'city' => fn($query, $value) => $query->where('city', 'LIKE', $value . '%'),
            'user_id' => fn($query, $value) => $query->where('user_id', $value),
            'active_only' => fn($query, $value) => $this->applyActiveOnlyFilter($query, $value),
            'apply_user_id' => function($query, $value) {
                $query->whereHas('applications', function($q) use ($value) {
                    $q->where('apply_user_id', $value);
                });
            },
        ];

        foreach ($filterMethods as $key => $method) {
            if (!empty($filters[$key])) {
                $method($query, $filters[$key]);
            }
        }
    }

    private function applyActiveOnlyFilter($query, $value)
    {
        if (filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
            $query->where('active', true)->where('deadline', '>=', now());
        }
    }

    private function applySorting($query, $filters)
    {
        // TODO:フロントのsort、orderを修正するAND条件分岐修正
        if (!empty($filters['sort']) && !empty($filters['order'])) {
            if($filters['sort'] == 'created_at') {
                $query->orderBy($filters['sort'], 'desc');
            }else{
                $query->where('event_date', '>=', now());
                $query->orderBy('event_date', 'asc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }
    }
}
