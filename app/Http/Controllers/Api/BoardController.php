<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBoardRequest;
use App\Http\Requests\UpdateBoardRequest;
use App\Http\Requests\UpdateGroupBoardRequest;
use App\Http\Resources\BoardResource;
use App\Models\Board;
use App\Http\Resources\PriorityResource;
use App\Models\Permission;
use App\Models\UserBoardSettings;
use App\Models\UserTenant;
use App\Services\Board\BoardService;
use App\Services\Group\GroupService;
use Illuminate\Support\Facades\Auth;

class BoardController extends Controller
{
    /**
     * @var BoardService
     */
    public $boardService;

    /**
     * @var GroupService
     */
    public $groupService;

    /**
     * BoardController constructor.
     * @param BoardService $boardService
     * @param GroupService $groupService
     */
    public function __construct(BoardService $boardService, GroupService $groupService)
    {
        $this->boardService = $boardService;
        $this->groupService = $groupService;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLatestActiveBoards()
    {
        $boards = $this->boardService->getLatestActiveBoardsByUserTenantId(Auth::userTenantId());

        return response()->json([
            'boards' => BoardResource::collection($boards)
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $boards = $this->boardService->getBoardsWithRelationsByUserTenantId(Auth::userTenantId());

        return response()->json([
            'boards' => BoardResource::collection($boards)
        ]);
    }

    /**
     * @param int $boardId
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function show(int $boardId)
    {
        $board = $this->boardService->getBoardModelById($boardId);
        $this->authorize('getAccess', $board);

        if (request()->get('board_relations') == 'none') {
            $board = $this->boardService->getBoardById($board->id);

            return response()->json([
                'board' => new BoardResource($board)
            ]);
        }

        $board = $this->boardService->getBoardWithRelationsById($board->id, Auth::userTenantId());

        return response()->json([
            'board' => new BoardResource($board)
        ]);
    }

    /**
     * @param CreateBoardRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function create(CreateBoardRequest $request)
    {
        $group = $this->groupService->getGroupModelById($request->get('group_id'));
        $this->authorize('create', [Board::class, $group]);

        if ($this->boardService->hasBoardNameInGroup($request->get('group_id'), $request->get('name'))) {
            abort(403, 'Board with this name in current group is already exists');
        }

        $board = $this->boardService->createBoard(collect($request->all())->merge(['creator_id' => Auth::id()])->toArray());

        app('PrioritySer')->createUniqDefaultPriorities($board->id);

        $priorities =  app('PrioritySer')->getPrioritiesByBoardId($board->id, Auth::userTenantId());

        if (request()->get('board_relations') == 'none') {
            $board = $this->boardService->getBoardById($board->id);

            return response()->json([
                'board' => new BoardResource($board),
                'priorities' => PriorityResource::collection($priorities),
            ]);
        }

        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();
        $board = $this->boardService->getBoardWithRelationsById($board->id, $userTenant->id);

        return response()->json([
            'board'   => new BoardResource($board),
            'priorities' => PriorityResource::collection($priorities),
        ]);
    }

    /**
     * @param UpdateBoardRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function update(UpdateBoardRequest $request)
    {
        $board = $this->boardService->getBoardModelById($request->get('board_id'));
        $this->authorize('update', $board);

        if ($this->boardService->hasBoardNameInGroup($request->get('group_id'), $request->get('name'), $board->id)) {
            abort(403, 'Board with this name in current group is already exists');
        }

        if ($board->group_id != $request->get('group_id')) {
            if (!$request->exists('members'))  {
                if ($members = $this->groupService->getDiffMembersBetweenGroups($board->group_id, $request->get('group_id'))) {
                    return response()->json([
                        'members' => array_column($members->toArray(), 'id')
                    ]);
                }
            } else {
                $this->groupService->attachUserTenantToGroup($request->get('members'), $request->get('group_id'));
            }

            $this->boardService->moveBoardInGroup($board, $request->get('group_id'));
        }

        $board = $this->boardService->updateBoard($request->all(), $board->id);

        if ($request->exists('quick_nav'))  {
            $boardSettings = UserBoardSettings::findByUseridAndBoardIdOrNew(Auth::id(), $request->get('board_id'));
            $boardSettings->quick_nav =  (bool) $request->get('quick_nav');
            if(empty($boardSettings->user_id)) {
                $boardSettings->user_id = Auth::id();
                $boardSettings->board_id = $request->get('board_id');
            }
            $boardSettings->save();
        }

        $board = request()->get('board_relations') == 'none'
            ? $this->boardService->getBoardById($board->id)
            : $this->boardService->getBoardWithRelationsById($board->id, Auth::userTenantId());

        return response()->json([
            'board'   => new BoardResource($board)
        ]);
    }

    /**
     * @param UpdateGroupBoardRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function updateGroup(UpdateGroupBoardRequest $request)
    {
        $board = $this->boardService->getBoardModelById($request->get('board_id'));
        $this->authorize('update', $board);
        if ($this->boardService->hasBoardNameInGroup($request->get('group_id'), $board->name, $board->id)) {
            abort(403, 'Board with this name in current group is already exists');
        }

        if (!$request->exists('members'))  {
            if ($members = $this->groupService->getDiffMembersBetweenGroups($board->group_id, $request->get('group_id'))) {
                return response()->json([
                    'members' => array_column($members->toArray(), 'id')
                ]);
            }
        } else {
            $this->groupService->attachUserTenantToGroup($request->get('members'), $request->get('group_id'));
        }

        $this->boardService->moveBoardInGroup($board, $request->get('group_id'));

        $board = $this->boardService->getBoardWithRelationsById($board->id, Auth::userTenantId());

        return response()->json([
            'board'   => new BoardResource($board)
        ]);
    }

    /**
     * @param int $boardId
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function destroy(int $boardId)
    {
        $board = $this->boardService->getBoardModelById($boardId);
        $this->authorize('destroy', $board);

        if ($this->boardService->destroyOrArchiveBoard($board->id)) {
            app('PrioritySer')->removePriorityByBoardId($board->id);

            return response()->json([
                'is_archive'    => false
            ]);
        }

        $board = $this->boardService->getBoardWithRelationsById($board->id, Auth::userTenantId());

        return response()->json([
            'board'         => new BoardResource($board),
            'is_archive'    => true
        ]);
    }

    /**
     * @param int $boardId
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function archived(int $boardId)
    {
        $board = $this->boardService->getBoardModelById($boardId);
        $this->authorize('update', $board);

        $isArchive = $this->boardService->archiveBoard($board->id);

        $board = $this->boardService->getBoardWithRelationsById($board->id, Auth::userTenantId());

        return response()->json([
            'board'         => new BoardResource($board),
            'is_archive'    => $isArchive
        ]);
    }

    /**
     * @param int $boardId
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function unarchived(int $boardId)
    {
        $board = $this->boardService->getBoardModelById($boardId);
        $this->authorize('update', $board);

        $isArchive = $this->boardService->unarchivedBoard($board->id);

        $board = $this->boardService->getBoardWithRelationsById($board->id, Auth::userTenantId());

        return response()->json([
            'board'         => new BoardResource($board),
            'is_archive'    => $isArchive
        ]);
    }
}
