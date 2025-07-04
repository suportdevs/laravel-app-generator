<footer class="content-footer footer bg-footer-theme">
    <div class="container-xxl">
      <div
        class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
        {{-- <div class="text-body"> --}}
            @if(isset($settings->general['copy_right']))
                {!!$settings->general['copy_right']!!}
            @else
                ©
                <script>
                    document.write(new Date().getFullYear());
                </script>
                , made with ❤️ by
            @endif
          <a href="https://github.com/suportdevs" target="_blank" class="footer-link">Md. Mamunur Rashid</a>
        {{-- </div> --}}
      </div>
    </div>
  </footer>
