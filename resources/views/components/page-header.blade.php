@props([
    'title' => '',
    'subtitle' => '',
    'breadcrumbs' => [],
])

<div class="page-header d-print-none mb-4">
    <div class="row g-2 align-items-center">
        <div class="col">
            @if(!empty($breadcrumbs))
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-arrows" style="--bs-breadcrumb-divider-color: 'rgba(0, 0, 0, 0.2)';">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @foreach ($breadcrumbs as $breadcrumb)
                            @if (!empty($breadcrumb['url']) && !$loop->last)
                                <li class="breadcrumb-item"><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a></li>
                            @else
                                <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb['title'] }}</li>
                            @endif
                        @endforeach
                    </ol>
                </nav>
            @endif

            <h2 class="page-title">
                {{ $title }}
            </h2>
            @if($subtitle)
                <div class="text-muted mt-1">
                    {{ $subtitle }}
                </div>
            @endif
        </div>

        @if(isset($actions))
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    {{ $actions }}
                </div>
            </div>
        @endif
    </div>
</div>
