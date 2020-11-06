<?php

namespace App\Repositories;

use App\Models\Attachment;
use App\Models\AttachmentComment;
use App\Models\Board;
use App\Models\BoardPriority;
use App\Models\BoardTask;
use App\Models\Budget;
use App\Models\BudgetType;
use App\Models\Comment;
use App\Models\CommentAttachment;
use App\Models\Currency;
use App\Models\Field;
use App\Models\Filter;
use App\Models\Group;
use App\Models\Image;
use App\Models\Log;
use App\Models\LogAttachment;
use App\Models\NotificationSubscription;
use App\Models\Pause;
use App\Models\PersonalDeadline;
use App\Models\Priority;
use App\Models\Repeat;
use App\Models\Review;
use App\Models\SubTask;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\Timer;
use App\Models\TimerBilling;
use App\Models\TimerLog;
use App\Models\User;
use App\Models\TaskSortOrder;
use App\Models\UserBoardSettings;
use App\Models\UserProfile;
use App\Models\UserTenant;
use App\Models\UserTenantGroup;
use App\Models\UserTenantPriority;
use App\Models\UserTenantRole;
use App\Models\UserTenantSettings;
use App\Models\UserTenantTask;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Eloquent\BaseRepository;
use Spatie\Activitylog\Models\Activity;

abstract class BaseRepositoryEloquent extends BaseRepository
{
    /** Table names list */
    protected $activityLogTable;
    protected $attachmentTable;
    protected $boardPriorityTable;
    protected $boardTable;
    protected $boardTaskTable;
    protected $budgetTable;
    protected $budgetTypesTable;
    protected $commentAttachmentTable;
    protected $commentTable;
    protected $filterTable;
    protected $groupTable;
    protected $imageTable;
    protected $logAttachmentTable;
    protected $logTable;
    protected $notificationSubscriptionTable;
    protected $pauseTable;
    protected $personalDeadlineTable;
    protected $priorityTable;
    protected $taskTable;
    protected $tenantTable;
    protected $timerBillingTable;
    protected $timerLogTable;
    protected $timerTable;
    protected $userProfileTable;
    protected $userTable;
    protected $userTaskSortOrderTable;
    protected $userTenantGroupRoleTable;
    protected $userTenantGroupTable;
    protected $userTenantRoleTable;
    protected $userTenantTable;
    protected $userTenantTaskTable;
    protected $subTaskTable;
    protected $userTenantPriority;
    protected $fieldTable;
    protected $userTenantSettingsTable;
    protected $currencyTable;
    protected $attachmentCommentTable;
    protected $repeatTaskTable;
    protected $userBoardSettingsTable;
    protected $reviewTable;

    /**
     * BaseRepositoryEloquent constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->activityLogTable                 = self::getTableName(Activity::class);
        $this->attachmentTable                  = self::getTableName(Attachment::class);
        $this->boardPriorityTable               = self::getTableName(BoardPriority::class);
        $this->boardTable                       = self::getTableName(Board::class);
        $this->userBoardSettingsTable           = self::getTableName(UserBoardSettings::class);
        $this->boardTaskTable                   = self::getTableName(BoardTask::class);
        $this->budgetTable                      = self::getTableName(Budget::class);
        $this->commentAttachmentTable           = self::getTableName(CommentAttachment::class);
        $this->commentTable                     = self::getTableName(Comment::class);
        $this->filterTable                      = self::getTableName(Filter::class);
        $this->groupTable                       = self::getTableName(Group::class);
        $this->imageTable                       = self::getTableName(Image::class);
        $this->logAttachmentTable               = self::getTableName(LogAttachment::class);
        $this->logTable                         = self::getTableName(Log::class);
        $this->notificationSubscriptionTable    = self::getTableName(NotificationSubscription::class);
        $this->pauseTable                       = self::getTableName(Pause::class);
        $this->personalDeadlineTable            = self::getTableName(PersonalDeadline::class);
        $this->priorityTable                    = self::getTableName(Priority::class);
        $this->taskTable                        = self::getTableName(Task::class);
        $this->tenantTable                      = self::getTableName(Tenant::class);
        $this->timerBillingTable                = self::getTableName(TimerBilling::class);
        $this->timerLogTable                    = self::getTableName(TimerLog::class);
        $this->timerTable                       = self::getTableName(Timer::class);
        $this->userProfileTable                 = self::getTableName(UserProfile::class);
        $this->userTable                        = self::getTableName(User::class);
        $this->userTaskSortOrderTable           = self::getTableName(TaskSortOrder::class);
        $this->userTenantGroupTable             = self::getTableName(UserTenantGroup::class);
        $this->userTenantTable                  = self::getTableName(UserTenant::class);
        $this->userTenantRoleTable              = self::getTableName(UserTenantRole::class);
        $this->userTenantTaskTable              = self::getTableName(UserTenantTask::class);
        $this->subTaskTable                     = self::getTableName(SubTask::class);
        $this->fieldTable                       = self::getTableName(Field::class);
        $this->userTenantSettingsTable          = self::getTableName(UserTenantSettings::class);
        $this->currencyTable                    = self::getTableName(Currency::class);
        $this->userTenantPriority               = self::getTableName(UserTenantPriority::class);
        $this->attachmentCommentTable           = self::getTableName(AttachmentComment::class);
        $this->repeatTaskTable                  = self::getTableName(Repeat::class);
        $this->budgetTypesTable                 = self::getTableName(BudgetType::class);
        $this->reviewTable                      = self::getTableName(Review::class);
    }

    /**
     * Get the name of the table for the given model
     *
     * @param string $model
     *
     * @return mixed
     */
    public static function getTableName(string $model) : string
    {
        return (new $model())->getTable();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findOrFail($id)
    {
        if(!$entity = $this->find($id)) {
            throw (new ModelNotFoundException)->setModel(
                get_class($this->model), $id
            );
        }

        return $entity;
    }
}
