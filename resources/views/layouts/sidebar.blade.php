@php
    $logo =
        // isset($settings) && method_exists($settings, 'getFirstMediaUrl')
        //     ? $settings->getFirstMediaUrl('logo')
        //     :
            asset('frontend/images/logo/logo-01.svg');
@endphp
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="" class="app-brand-link">
            <img src="{{ $logo }}" alt="">
            {{-- <span class="app-brand-text demo menu-text fw-bold ms-2">sneat</span> --}}
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm d-flex align-items-center justify-content-center"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        {{-- Galleries  --}}
        {{-- <li class="menu-item {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
            <a href="{{ route('admin.pages.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-book-content"></i>
                <div class="text-truncate" data-i18n="Tables">Pages</div>
            </a>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.sliders.*') ? 'active' : '' }}">
            <a href="{{ route('admin.sliders.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-book-content"></i>
                <div class="text-truncate" data-i18n="Tables">Sliders</div>
            </a>
        </li> --}}
        <!-- Blogs -->
        {{-- <li
            class="menu-item {{ request()->routeIs('admin.blog.posts.*') || request()->routeIs('admin.blog.categories.*') || request()->routeIs('admin.blog.tags.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div class="text-truncate" data-i18n="Blogs">Blogs</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.blog.posts.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.blog.posts.index') }}" class="menu-link ">
                        <i class="menu-icon tf-icons bx bx-file me-2"></i>
                        <div class="text-truncate" data-i18n="Analytics">Posts</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.blog.categories.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.blog.categories.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-folder-open me-2"></i>
                        <div class="text-truncate" data-i18n="Analytics">Categories</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.blog.tags.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.blog.tags.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-purchase-tag-alt me-2"></i>
                        <div class="text-truncate" data-i18n="Analytics">Tags</div>
                    </a>
                </li>
            </ul>
        </li> --}}

        <li class="menu-item">
            <form method="POST" action="" x-data>
                @csrf
                {{-- <a href="#" class="menu-link" @click.prevent="$root.submit();">
            <i class="menu-icon tf-icons bx bx-power-off"></i>
            <div class="text-truncate" data-i18n="Boxicons">Logout</div>
        </a> --}}
                <button class="menu-link bg-white" style="border:none; outline: none; "> <i
                        class="bx bx-power-off bx-md me-3"></i> {{ __('Log Out') }}</button>
            </form>
        </li>

    </ul>
</aside>
