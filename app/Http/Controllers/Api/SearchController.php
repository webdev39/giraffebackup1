<?php

namespace App\Http\Controllers\Api;

use App\Models\BaseModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SearchController extends Controller
{

    /**
     * Search for specified models.
     *
     * @param  Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        if(!$request->has('query')) {
            return response()->json(['message' => 'Please type a keyword.']);
        }

        $userTenant = Auth::userTenant();
        $user = Auth::user();

        $searchableModelNameSpace = 'App\\Models\\';
        $result = [];
        // Loop through the searchable models.
        foreach (config('scout.searchableModels') as $model) {
            $modelClass  = $searchableModelNameSpace.$model;
            $modelService = app($model.'Ser');

            if($model == 'Task') {
                $allowedModels = $modelService->searchForUserTenant($userTenant, $request->get('query'))->get();
            } else {
                $foundModels = $modelClass::search($request->get('query'))->get();
                if ($model === 'Comment') {
                    $foundModels->where('groupId', '!=', null)->toArray();
                }
                $policy = app()->make('App\Policies\V2\\' . $model . 'Policy');
                $allowedModels = $foundModels->filter(function(BaseModel $foundModel) use($user, $policy) {
                    return $policy->getAccess($user, $foundModel);
                });
            }

            $result[Str::lower($model) . 's'] = $allowedModels;
        }

        $responseData = ['message' => 'Keyword(s) matched!', 'results' => $result, 'pagination' => ['more' => false]];
        $responseHeader = Response::HTTP_OK;
        if (!count($result)) {
            $responseData = ['message' => 'No results found, please try with different keywords.'];
            $responseHeader = Response::HTTP_NOT_FOUND;
        }

        return response()->json($responseData, $responseHeader);
    }
}