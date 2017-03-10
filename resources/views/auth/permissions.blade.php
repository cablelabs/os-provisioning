<?php
    $meta_id = Request::segment(3);
    $model = new App\Authmetacore();
    $role_assigned_permissions = $model->get_rights_by_metaid($meta_id);

    if (count($role_assigned_permissions) == 0) {
    	$roles = '';
    }
?>

@DivOpen(12)
    <div id="failure" class="alert alert-danger" hidden></div>

    @if (isset($role_assigned_permissions) && count($role_assigned_permissions) > 0)
        {{ Form::open(array('route' => array('Permission.update', null), 'method' => 'POST')) }}
        <table class="table table-hover itable">
            <thead>
                <th>Name</th>
                <th>Type</th>
                <th>View</th>
                <th>Create</th>
                <th>Edit</th>
                <th>Delete</th>
            </thead>

            @foreach ($role_assigned_permissions as $row)
                <tr>
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
        {{ Form::close() }}
    @else
        <div>
        </div>
    @endif
@DivClose()

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
</script>