@extends('layouts.app')

@push('content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
        <div class="col-xxl-8 mb-6 order-0">
            <div class="card">
            <div class="d-flex align-items-start row">
                <div class="col-sm-7">
                <div class="card-body">
                    <h5 class="card-title text-primary mb-3">Welcome back Admin! 🎉</h5>
                    {{-- <p class="mb-6">
                    You have done 72% more sales today.<br />Check your new badge in your profile.
                    </p> --}}
                    <a href="" class="btn btn-sm btn-outline-primary">View Profile</a>
                </div>
                </div>
                <div class="col-sm-5 text-center text-sm-left">
                <div class="card-body pb-0 px-0 px-md-6">
                    <img
                    src="{{asset('/')}}assets/img/illustrations/man-with-laptop.png"
                    height="175"
                    class="scaleX-n1-rtl"
                    alt="View Badge User" />
                </div>
                </div>
            </div>
            </div>
        </div>
        {{-- <div class="col-lg-12 col-md-12 order-1">
            <div class="row">
            @if($pageCount > 0)
            <div class="col-lg-3 col-md-3 col-3 mb-6">
                <div class="card h-100">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between mb-4">
                        <i class="bx bx-book-content rounded bg-success" style="font-size: 30px"></i>
                    </div>
                    <div class="dropdown">
                        <button
                        class="btn p-0"
                        type="button"
                        id="cardOpt3"
                        data-bs-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false">
                        <i class="bx bx-dots-vertical-rounded text-muted"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                        <a class="dropdown-item" href="">View More</a>
                        </div>
                    </div>
                    </div>
                    <p class="mb-1">Pages</p>
                    <h4 class="card-title mb-3">{{$pageCount}}</h4>
                </div>
                </div>
            </div>
            @endif
            @if($sliderCount > 0)
            <div class="col-lg-3 col-md-3 col-3 mb-6">
                <div class="card h-100">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between mb-4">
                    <div class="avatar flex-shrink-0">
                        <i class="bx bx-book-content rounded bg-info" style="font-size: 30px"></i>
                    </div>
                    <div class="dropdown">
                        <button
                        class="btn p-0"
                        type="button"
                        id="cardOpt3"
                        data-bs-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false">
                        <i class="bx bx-dots-vertical-rounded text-muted"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                        <a class="dropdown-item" href="{{route('admin.sliders.index')}}">View More</a>
                        </div>
                    </div>
                    </div>
                    <p class="mb-1">Sliders</p>
                    <h4 class="card-title mb-3">{{$sliderCount}}</h4>
                </div>
                </div>
            </div>
            </div>
        </div> --}}
        </div>
    </div>

@endpush
