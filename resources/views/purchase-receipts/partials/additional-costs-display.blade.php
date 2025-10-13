@php($costs = $costs ?? [])
@php($wrap = $wrap ?? true)
@if(!empty($costs) && count($costs) > 0)
    @if($wrap)
    <div class="row mt-4">
        <div class="col-12">
    @endif
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Biaya Tambahan</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Jenis Biaya</th>
                                    <th>Jumlah</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($costs as $cost)
                                    <tr>
                                        <td>{{ $cost->cost_name }}</td>
                                        <td>Rp {{ number_format($cost->amount, 0, ',', '.') }}</td>
                                        <td>{{ $cost->notes ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    @if($wrap)
        </div>
    </div>
    @endif
@endif
