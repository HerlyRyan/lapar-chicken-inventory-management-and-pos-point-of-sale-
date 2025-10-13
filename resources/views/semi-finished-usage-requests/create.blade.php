{{-- Proxy view to avoid duplication. Renders the existing material-usage-requests create page. --}}
{!! view('material-usage-requests.create', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render() !!}
