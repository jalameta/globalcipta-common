<ul class="{{ $menu->cssClasses() }}">
  @foreach ($menu->all() as $item)
    @if ($item->renderable())
      <li class="{{ $item->cssClasses() }} {{ $item->active() ? $item->active_class : '' }}">
        <a href="{{ $item->uri }}">
          @if ($item->hasBadge())
            <span class="nav-label">
            <b class="label label-sm primary">{!! $item->getBadgeContent() !!}</b>
          </span>
          @endif
          <span class="nav-icon">
          <i class="material-icons">{!! $item->icon !!}</i>
        </span>
          <span class="nav-text">{{ $item->text }}</span>
        </a>
      </li>
    @endif
  @endforeach
</ul>
