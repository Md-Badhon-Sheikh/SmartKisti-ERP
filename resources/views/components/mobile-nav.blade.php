<div class="mobile-navigation d-block d-lg-none">
    <div class="position-relative">
        <div class="d-flex align-items-center justify-content-around position-relative">
            <div class="m-navitem menu-toggle" id="mobile-nav-menu-toggle">
                <i class="fa-solid fa-bars"></i>
            </div>
            <div class="m-navitem home {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}"><i class="fa-solid fa-house"></i></a>
            </div>
            <div class="m-navitem products {{ request()->routeIs('products.index') ? 'active' : '' }}">
                <a href="{{ route('products.index') }}"><i class="fa-solid fa-couch"></i></a>
            </div>
            <div class="m-navitem profile-swip"><i class="fa-regular fa-user"></i></div>
        </div>

        <div class="profile-section" style="display: none;">
            <div class="menu-item px-3 d-flex align-items-center justify-content-between">
                <div class="menu-content d-flex align-items-center px-3">
                    <div class="symbol symbol-40px me-3">
                        @if (auth()->user()->avatar)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url(auth()->user()->avatar) }}" class="rounded-circle" alt="{{ auth()->user()->name }}">
                        @else
                            <div class="symbol-label bg-light-primary text-primary fw-bold fs-5">
                                {{ mb_substr(auth()->user()->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="d-flex flex-column">
                        <div class="fw-bolder fs-6">{{ auth()->user()->name }}</div>
                        <span class="fw-bold text-muted fs-8">{{ auth()->user()->mobile }}</span>
                    </div>
                </div>
                <x-language-selector />
            </div>
            <div class="separator my-2"></div>
            <div class="menu-item px-5">
                <a href="{{ route('profile.show') }}" class="menu-link px-5">{{ __('My Profile') }}</a>
            </div>
            <div class="menu-item px-5">
                <a href="{{ route('profile.edit') }}" class="menu-link px-5">{{ __('Edit Profile') }}</a>
            </div>
            <div class="separator my-2"></div>
            <div class="menu-item px-5">
                <a href="#" class="menu-link px-5" onclick="event.preventDefault(); document.getElementById('mobile-logout-form').submit();">{{ __('Sign Out') }}</a>
                <form id="mobile-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .mobile-navigation {
        padding: 12px 0;
        background-color: rgb(5, 41, 60);
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
        z-index: 9999;
    }
    .mobile-navigation .m-navitem {
        padding: 8px;
        cursor: pointer;
    }
    .mobile-navigation i {
        font-size: 18px;
        color: rgb(41, 123, 204);
    }
    .mobile-navigation .m-navitem.item-active i,
    .mobile-navigation .m-navitem.active i,
    .mobile-navigation .m-navitem.active a i {
        color: #fff !important;
    }
    .profile-section {
        padding: 10px 15px;
        background-color: #fff;
        position: absolute;
        bottom: 50px;
        left: 12px;
        right: 12px;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
        box-shadow: 0px 4px 15px 12px rgba(0, 0, 0, 0.08);
    }
    .profile-section .menu-item .menu-link {
        color: #3f4254;
        font-size: 14px;
    }
    @media (max-width: 991.98px) {
        #kt_content {
            padding-bottom: 70px;
        }
    }
</style>

@once
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.m-navitem').forEach(function (item) {
                item.addEventListener('click', function () {
                    var alreadyActive = item.classList.contains('item-active');
                    document.querySelectorAll('.m-navitem').forEach(function (sibling) {
                        sibling.classList.remove('item-active');
                    });
                    if (! alreadyActive) {
                        item.classList.add('item-active');
                    }
                });
            });

            var menuToggle = document.getElementById('mobile-nav-menu-toggle');
            if (menuToggle) {
                menuToggle.addEventListener('click', function () {
                    var asideToggle = document.getElementById('kt_aside_mobile_toggle');
                    if (asideToggle) {
                        asideToggle.click();
                    }
                });
            }

            document.querySelectorAll('.profile-swip').forEach(function (item) {
                item.addEventListener('click', function (e) {
                    e.stopPropagation();
                    document.querySelectorAll('.profile-section').forEach(function (panel) {
                        panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
                    });
                });
            });

            document.addEventListener('click', function (e) {
                if (! e.target.closest('.profile-section') && ! e.target.closest('.profile-swip')) {
                    document.querySelectorAll('.profile-section').forEach(function (panel) {
                        panel.style.display = 'none';
                    });
                    document.querySelectorAll('.mobile-navigation .m-navitem').forEach(function (item) {
                        item.classList.remove('item-active');
                    });
                }
            });
        });
    </script>
    @endpush
@endonce
