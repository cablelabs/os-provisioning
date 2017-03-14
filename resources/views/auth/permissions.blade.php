<?php
    // get all assigned permissions to role
    $role_id = Request::segment(3);
    $model = new App\Authmetacore();
    $assigned_permissions = $model->get_permissions_by_metaid($role_id);

    // find not assigned permissions to role
    $not_assigned_permissions = $model->get_not_assigned_permissions($role_id);
?>

@if (count($assigned_permissions) == 0 || (isset($not_assigned_permissions) && count($not_assigned_permissions) > 0))
    @DivOpen(12)
        <a href="#modal-dialog" class="btn btn-primary btn-sm" data-toggle="modal">Assign permissions</a>
    @DivClose()
@endif

{{ Form::hr() }}

{{ Form::open(array('route' => array('Permission.delete', null), 'method' => 'POST')) }}
@DivOpen(12)

    <div id="failure" class="alert alert-danger" hidden></div>

    @if (isset($assigned_permissions) && count($assigned_permissions) > 0)

        <table class="table table-hover itable">
            <thead>
                <th></th>
                <th>Name</th>
                <th>Type</th>
                <th>View</th>
                <th>Create</th>
                <th>Edit</th>
                <th>Delete</th>
            </thead>

            @foreach ($assigned_permissions as $row)
                <tr>
                    <td width=50> {{ Form::checkbox('delete_ids['.$row->id.']', 1, null, null, ['style' => 'simple']) }} </td>
                    <td>{{ $row->name }}&nbsp;&nbsp;</td>
                    <td>{{ $row->type }}</td>
                    <td align="center">
                        <?php $checked = ($row->view) ? 'checked' : ''; ?>
                        {{ Form::checkbox(
                            'right[]',
                            $row->id . '_view_' . $row->view,
                            null,
                            $checked,
                            ['style' => 'simple', 'onchange' => 'updateRolePermission($(this))']) }}
                    </td>
                    <td align="center">
						<?php $checked = ($row->create) ? 'checked' : ''; ?>
                        {{ Form::checkbox(
                            'right[]',
                            $row->id . '_create_' . $row->create,
                            null,
                            $checked,
                            ['style' => 'simple', 'onchange' => 'updateRolePermission($(this))']) }}
                    </td>
                    <td align="center">
						<?php $checked = ($row->edit) ? 'checked' : ''; ?>
                        {{ Form::checkbox(
                            'right[]',
                            $row->id . '_edit_' . $row->edit,
                            null,
                            $checked,
                            ['style' => 'simple', 'onchange' => 'updateRolePermission($(this))']) }}
                    </td>
                    <td align="center">
						<?php $checked = ($row->delete) ? 'checked' : ''; ?>
                        {{ Form::checkbox(
                            'right[]',
                            $row->id . '_delete_' . $row->delete,
                            null,
                             $checked,
                            ['style' => 'simple', 'onchange' => 'updateRolePermission($(this))']) }}
                    </td>
                </tr>
            @endforeach
        </table>
    @endif
@DivClose()

@DivOpen(12)
    <input type="hidden" name="role_id" id="role_id" value="{{ $role_id }}">
    {{ Form::submit( \App\Http\Controllers\BaseViewController::translate_view('Delete selected permissions', 'Button' ) , ['style' => 'simple', 'class' => 'btn btn-danger m-r-5']) }}
@DivClose()
{{ Form::close() }}

@if (count($assigned_permissions) == 0 || (isset($not_assigned_permissions) && count($not_assigned_permissions) > 0))
    @DivOpen(12)
        {{ Form::open(array('route' => array('Permission.assign', null), 'method' => 'POST')) }}
        <div id="modal-dialog" class="modal modal-message fade" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                        <h4>Select permissions</h4>

                        <br><br>

                        <input type="button" class="btn btn-sm btn-white" value="Select all" id="btn-toggle" onclick="toggleAll()" style="width: 85px">
                        <input type="submit" class="btn btn-sm btn-primary" value="Assign permissions">
                        <input type="hidden" name="role_id" id="role_id" value="{{ $role_id }}">

                        <br><br>
                    </div>

                    <div class="modal-body">
                        <table class="table table-hover itable">
                            <thead>
                            <th>Name</th>
                            <th>View</th>
                            <th>Create</th>
                            <th>Edit</th>
                            <th>Delete</th>
                            </thead>

                            @foreach ($not_assigned_permissions as $key => $permission)
                                <tr>
                                    <td>{{ $permission['name']; }}</td>
                                    <td>{{ Form::checkbox(
                                        'permission[' . $permission["id"] .'][]',
                                        'view',
                                        null,
                                        null,
                                        ['style' => 'simple']) }}
                                    </td>
                                    <td>{{ Form::checkbox(
                                        'permission[' . $permission["id"] .'][]',
                                        'create',
                                        null,
                                        null,
                                        ['style' => 'simple']) }}
                                    </td>
                                    <td>{{ Form::checkbox(
                                        'permission[' . $permission["id"] .'][]',
                                        'edit',
                                        null,
                                        null,
                                        ['style' => 'simple']) }}
                                    </td>
                                    <td>{{ Form::checkbox(
                                        'permission[' . $permission["id"] .'][]',
                                        'delete',
                                        null,
                                        null,
                                        ['style' => 'simple']) }}
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>

                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>
        {{ Form::close() }}
    @DivClose()
@endif

<script type="text/javascript">
    function updateRolePermission(permission)
    {
        var msg = '';
        var delay = 1000;
        var data = permission.val().split('_');
        var authmethacore_id =  data[0];
        var authmetacore_right = data[1];
        var authmetacore_right_value = data[2];
        var url = window.location.protocol + '//' + window.location.host + '/lara/admin/Authrole/UpdatePermission';
        var token = $('input[name="_token"]').val();

        $.ajaxSetup({
            url: url,
            type: 'POST',
            headers: {'X-CSRF-TOKEN': token},
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            success: function (data) {
                if (data == 1) {
                    msg = 'Permission "' + authmetacore_right + '" for ID ' + authmethacore_id + ' updated successfully.';
                } else {
                    msg = 'Error while updating user permission (ID: ' + authmethacore_id + ', permission: ' + authmetacore_right + '). ' + data;
                    $("#failure").text(msg).attr('hidden', false);
                    delay = 3000;
                }
                console.log(msg);

                // reload the page
                window.setTimeout(function() {
                    location.reload();
                }, delay);
            }
        });

        $.ajax({
            data: {
                authmethacore_id: authmethacore_id,
                authmethacore_right: authmetacore_right,
                authmethacore_right_value: authmetacore_right_value
            }
        });
    }

    window.load(function() {
        $('#modal-dialog').find(':checkbox').each(function() {
            $(this).removeAttr('checked');
        });
    });

    function toggleAll()
    {
        var btn_text = $('#btn-toggle').val();

        if (btn_text == 'Select all') {
            $('#modal-dialog').find(':checkbox').each(function() {
                $(this).attr('checked', 'checked');
            });
            $('#btn-toggle').val('Unselect all')
        } else if (btn_text == 'Unselect all') {
            $('#modal-dialog').find(':checkbox').each(function() {
                $(this).removeAttr('checked');
            });
            $('#btn-toggle').val('Select all')
        }
    }
</script>