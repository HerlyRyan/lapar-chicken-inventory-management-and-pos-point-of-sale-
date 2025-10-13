@extends('layouts.app')

@section('title', 'Page Not Found')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Page Not Found</h5>
        </div>
        <div class="card-body text-center py-5">
            <div class="mb-4">
                <i class="fas fa-search fa-4x text-muted"></i>
            </div>
            <h2 class="mb-4">404 - Page Not Found</h2>
            <p class="lead mb-4">
                {{ $message ?? 'The page you are looking for does not exist or has been moved.' }}
            </p>
            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                <i class="fas fa-home me-2"></i>Return to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
