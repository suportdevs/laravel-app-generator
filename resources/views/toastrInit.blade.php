<script type="text/javascript">
    toastr.options = {
        "closeButton": true,
    };

    @if(Session::has('message'))
        var type = "{{ Session::get('alert-type', 'info') }}";
        switch(type){
            case 'info':
                toastr.info("{{ Session::get('message') }}");
                break;

            case 'warning':
                toastr.warning("{{ Session::get('message') }}");
                break;

            case 'success':
                toastr.success("{{ Session::get('message') }}");
                break;

            case 'error':
                toastr.error("{{ Session::get('message') }}");
                break;
            case 'danger':
                toastr.error("{{ Session::get('message') }}");
                break;
            default:
                toastr.info("{{ Session::get('message') }}");
        }
    @endif
    @if(Session::has('success'))
            toastr.success("{{ session('success') }}");
    @endif
    @if(Session::has('error'))
        toastr.error("{{ session('error') }}");
    @endif
    @if(Session::has('danger'))
        toastr.error("{{ session('danger') }}");
    @endif

</script>


@if ($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let toastMessageList = '<div>';
        @foreach ($errors->all() as $error)
            toastMessageList += '<li>{{ $error }}</li>';
        @endforeach
        toastMessageList += '</div>';
        toastr.warning(toastMessageList);
    });
</script>
@endif
