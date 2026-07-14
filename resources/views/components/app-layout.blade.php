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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css">

    <link href="{{ asset('admin/assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
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

            <div class="aside-menu flex-column-fluid" style="font-size: 15px;">
                <div class="hover-scroll-overlay-y my-5 my-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true"
                     data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto"
                     data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer" data-kt-scroll-wrappers="#kt_aside_menu"
                     data-kt-scroll-offset="0">
                    <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500"
                         id="#kt_aside_menu" data-kt-menu="true">

                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <span class="menu-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                        <path
                                        d="M7.5 2.5H3.33333C2.8731 2.5 2.5 2.8731 2.5 3.33333V9.16667C2.5 9.6269 2.8731 10 3.33333 10H7.5C7.96024 10 8.33333 9.6269 8.33333 9.16667V3.33333C8.33333 2.8731 7.96024 2.5 7.5 2.5Z"
                                        stroke="#99A1AF" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
                                        <path
                                        d="M16.6667 2.5H12.5C12.0398 2.5 11.6667 2.8731 11.6667 3.33333V5.83333C11.6667 6.29357 12.0398 6.66667 12.5 6.66667H16.6667C17.1269 6.66667 17.5 6.29357 17.5 5.83333V3.33333C17.5 2.8731 17.1269 2.5 16.6667 2.5Z"
                                        stroke="#99A1AF" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
                                        <path
                                        d="M16.6667 10H12.5C12.0398 10 11.6667 10.3731 11.6667 10.8333V16.6667C11.6667 17.1269 12.0398 17.5 12.5 17.5H16.6667C17.1269 17.5 17.5 17.1269 17.5 16.6667V10.8333C17.5 10.3731 17.1269 10 16.6667 10Z"
                                        stroke="#99A1AF" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
                                        <path
                                        d="M7.5 13.3334H3.33333C2.8731 13.3334 2.5 13.7065 2.5 14.1667V16.6667C2.5 17.1269 2.8731 17.5 3.33333 17.5H7.5C7.96024 17.5 8.33333 17.1269 8.33333 16.6667V14.1667C8.33333 13.7065 7.96024 13.3334 7.5 13.3334Z"
                                        stroke="#99A1AF" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>

                                </i></span>
                                <span class="menu-title">{{ __('Dashboard') }}</span>
                            </a>
                        </div>

                        <div data-kt-menu-trigger="click"
                             class="menu-item menu-accordion {{ request()->routeIs('products.*', 'categories.*', 'sub-categories.*', 'brands.*') ? 'hover show active' : '' }}">
                            <span class="menu-link">
                                <span class="menu-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 8.2 12 3 3 8.2v7.6L12 21l9-5.2V8.2z"/>
                                        <path d="M3.3 8.4 12 13.3l8.7-4.9"/>
                                        <path d="M12 13.3V21"/>
                                        <path d="M7.5 5.6l9 5v3.2"/>
                                    </svg>
                                </span>
                                <span class="menu-title">{{ __('Product Management') }}</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <div class="menu-sub menu-sub-accordion menu-active-bg">
                                <div class="menu-item" style="margin-left: 20px;">
                                    <a class="menu-link {{ request()->routeIs('products.index') ? 'active' : '' }}" href="{{ route('products.index') }}">
                                        <span class="menu-bullet">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="12" width="8" height="8" rx="1"/>
                                            <rect x="13" y="12" width="8" height="8" rx="1"/>
                                            <rect x="8" y="4" width="8" height="8" rx="1" fill="none"/>
                                            <path d="M12 4v3M7 12v3M17 12v3"/>
                                            </svg>
                                        </span>
                                        <span class="menu-title">{{ __('All Products') }}</span>
                                    </a>
                                </div>
                                @hasanyrole('super-admin|admin|manager')
                                <div class="menu-item" style="margin-left: 20px;">
                                    <a class="menu-link {{ request()->routeIs('products.create') ? 'active' : '' }}" href="{{ route('products.create') }}">
                                        <span class="menu-bullet">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="12" width="8" height="8" rx="1"/>
                                            <rect x="13" y="12" width="8" height="8" rx="1"/>
                                            <rect x="8" y="4" width="8" height="8" rx="1" fill="none"/>
                                            <path d="M17 7v6"/>
                                            <path d="M14 10h6"/>
                                            </svg>
                                        </span>
                                        <span class="menu-title">{{ __('New Product') }}</span>
                                    </a>
                                </div>
                                <div class="menu-item" style="margin-left: 20px;">
                                    <a class="menu-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                                        <span class="menu-bullet">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6a2 2 0 0 1 2-2h4.2a2 2 0 0 1 1.6.8l1 1.4a2 2 0 0 0 1.6.8H19a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6z"/>
                                            </svg>
                                        </span>
                                        <span class="menu-title">{{ __('Categories') }}</span>
                                    </a>
                                </div>
                                <div class="menu-item" style="margin-left: 20px;">
                                    <a class="menu-link {{ request()->routeIs('sub-categories.*') ? 'active' : '' }}" href="{{ route('sub-categories.index') }}">
                                        <span class="menu-bullet">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                                            <path d="M6.5 10v3h6.5"/>
                                            <path d="M6.5 13v6.5H13"/>
                                            <rect x="15" y="9.5" width="6.5" height="6" rx="1.5"/>
                                            <rect x="15" y="16" width="6.5" height="6" rx="1.5"/>
                                            </svg>
                                        </span>
                                        <span class="menu-title">{{ __('Sub Categories') }}</span>
                                    </a>
                                </div>
                                <div class="menu-item" style="margin-left: 20px;">
                                    <a class="menu-link {{ request()->routeIs('brands.*') ? 'active' : '' }}" href="{{ route('brands.index') }}">
                                        <span class="menu-bullet">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12.9 3.6 20.4 11a2 2 0 0 1 0 2.9l-6.5 6.5a2 2 0 0 1-2.9 0L3.6 12.9A2 2 0 0 1 3 11.5V5a2 2 0 0 1 2-2h6.5a2 2 0 0 1 1.4.6z"/>
                                            <circle cx="8" cy="8" r="1.4"/>
                                            </svg>
                                        </span>
                                        <span class="menu-title">{{ __('Brands') }}</span>
                                    </a>
                                </div>
                                @endhasanyrole
                            </div>
                        </div>

                        <div data-kt-menu-trigger="click"
                             class="menu-item menu-accordion {{ request()->routeIs('customers.*', 'areas.*') ? 'hover show active' : '' }}">
                            <span class="menu-link">
                                <span class="menu-icon">
                                   <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="8" r="4"/>
                                    <path d="M4.5 20.5a7.5 7.5 0 0 1 15 0"/>
                                    </svg>
                                </span>
                                <span class="menu-title">{{ __('Customer Management') }}</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <div class="menu-sub menu-sub-accordion menu-active-bg">
                                <div class="menu-item" style="margin-left: 20px;">
                                    <a class="menu-link {{ request()->routeIs('customers.index') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                                        <span class="menu-bullet">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="12" width="8" height="8" rx="1"/>
                                            <rect x="13" y="12" width="8" height="8" rx="1"/>
                                            <rect x="8" y="4" width="8" height="8" rx="1" fill="none"/>
                                            <path d="M12 4v3M7 12v3M17 12v3"/>
                                            </svg>
                                        </span>
                                        <span class="menu-title">{{ __('All Customers') }}</span>
                                    </a>
                                </div>
                                @hasanyrole('super-admin|admin|manager')
                                <div class="menu-item" style="margin-left: 20px;">
                                    <a class="menu-link {{ request()->routeIs('customers.create') ? 'active' : '' }}" href="{{ route('customers.create') }}">
                                        <span class="menu-bullet">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="12" width="8" height="8" rx="1"/>
                                            <rect x="13" y="12" width="8" height="8" rx="1"/>
                                            <rect x="8" y="4" width="8" height="8" rx="1" fill="none"/>
                                            <path d="M17 7v6"/>
                                            <path d="M14 10h6"/>
                                            </svg>
                                        </span>
                                        <span class="menu-title">{{ __('New Customer') }}</span>
                                    </a>
                                </div>
                                <div class="menu-item" style="margin-left: 20px;">
                                    <a class="menu-link {{ request()->routeIs('areas.*') ? 'active' : '' }}" href="{{ route('areas.index') }}">
                                        <span class="menu-bullet">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6a2 2 0 0 1 2-2h4.2a2 2 0 0 1 1.6.8l1 1.4a2 2 0 0 0 1.6.8H19a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6z"/>
                                            </svg>
                                        </span>
                                        <span class="menu-title">{{ __('Areas') }}</span>
                                    </a>
                                </div>
                                @endhasanyrole
                            </div>
                        </div>

                        <div data-kt-menu-trigger="click"
                             class="menu-item menu-accordion {{ request()->routeIs('sales.*', 'installments.*') ? 'hover show active' : '' }}">
                            <span class="menu-link">
                                <span class="menu-icon">
                                   <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 3h2l2.4 12.2a2 2 0 0 0 2 1.6h7.6a2 2 0 0 0 2-1.6L21 7H6"/>
                                    <circle cx="9" cy="21" r="1"/>
                                    <circle cx="18" cy="21" r="1"/>
                                    </svg>
                                </span>
                                <span class="menu-title">{{ __('Sales Management') }}</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <div class="menu-sub menu-sub-accordion menu-active-bg">
                                <div class="menu-item" style="margin-left: 20px;">
                                    <a class="menu-link {{ request()->routeIs('sales.index') ? 'active' : '' }}" href="{{ route('sales.index') }}">
                                        <span class="menu-bullet">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="12" width="8" height="8" rx="1"/>
                                            <rect x="13" y="12" width="8" height="8" rx="1"/>
                                            <rect x="8" y="4" width="8" height="8" rx="1" fill="none"/>
                                            <path d="M12 4v3M7 12v3M17 12v3"/>
                                            </svg>
                                        </span>
                                        <span class="menu-title">{{ __('All Sales') }}</span>
                                    </a>
                                </div>
                                @hasanyrole('super-admin|admin|manager')
                                <div class="menu-item" style="margin-left: 20px;">
                                    <a class="menu-link {{ request()->routeIs('sales.create') ? 'active' : '' }}" href="{{ route('sales.create') }}">
                                        <span class="menu-bullet">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="12" width="8" height="8" rx="1"/>
                                            <rect x="13" y="12" width="8" height="8" rx="1"/>
                                            <rect x="8" y="4" width="8" height="8" rx="1" fill="none"/>
                                            <path d="M17 7v6"/>
                                            <path d="M14 10h6"/>
                                            </svg>
                                        </span>
                                        <span class="menu-title">{{ __('New Sale') }}</span>
                                    </a>
                                </div>
                                @endhasanyrole
                                <div class="menu-item" style="margin-left: 20px;">
                                    <a class="menu-link {{ request()->routeIs('installments.*') ? 'active' : '' }}" href="{{ route('installments.index') }}">
                                        <span class="menu-bullet">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                                            <path d="M3 9h18"/>
                                            <path d="M8 2v4M16 2v4"/>
                                            </svg>
                                        </span>
                                        <span class="menu-title">{{ __('Installment Plans') }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div data-kt-menu-trigger="click"
                             class="menu-item menu-accordion {{ request()->routeIs('custom-orders.*', 'deliveries.*') ? 'hover show active' : '' }}">
                            <span class="menu-link">
                                <span class="menu-icon">
                                   <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h10l6 6v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1z"/>
                                    <path d="M14 4v6h6"/>
                                    <path d="M8 14h8M8 17h5"/>
                                    </svg>
                                </span>
                                <span class="menu-title">{{ __('Custom Furniture Orders') }}</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <div class="menu-sub menu-sub-accordion menu-active-bg">
                                <div class="menu-item" style="margin-left: 20px;">
                                    <a class="menu-link {{ request()->routeIs('custom-orders.index') ? 'active' : '' }}" href="{{ route('custom-orders.index') }}">
                                        <span class="menu-bullet">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="12" width="8" height="8" rx="1"/>
                                            <rect x="13" y="12" width="8" height="8" rx="1"/>
                                            <rect x="8" y="4" width="8" height="8" rx="1" fill="none"/>
                                            <path d="M12 4v3M7 12v3M17 12v3"/>
                                            </svg>
                                        </span>
                                        <span class="menu-title">{{ __('All Custom Orders') }}</span>
                                    </a>
                                </div>
                                @hasanyrole('super-admin|admin|manager')
                                <div class="menu-item" style="margin-left: 20px;">
                                    <a class="menu-link {{ request()->routeIs('custom-orders.create') ? 'active' : '' }}" href="{{ route('custom-orders.create') }}">
                                        <span class="menu-bullet">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="12" width="8" height="8" rx="1"/>
                                            <rect x="13" y="12" width="8" height="8" rx="1"/>
                                            <rect x="8" y="4" width="8" height="8" rx="1" fill="none"/>
                                            <path d="M17 7v6"/>
                                            <path d="M14 10h6"/>
                                            </svg>
                                        </span>
                                        <span class="menu-title">{{ __('New Custom Order') }}</span>
                                    </a>
                                </div>
                                @endhasanyrole
                                <div class="menu-item" style="margin-left: 20px;">
                                    <a class="menu-link {{ request()->routeIs('deliveries.*') ? 'active' : '' }}" href="{{ route('deliveries.index') }}">
                                        <span class="menu-bullet">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="1" y="7" width="15" height="10" rx="1"/>
                                            <path d="M16 10h4l3 3v4h-7z"/>
                                            <circle cx="6" cy="19" r="1.5"/>
                                            <circle cx="18.5" cy="19" r="1.5"/>
                                            </svg>
                                        </span>
                                        <span class="menu-title">{{ __('Deliveries') }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        @hasanyrole('super-admin|admin|manager')
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('sms-logs.*') ? 'active' : '' }}" href="{{ route('sms-logs.index') }}">
                                <span class="menu-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h16v12H7l-3 3V4z"/>
                                    <path d="M8 9h8M8 12h5"/>
                                    </svg>
                                </span>
                                <span class="menu-title">{{ __('SMS Logs') }}</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('receipts.*') ? 'active' : '' }}" href="{{ route('receipts.index') }}">
                                <span class="menu-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 2h9l3 3v17H6z"/>
                                    <path d="M9 8h6M9 12h6M9 16h4"/>
                                    </svg>
                                </span>
                                <span class="menu-title">{{ __('Receipts') }}</span>
                            </a>
                        </div>
                        @endhasanyrole

                        @hasanyrole('super-admin|admin')
                        <div data-kt-menu-trigger="click"
                             class="menu-item menu-accordion {{ request()->routeIs('users.*') ? 'hover show active' : '' }}">
                            <span class="menu-link">
                                <span class="menu-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="9" cy="8" r="3.5"/>
                                    <path d="M2.5 20a6.5 6.5 0 0 1 13 0"/>
                                    <circle cx="17.5" cy="9.5" r="2.5"/>
                                    <path d="M15.5 14.5a5.5 5.5 0 0 1 6 5.5"/>
                                    </svg>
                                </span>
                                <span class="menu-title">{{ __('User Management') }}</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <div class="menu-sub menu-sub-accordion menu-active-bg">
                                <div class="menu-item" style="margin-left: 20px;">
                                    <a class="menu-link {{ request()->routeIs('users.index') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                        <span class="menu-bullet">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="8" cy="8" r="3.5"/>
                                            <path d="M2 20a6 6 0 0 1 12 0"/>
                                            <path d="M17 7h5"/>
                                            <path d="M17 12h5"/>
                                            <path d="M17 17h5"/>
                                            </svg>
                                        </span>
                                        <span class="menu-title">{{ __('All Users') }}</span>
                                    </a>
                                </div>
                                <div class="menu-item" style="margin-left: 20px;">
                                    <a class="menu-link {{ request()->routeIs('users.create') ? 'active' : '' }}" href="{{ route('users.create') }}">
                                        <span class="menu-bullet">
                                           <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="9.5" cy="8" r="4"/>
                                            <path d="M2.5 20.5a7 7 0 0 1 14 0"/>
                                            <path d="M19 7v6"/>
                                            <path d="M16 10h6"/>
                                            </svg>
                                        </span>
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
<script src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.colVis.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"></script>
<script>
    toastr.options = {
        closeButton: false,
        newestOnTop: false,
        progressBar: true,
        positionClass: 'toast-top-right',
        showDuration: '300',
        hideDuration: '1000',
        timeOut: '5000',
    };
</script>
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
