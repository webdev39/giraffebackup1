<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Http\Resources\TagsCollectionResource;
use App\Models\Tag;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TagController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        $tags = Tag::query()->with(['boards', 'tasks', 'comments'])->paginate(30);

        return new TagsCollectionResource($tags);
    }

    /**
     * @param string $tagName
     * @return TagResource
     */
    public function show(string $tagName)
    {
        if(!$tag = Tag::whereName($tagName)->with(['boards', 'tasks', 'comments'])->first()) {
            abort(404);
        }

        return new TagResource($tag);
    }

    /**
     * @param Tag $tag
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();

        return response()->json(['message' => 'Success']);
    }
}
