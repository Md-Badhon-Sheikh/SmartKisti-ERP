@php
    $currentLocale = app()->getLocale();
@endphp
<div class="language-selector">
    <input type="checkbox" id="language-dropdown-toggle" class="language-dropdown-toggle">
    <label for="language-dropdown-toggle" class="language-dropdown-label">
        <span class="fi fi-{{ $currentLocale === 'en' ? 'us' : 'bd' }}"></span>&nbsp;
        <span class="selected-language">{{ $currentLocale === 'en' ? 'EN' : 'BN' }}</span>
        <span class="dropdown-arrow"></span>
    </label>
    <ul class="language-dropdown-menu" style="display: none">
        <li class="{{ $currentLocale === 'bn' ? 'active' : '' }}">
            <a href="#" data-locale="bn"><span class="fi fi-bd"></span> বাংলা</a>
        </li>
        <li class="{{ $currentLocale === 'en' ? 'active' : '' }}">
            <a href="#" data-locale="en"><span class="fi fi-us"></span> English</a>
        </li>
    </ul>

    <form id="locale-form" method="POST" action="{{ route('change.locale') }}">
        @csrf
        <input type="hidden" name="locale" id="locale-input" value="">
    </form>
</div>

<style>
    .language-selector {
        position: relative;
        padding: 5px 16px;
        background-color: #DBDFE9;
        border-radius: 4px;
    }
    .language-dropdown-toggle {
        display: none;
    }
    .language-dropdown-toggle:checked + .language-dropdown-label .dropdown-arrow {
        transform: rotate(180deg);
    }
    .language-dropdown-label {
        cursor: pointer;
        display: flex;
        align-items: center;
    }
    .selected-language {
        margin-right: 5px;
    }
    .dropdown-arrow {
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 5px 5px 0 5px;
        border-color: #666 transparent transparent transparent;
        margin-left: 5px;
    }
    .language-dropdown-menu {
        position: absolute;
        top: 34px;
        right: 0;
        width: 100px;
        background-color: #fff;
        border: 1px solid rgba(82, 63, 105, 0.15);
        box-shadow: 0 0 50px 0 rgba(82, 63, 105, 0.15);
        padding: 5px 3px;
        list-style: none;
        z-index: 1000;
    }
    .language-dropdown-menu li a {
        display: block;
        padding: 5px;
        color: #3f4254;
        text-decoration: none;
    }
    .language-dropdown-menu li a:hover {
        color: #009ef7;
        background-color: rgba(232, 239, 243, 0.8);
    }
    .language-dropdown-menu li.active a {
        color: #009ef7;
        background-color: rgba(232, 239, 243, 0.8);
    }
</style>

@once
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.language-dropdown-label').forEach(function (label) {
                label.addEventListener('click', function (e) {
                    e.stopPropagation();
                    var menu = label.parentElement.querySelector('.language-dropdown-menu');
                    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
                });
            });

            document.addEventListener('click', function () {
                document.querySelectorAll('.language-dropdown-menu').forEach(function (menu) {
                    menu.style.display = 'none';
                });
            });

            document.querySelectorAll('.language-selector [data-locale]').forEach(function (link) {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    var form = link.closest('.language-selector').querySelector('#locale-form');
                    form.querySelector('#locale-input').value = link.dataset.locale;
                    form.submit();
                });
            });
        });
    </script>
    @endpush
@endonce
