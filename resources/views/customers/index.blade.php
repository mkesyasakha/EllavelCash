@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <h1 class="h3 mb-4 text-gray-800">Daftar Customer</h1>
    <form action="{{route('users.index')}}" method="GET">
        <div class="input-group w-50 mx-auto mb-3">
            <input type="text" name="search" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2" value="{{request('search')}}">
            <div class="input-group-append">
                <button class="btn btn-primary" type="button">
                    <i class="fas fa-search fa-sm"></i>
                </button>
            </div>
        </div>
    </form>
    <!-- Tabel Customer untuk Admin -->
    @hasrole('admin')
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-primary">
                <tr>
                    <th>No</th>
                    <th>Photo</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>No. Telepon</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $index => $user)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <img src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('asset/img/undraw_profile_1.svg') }}" alt="User Photo" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                    </td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone }}</td>
                    <td>
                        <!-- Show Button -->
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#showModal{{ $user->id }}">
                            <i class="fas fa-eye"></i>
                        </button>
                        <!-- Edit Button -->
                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $user->id }}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <!-- Delete Button -->
                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $user->id }}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>

                <!-- Modal Show Pelanggan -->
                <div class="modal fade" id="showModal{{ $user->id }}" tabindex="-1" aria-labelledby="showModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="showModalLabel">Detail Pelanggan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <strong>Nama:</strong> {{ $user->name }}
                                </div>
                                <div class="mb-3">
                                    <strong>Email:</strong> {{ $user->email }}
                                </div>
                                <div class="mb-3">
                                    <strong>No. Telepon:</strong> {{ $user->phone }}
                                </div>
                                <div class="mb-3">
                                    <strong>Foto:</strong>
                                    <img src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('asset/img/undraw_profile_1.svg') }}" alt="User Photo" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Edit Pelanggan -->
                <div class="modal fade" id="editModal{{ $user->id }}" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('users.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">Edit Pelanggan</h5>
                                    <button type="button" class="btn" data-bs-dismiss="modal"><b>X</b></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="edit-name" class="form-label">Nama</label>
                                        <input type="text" class="form-control" id="edit-name" name="name" value="{{ $user->name }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit-email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="edit-email" name="email" value="{{ $user->email }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit-phone" class="form-label">No. Telepon</label>
                                        <input type="text" class="form-control" id="edit-phone" name="phone" value="{{ $user->phone }}">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal Hapus Pelanggan -->
                <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                Apakah Anda yakin ingin menghapus pelanggan ini?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </tbody>
        </table>
    </div>
    @endhasrole
</div>
@endsection