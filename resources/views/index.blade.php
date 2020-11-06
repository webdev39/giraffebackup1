<?php
    $tenant = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6Ly9sb2NhbC5vY2FtLmNvbS9hcGkvdjEvbG9naW4iLCJpYXQiOjE1MjM1MTkyNzYsImV4cCI6MTUyMzYwNTY3NiwibmJmIjoxNTIzNTE5Mjc2LCJqdGkiOiJaaldwUFo4VzdHMWhIYXpZIn0.cirMkvVIZSR7ki71Uv-OryNIPGKqEVaN-iUJRoq40N8';
    $member = $tenant;

?>

<form method="post" action="/api/v1/register">
    <input name="name" value="qwert" placeholder="name"><br>
    <input name="last_name" value="qweqwe" placeholder="last_name"><br>
    <input name="email" value="qwer@qwer.qwer" placeholder="email"><br>
    <input name="password" value="QWE123qwe" placeholder="pass"><br>
    <input type="submit" value="go!">
</form>
<br>
<br>
<form method="post" action="/api/v1/login">
    <input name="email" value="qwer@qwer.qwer" placeholder="email"><br>
    <input name="password" value="QWE123qwe" placeholder="pass"><br>

    <input type="submit" value="go!">
</form>
<br>
<br>
<br>
<br> add attachment
<br>
<form method="post" action="{{ route('comment.save-attachment', ['token' => $tenant])}}" enctype="multipart/form-data">
    <input name="taskId" value="2" placeholder="taskId">taskId<br>
    <input name="attachment" type="file" placeholder="time">file<br>
    <input type="submit" value="add">
</form>
<br>
<br>
<br> update log
<br>
<form method="post" action="{{ route('log.timer.update', ['logId' => 5, 'token' => $tenant])}}">
    <input type="hidden" name="_method" value="put" />
    <input name="taskId" value="2" placeholder="taskId">taskId<br>
    <input name="time" value="03:04:04" placeholder="time">time<br>
    <input name="comment" value="123123213213123123123" placeholder="comment">comment<br>
    <input type="submit" value="update">
</form>
<br>
<br>
<br>
<br> create log
<br>
<form method="post" action="{{ route('log.timer.index', ['token' => $tenant])}}">
    <input name="taskId" value="2" placeholder="taskId">boardId<br>
    <input name="time" value="02:02:02" placeholder="time">time<br>
    <input name="comment" value="Comment1!!!" placeholder="comment">comment<br>
    <input type="submit" value="create">
</form>
<br>
<br>
<br>
<br> task order
<br>
<form method="post" action="{{ route('task.order.change', ['block' => 'open', 'token' => $tenant])}}">
    <input name="boardId" value="1" placeholder="boardId">boardId<br>
    <input name="order[0]" value="3" placeholder="order">order<br>
    <input name="order[1]" value="1" placeholder="order">order<br>
    <input name="order[2]" value="2" placeholder="order">order<br>
    <input type="submit" value="task order">
</form>
<br>
<br>
<br> remove filter
<br>
<form method="post" action="{{ route('filter.remove', ['token' => $tenant])}}">
    <input type="hidden" name="_method" value="delete" />
    <input name="filterId" value="1" placeholder="filterId">filterId<br>
    <input type="submit" value="remove filter">
</form>
<br>
<br>
<br> update filter
<br>
<form method="post" action="{{ route('filter.update', ['token' => $tenant])}}">
    <input type="hidden" name="_method" value="put" />
    <input name="name" value="qwewqe" placeholder="name">name<br>
    <input name="range" value="2017-10-10/2017-12-31" placeholder="range">range<br>
    <input name="assignerIds[1]" value="1" placeholder="assigner_ids[]">assigner_ids[]<br>
    <input name="groupIds[0]" value="1" placeholder="group_ids[]">group_ids[]<br>
    <input name="boardIds[1]" value="9" placeholder="boardIds[]">boardIds[]<br>
    <input name="boardIds[2]" value="1" placeholder="boardIds[]">boardIds[]<br>
    <input name="priorityIds[1]" value="1" placeholder="priorityIds[]">priorityIds[]<br>
    <input name="priorityIds[0]" value="9" placeholder="priorityIds[]">priorityIds[]<br>
    <input name="status" value="0" placeholder="status">status<br>
    <input name="filterId" value="1" placeholder="filterId">filterId<br>
    <input type="submit" value="update filter">
</form>
<br>
<br>
<br> create filter
<br>
<form method="post" action="{{ route('filter.create', ['token' => $tenant])}}">
    <input name="name" value="qwewqe" placeholder="name">name<br>
    <input name="range" value="2017-10-10/2017-12-31" placeholder="range">range<br>
    <input name="assignerIds[1]" value="1" placeholder="assigner_ids[]">assigner_ids[]<br>
    <input name="assignerIds[2]" value="2" placeholder="assigner_ids[]">assigner_ids[]<br>
    <input name="groupIds[1]" value="1" placeholder="group_ids[]">group_ids[]<br>
    <input name="groupIds[2]" value="2" placeholder="group_ids[]">group_ids[]<br>
    {{--<input name="boardIds[1]" value="1" placeholder="boardIds[]">boardIds[]<br>--}}
    {{--<input name="boardIds[2]" value="2" placeholder="boardIds[]">boardIds[]<br>--}}
    {{--<input name="priorityIds[1]" value="1" placeholder="priorityIds[]">priorityIds[]<br>--}}
    {{--<input name="priorityIds[2]" value="2" placeholder="priorityIds[]">priorityIds[]<br>--}}
    <input name="status" value="1" placeholder="status">status<br>
    <input type="submit" value="create filter">
</form>
<br>
<br>
<br> task.attach-to-other-board
<br>
<form method="post" action="{{ route('task.attach-to-other-board', ['token' => $tenant])}}">
    <input type="hidden" name="_method" value="put" />
    <input name="taskId" value="1" placeholder="taskId">taskId<br>
    <input name="boardId" value="2" placeholder="boardId">boardId<br>
    <input type="submit" value="remove task">
</form>
<br>
<br>
<br> remove task
<br>
<form method="post" action="{{ route('task.remove', ['token' => $tenant])}}">
    <input type="hidden" name="_method" value="delete" />
    <input name="taskId" value="1" placeholder="taskId">taskId<br>
    <input type="submit" value="remove task">
</form>
<br>
<br>
<br> clone group
<br>
<form method="post" action="{{ route('group.clone', ['token' => $tenant])}}">
    <input name="groupId" value="1" placeholder="groupId">groupId<br>
    <input type="submit" value="clone group">
</form>
<br>
<br>
<br> change task workflow
<br>
<form method="post" action="{{ route('task.workflow.change', ['token' => $tenant])}}">
    <input type="hidden" name="_method" value="put" />
    <input name="taskId" value="1" placeholder="taskId">taskId<br>
    <input name="isDone" value="1" placeholder="isDone">isDone<br>
    <input type="submit" value="change task workflow">
</form>
<br>
<br>
<br>
<br> update deadline
<br>
<form method="post" action="{{ route('task.update.deadline', ['token' => $tenant])}}">
    <input type="hidden" name="_method" value="put" />
    <input name="taskId" value="1" placeholder="taskId">taskId<br>
    <input name="deadline" value="2018-12-06 09:14:27" placeholder="deadline">deadline<br>
    <input name="plannedDeadline" value="2017-12-06 09:14:27" placeholder="plannedDeadline">plannedDeadline<br>
    <input type="submit" value="update task deadline">
</form>
<br>
<br>
<br>
<br> remove comment
<br>
<form method="post" action="{{ route('comment.remove', ['token' => $tenant])}}">
    <input type="hidden" name="_method" value="delete" />
    <input name="commentId" value="2" placeholder="commentId">commentId<br>
    <input type="submit" value="remove comment!">
</form>
<br>
<br>
<br>
<br> update comment
<br>
<form method="post" action="{{ route('comment.update', ['token' => $tenant])}}">
    <input type="hidden" name="_method" value="put" />
    <input name="commentId" value="1" placeholder="commentId">commentId<br>
    <input name="body" value="123m 123 112312 123" placeholder="body">body<br>
    <input name="attachmentIds[0]" value="2" placeholder="body">image 2<br>
    <input name="attachmentIds[2]" value="3" placeholder="body">image 3<br>
    <input name="attachmentIds[1]" value="4" placeholder="body">image 4<br>
    <input type="submit" value="update comment!">
</form>
<br>
<br>
<br>
<br> create comment
<br>
<form method="post" action="{{ route('comment.create', ['token' => $tenant])}}">
    <input name="taskId" value="1" placeholder="taskId">taskId<br>
    <input name="body" value="qweqweqwe qweqqwe qwe" placeholder="body">body<br>
    <input name="attachmentIds[0]" value="2" placeholder="body">image 2<br>
    <input name="attachmentIds[1]" value="4" placeholder="body">image 4<br>
    <input name="attachmentIds[2]" value="5" placeholder="body">image 5<br>
    <input type="submit" value="create comment!">
</form>
<br>
<br>
<br> change sub task status
<br>
<form method="post" action="{{ route('task.sub-task.change', ['token' => $tenant])}}">
    <input type="hidden" name="_method" value="put" />
    <input name="subTaskId" value="2" placeholder="subTaskId">subTaskId<br>
    <input name="isCompleted" value="1" placeholder="isCompleted">isCompleted<br>
    <input type="submit" value="change sub-task status!">
</form>
<br>
<br>
<br>
<br> remove sub task
<br>
<form method="post" action="{{ route('task.sub-task.remove', ['token' => $tenant])}}">
    <input type="hidden" name="_method" value="delete" />
    <input name="sabTaskId" value="1" placeholder="sabTaskId">sabTaskId<br>
    <input type="submit" value="remove sub-task!">
</form>
<br>
<br>
<br> create sub task
<br>
<form method="post" action="{{ route('task.sub-task.create', ['token' => $tenant])}}">
    <input name="taskId" value="1" placeholder="taskId">taskId<br>
    <input name="name" value="qweqwe" placeholder="name">name<br>
    <input type="submit" value="create sub-task!">
</form>
<br>
<br>
<br>
<br> update task priority
<br>
<form method="post" action="{{ route('task.update.priority', ['token' => $tenant])}}">
    <input name="taskId" value="2" placeholder="taskId">taskId<br>
    <input name="priorityTypeId" type="number" value="3" placeholder="priorityTypeId"><br>
    <input type="submit" value="update priority!">
</form>
<br>
<br>
<br> update task budget
<br>
<form method="post" action="{{ route('task.update.budget', ['token' => $tenant])}}">
    <input name="taskId" value="2" placeholder="taskId">taskId<br>
    <input name="budgetTypeId" type="number" value="1" placeholder="budgetTypeId"><br>
    <input name="softBudget" type="number" value="11232" placeholder="softBudget"><br>
    <input name="hardBudget" type="number" value="21232" placeholder="hardBudget"><br>
    <input type="submit" value="update budget!">
</form>
<br>
<br>
<br>
<br> detach user from task
<br>
<form method="post" action="{{ route('task.subscriber.detach', ['token' => $tenant])}}">
    <input name="userTenantTaskId" value="1" placeholder="userTenantTaskId">userTenantTaskId<br>
    <input type="submit" value="detach!">
</form>
<br>
<br>
<br> attach user to task
<br>
<form method="post" action="{{ route('task.subscriber.attach', ['token' => $tenant])}}">
    <input name="userTenantId" value="2" placeholder="userTenantId">userTenantId<br>
    <input name="taskId" value="2" placeholder="taskId">taskId<br>
    <input type="submit" value="attach!">
</form>
<br>
<br>
<br> create task
<br>
<form method="post" action="{{ route('task.create', ['token' => $tenant])}}">
    <input name="name" value="name" placeholder="name"><br>
    <input name="description" value="description" placeholder="description"><br>
    <input name="deadline" value="2017-12-10" placeholder="deadline"><br>
    <input name="plannedDeadline" value="2017-11-10" placeholder="planned_deadline"><br>
    <input name="boardId" type="number" value="1" placeholder="board_id">board_id<br>
    <input name="userTenantId" type="number" value="1" placeholder="userTenantId">userTenantId<br>
    <input name="priorityId" type="number" value="1" placeholder="priorityId">priorityId<br>
    <input name="budgetTypeId" type="number" value="1" placeholder="budgetTypeId"><br>
    <input name="softBudget" type="number" value="12" placeholder="softBudget"><br>
    <input name="hardBudget" type="number" value="22" placeholder="hardBudget"><br>
    <input type="submit" value="create!">
</form>
<br>
<br>
<br>
<br> detach user from group
<form method="post" action="{{ route('group.member.detach', ['token' => $tenant])}}">
    <input name="userTenantId" value="3" placeholder="userTenantId"><br>
    <input name="groupId" value="2" placeholder="groupId"><br>
    <input type="submit" value="detach!">
</form>
<br>
<br>
<br> attach user to group
<form method="post" action="{{ route('group.member.attach', ['token' => $tenant])}}">
    <input name="userTenantId" value="3" placeholder="userTenantId"><br>
    <input name="groupId" value="2" placeholder="groupId"><br>
    <input type="submit" value="attach!">
</form>
<br>reset send email
<form method="post"  enctype="multipart/form-data" action="{{ route('profile.update') . '?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6Ly9sb2NhbC5vY2FtLmNvbS9hcGkvdjEvbG9naW4iLCJpYXQiOjE1MTA5NDk4NDYsImV4cCI6MTUxMDk1MzQ0NiwibmJmIjoxNTEwOTQ5ODQ2LCJqdGkiOiJLeVlwenJLU2tVRnNXRDNkIn0.b6IFiz0hiezZMqOCSpUY6S9BJIx_owbo55K0m01Xgjc'}}">
    <input type="hidden" name="_method" value="put" />
    <input name="user_id" value="1" placeholder="user_id"><br>
    <input name="language_id" value="1" placeholder="language_id"><br>
    <input name="font_id" value="1" placeholder="font_id"><br>
    <input name="primary_color" value="black" placeholder="primary_color"><br>
    <input name="secondary_color" value="brown" placeholder="secondary_color"><br>
    <input name="image" type="file" id="qwer" value="" placeholder="image"><br>
    <input type="submit" value="send!">
</form>
<br>
<br>update role
<form method="post"  action="{{ route('role.update', ['token' => $tenant])}}">
    <input type="hidden" name="_method" value="put" />
    <input name="roleId" value="9" placeholder="roleId"><br>
    <input name="roleDescription" value="wqewqeqweqw weqwe qwewqe wqewqe" placeholder="roleDescription"><br>
    <input name="roleName" value="wqeq qwewqe -weq" placeholder="roleName"><br>
    <input name="permissionIds[]" value="15" placeholder="permissionIds"><br>
    <input name="permissionIds[]" value="16" placeholder="permissionIds"><br>
    <input name="permissionIds[]" value="17" placeholder="permissionIds"><br>
    <input type="submit" value="send!">
</form>
<br>
<br>
<br> delete board
<form method="post" action="{{ route('board.destroy', ['token' => $tenant])}}">
    <input type="hidden" name="_method" value="delete" />
    <input name="boardId" value="1" placeholder="boardId"><br>
    <input type="submit" value="delete!">
</form>
<br>
<br>
<br>update board
<form method="post"  action="{{ route('board.update', ['token' => $tenant])}}">
    <input type="hidden" name="_method" value="put" />
    <input name="name" value="name123" placeholder="name"><br>
    <input name="description" value="description123" placeholder="description"><br>
    <input name="deadline" value="2019-12-10" placeholder="deadline"><br>
    <input name="boardId" type="number" value="1" placeholder="boardId"><br>
    <input name="budgetId" type="number" value="1" placeholder="budgetId"><br>
    <input name="viewTypeId" type="number" value="1" placeholder="viewTypeId"><br>
    <input name="budgetTypeId" type="number" value="1" placeholder="budgetTypeId"><br>
    <input name="softBudget" type="number" value="12123" placeholder="softBudget"><br>
    <input name="hardBudget" type="number" value="22123" placeholder="hardBudget"><br>
    <input type="submit" value="send!">
</form>
<br>
<br>
<br> create board
<form method="post" action="{{ route('board.create', ['token' => $tenant])}}">
    <input name="name" value="name" placeholder="name"><br>
    <input name="description" value="description" placeholder="description"><br>
    <input name="deadline" value="2017-12-10" placeholder="deadline"><br>
    <input name="groupId" type="number" value="1" placeholder="groupId"><br>
    <input name="viewTypeId" type="number" value="1" placeholder="viewTypeId"><br>
    <input name="budgetTypeId" type="number" value="1" placeholder="budgetTypeId"><br>
    <input name="softBudget" type="number" value="12" placeholder="softBudget"><br>
    <input name="hardBudget" type="number" value="22" placeholder="hardBudget"><br>
    <input type="submit" value="create!">
</form>
<br>
<br>
<br> delete group
<form method="post" action="{{ route('group.destroy', ['token' => $tenant])}}">
    <input type="hidden" name="_method" value="delete" />
    <input name="groupId" value="1" placeholder="groupId"><br>
    <input type="submit" value="create!">
</form>
<br>
<br> update group
<form method="post" action="{{ route('group.update', ['token' => $tenant])}}">
    <input type="hidden" name="_method" value="put" />
    <input name="groupId" value="1" placeholder="groupId"><br>
    <input name="name" value="qweqwe" placeholder="name"><br>
    <input name="description" value="wqeq wqeqwe eqwqwe" placeholder="description"><br>
    <input type="submit" value="create!">
</form>
<br>
<br>
<br> create group
<form method="post" action="{{ route('group.create', ['token' => $member])}}">
    <input name="name" value="name" placeholder="name"><br>
    <input name="description" value="description" placeholder="description"><br>
    <input type="submit" value="create!">
</form>
<br>
<br>
<br> create timer
<form method="post" action="{{ route('timer.create', ['token' => $member])}}">
    <input name="name" value="name" placeholder="name"><br>
    <textarea name="comment" placeholder="comment"></textarea>
    <input name="taskId" value="" placeholder="id of task"><br>
    <input type="submit" value="create!">
</form>
<br>
<br> update timer
<form method="post" action="{{ route('timer.update', ['token' => $member])}}">
    <input type="hidden" name="_method" value="put" />
    <input name="timerId" value="1" placeholder="timerId"><br>
    <input name="name" value="name" placeholder="name"><br>
    <textarea name="comment" placeholder="comment">comment 1</textarea>
    <input name="taskId" value="" placeholder="id of task"><br>
    <input type="submit" value="update!">
</form>
<br>
<br> update timer task
<form method="post" action="{{ route('timer.updateTimerTask', ['token' => $member])}}">
    <input type="hidden" name="_method" value="put" />
    <input name="timerId" value="1" placeholder="timerId"><br>
    <input name="taskId" value="" placeholder="id of task"><br>
    <input type="submit" value="update timer task!">
</form>
<br>
<br> update timer comment
<form method="post" action="{{ route('timer.updateComment', ['token' => $member])}}">
    <input type="hidden" name="_method" value="put" />
    <input name="timerId" value="1" placeholder="timerId"><br>
    <textarea name="comment" placeholder="comment">comment 1</textarea>
    <input type="submit" value="update comment!">
</form>
<br>
<br> start timer
<form method="post" action="{{ route('timer.start', ['token' => $member])}}">
    <input name="timerId" value="1" placeholder="timerId"><br>
    <input type="submit" value="Start!">
</form>
<br>
<br>
<br> stop timer
<form method="post" action="{{ route('timer.stop', ['token' => $member])}}">
    <input name="timerId" value="1" placeholder="timerId"><br>
    <input type="submit" value="Stop!">
</form>
<br>
<br>
<br> pause timer
<form method="post" action="{{ route('timer.pause', ['token' => $member])}}">
    <input name="timerId" value="1" placeholder="timerId"><br>
    <input type="submit" value="Pause!">
</form>
<br>
<br>
<br>
<br> continue timer
<form method="post" action="{{ route('timer.continue', ['token' => $member])}}">
    <input name="timerId" value="1" placeholder="timerId"><br>
    <input type="submit" value="Continue!">
</form>
<br>
<br>
<br> delete timer
<form method="post" action="{{ route('timer.destroy', ['token' => $member])}}">
    <input type="hidden" name="_method" value="delete" />
    <input name="timerId" value="1" placeholder="timerId"><br>
    <input type="submit" value="Delete!">
</form>
<br>
<br>
<br>update password
<form method="post" action="{{ route('profile.changePassword', ['token' => $tenant])}}">
    <input name="password" value="QWE123qwe" placeholder="password"><br>
    <input name="password_confirm" value="QWE123qwe" placeholder="password_confirm"><br>
    <input name="old_password" value="qwerqweqwe" placeholder="old_password"><br>
    <input type="submit" value="change!">
</form>
<br>
<br>
{{--<br>attach custom role to member--}}
{{--<form method="post" action="{{ route('role.attach', ['token' => $tenant])}}">--}}
    {{--<input name="roleId" value="9" placeholder="role id"><br>--}}
    {{--<input name="memberId" value="2" placeholder="member id"><br>--}}
    {{--<input type="submit" value="attach!">--}}
{{--</form>--}}
<br>
<br>
<br>
{{--<br>detach custom role from member--}}
{{--<form method="post" action="{{ route('role.detach', ['token' => $tenant])}}">--}}
    {{--<input name="userTenantRoleId" value="4" placeholder="userTenantRoleId id"><br>--}}
    {{--<input type="submit" value="detach!">--}}
{{--</form>--}}
{{--<br>--}}
<br>
<br>change current tenant
<form method="post" action="{{ route('tenant.change', ['token' => $tenant]) }}">
    <input name="tenantId" value="2" placeholder="tenantId"><br>
    <input type="submit" value="change!">
</form>
<br>
<br>
<br>attach custom role to user_tenant_group
<form method="post" action="{{ route('group.role.attach', ['token' => $tenant])}}">
    <input name="roleId" value="8" placeholder="roleId">roleId<br>
    <input name="groupId" value="1" placeholder="groupId">groupId<br>
    <input name="userTenantId"  value="2" placeholder="userTenantId">userTenantId<br>
    <input type="submit" value="attach!">
</form>
<br>
<br>
<br>detach custom role from user_tenant_group
<form method="post" action="{{ route('group.role.detach', ['token' => $tenant])}}">
    <input name="roleId" value="8" placeholder="roleId">roleId<br>
    <input name="groupId" value="1" placeholder="groupId">groupId<br>
    <input name="userTenantId"  value="2" placeholder="userTenantId">userTenantId<br>
    <input type="submit" value="attach!">
</form>
<br>
<br>
<br>attach custom role to user_tenant_group
<form method="post" action="{{ route('group.role.attach', ['token' => $tenant])}}">
    <input name="roleId" value="8" placeholder="roleId">roleId<br>
    <input name="groupId" value="1" placeholder="groupId">groupId<br>
    <input name="userTenantId"  value="2" placeholder="userTenantId">userTenantId<br>
    <input type="submit" value="attach!">
</form>
<br>
<br>role
<form method="post" action="{{ route('role.create', ['token' => $tenant])}}">
    <input name="roleName" value="qwer qwW weqe" placeholder="role name"><br>
    <input name="roleDescription" value="weqwe qwewq qeqwe" placeholder="description"><br>
    <input name="permissionNames[]"  value="report-other-data" placeholder="0"><br>
    <input name="permissionNames[]"  value="billing-module" placeholder="1"><br>
    <input type="submit" value="send!">
</form>
<br>
{{--<br>attach permissions to role--}}
{{--<form method="post" action="{{ route('role.attach', ['token' => $tenant])}}">--}}
    {{--<input name="userId" value="2" placeholder="user id"><br>--}}
    {{--<input name="roleId" value="" placeholder="role id"><br>--}}
    {{--<input name="permissionNames[]" value="management-other-data" placeholder="0"><br>--}}
    {{--<input name="permissionNames[]"  value="report-own-data" placeholder="1"><br>--}}
    {{--<input type="submit" value="send!">--}}
{{--</form>--}}
<br>
<br>
<br>
<br>profile
<form method="post" action="{{ route('auth.restorePassword')}}">
    <input name="resetToken" value="q24JdpOjG3Kgm7k07WVe6T2MIeF5Vx0KVIaaPU9MNsGR4U4JpZiLiHjlqADtE3kY" placeholder="reset"><br>
    <input name="password" value="qwerqweqwe" placeholder="pass"><br>
    <input type="submit" value="send!">
</form>
<br>
<br>
<br>
<br>restore
<form method="post" action="{{ route('auth.restorePassword')}}">
    <input name="resetToken" value="q24JdpOjG3Kgm7k07WVe6T2MIeF5Vx0KVIaaPU9MNsGR4U4JpZiLiHjlqADtE3kY" placeholder="reset"><br>
    <input name="password" value="qwerqweqwe" placeholder="pass"><br>
    <input type="submit" value="send!">
</form>
<br>
<br>tenant
<form method="post" action="{{ route('auth.confirm') }}">
    <input name="userId" value="1" placeholder="userId"><br>
    <input name="project_name" value="" placeholder="project_name"><br>
    <input name="company_name" value="" placeholder="company_name"><br>
    <input type="submit" value="go!">
</form>
<br>
<br>invite
<form method="post" action="{{ route('auth.invite', ['token' => $tenant]) }}">
    <input name="tenantId" value="1" placeholder="tenantId"><br>
    <input name="name" value="asdasd" placeholder="name"><br>
    <input name="last_name" value="asdasd" placeholder="last_name"><br>
    <input name="email" value="asd@asd.asd" placeholder="email"><br>
    <input type="submit" value="invite!">
</form>
<br>
<br>join
<form method="post" action="{{ route('auth.join')}}">
    <input name="userId" value="2" placeholder="tenantId"><br>
    <input name="invite_hash" value="srcPT5huyZHvmOthdtpYsowGyck2CHtg" placeholder="invite_hash"><br>
    <input name="password" value="QWE123qwe" placeholder="password"><br>
    <input type="submit" value="join!">
</form>
<br>
<br>
<form method="get" action="/api/v1/logout">
    <input type="submit" value="out!">
</form>
<br>
<form method="get" action="{{ route('billing.year', ['token' => $tenant])}}">
    <input type="submit" value="get billing!">
</form>


