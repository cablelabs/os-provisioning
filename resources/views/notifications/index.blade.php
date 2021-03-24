@extends ('Layout.split84-nopanel')

@section('content')
<div id="app" class="row">
    <div class="card w-100 px-2">
        <h2 class="card-title d-flex" style="justify-content: space-between;padding:0 1rem;">
            <div class="d-flex align-items-baseline">
              <i class="fa fa-inbox"></i>
              <div>{{ trans('messages.notifications') }}</div>
            </div>
        </h2>
        <div class="d-flex flex-column-reverse flex-md-column mb-4">
            @if ($user->unread_notifications_count)
                <form action="{{ route('Notifications.markAllRead') }}" method="post">
                    @csrf
                    <div class="text-right" style="padding:1rem;">
                        <button type="submit" class="btn btn-primary"> {{ trans('messages.mark all as read') }}</button>
                    </div>
                </form>
                <table id="datatable" class="w-100 table table-hover" style="border-bottom: 1px solid rgba(0,0,0,.125);">
                    <thead class="text-dark" style="border-bottom: 3px solid rgba(0,0,0,.125);">
                        <tr>
                            <th data-priority="1"></th>
                            <th scope="col" style="padding:0.75rem 1.5rem;text-transform:uppercase;letter-spacing:0.05em;background-color: #F8FAFC;" data-priority="1">
                                {{ trans('messages.title') }}
                            </th>
                            <th scope="col" style="padding:0.75rem 1.5rem;text-transform:uppercase;letter-spacing:0.05em;background-color: #F8FAFC;">
                                {{ trans('messages.details') }}
                            </th>
                            <th scope="col" style="padding:0.75rem 1.5rem;text-transform:uppercase;letter-spacing:0.05em;background-color: #F8FAFC;" data-priority="10">
                                {{ trans('messages.State') }}
                            </th>
                            <th scope="col" style="padding:0.75rem 1.5rem;background-color: #F8FAFC;" data-priority="1">
                            </th>
                        </tr>
                    </thead>
            @else
                <table class="w-100">
            @endif
                <tbody>
                    @forelse($user->unreadNotifications as $notification)
                        <tr style="border-bottom: 1px solid rgba(0,0,0,.125);">
                            <td></td>
                            <td style="padding:1rem">
                                <div class="d-flex align-items-center">
                                    <div style="flex:0 0;">
                                        @if (isset($notification->data['imgPath']))
                                            <img src="{{ $notification->data['imgPath'] }}" style="height:2rem;width:2rem;" alt="{{ array_slice(explode('\\', $notification->type), -1)[0] }}">
                                        @elseif(isset($notification->data['icon']))
                                            <i class="fa fa-2x {{ $notification->data['icon']['fa'] }} {{ $notification->data['icon']['color'] }}"></i>
                                        @else
                                            <i class="fa fa-2x fa-info-circle text-info"></i>
                                        @endif
                                    </div>
                                    <div class="ml-3 text-ellipsis mw-48 mw-sm-80 mw-100-md">
                                        <a href="{{ $notification->data['link'] }}" class="font-weight-bold text-dark ">
                                            {{ trans('view.ticket.notification.'.array_slice(explode('\\', $notification->type), -1)[0]) }}: {{ $notification->data['title'] ?? '' }}
                                        </a>
                                        <div class="text-secondary">
                                            {{ $notification->created_at->diffForHumans() }} {{ $notification->data['user'] ?? '' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding:1rem">
                                <div class="text-dark">{{ $notification->data['shortDetail'] }}</div>
                            </td>
                            <td style="padding:1rem">
                                <div class="badge badge-danger">
                                    {{ trans('messages.unread') }}
                                </div>
                            </td>
                            <td style="padding:1rem">
                                <form class="text-center" action="{{ route('Notifications.markRead', [$notification]) }}" method="post">
                                    @csrf
                                    <a href="javascript;" onclick="this.parentNode.submit(); return false;" class="d-none d-md-block text-secondary">
                                        <i class="fa fa-check fa-lg" alt="{{ trans('messages.mark as read') }}"></i>
                                    </a>
                                    <a href="javascript;" onclick="this.parentNode.submit(); return false;" class="btn btn-secondary d-md-none">
                                        <i class="fa fa-check fa-lg" alt="{{ trans('messages.mark as read') }}"></i>
                                        {{ trans('messages.mark as read') }}
                                    </a>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="42">
                                <div class="d-flex h4" style="padding: 2rem;align-items:center;justify-content:center;">
                                    <i class="fa fa-info" aria-hidden="true" style="width: 20px;"></i>
                                    <div class="text-dark">
                                        {{ trans('messages.No unread Notifications') }}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section ('javascript_extra')
<script src="{{asset('components/assets-admin/plugins/vue/dist/vue.js')}}"></script>
<script>
    $(document).ready(function() {
        let table = $('#datatable').DataTable(
            {
            {{-- Translate Datatables Base --}}
                @include('datatables.lang')
            responsive: {
                details: {
                    type: 'column', {{-- auto resize the Table to fit the viewing device --}}
                }
            },
            autoWidth: true, {{-- Option to ajust Table to Width of container --}}
            dom: 'ltip', {{-- sets order and what to show  --}}
            lengthMenu:  [ [5, 10, 25, -1], [5, 10, 25, "{{ trans('view.jQuery_All') }}" ] ],
            {{-- Responsive Column --}}
            columnDefs: [],
            aoColumnDefs: [ {
                    className: 'control',
                    orderable: false,
                    searchable: false,
                    targets:   [0]
                },
                {
                    orderable: false,
                    searchable: false,
                    targets:   [4]
                },
                {{-- Dont print error message, but fill NULL Fields with empty string --}}
                {
                    defaultContent: "",
                    targets: "_all"
                },
             ],
    })
})
</script>
<script>

</script>
@endsection
