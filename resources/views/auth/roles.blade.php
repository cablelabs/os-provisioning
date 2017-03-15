<?php
    $user_id = Request::segment(3);
    $role_model = new App\Authrole();
    $user_assigned_roles = $role_model->get_roles_by_userid($user_id);
    $roles = $role_model->html_list($role_model->get_not_assigned_roles_by_userid($user_id), 'name');
?>

@DivOpen(12)
    {{ Form::open(array('route' => ['AssignRole.add', $user_id], 'method' => 'POST')) }}
    {{ Form::select('role_ids[]', $roles, null, array('multiple' => true, 'style' => 'width: 150px')) }}
    {{ Form::hidden('user_id', $user_id) }}
    {{ Form::submit( \App\Http\Controllers\BaseViewController::translate_view('Assign roles', 'Button' ) , ['style' => 'simple', 'class' => 'btn btn-primary m-r-5']) }}
    {{ Form::close() }}

    <br><br><br>

    @if (isset($user_assigned_roles) && !is_null($user_assigned_roles))
        {{ Form::open(array('route' => array('AssignRole.delete', null), 'method' => 'POST')) }}

        <table class="table table-hover itable">
            @foreach ($user_assigned_roles as $role)
                <tr>
                    @if ($user_id == 1 && $role->name == 'super_admin')
                        <td width="50px"> {{ Form::checkbox('role_ids[]', $role->id, null, null, ['style' => 'simple', 'disabled' => 'disabled']) }} </td>
                    @else
                        <td width="50px"> {{ Form::checkbox('role_ids[]', $role->id, null, null, ['style' => 'simple']) }} </td>
                    @endif
                    <td>{{ $role->name }}</td>
                </tr>
            @endforeach
        </table>

        {{ Form::hidden('user_id', $user_id) }}
        {{ Form::submit( \App\Http\Controllers\BaseViewController::translate_view('Delete selected roles', 'Button' ) , ['style' => 'simple', 'class' => 'btn btn-danger m-r-5']) }}
        {{ Form::close() }}
    @endif
@DivClose()
