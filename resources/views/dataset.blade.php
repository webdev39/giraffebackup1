window.Laravel = {!! json_encode($data) !!};
window.Laravel.csrf_token = "{{ csrf_token() }}";