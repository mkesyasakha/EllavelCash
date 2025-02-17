@extends('layouts.app')

@section('content')

<h2>Daftar Diskon</h2>
<button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addDiscountModal">Tambah Diskon</button>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Tabel Discount</h6>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Persentase</th>
                    <th>Berlaku Hingga</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($discounts as $discount)
                <tr>
                    <td>{{ $discount->code }}</td>
                    <td>{{ $discount->discount_percentage ? $discount->discount_percentage . '%' : '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($discount->valid_until)->format('j M Y') }}</td>
                    <td>
                        <span class="badge {{ $discount->status == 'expired' ? 'bg-danger' : 'bg-success' }}">
                            {{ ucfirst($discount->status) }}
                        </span>
                    </td>
                    <td>
                        @if ($discount->status == 'active')
                            
                        <!-- Tombol Edit -->
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editDiscountModal-{{ $discount->id }}">
                            Edit
                        </button>
                        @endif
    
                        <!-- Tombol Hapus -->
                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteDiscountModal-{{ $discount->id }}">
                            Hapus
                        </button>
                    </td>
                </tr>
    
                <!-- Modal Edit Diskon -->
                <div class="modal fade" id="editDiscountModal-{{ $discount->id }}" tabindex="-1" aria-labelledby="editDiscountModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Diskon</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('discounts.update', $discount->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="mb-3">
                                        <label>Kode Promo</label>
                                        <input type="text" name="code" class="form-control" value="{{ $discount->code }}" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label>Persentase Diskon (Opsional)</label>
                                        <input type="number" name="discount_percentage" class="form-control" value="{{ $discount->discount_percentage }}" min="0" max="100">
                                    </div>
                                    <div class="mb-3">
                                        <label>Berlaku Hingga</label>
                                        <input type="date" name="valid_until" class="form-control" value="{{ $discount->valid_until }}">
                                    </div>
                                    <div class="mb-3">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            <option value="active" {{ $discount->status == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="expired" {{ $discount->status == 'active' ? 'selected' : '' }}>Expired</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-success">Update</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
    
                <!-- Modal Hapus Diskon -->
                <div class="modal fade" id="deleteDiscountModal-{{ $discount->id }}" tabindex="-1" aria-labelledby="deleteDiscountModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Konfirmasi Hapus</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Apakah Anda yakin ingin menghapus diskon <strong>{{ $discount->code }}</strong>?</p>
                            </div>
                            <div class="modal-footer">
                                <form action="{{ route('discounts.destroy', $discount->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada barang tersedia.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Diskon -->
<div class="modal fade" id="addDiscountModal" tabindex="-1" aria-labelledby="addDiscountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Diskon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('discounts.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label>Kode Promo</label>
                        <input type="text" name="code" class="form-control" value="{{ $randomCode }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label>Persentase Diskon (Opsional)</label>
                        <input type="number" name="discount_percentage" class="form-control" min="0" max="100">
                    </div>
                    <div class="mb-3">
                        <label>Berlaku Hingga</label>
                        <input type="date" name="valid_until" class="form-control">
                    </div>
                    <div class="mb-3">
                        <select name="status" class="form-control" hidden>
                            <option value="active">Active</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection