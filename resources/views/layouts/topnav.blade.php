<nav class="topnav">

    {{-- Hamburger --}}
    <button id="topnav-toggle" class="btn btn-sm p-2 border-0 rounded-2" style="color:var(--muted);background:transparent;" title="Toggle Sidebar">
        <i class="fa-solid fa-bars" style="font-size:1.1rem;"></i>
    </button>

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="d-none d-md-block flex-1">
        <ol class="breadcrumb mb-0" style="font-size:.82rem;">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}" style="color:var(--primary);text-decoration:none;">
                    <i class="fa-solid fa-house me-1"></i>
                    @if(auth()->user()->isSuperAdmin()) Platform
                    @elseif(auth()->user()->isStaff()) Staff Portal
                    @elseif(auth()->user()->isTeacher()) Faculty Portal
                    @else Home
                    @endif
                </a>
            </li>
            @yield('breadcrumb')
        </ol>
    </nav>

    {{-- Right side --}}
    <div class="d-flex align-items-center gap-2 ms-auto">

        {{-- Dark mode --}}
        <button id="theme-toggle" class="btn btn-sm p-2 border-0 rounded-2" style="color:var(--muted);background:var(--bg);" title="Toggle Dark Mode">
            <i class="fa-solid fa-moon" id="theme-icon" style="font-size:1rem;"></i>
        </button>

        {{-- Notifications --}}
        <div class="dropdown">
            <button class="btn btn-sm p-2 border-0 rounded-2 position-relative" style="color:var(--muted);background:var(--bg);" data-bs-toggle="dropdown" title="Notifications">
                <i class="fa-solid fa-bell" style="font-size:1rem;"></i>
                <span class="position-absolute top-0 end-0 translate-middle badge rounded-pill" style="background:var(--primary);font-size:.55rem;padding:.2em .4em;">3</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg mt-1" style="min-width:300px;border-radius:12px;">
                <li class="px-3 py-2" style="border-bottom:1px solid var(--border);">
                    <span style="font-size:.875rem;font-weight:700;color:var(--text);">Notifications</span>
                </li>
                @php $notifItems = [
                    ['icon'=>'fa-user-plus','color'=>'#2563EB','bg'=>'rgba(37,99,235,.1)','title'=>'New student admitted','time'=>'2 min ago'],
                    ['icon'=>'fa-money-bill','color'=>'#059669','bg'=>'rgba(5,150,105,.1)','title'=>'Fee payment received','time'=>'1 hour ago'],
                    ['icon'=>'fa-triangle-exclamation','color'=>'#D97706','bg'=>'rgba(217,119,6,.1)','title'=>'Attendance below 75%','time'=>'3 hours ago'],
                ]; @endphp
                @foreach($notifItems as $n)
                <li>
                    <a class="dropdown-item py-2 px-3" href="#" style="color:var(--text);">
                        <div class="d-flex gap-2 align-items-start">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:32px;height:32px;background:{{ $n['bg'] }};">
                                <i class="fa-solid {{ $n['icon'] }}" style="color:{{ $n['color'] }};font-size:.75rem;"></i>
                            </div>
                            <div>
                                <div style="font-size:.82rem;font-weight:500;color:var(--text);">{{ $n['title'] }}</div>
                                <div style="font-size:.72rem;color:var(--muted);">{{ $n['time'] }}</div>
                            </div>
                        </div>
                    </a>
                </li>
                @endforeach
                <li style="border-top:1px solid var(--border);">
                    <a class="dropdown-item text-center py-2" style="font-size:.8rem;color:var(--primary);" href="{{ route('admin.messages.index') }}">
                        View all notifications
                    </a>
                </li>
            </ul>
        </div>

        {{-- User Profile --}}
        <div class="dropdown">
            <button class="btn btn-sm p-1 border-0 d-flex align-items-center gap-2" style="background:transparent;" data-bs-toggle="dropdown">
                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="rounded-circle" style="width:34px;height:34px;object-fit:cover;border:2px solid var(--border);">
                <div class="d-none d-md-block text-start">
                    <div style="font-size:.82rem;font-weight:600;color:var(--text);line-height:1.2;">{{ auth()->user()->name }}</div>
                    <div style="font-size:.68rem;color:var(--muted);">{{ auth()->user()->getRoleDisplayName() }}</div>
                </div>
                <i class="fa-solid fa-chevron-down d-none d-md-block" style="font-size:.6rem;color:var(--muted);"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg mt-1" style="min-width:220px;border-radius:12px;">
                <li class="px-3 py-2" style="border-bottom:1px solid var(--border);">
                    <div style="font-size:.875rem;font-weight:600;color:var(--text);">{{ auth()->user()->name }}</div>
                    <div style="font-size:.75rem;color:var(--muted);">{{ auth()->user()->email }}</div>
                </li>
                <li><a class="dropdown-item py-2 px-3" href="{{ route('admin.profile.index') }}" style="color:var(--text);"><i class="fa-solid fa-user me-2" style="color:var(--primary);width:16px;"></i>My Profile</a></li>
                <li><a class="dropdown-item py-2 px-3" href="{{ route('admin.settings.index') }}" style="color:var(--text);"><i class="fa-solid fa-gear me-2" style="color:var(--muted);width:16px;"></i>Settings</a></li>
                <li><hr class="dropdown-divider" style="border-color:var(--border);"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item py-2 px-3" style="color:#DC2626;">
                            <i class="fa-solid fa-right-from-bracket me-2" style="width:16px;"></i>Sign Out
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
