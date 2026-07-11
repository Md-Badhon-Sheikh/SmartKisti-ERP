<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'SmartKisti ERP') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <link href="{{ asset('admin/assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <link href="{{ asset('admin/assets/css/plugins.bundle.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('admin/assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('admin/assets/css/datatable-custom.css') }}" rel="stylesheet" type="text/css">
</head>
<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled aside-enabled aside-fixed">
<div class="d-flex flex-column flex-root">
    <div class="page d-flex flex-row flex-column-fluid">

        {{-- Aside/sidebar --}}
        <div id="kt_aside" class="aside aside-light aside-hoverable" data-kt-drawer="true" data-kt-drawer-name="aside"
             data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
             data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start"
             data-kt-drawer-toggle="#kt_aside_mobile_toggle">
            <div class="aside-logo flex-column-auto" id="kt_aside_logo">
                <a href="{{ route('dashboard') }}">
                    <span class="fs-3 fw-bolder text-gray-800">SmartKisti ERP</span>
                </a>
                <div id="kt_aside_toggle" class="btn btn-icon w-auto px-0 btn-active-color-primary aside-toggle"
                     data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
                     data-kt-toggle-name="aside-minimize">
                    <span class="svg-icon svg-icon-1 rotate-180">
                        <i class="fas fa-angles-left"></i>
                    </span>
                </div>
            </div>

            <div class="aside-menu flex-column-fluid">
                <div class="hover-scroll-overlay-y my-5 my-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true"
                     data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto"
                     data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer" data-kt-scroll-wrappers="#kt_aside_menu"
                     data-kt-scroll-offset="0">
                    <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500"
                         id="#kt_aside_menu" data-kt-menu="true">

                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <span class="menu-icon"><i class="fas fa-gauge"></i></span>
                                <span class="menu-title">{{ __('Dashboard') }}</span>
                            </a>
                        </div>

                        <div data-kt-menu-trigger="click"
                             class="menu-item menu-accordion {{ request()->routeIs('products.*', 'categories.*', 'sub-categories.*', 'brands.*', 'manufacturers.*') ? 'hover show active' : '' }}">
                            <span class="menu-link">
                                <span class="menu-icon"><i class="fas fa-couch"></i></span>
                                <span class="menu-title">{{ __('Product Management') }}</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <div class="menu-sub menu-sub-accordion menu-active-bg">
                                <div class="menu-item" style="margin-left: 20px;">
                                    <a class="menu-link {{ request()->routeIs('products.index') ? 'active' : '' }}" href="{{ route('products.index') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">{{ __('All Products') }}</span>
                                    </a>
                                </div>
                                @hasanyrole('super-admin|admin|manager')
                                <div class="menu-item" style="margin-left: 20px;">
                                    <a class="menu-link {{ request()->routeIs('products.create') ? 'active' : '' }}" href="{{ route('products.create') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">{{ __('New Product') }}</span>
                                    </a>
                                </div>
                                <div class="menu-item" style="margin-left: 20px;">
                                    <a class="menu-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">{{ __('Categories') }}</span>
                                    </a>
                                </div>
                                <div class="menu-item" style="margin-left: 20px;">
                                    <a class="menu-link {{ request()->routeIs('sub-categories.*') ? 'active' : '' }}" href="{{ route('sub-categories.index') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">{{ __('Sub Categories') }}</span>
                                    </a>
                                </div>
                                <div class="menu-item" style="margin-left: 20px;">
                                    <a class="menu-link {{ request()->routeIs('brands.*') ? 'active' : '' }}" href="{{ route('brands.index') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">{{ __('Brands') }}</span>
                                    </a>
                                </div>
                                <div class="menu-item" style="margin-left: 20px;">
                                    <a class="menu-link {{ request()->routeIs('manufacturers.*') ? 'active' : '' }}" href="{{ route('manufacturers.index') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">{{ __('Manufacturers') }}</span>
                                    </a>
                                </div>
                                @endhasanyrole
                            </div>
                        </div>

                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('customers.index') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                                <span class="menu-icon"><i class="fas fa-users"></i></span>
                                <span class="menu-title">{{ __('Customers') }}</span>
                            </a>
                        </div>

                        @hasanyrole('super-admin|admin')
                        <div data-kt-menu-trigger="click"
                             class="menu-item menu-accordion {{ request()->routeIs('users.*') ? 'hover show active' : '' }}">
                            <span class="menu-link">
                                <span class="menu-icon"><i class="fas fa-user-gear"></i></span>
                                <span class="menu-title">{{ __('User Management') }}</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <div class="menu-sub menu-sub-accordion menu-active-bg">
                                <div class="menu-item" style="margin-left: 20px;">
                                    <a class="menu-link {{ request()->routeIs('users.index') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">{{ __('All Users') }}</span>
                                    </a>
                                </div>
                                <div class="menu-item" style="margin-left: 20px;">
                                    <a class="menu-link {{ request()->routeIs('users.create') ? 'active' : '' }}" href="{{ route('users.create') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">{{ __('New User') }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endhasanyrole

                    </div>
                </div>
            </div>
        </div>

        {{-- Wrapper (topbar + content) --}}
        <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
            <div id="kt_header" class="header align-items-stretch">
                <div class="container-fluid d-flex align-items-stretch justify-content-between">
                    <div class="d-flex align-items-center d-lg-none ms-n3 me-1">
                        <div class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px" id="kt_aside_mobile_toggle">
                            <i class="fas fa-bars"></i>
                        </div>
                    </div>

                    <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
                        <a href="{{ route('dashboard') }}" class="d-lg-none fs-4 fw-bolder text-gray-800">SmartKisti ERP</a>
                    </div>

                    <div class="d-flex align-items-stretch flex-lg-grow-1">
                        <div class="d-flex align-items-stretch flex-shrink-0 ms-auto">
                            <div class="d-none d-lg-flex align-items-center ms-1 ms-lg-3">
                                <x-language-selector />
                            </div>
                            <div class="d-flex align-items-center ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
                                <div class="cursor-pointer symbol symbol-30px symbol-md-40px" data-kt-menu-trigger="click"
                                     data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                                    @if (auth()->user()->avatar)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="rounded-circle">
                                    @else
                                        <div class="symbol-label bg-light-primary text-primary fw-bold fs-5">
                                            {{ mb_substr(auth()->user()->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px"
                                     data-kt-menu="true">
                                    <div class="menu-item px-3">
                                        <div class="menu-content d-flex align-items-center px-3">
                                            <div class="d-flex flex-column">
                                                <div class="fw-bolder d-flex align-items-center fs-5">{{ auth()->user()->name }}</div>
                                                <span class="fw-bold text-muted fs-7">{{ auth()->user()->mobile }}</span>
                                            </div>
                                        </div>
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
                                        <a href="#" class="menu-link px-5" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('Sign Out') }}</a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-mobile-nav />

            <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                <div class="post d-flex flex-column-fluid" id="kt_post">
                    <div id="kt_content_container" class="container-xxl">
                        @if (session('status'))
                            <div class="alert alert-success">{{ session('status') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        {{ $slot }}
                    </div>
                </div>
            </div>

            <div class="footer py-4 d-flex flex-lg-column" id="kt_footer">
                <div class="container-fluid d-flex flex-column flex-md-row align-items-center justify-content-between">
                    <div class="text-dark order-2 order-md-1">
                        <span class="text-muted fw-bold me-1">{{ date('Y') }}©</span>
                        <span class="text-gray-800">SmartKisti ERP</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('admin/assets/js/plugins.bundle.js') }}"></script>
<script src="{{ asset('admin/assets/js/scripts.bundle.js') }}"></script>
<script src="{{ asset('admin/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function smartkistiDataTable(selector, options) {
        return $(selector).DataTable($.extend(true, {
            processing: true,
            serverSide: true,
            lengthMenu: [[10, 30, 50, -1], [10, 30, 50, 'All']],
            pageLength: 10,
            dom: "<'row'<'col-sm-4'l><'col-sm-4 d-flex justify-content-center'B><'col-sm-4'f>>"
                + "<'row'<'col-sm-12'tr>>"
                + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [{ extend: 'colvis', columns: ':not(:first-child)' }],
            language: {
                search: '<div class="input-group">'
                    + '<span class="input-group-text"><i class="fas fa-search"></i></span>'
                    + '_INPUT_'
                    + '</div>',
            },
            columnDefs: [{ targets: '_all', searchable: true, orderable: true }],
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.childRowImmediate,
                    type: '',
                },
            },
        }, options));
    }
</script>
@stack('scripts')
</body>
</html>
