<nav
            class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
            id="layout-navbar">
            <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
              <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
                <i class="bx bx-menu bx-md"></i>
              </a>
            </div>

            <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
              <!-- Search -->
              <div class="navbar-nav align-items-center">
                <div class="nav-item d-flex align-items-center">
                  <i class="bx bx-search bx-md"></i>
                  <input
                    type="text"
                    class="form-control border-0 shadow-none ps-1 ps-sm-2"
                    placeholder="Search..."
                    aria-label="Search..." />
                </div>
              </div>
              <!-- /Search -->

              <ul class="navbar-nav flex-row align-items-center ms-auto">
                <!-- Place this tag where you want the button to render. -->
                <li class="nav-item lh-1 me-4">
                  <a
                    class="btn btn-outline-primary"
                    href="{{route('home')}}"
                    target="_blank"
                    ><i class="bx bx-link-alt"></i> Browse Website</a
                  >
                </li>

                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                  <a
                    class="nav-link dropdown-toggle hide-arrow p-0"
                    href="javascript:void(0);"
                    data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <img src="" alt="" class="rounded-full object-cover">
                      {{-- <img src="{{asset('/')}}assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" /> --}}
                    </div>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a class="dropdown-item " href="" >
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar avatar-online">
                                <img src="" alt="" class="rounded-full object-cover">
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <h6 class="mb-0">John Doe</h6>
                            <small class="text-muted">Admin</small>
                          </div>
                        </div>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider my-1"></div>
                    </li>
                    {{-- <li>
                      <a class="dropdown-item {{request()->routeIs('profile.show') ? 'active' : ''}}" href="{{route('profile.show')}}" >
                        <i class="bx bx-user bx-md me-3"></i><span>My Profile</span>
                      </a>
                    </li> --}}
                    <li>
                      <a class="dropdown-item" href="#"> <i class="bx bx-cog bx-md me-3"></i><span>Settings</span> </a>
                    </li>
                    {{-- <li>
                      <a class="dropdown-item" href="#">
                        <span class="d-flex align-items-center align-middle">
                          <i class="flex-shrink-0 bx bx-credit-card bx-md me-3"></i
                          ><span class="flex-grow-1 align-middle">Billing Plan</span>
                          <span class="flex-shrink-0 badge rounded-pill bg-danger">4</span>
                        </span>
                      </a>
                    </li> --}}
                    <li>
                      <div class="dropdown-divider my-1"></div>
                    </li>
                    <li>
                        <form method="POST" action="" x-data>
                            @csrf
                            {{-- <a href="#" class="dropdown-item" @click.prevent="document.querySelector('form').submit();">
                                <i class="bx bx-power-off bx-md me-3"></i>
                                <span>{{ __('Log Out') }}</span>
                            </a> --}}
                            <button class="dropdown-item"> <i class="bx bx-power-off bx-md me-3"></i> {{ __('Log Out') }}</button>
                        </form>
                    </li>

                  </ul>
                </li>
                <!--/ User -->
              </ul>
            </div>
          </nav>
