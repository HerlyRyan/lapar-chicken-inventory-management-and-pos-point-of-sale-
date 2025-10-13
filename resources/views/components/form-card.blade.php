@props(['title', 'action', 'method' => 'POST', 'enctype' => null])

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-gradient text-white border-0">
                <h4 class="card-title mb-0 fw-semibold">
                    <i class="bi bi-plus-circle me-2"></i>{{ $title }}
                </h4>
            </div>
            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                <form action="{{ $action }}" method="POST" @if($enctype) enctype="{{ $enctype }}" @endif>
                    @csrf
                    @if($method !== 'POST')
                        @method($method)
                    @endif
                    
                    {{ $slot }}
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient {
    background: var(--gradient-main) !important;
}
</style>
