@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Daftar Transaksi</h1>

    <form action="{{ route('transactions.index') }}" method="GET">
        <div class="input-group w-50 mx-auto mb-3">
            <input type="text" name="search" class="form-control bg-light border-0 small" placeholder="Search for..." value="{{ request('search') }}">
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search fa-sm"></i>
                </button>
            </div>
        </div>
    </form>

    <button class="btn btn-primary mb-4" data-toggle="modal" data-target="#addTransactionModal">Tambah Transaksi</button>

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
                            @if ('status' == 'pending')
                            <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $index => $transaction)
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
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#accTransactionModal-{{ $transaction->id }}">Acc</button>
                                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editTransactionModal-{{ $transaction->id }}">Edit</button>
                                @endif
                                <!-- Tombol Hapus memicu modal delete -->
                            </td>
                        </tr>



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

                        <!-- Modal Acc Transaksi -->
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

                            @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Transaksi -->
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
                        <select name="user_id" class="form-control">
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
                                    <option value="{{ $item->id }}">{{ $item->name }} - Rp{{ number_format($item->price, 0, ',', '.') }}</option>
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
<!-- End Modal Tambah Transaksi -->

<!-- Script untuk menambah item secara dinamis -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function updateItemSelection(containerId) {
            let selectedItems = new Set();
            document.querySelectorAll(`#${containerId} select[name="items[]"]`).forEach(select => {
                selectedItems.add(select.value);
            });
            return selectedItems;
        }

        function addItem(containerId, transactionId = null) {
            let container = document.getElementById(containerId);
            let selectedItems = updateItemSelection(containerId);

            let newItem = document.createElement('div');
            newItem.classList.add('d-flex', 'mb-2');

            let selectOptions = `
                @foreach($items as $item)
                    <option value="{{ $item->id }}">{{ $item->name }} - Rp{{ number_format($item->price, 0, ',', '.') }}</option>
                @endforeach
            `;

            newItem.innerHTML = `
                <select name="items[]" class="form-control mr-2">
                    ${selectOptions}
                </select>
                <input type="number" name="quantities[]" class="form-control w-25" placeholder="Qty" min="1" value="1">
                <button type="button" class="btn btn-danger ml-2 remove-item">-</button>
            `;

            let selectElement = newItem.querySelector('select');

            // Filter item yang sudah dipilih
            selectElement.querySelectorAll('option').forEach(option => {
                if (selectedItems.has(option.value)) {
                    option.disabled = true;
                }
            });

            selectElement.addEventListener('change', function() {
                updateItemSelection(containerId);
            });

            container.appendChild(newItem);
        }

        function handleItemSelection(containerId) {
            document.getElementById(containerId).addEventListener('change', function(event) {
                if (event.target.name === "items[]") {
                    let selectedValue = event.target.value;
                    let allSelects = document.querySelectorAll(`#${containerId} select[name="items[]"]`);

                    allSelects.forEach(select => {
                        select.querySelectorAll('option').forEach(option => {
                            option.disabled = option.value !== selectedValue && updateItemSelection(containerId).has(option.value);
                        });
                    });
                }
            });
        }

        // Untuk modal tambah transaksi
        document.querySelector('.add-item').addEventListener('click', function() {
            addItem('items-container');
        });

        handleItemSelection('items-container');

        // Untuk modal edit transaksi
        document.querySelectorAll('.add-item-edit').forEach(function(button) {
            button.addEventListener('click', function() {
                let transactionId = this.getAttribute('data-transaction-id');
                addItem('edit-items-container-' + transactionId, transactionId);
                handleItemSelection('edit-items-container-' + transactionId);
            });
        });

        // Event delegation untuk tombol remove item
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-item')) {
                let parent = event.target.parentElement;
                let containerId = parent.parentElement.id;
                parent.remove();
                updateItemSelection(containerId);
            }
        });
    });
</script>


@endsection