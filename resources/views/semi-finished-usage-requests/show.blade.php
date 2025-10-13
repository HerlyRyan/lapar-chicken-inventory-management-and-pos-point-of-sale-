{{-- Proxy view to avoid duplication. Renders the existing material-usage-requests show page. --}}
{!! $__env->make('material-usage-requests.show', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render() !!}
