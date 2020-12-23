<li id="js-notifications" class="nav-item dropdown">
    <a id="navbarDropdown"
      class="nav-link dropdown-toggle"
      href="#"
      role="button"
      data-toggle="dropdown"
      aria-haspopup="true"
      aria-expanded="false"
      style="padding: 12px 10x 8px 8px;">
      <i class="fa fa-inbox fa-2x" aria-hidden="true"></i>
      <span class="label {{ $user->unreadNotifications->count() ?  '' : 'd-none' }}">{{ $user->unread_notifications_count }}</span>
    </a>
    <div class="dropdown-menu media-list" aria-labelledby="navbarDropdown" style="right: 0;left:auto;">
        <div class="dropdown-header">{{ Str::upper(trans('messages.Notifications')) }} ({{ $user->unread_notifications_count }})</div>
        @forelse($user->unreadNotifications->take(config('notifications.navbarLength')) as $notification)
            <div class="dropdown-item media" style="padding: 0.5rem;">
                <div class="media-left">
                    @if (isset($notification->data['imgPath']))
                        <img src="{{ $notification->data['imgPath'] }}" class="media-object" alt="{{ $notification->data['name'] }}">
                    @elseif(isset($notification->data['icon']))
                        <i class="fa fa-2x {{ $notification->data['icon']['fa'] }} {{ $notification->data['icon']['color'] }}"></i>
                    @else
                        <i class="fa fa-2x fa-info-circle text-info"></i>
                    @endif
                </div>
                <div class="media-body pr-3 text-ellipsis">
                    <a href="{{ $notification->data['link'] }}" class="media-heading h6 text-ellipsis">
                        {{ $notification->data['name'] }}: {{ $notification->data['title'] ?? '' }}</a>
                    @isset($notification->data['shortDetail'])
                        <div class="text-muted f-s-10 text-ellipsis">{{ $notification->data['shortDetail'] }}</div>
                    @endisset
                    <div class="text-muted f-s-10 text-ellipsis">{{ $notification->created_at->diffForHumans() }} {{ $notification->data['user'] ?? '' }}</div>
                </div>
                <form action="{{ route('Notifications.markRead', [$notification]) }}" method="post">
                    @csrf
                    <a href="javascript;" onclick="this.parentNode.submit(); return false;" class="d-flex align-items-center text-secondary" style="padding: 10px 0 !important;">
                        <i class="fa fa-check fa-lg" alt="mark as read"></i>
                    </a>
                </form>
            </div>
        @empty
            <div class="dropdown-item d-flex">
                <i class="fa fa-info" aria-hidden="true" style="width: 20px;"></i>
                <div>
                    {{ trans('messages.No unread Notifications') }}
                </div>
            </div>
            <div class="dropdown-divider"></div>
        @endforelse
        <div class="dropdown-footer text-center">
            <a href="{{ route('Notifications.index') }}">{{ trans('messages.View all') }}</a>
        </div>
    </div>
</li>
