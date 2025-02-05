@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Daftar Transaksi</h1>

    <!-- Tombol Tambah Transaksi -->
    <button class="btn btn-primary mb-4" data-toggle="modal" data-target="#addTransactionModal">Tambah Transaksi</button>

    <!-- Tabel Transaksi -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tabel Transaksi</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pengguna</th>
                            <th>Deskripsi</th>
                            <th>Total</th>
                            <th>Tanggal Transaksi</th>
                            <th>Status</th>
                            <th>Bukti</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $index => $transaction)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $transaction->customers->name }}</td>
                            <td>{{ $transaction->description }}</td>
                            <td>{{ $transaction->total }}</td>
                            <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('j M Y') }}</td>
                            <td>
                                @if ($transaction->status == 'pending')
                                <span class="badge bg-warning">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                                @else
                                <span class="badge bg-success">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#proofModal{{ $transaction->id }}">
                                    Lihat Bukti
                                </button>
                            </td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editTransactionModal{{ $transaction->id }}">Edit</button>
                                <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteTransactionModal{{ $transaction->id }}">Hapus</button>
                            </td>
                        </tr>

                        <div class="modal fade" id="proofModal{{ $transaction->id }}" tabindex="-1" aria-labelledby="proofModalLabel{{ $transaction->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="proofModalLabel{{ $transaction->id }}">Bukti Transaksi</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body text-center">
                                        @if(pathinfo($transaction->proof, PATHINFO_EXTENSION) == 'pdf')
                                        <iframe src="{{ asset('storage/' . $transaction->proof) }}" width="100%" height="500px"></iframe>
                                        @else
                                        <img src="{{ asset('storage/' . $transaction->proof) }}" class="img-fluid rounded" alt="Bukti Transaksi">
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <a href="{{ asset('storage/' . $transaction->proof) }}" class="btn btn-primary" download>Download</a>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Modal Edit Transaksi -->
                        <div class="modal fade" id="editTransactionModal{{ $transaction->id }}" tabindex="-1" aria-labelledby="editTransactionModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="{{ route('transactions.update', $transaction->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editTransactionModalLabel">Edit Transaksi</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="user_id">Pilih Pengguna</label>
                                                <select name="user_id" id="user_id" class="form-control">
                                                    @foreach ($customers as $customer)
                                                    <option value="{{ $customer->id }}" @if($customer->id == $transaction->user_id) selected @endif>
                                                        {{ $customer->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="description">Deskripsi Transaksi</label>
                                                <textarea name="description" id="description" class="form-control">{{ $transaction->description }}</textarea>
                                            </div>

                                            <div class="form-group">
                                                <label for="proof">Bukti Transaksi</label>
                                                <input type="file" name="proof" id="proof" class="form-control">
                                                @if($transaction->proof)
                                                <p class="mt-2">Bukti saat ini: <a href="{{ asset('storage/' . $transaction->proof) }}" target="_blank">Lihat Bukti</a></p>
                                                @endif
                                            </div>

                                            <div class="form-group">
                                                <label for="total">Total Transaksi</label>
                                                <input type="number" name="total" id="total" class="form-control" value="{{ $transaction->total }}" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="transaction_date">Tanggal Transaksi</label>
                                                <input type="date" name="transaction_date" id="transaction_date" class="form-control" value="{{ $transaction->transaction_date }}" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select name="status" id="status" class="form-control">
                                                    <option value="pending" @if($transaction->status == 'pending') selected @endif>Pending</option>
                                                    <option value="success" @if($transaction->status == 'success') selected @endif>Success</option>
                                                    <option value="failed" @if($transaction->status == 'failed') selected @endif>Failed</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>


                        <!-- Modal Hapus Transaksi -->
                        <div class="modal fade" id="deleteTransactionModal{{ $transaction->id }}" tabindex="-1" aria-labelledby="deleteTransactionModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteTransactionModalLabel">Konfirmasi Hapus</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Apakah Anda yakin ingin menghapus transaksi ini?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                        <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada transaksi tersedia.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="modal fade" id="addTransactionModal" tabindex="-1" aria-labelledby="addTransactionModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addTransactionModalLabel">Tambah Transaksi Baru</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="user_id">Pilih Pengguna</label>
                                        <select name="user_id" id="user_id" class="form-control">
                                            @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="description">Deskripsi Transaksi</label>
                                        <textarea name="description" id="description" class="form-control"></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="proof">Bukti Transaksi</label>
                                        <input type="file" name="proof" id="proof" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="transaction_date">Tanggal Transaksi</label>
                                        <input type="date" name="transaction_date" id="transaction_date" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="status">Status Transaksi</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="pending">Pending</option>
                                            <option value="success">Success</option>
                                            <option value="failed">Failed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                    <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection