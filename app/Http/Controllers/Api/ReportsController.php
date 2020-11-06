<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportsFilterRequest;
use App\Http\Resources\BoardResource;
use App\Http\Resources\GroupResource;
use App\Models\Permission;
use App\Models\UserTenant;
use Illuminate\Support\Facades\Auth;
use League\Csv\Writer;

class ReportsController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function boards()
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        if ($userTenant->can(Permission::REPORT_OTHER_DATA_PERMISSION)) {
            $boards = app('BoardSer')->getBoardsByTenantId($userTenant->tenant_id);
        } else {
            $boards = app('BoardSer')->getBoardsByUserTenantId($userTenant->id);
        }

        return response()->json([
            'boards' => BoardResource::collection($boards)
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function groups()
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        if ($userTenant->can(Permission::REPORT_OTHER_DATA_PERMISSION)) {
            $groups = app('GroupSer')->getGroupsWithRelationsByTenantId($userTenant->tenant_id);
        } else {
            $groups = app('GroupSer')->getGroupsWithRelationsByUserTenantId($userTenant->id);
        }

        return response()->json([
            'groups' => GroupResource::collection($groups)
        ]);
    }







    /**
     * @param ReportsFilterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filter(ReportsFilterRequest $request)
    {
        $requestQuery    = $request->get('selectedItems');
        $customTimerange = $request->get('customTimerange');
        $reports         = [];

        if ($requestQuery) {
            /** @var UserTenant $userTenant */
            $userTenant = Auth::userTenant();
            $canReadReportsFully = false;

            if ($userTenant->can(Permission::REPORT_OTHER_DATA_PERMISSION['name'])) {
                $canReadReportsFully = true;
            } elseif ($userTenant->can(Permission::REPORT_OWN_DATA_PERMISSION['name'])) {
                $canReadReportsFully = false;
            } else {
                abort(403, 'User is cannot read reports');
            }

            $reports = app('ReportsSer')->filterReports($requestQuery, $canReadReportsFully, $customTimerange);
        }

        return response()->json([
            'message'           => 'Reports feed',
            'records'           => $reports['records'],
            'totalTimeUsed'     => $reports['totalTimeUsed'],
            'totalTimeBill'     => $reports['totalTimeBill'],
            'totalUnbilledTime' => $reports['totalUnbilledTime'],
            'totalBilledTime'   => $reports['totalBilledTime']
        ]);
    }

    /**
     * @param ReportsFilterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function export(ReportsFilterRequest $request)
    {
        $requestQuery    = $request->get('selectedItems');
        $customTimerange = $request->get('customTimerange');
        $reports         = [];

        if ($requestQuery) {
            $requestQuery = json_decode($requestQuery, true);
            /** @var UserTenant $userTenant */
            $userTenant = Auth::userTenant();
            $canReadReportsFully = false;

            if ($userTenant->can(Permission::REPORT_OTHER_DATA_PERMISSION['name'])) {
                $canReadReportsFully = true;
            } elseif ($userTenant->can(Permission::REPORT_OWN_DATA_PERMISSION['name'])) {
                $canReadReportsFully = false;
            } else {
                abort(403, 'User is cannot read reports');
            }

            $reports = app('ReportsSer')->filterReports($requestQuery, $canReadReportsFully, $customTimerange);
        }

        
        $csv = Writer::createFromPath(tempnam(sys_get_temp_dir(), 'report'), 'w+');
        $csv->insertOne(['Group', 'Board', 'Task', 'User', 'Time-used', 'Time-Bill', 'Bill-Status', 'Comment']);
        foreach ($reports['records'] as $record) {
            $csv->insertOne([
                $record->group_name,
                $record->board_name,
                $record->task_name,
                $record->user_name . ' ' . $record->user_last_name,
                $record->time_used->forHumans(),
                $record->time_bill->forHumans(),
                $record->billing_status_alias,
                $record->comment
            ]);
        }
        
        return $csv;
    }
}
