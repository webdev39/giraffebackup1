<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ReviewRequest;
use App\Models\Review;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ReviewRepositoryEloquent;

class ReviewController extends Controller
{
    /**
     * The service object.
     *
     * @var \App\Services\Pipeline\PipelineService
     */
    protected $reviewRepository;

    /**
     * PipelineController constructor.
     * @param ReviewRepositoryEloquent $reviewRepositoryEloquent
     */
    public function __construct(ReviewRepositoryEloquent $reviewRepositoryEloquent)
    {
        $this->reviewRepository = $reviewRepositoryEloquent;
    }

    /**
     * Show a newly created resource in storage.
     *
     * @param ReviewRequest $request
     * @return \Illuminate\Http\Response
     */
    public function create(ReviewRequest $request)
    {
        return dd($request, $this->reviewRepository->getTableTest());
    }

}
