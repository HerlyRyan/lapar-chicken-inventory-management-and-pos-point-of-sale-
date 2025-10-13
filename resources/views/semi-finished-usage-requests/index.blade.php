{{-- Proxy view to avoid duplication. Renders the existing material-usage-requests index page. --}}
{!! $__env->make('material-usage-requests.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render() !!}
