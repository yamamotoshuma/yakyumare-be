<?php

namespace App\Repositories;

use App\Models\Application;
use Illuminate\Support\Facades\DB;

/**
 * 応募情報に関する処理を実行するレポジトリ
 * @version 1.0.0
 * @author S.Yamamoto
 */
class ApplicationRepository
{
    public function getApplication($id)
    {
        return Application::with('recruitment')->with('apply_user')->find($id);
    }

    public function createApplication($data)
    {
        // 連番IDの生成
        $lastApplication = Application::withTrashed()->orderBy('id', 'desc')->first();
        $nextId = $lastApplication ? intval(substr($lastApplication->id, 3)) + 1 : 1; // YKMの後の数字を取得
        $customId = 'APL' . str_pad($nextId, 10, '0', STR_PAD_LEFT); // YKM0000000001形式
        $data['id'] = $customId;

        return Application::create($data);
    }

    public function updateApproval($id, $approval)
    {
        $application = Application::where('id', $id)->first();
        $application->approval = $approval;
        $application->save();
    }
}
