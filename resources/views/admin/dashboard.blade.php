@extends('admin.layout.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>All Products</h3>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Add Product</a>
    </div>

    {{-- Add after the header row --}}
    <div class="d-flex justify-content-center mb-4">
        <div class="input-group" style="max-width: 500px;">
            <input type="text"
                   id="adminSearchInput"
                   class="form-control"
                   placeholder="Search by name, category or price..."
                   value="{{ request('search') }}">
            @if(request('search'))
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">✕</a>
            @endif
        </div>
        @if(request('search'))
            <small class="text-muted mt-1 d-block">
                {{ $products->total() }} result(s) for "<strong>{{ request('search') }}</strong>"
            </small>
        @endif
    </div>

    <div class="bg-white p-4 rounded shadow-sm mb-4">
        <form action="{{ route('import.excel') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <!-- Left: Label -->
                <div>
                    <h5 class="mb-1 fw-bold">Import Products</h5>
                    <small class="text-muted">Upload Excel or CSV file to bulk add products</small>
                </div>
                <!-- Middle: File Input -->
                <div class="d-flex align-items-center gap-2">
                    <input type="file" name="file" class="form-control" required>
                </div>
                <!-- Right: Button -->
                <div class="flex">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-upload me-1"></i> 
                        Import
                    </button>
                </div>
            </div>
        </form>
        <form action="{{ route('export.excel') }}" method="GET">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h5 class="mb-1 fw-bold">Export Products</h5>
                    <small class="text-muted">Download products as CSV or Excel</small>
                </div>
                <div class="d-flex gap-2">
                    <select name="type">
                        <option value="select">Select</option>
                        <option value="xlsx">Excel</option>
                        <option value="csv">CSV</option>
                    </select>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-download me-1"></i>
                        Export
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if($products->isEmpty())
        <div class="alert alert-info">No products found.</div>
    @else
        <div class="row g-4">
            @foreach($products as $product)
                <div class="col-md-3">
                    <div class="card h-100 shadow-sm border-0">
                        {{-- Product Image --}}
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}"
                                    class="card-img-top"
                                    style="height: 220px; width: 100%; object-fit: contain; padding: 10px; background: #ffffff;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center"
                                    style="height: 220px; width: 100%;">
                                <span class="text-muted">No Image</span>
                            </div>
                        @endif

                        {{-- Product Details --}}
                        <div class="card-body pb-0" style="min-height: 140px">
                            <h6 class="card-title fw-bold mb-1" style="min-height: 48px">{{ $product->name }}</h6>
                            @if($product->category)
                                <span class="badge bg-secondary mb-1" style="min-height: 20px">{{ $product->category->name }}</span>
                            @endif
                            <p class="fw-bold mb-2">₹{{ $product->price }}</p>
                        </div>

                        {{-- Actions --}}
                        <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.products.edit', $product->id) }}"
                                    class="btn rounded-circle border border-dark d-flex align-items-center justify-content-center"
                                    style="width: 45px; height: 45px;"
                                    title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                        fill="none" stroke="black" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </a>
                            <form action="{{ route('admin.products.destroy', $product->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Delete this product?')">
                                @csrf
                                <button class="btn rounded-circle border border-dark
                                        d-flex align-items-center justify-content-center"
                                        style="width: 45px; height: 45px;"
                                        title="Delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            fill="none" stroke="black" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 6h18M8 6V4h8v2M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Add before @endsection --}}
    @if($products->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $products->links() }}
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        let searchTimer;
        document.getElementById('adminSearchInput').addEventListener('input', function () {
            clearTimeout(searchTimer);
            const query = this.value.trim();
            searchTimer = setTimeout(() => {
                const url = new URL(window.location.href);
                url.searchParams.set('search', query);
                url.searchParams.delete('page');
                if (!query) url.searchParams.delete('search');
                    window.location.href = url.toString();
                }, 500);
            });
    </script>
@endsection