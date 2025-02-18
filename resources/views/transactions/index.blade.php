@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Daftar Transaksi</h1>


    <button class="btn btn-primary mb-4" data-toggle="modal" data-target="#addTransactionModal">Tambah Transaksi</button>
    @hasrole('admin')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tabel Transaksi</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('transactions.index') }}" class="mb-4">
                <div class="input-group">
                    <input type="text" class="form-control" name="search" placeholder="Search category..." value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Search</button>
                    </div>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pengguna</th>
                            <th>Deskripsi</th>
                            <th>Total</th>
                            <th>Tanggal Transaksi</th>
                            <th>Bukti</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $index => $transaction)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ optional($transaction->customers)->name }}</td>
                            <td>{{ $transaction->description }}</td>
                            @if ($transaction->discounts)
                            <td>
                                Potongan:{{$transaction->discounts->discount_percentage }}%
                                <br>
                                <del style="color: red;">Rp.{{ number_format($transaction->total + $transaction->discount, 2) }}</del>
                                <br>
                                <span style="color: green;">Rp.{{ number_format($transaction->total, 2) }}</span>
                            </td>
                            @else
                            <td>Rp.{{ number_format($transaction->total, 2) }}</td>
                            @endif
                            <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('j M Y') }}</td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#proofModal{{ $transaction->id }}"><i class="bi bi-eye-fill    "></i> Lihat Bukti
                                </button>
                            </td>
                            <td>
                                @if($transaction->status == 'failed')
                                <span class="badge bg-danger text-light">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                                @else
                                <span class="badge {{ $transaction->status == 'pending' ? 'bg-warning' : 'bg-success' }} text-light">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                                @endif
                            </td>
                            <td>
                                <!-- Tombol Edit memicu modal edit -->
                                @if ($transaction->status == 'pending')
                                <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#accTransactionModal-{{ $transaction->id }}">Acc</button>
                                <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#accTransactionModalReject-{{ $transaction->id }}">Reject</button>
                                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editTransactionModal-{{ $transaction->id }}">Edit</button>
                                @if ($transaction->discount_id == null)
                                <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#applyPromoModal">Apply Promo Code</button>
                                @endif
                                @endif
                                <!-- Tombol Hapus memicu modal delete -->
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#showTransactionModal-{{ $transaction->id }}">Detail</button>
                            </td>
                        </tr>

                        <!-- Modal Apply Promo -->
                        <div class="modal fade" id="applyPromoModal" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Apply Promo Code</h5>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <form action="{{ route('transactions.applyPromo') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">
                                        <div class="modal-body">
                                            <label for="promo_code">Enter Promo Code:</label>
                                            <input type="text" name="promo_code" class="form-control">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">Apply</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="proofModal{{ $transaction->id }}" tabindex="-1" aria-labelledby="proofModalLabel{{ $transaction->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="proofModalLabel{{ $transaction->id }}">Bukti Transaksi</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <img src="{{ asset('storage/' . $transaction->proof) }}" class="img-fluid rounded" alt="Bukti Transaksi">
                                    </div>
                                    <div class="modal-footer">
                                        <a href="{{ asset('storage/' . $transaction->proof) }}" class="btn btn-primary" download>Download</a>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Detail Transaksi -->
                        <div class="modal fade" id="showTransactionModal-{{ $transaction->id }}" tabindex="-1" aria-labelledby="showTransactionModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="showTransactionModalLabel">Struk Pembelian</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <h4>EllavelCash</h4>
                                        <hr>
                                        <p><strong>Nama Pelanggan:</strong> {{ optional($transaction->customers)->name }}</p>
                                        <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('j M Y') }}</p>
                                        <hr>
                                        <div class="d-flex flex-column align-items-start">
                                            @foreach($transaction->items as $item)
                                            <div class="w-100 d-flex justify-content-between">
                                                <span>{{ $item->name }} ({{ $item->pivot->quantity }}x)</span>
                                                <span>Rp.{{ number_format($item->pivot->quantity * $item->price, 2) }}</span>
                                            </div>
                                            @endforeach
                                        </div>
                                        <hr>
                                        <p><strong>Total:</strong> Rp.{{ number_format($transaction->total, 2) }}</p>
                                        @if($transaction->status == 'failed')
                                        <p><strong>Status:</strong> <span class="badge bg-danger text-light">{{ ucfirst($transaction->status) }}</span></p>
                                        @else
                                        <p><strong>Status:</strong> <span class="badge {{ $transaction->status == 'pending' ? 'bg-warning' : 'bg-success' }} text-light">{{ ucfirst($transaction->status) }}</span></p>
                                        @endif
                                        <hr>
                                        <p><strong>Bukti Transaksi:</strong></p>
                                        <img src="{{ asset('storage/' . $transaction->proof) }}" class="img-fluid" alt="Bukti Transaksi">
                                        <hr>
                                        @if($transaction->status == 'success')
                                        <p>Terima kasih atas pembelian Anda!</p>
                                        @elseif($transaction->status == 'pending')
                                        <p>Pembelian anda sedang di proses</p>
                                        @else
                                        <p style="color: red;">Maaf, Pembelian Anda Gagal!</p>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        @if($transaction->status == 'success')
                                        <a href="{{ route('transactions.download-pdf', $transaction->id) }}" class="btn btn-danger">
                                            <i class="fas fa-file-pdf"></i> Download PDF
                                        </a>
                                        @endif
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Modal Edit Transaksi -->
                        <div class="modal fade" id="editTransactionModal-{{ $transaction->id }}">
                            <div class="modal-dialog">
                                <form action="{{ route('transactions.update', $transaction->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Transaksi</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Bukti Transaksi -->
                                            <div class="form-group">
                                                <label for="proof">Bukti Transaksi</label>
                                                <input type="file" name="proof" class="form-control">
                                            </div>

                                            <!-- Pilih Pengguna -->
                                            <div class="form-group">
                                                <label for="user_id">Pengguna</label>
                                                <select name="user_id" class="form-control">
                                                    @foreach($customers as $customer)
                                                    <option value="{{ $customer->id }}" {{ $customer->id == $transaction->user_id ? 'selected' : '' }}>
                                                        {{ $customer->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Deskripsi -->
                                            <div class="form-group">
                                                <label for="description">Deskripsi</label>
                                                <textarea name="description" class="form-control">{{ $transaction->description }}</textarea>
                                            </div>

                                            <!-- Tanggal Transaksi -->
                                            <div class="form-group">
                                                <label for="transaction_date">Tanggal Transaksi</label>
                                                <input type="date" name="transaction_date" class="form-control" value="{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('Y-m-d') }}">
                                            </div>

                                            <!-- Status -->
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <input type="text" name="status" class="form-control" value="{{ $transaction->status }}">
                                            </div>

                                            <!-- Pilih Item dan Kuantitas -->
                                            <div class="form-group">
                                                <label for="items">Pilih Item</label>
                                                <div id="edit-items-container-{{ $transaction->id }}">
                                                    @if($transaction->items->isNotEmpty())
                                                    @foreach($transaction->items as $tItem)
                                                    <div class="d-flex mb-2">
                                                        <select name="items[]" class="form-control mr-2">
                                                            @foreach($items as $item)
                                                            <option value="{{ $item->id }}" {{ $item->id == $tItem->id ? 'selected' : '' }}>
                                                                {{ $item->name }} - Rp{{ number_format($item->price, 0, ',', '.') }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                        <input type="number" name="quantities[]" class="form-control w-25" placeholder="Qty" min="1" value="{{ $tItem->pivot->quantity }}">
                                                        <button type="button" class="btn btn-danger ml-2 remove-item">-</button>
                                                    </div>
                                                    @endforeach
                                                    @else
                                                    <div class="d-flex mb-2">
                                                        <select name="items[]" class="form-control mr-2">
                                                            @foreach($items as $item)
                                                            <option value="{{ $item->id }}">
                                                                {{ $item->name }} - Rp{{ number_format($item->price, 0, ',', '.') }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                        <input type="number" name="quantities[]" class="form-control w-25" placeholder="Qty" min="1" value="1">
                                                        <button type="button" class="btn btn-danger ml-2 remove-item">-</button>
                                                    </div>
                                                    @endif
                                                </div>
                                                <button type="button" class="btn btn-success mt-2 add-item-edit" data-transaction-id="{{ $transaction->id }}">+</button>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- End Modal Edit Transaksi -->

                        <!-- Modal Hapus Transaksi -->
                        <div class="modal fade" id="deleteTransactionModal-{{ $transaction->id }}">
                            <div class="modal-dialog">
                                <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Hapus Transaksi</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Apakah anda yakin ingin menghapus transaksi ini?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="modal fade" id="accTransactionModal-{{ $transaction->id }}">
                            <div class="modal-dialog">
                                <form action="{{ route('transactions.acc', $transaction->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Konfirmasi Transaksi</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Apakah anda yakin ingin mengkonfirmasi transaksi ini?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Konfirmasi</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="modal fade" id="accTransactionModalReject-{{ $transaction->id }}">
                            <div class="modal-dialog">
                                <form action="{{ route('transactions.reject', $transaction->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Tolak Transaksi</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Apakah anda yakin ingin menolak transaksi ini?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger">Konfirmasi</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- End Modal Hapus Transaksi -->
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada barang tersedia.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $transactions->links('vendor.pagination.bootstrap-4') }}
            </div>
        </div>
    </div>
    @endhasrole

    @hasrole('customers')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tabel Transaksi</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pengguna</th>
                            <th>Deskripsi</th>
                            <th>Total</th>
                            <th>Tanggal Transaksi</th>
                            <th>Bukti</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transaction_customers as $index => $transaction)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ optional($transaction->customers)->name }}</td>
                            <td>{{ $transaction->description }}</td>
                            <td>Rp.{{ number_format($transaction->total, 2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('j M Y') }}</td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#proofModal{{ $transaction->id }}"><i class="bi bi-eye-fill    "></i> Lihat Bukti
                                </button>
                            </td>
                            <td>
                                <span class="badge {{ $transaction->status == 'pending' ? 'bg-warning' : 'bg-success' }}">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                            <td>

                                <!-- Tombol Edit memicu modal edit -->
                                @if ($transaction->status == 'pending')
                                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editTransactionModal-{{ $transaction->id }}">Edit</button>
                                @endif
                                <!-- Tombol Hapus memicu modal delete -->
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#showTransactionModal-{{ $transaction->id }}">Detail</button>
                            </td>
                        </tr>

                        <div class="modal fade" id="proofModal{{ $transaction->id }}" tabindex="-1" aria-labelledby="proofModalLabel{{ $transaction->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="proofModalLabel{{ $transaction->id }}">Bukti Transaksi</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <img src="{{ asset('storage/' . $transaction->proof) }}" class="img-fluid rounded" alt="Bukti Transaksi">
                                    </div>
                                    <div class="modal-footer">
                                        <a href="{{ asset('storage/' . $transaction->proof) }}" class="btn btn-primary" download>Download</a>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Detail Transaksi -->
                        <div class="modal fade" id="showTransactionModal-{{ $transaction->id }}" tabindex="-1" aria-labelledby="showTransactionModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="showTransactionModalLabel">Struk Pembelian</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <h4>EllavelCash</h4>
                                        <hr>
                                        <p><strong>Nama Pelanggan:</strong> {{ optional($transaction->customers)->name }}</p>
                                        <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('j M Y') }}</p>
                                        <hr>
                                        <div class="d-flex flex-column align-items-start">
                                            @foreach($transaction->items as $item)
                                            <div class="w-100 d-flex justify-content-between">
                                                <span>{{ $item->name }} ({{ $item->pivot->quantity }}x)</span>
                                                <span>Rp.{{ number_format($item->pivot->quantity * $item->price, 2) }}</span>
                                            </div>
                                            @endforeach
                                        </div>
                                        <hr>
                                        <p><strong>Total:</strong> Rp.{{ number_format($transaction->total, 2) }}</p>
                                        <p><strong>Status:</strong> <span class="badge {{ $transaction->status == 'pending' ? 'bg-warning' : 'bg-success' }}">{{ ucfirst($transaction->status) }}</span></p>
                                        <hr>
                                        <p><strong>Bukti Transaksi:</strong></p>
                                        <img src="{{ asset('storage/' . $transaction->proof) }}" class="img-fluid" alt="Bukti Transaksi">
                                        <hr>
                                        <p>Terima kasih atas pembelian Anda!</p>
                                    </div>
                                    <div class="modal-footer">

                                        <a href="{{ route('transactions.download-pdf', $transaction->id) }}" class="btn btn-danger">
                                            <i class="fas fa-file-pdf"></i> Download PDF
                                        </a>

                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Modal Edit Transaksi -->
                        <div class="modal fade" id="editTransactionModal-{{ $transaction->id }}">
                            <div class="modal-dialog">
                                <form action="{{ route('transactions.update', $transaction->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Transaksi</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Bukti Transaksi -->
                                            <div class="form-group">
                                                <label for="proof">Bukti Transaksi</label>
                                                <input type="file" name="proof" class="form-control">
                                            </div>
                                            <input type="text" name="user_id" class="form-control" value="{{Auth::user()->id}}" hidden>
                                            <!-- Deskripsi -->
                                            <div class="form-group">
                                                <label for="description">Deskripsi</label>
                                                <textarea name="description" class="form-control">{{ $transaction->description }}</textarea>
                                            </div>

                                            <!-- Tanggal Transaksi -->
                                            <div class="form-group">
                                                <label for="transaction_date">Tanggal Transaksi</label>
                                                <input type="date" name="transaction_date" class="form-control" value="{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('Y-m-d') }}">
                                            </div>

                                            <!-- Status -->
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <input type="text" name="status" class="form-control" value="{{ $transaction->status }}">
                                            </div>

                                            <!-- Pilih Item dan Kuantitas -->
                                            <div class="form-group">
                                                <label for="items">Pilih Item</label>
                                                <div id="edit-items-container-{{ $transaction->id }}">
                                                    @if($transaction->items->isNotEmpty())
                                                    @foreach($transaction->items as $tItem)
                                                    <div class="d-flex mb-2">
                                                        <select name="items[]" class="form-control mr-2">
                                                            @foreach($items as $item)
                                                            <option value="{{ $item->id }}" {{ $item->id == $tItem->id ? 'selected' : '' }}>
                                                                {{ $item->name }} - Rp{{ number_format($item->price, 0, ',', '.') }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                        <input type="number" name="quantities[]" class="form-control w-25" placeholder="Qty" min="1" value="{{ $tItem->pivot->quantity }}">
                                                        <button type="button" class="btn btn-danger ml-2 remove-item">-</button>
                                                    </div>
                                                    @endforeach
                                                    @else
                                                    <div class="d-flex mb-2">
                                                        <select name="items[]" class="form-control mr-2">
                                                            @foreach($items as $item)
                                                            <option value="{{ $item->id }}">
                                                                {{ $item->name }} - Rp{{ number_format($item->price, 0, ',', '.') }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                        <input type="number" name="quantities[]" class="form-control w-25" placeholder="Qty" min="1" value="1">
                                                        <button type="button" class="btn btn-danger ml-2 remove-item">-</button>
                                                    </div>
                                                    @endif
                                                </div>
                                                <button type="button" class="btn btn-success mt-2 add-item-edit" data-transaction-id="{{ $transaction->id }}">+</button>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- End Modal Edit Transaksi -->

                        <!-- Modal Hapus Transaksi -->
                        <div class="modal fade" id="deleteTransactionModal-{{ $transaction->id }}">
                            <div class="modal-dialog">
                                <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Hapus Transaksi</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Apakah anda yakin ingin menghapus transaksi ini?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="modal fade" id="accTransactionModal-{{ $transaction->id }}">
                            <div class="modal-dialog">
                                <form action="{{ route('transactions.acc', $transaction->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Konfirmasi Transaksi</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Apakah anda yakin ingin mengkonfirmasi transaksi ini?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Konfirmasi</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- End Modal Hapus Transaksi -->

                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-center mt-3">
                    {{ $transaction_customers->links('vendor.pagination.bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
    @endhasrole
</div>

<!-- Modal Tambah Transaksi -->
@hasrole('admin')
<div class="modal fade" id="addTransactionModal">
    <div class="modal-dialog">
        <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Transaksi Baru</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <!-- Bukti Transaksi -->
                    <div class="form-group">
                        <label for="proof">Bukti Transaksi</label>
                        <input type="file" name="proof" class="form-control">
                    </div>

                    <!-- Pilih Pengguna -->
                    <div class="form-group">
                        <label for="user_id">Pengguna</label>
                        <select name="user_id" class="form-control mr-2">
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Deskripsi -->
                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>

                    <!-- Tanggal Transaksi -->
                    <div class="form-group">
                        <label for="transaction_date">Tanggal Transaksi</label>
                        <input type="date" name="transaction_date" class="form-control">
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <label for="status">Status</label>
                        <input type="text" name="status" class="form-control" value="pending" readonly>
                    </div>

                    <!-- Pilih Item dan Kuantitas -->
                    <div class="form-group">
                        <label for="items">Pilih Item</label>
                        <div id="items-container">
                            <div class="d-flex mb-2">
                                <select name="items[]" class="form-control mr-2">
                                    @foreach($items as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }} - Rp{{ number_format($item->price, 0, ',', '.') }} ({{$item->stock}})</option>
                                    @endforeach
                                </select>
                                <input type="number" name="quantities[]" class="form-control w-25" placeholder="Qty" min="1" value="1">
                                <button type="button" class="btn btn-success ml-2 add-item">+</button>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endhasrole

@hasrole('customers')
<div class="modal fade" id="addTransactionModal">
    <div class="modal-dialog">
        <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Transaksi Baru</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <!-- Bukti Transaksi -->
                    <div class="form-group">
                        <label for="proof">Bukti Transaksi</label>
                        <input type="file" name="proof" class="form-control">
                    </div>

                    <!-- Pilih Pengguna -->
                    <div class="form-group">
                        <label for="user_id">Pengguna</label>
                        <input type="text" name="user_id" class="form-control" value="{{ Auth::user()->id }}" hidden>
                        <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
                    </div>

                    <!-- Deskripsi -->
                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>

                    <!-- Tanggal Transaksi -->
                    <div class="form-group">
                        <label for="transaction_date">Tanggal Transaksi</label>
                        <input type="date" name="transaction_date" class="form-control">
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <label for="status">Status</label>
                        <input type="text" name="status" class="form-control" value="pending" readonly>
                    </div>

                    <!-- Pilih Item dan Kuantitas -->
                    <div class="form-group">
                        <label for="items">Pilih Item</label>
                        <div id="items-container">
                            <div class="d-flex mb-2">
                                <select name="items[]" class="form-control mr-2">
                                    @foreach($items as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }} - Rp{{ number_format($item->price, 0, ',', '.') }} ({{$item->stock}})</option>
                                    @endforeach
                                </select>
                                <input type="number" name="quantities[]" class="form-control w-25" placeholder="Qty" min="1" value="1">
                                <button type="button" class="btn btn-success ml-2 add-item">+</button>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endhasrole
<!-- End Modal Tambah Transaksi -->

<!-- Script untuk menambah item secara dinamis -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function updateAvailableItems(container) {
            let selectedItems = Array.from(container.querySelectorAll('select[name="items[]"]'))
                .map(select => select.value);

            container.querySelectorAll('select[name="items[]"]').forEach(select => {
                let currentValue = select.value;
                select.querySelectorAll('option').forEach(option => {
                    option.hidden = selectedItems.includes(option.value) && option.value !== currentValue;
                });
            });
        }

        // Untuk modal tambah transaksi
        document.querySelector('.add-item').addEventListener('click', function() {
            let container = document.getElementById('items-container');
            let newItem = document.createElement('div');
            newItem.classList.add('d-flex', 'mb-2');

            newItem.innerHTML = `
            <select name="items[]" class="form-control mr-2">
                <option value="" selected disabled>Pilih Item</option>
                @foreach($items as $item)
                <option value="{{ $item->id }}">{{ $item->name }} - Rp{{ number_format($item->price, 0, ',', '.') }} ({{$item->stock}})</option>
                @endforeach
            </select>
            <input type="number" name="quantities[]" class="form-control w-25" placeholder="Qty" min="1" value="1">
            <button type="button" class="btn btn-danger ml-2 remove-item">-</button>
        `;

            container.appendChild(newItem);
            updateAvailableItems(container);
        });

        // Untuk modal tambah transaksi (remove item)
        document.getElementById('items-container').addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-item')) {
                event.target.parentElement.remove();
                updateAvailableItems(document.getElementById('items-container'));
            }
        });

        // Untuk modal edit transaksi
        document.querySelectorAll('.add-item-edit').forEach(function(button) {
            button.addEventListener('click', function() {
                let transactionId = this.getAttribute('data-transaction-id');
                let container = document.getElementById('edit-items-container-' + transactionId);
                let newItem = document.createElement('div');
                newItem.classList.add('d-flex', 'mb-2');

                newItem.innerHTML = `
                <select name="items[]" class="form-control mr-2">
                    <option value="" selected disabled>Pilih Item</option>
                    @foreach($items as $item)
                    <option value="{{ $item->id }}">{{ $item->name }} - Rp{{ number_format($item->price, 0, ',', '.') }} ({{$item->stock}})</option>
                    @endforeach
                </select>
                <input type="number" name="quantities[]" class="form-control w-25" placeholder="Qty" min="1" value="1">
                <button type="button" class="btn btn-danger ml-2 remove-item">-</button>
            `;

                container.appendChild(newItem);
                updateAvailableItems(container);
            });
        });

        // Event delegation untuk tombol remove item (berlaku untuk semua modal)
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-item')) {
                let container = event.target.closest('#items-container, .edit-items-container');
                event.target.parentElement.remove();
                updateAvailableItems(container);
            }
        });

        // Event listener untuk memastikan hanya satu item yang bisa dipilih
        document.addEventListener('change', function(event) {
            if (event.target.matches('select[name="items[]"]')) {
                let container = event.target.closest('#items-container, .edit-items-container');
                updateAvailableItems(container);
            }
        });
    });
</script>

@endsection