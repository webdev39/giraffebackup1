<?php

use Illuminate\Database\Seeder;

class StressTestingSeeder extends Seeder
{
    const DAYS      = 360;
    const PEOPLES   = 40;

    /**
     * The number of new boards created per week
     *
     * @var array [min, max]
     */
    const COUNT_NEW_BOARDS_PER_MONTH = [1, 2];

    /**
     * The number of new objects created per period by one people
     *
     * @var array [min, max]
     */
    const COUNT_NEW_TASKS_PER_DAY = [1, 4];
    const COUNT_NEW_TIMER_PER_DAY = [2, 10];
    const COUNT_NEW_PAUSE_PER_DAY = [4, 20];

    /**
     * @var array
     */
    private $countNew = [
        'boards' => 0,
        'tasks'  => 0,
        'timers' => 0,
        'pauses' => 0,
    ];

    /**
     * @throws Throwable
     */
    public function run()
    {
        $userTenant = \App\Models\UserTenant::find(1);

        /** @var \App\Models\Group $group */
        $group = $userTenant->groups()->first();

        Auth::setUser($userTenant->user);

        DB::transaction(function () use ($userTenant, $group) {
            \Illuminate\Database\Eloquent\Model::reguard();

            $week       = ceil(self::DAYS / 30);
            $currentDay = \Carbon\Carbon::today();

            self::print("Running boards creation");

            for ($w = 1; $w <= $week; $w++) {
                for ($b = 1; $b <= self::getRandomNumber(self::COUNT_NEW_BOARDS_PER_MONTH); $b++) {
                    app('BoardSer')->createBoard([
                        'name'          => "{$group->name} Board #{$w}-{$b}",
                        'group_id'      => $group->id,
                        'created_at'    => $currentDay,
                        'updated_at'    => $currentDay,
                    ]);

                    $this->countNew['boards']++;
                }

                $currentDay->addDay(7);
            }

            $boards     = app('BoardRepo')->getBoardsByGroupIds([$group->id]);
            $boardIds   = $boards->pluck('id')->unique()->toArray();
            $currentDay = \Carbon\Carbon::today();

            self::print("{$this->countNew['boards']} boards were created");
            self::print("Running tasks creation");

            for ($d = 1; $d <= self::DAYS; $d++) {
                for ($tk = 1; $tk <= self::getRandomNumber(self::COUNT_NEW_TASKS_PER_DAY); $tk++) {
                    /** @var \App\Models\Board $board */
                    $board = $boards->random();

                    /** @var \App\Models\Task $task */
                    $task = app('TaskSer')->create([
                        'name'          => "{$board->name} Task #{$d}-{$tk}",
                        'board_id'      => $board->id,
                        'created_at'    => $currentDay,
                        'updated_at'    => $currentDay,
                    ], $userTenant->id);

                    app('TaskSer')->attachUserTenantToTask($task, $userTenant->id);

                    $this->countNew['tasks']++;
                }

                $currentDay->addDay(1);
            }

            $tasks      = app('TaskRepo')->getTasksByBoardIds($boardIds);
            $currentDay = \Carbon\Carbon::today();

            self::print("{$this->countNew['tasks']} tasks were created");
            self::print("Running timers creation");

            for ($d = 1; $d <= self::DAYS; $d++) {
                for ($tr = 1; $tr <= self::getRandomNumber(self::COUNT_NEW_TIMER_PER_DAY); $tr++) {
                    /** @var \App\Models\Task $task */
                    $task = $tasks->random();

                    app('TimerLogSer')->createOrUpdateTimerLog([
                        'attachments_id' => [],
                        'task_id'        => $task->id,
                        'comment'        => null,
                        'time'           => self::getRandomTime(1, 1440),
                        'logged_at'      => $currentDay->format('Y-m-d H:m:s'),
                        'user_tenant_id' => Auth::userTenantId(),
                    ]);

                    $this->countNew['timers']++;
                }

                $currentDay->addDay(1);
            }

            $timers     = app('TimerSer')->getTimerListByUserTenantId($userTenant->id);
            $currentDay = \Carbon\Carbon::today();

            self::print("{$this->countNew['timers']} timers were created");
            self::print("Running pauses creation");

            for ($d = 1; $d <= self::DAYS; $d++) {
                for ($pe = 1; $pe <= self::getRandomNumber(self::COUNT_NEW_PAUSE_PER_DAY); $pe++) {
                    /** @var \App\Models\Timer $timer */
                    $timer = $timers->random();

                    app('TimerSer')->createPauseForTimer($timer->id, self::getRandomTime(1, 100), $currentDay->format('Y-m-d H:m:s'));

                    $this->countNew['pauses']++;
                }

                $currentDay->addDay(1);
            }

            self::print("{$this->countNew['pauses']} pauses were created");
            self::print("Finished");
        });
    }


    /**
     * @param $min
     * @param $max
     *
     * @return string
     */
    private static function getRandomTime($min, $max)
    {
        $minutes = rand($min, $max);

        return \Carbon\CarbonInterval::hours(floor($minutes / 60))->minutes($minutes % 60)->format("%H:%I:%S");
    }

    /**
     * @param array $range
     *
     * @return int
     */
    private static function getRandomNumber(array $range) : int
    {
        return rand($range[0], $range[1]);// * self::PEOPLES;
    }

    /**
     * @param string $message
     */
    private static function print(string $message)
    {
        $timestamp = now()->format('Y-m-d H:m:s');

        echo "\033[36m[{$timestamp}]\033[0m {$message}\n";
        flush();
    }
}
