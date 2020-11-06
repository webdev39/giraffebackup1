<?php

function enable_profiler(): bool
{
    $xhprofLibFilePath = public_path() . '/xhprof/xhprof_lib/utils/xhprof_lib.php';
    $xhrpofRunsFilePath = public_path() . '/xhprof/xhprof_lib/utils/xhprof_runs.php';
    if(file_exists($xhprofLibFilePath) && file_exists($xhrpofRunsFilePath)) {
        require_once $xhprofLibFilePath;
        require_once $xhrpofRunsFilePath;
        xhprof_enable(XHPROF_FLAGS_CPU);

        return true;
    }
    return false;
}

function run_profiler($profilerNamespace)
{
    $xhprofData = xhprof_disable();
    $xhprofRuns = new \XHProfRuns_Default();
    $xhprofRuns->save_run($xhprofData, $profilerNamespace);
}