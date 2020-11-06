<?php

namespace App\Http\Controllers;

use App\Http\Responses\DataSetResponse;
use App\Models\Comment;
use App\Models\Task;
use App\Models\UserTenant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class IndexController
 *
 * @package App\Http\Controllers\Api
 */
class IndexController extends Controller
{
    /**
     * @return Response
     */
    public function index() : Response
    {
        return response()->view('global');
    }

    public function wrongUrl(Request $request) : Response
    {
        return response()->view('wrong',['message' => $request->get('message')]);
    }

    /**
     * @return DataSetResponse
     */
    public function getDataSet()
    {
        return new DataSetResponse();
    }

    /**
     * Test router
     */
    public function test()
    {
        //
    }
}
