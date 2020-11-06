<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CreatePipelineRequest;
use App\Http\Requests\CreatePipelineRuleRequest;
use App\Http\Requests\UpdatePipelineRequest;
use App\Http\Requests\UpdatePipelineRuleRequest;
use App\Http\Resources\PipelineResource;
use App\Http\Resources\PipelineRuleResource;
use App\Models\Permission;
use App\Http\Controllers\Controller;
use App\Models\UserTenant;
use Illuminate\Support\Facades\Auth;

/**
 * Class PipelineController
 *
 * @package App\Http\Controllers\Api
 *
 * @author  LexXxurio
 */
class PipelineController extends Controller
{

    /**
     * The service object.
     *
     * @var \App\Services\Pipeline\PipelineService
     */
    protected $pipelineService;

    /**
     * PipelineController constructor.
     */
    public function __construct()
    {
        $this->pipelineService = app('PipelineSer');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Auth::failIfHasNoPermission(Permission::READ_PIPELINE_PERMISSION);

        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        $pipelines = $this->pipelineService->getPipelinesByTenantId($userTenant->tenant_id);

        return response()->json([
            'pipelines' => PipelineResource::collection($pipelines)
        ]);
    }

    /**
     * Retrieve all available filters for a pipeline rule.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function filters()
    {
        $filters = $this->pipelineService->getPipelineFilters();

        return response()->json([
            'filters' => $filters
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $pipelineId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showPipeline(int $pipelineId)
    {
        Auth::failIfHasNoPermission(Permission::READ_PIPELINE_PERMISSION);

        $pipeline = $this->pipelineService->getPipelineById($pipelineId);

        return response()->json([
            'pipeline' => new PipelineResource($pipeline)
        ]);
    }

    /**
     * Create a new pipeline.
     *
     * @param CreatePipelineRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function createPipeline(CreatePipelineRequest $request)
    {
        Auth::failIfHasNoPermission(Permission::CREATE_PIPELINE_PERMISSION);

        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        $pipeline = $this->pipelineService->createPipeline($request->all(), $userTenant->tenant_id);

        return response()->json([
            'pipeline' => new PipelineResource($pipeline)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePipelineRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePipeline(UpdatePipelineRequest $request)
    {
        try {
            Auth::failIfHasNoPermission(Permission::UPDATE_PIPELINE_PERMISSION);

            $this->pipelineService->updatePipeline($request->all(), $request->get('pipeline_id'));

            $pipeline = $this->pipelineService->getPipelineById($request->get('pipeline_id'));

            return response()->json([
                'pipeline' => new PipelineResource($pipeline)
            ]);
        } catch (\Exception $ex) {
            abort(500, $ex->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $pipelineId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyPipeline(int $pipelineId)
    {
        Auth::failIfHasNoPermission(Permission::DELETE_PIPELINE_PERMISSION);

        if (!$this->pipelineService->destroyPipeline($pipelineId)) {
            abort(500, 'Pipeline is not deleted');
        }

        return response()->json(['message' => 'Success']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $pipelineRuleId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showRule(int $pipelineRuleId)
    {
        Auth::failIfHasNoPermission(Permission::READ_PIPELINE_PERMISSION);

        $rule = $this->pipelineService->getPipelineRuleById($pipelineRuleId);

        return response()->json([
            'rule' => new PipelineRuleResource($rule)
        ]);
    }

    /**
     * Create a new rule for a passed pipeline.
     *
     * @param CreatePipelineRuleRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function createRule(CreatePipelineRuleRequest $request)
    {
        Auth::failIfHasNoPermission(Permission::CREATE_PIPELINE_PERMISSION);

        $this->pipelineService->createPipelineRule($request->get('boards'), $request->all());

        $pipeline = $this->pipelineService->getPipelineById($request->get('pipeline_id'));

        return response()->json([
            'pipeline' => new PipelineResource($pipeline)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePipelineRuleRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateRule(UpdatePipelineRuleRequest $request)
    {
        Auth::failIfHasNoPermission(Permission::UPDATE_PIPELINE_PERMISSION);

        $this->pipelineService->updatePipelineRule($request->get('rule_id'), $request->get('boards'), $request->all());

        $pipeline = $this->pipelineService->getPipelineById($request->get('pipeline_id'));

        return response()->json([
            'pipeline' => new PipelineResource($pipeline)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $pipelineId
     * @param int $ruleId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyRule(int $pipelineId, int $ruleId)
    {
        Auth::failIfHasNoPermission(Permission::DELETE_PIPELINE_PERMISSION);

        if (!$this->pipelineService->destroyPipelineRule($ruleId)) {
            abort(500, 'Pipeline rule is not deleted');
        }

        $pipeline = $this->pipelineService->getPipelineById($pipelineId);

        return response()->json([
            'pipeline' => new PipelineResource($pipeline)
        ]);
    }





    /**
     * Retrieve all mails from a mailbox.
     *
     * @param int $pipelineId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function mails(int $pipelineId)
    {
        Auth::failIfHasNoPermission(Permission::READ_PIPELINE_PERMISSION);

        /** @var \Webklex\IMAP\Support\MessageCollection $mails */
        $mails = $this->pipelineService->getMailsByPipelineId($pipelineId);

        //Handle errors
        if (!isset($mails['error'])) {
            return response()->json(['message' => 'Mails have been collected', 'mails' => $mails]);
        } else {
            if (!isset($mails['error']['code'])) {
                return response()->json(['message' => $mails['error']['message']]);
            } else {
                return response()->json(['message' => $mails['error']['message']], $mails['error']['code']);
            }
        }
    }
}
