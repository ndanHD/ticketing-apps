@extends('template.layout')

@section('title', 'Buat Tiket Baru')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Buat Tiket Baru</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.tickets.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="ticket_type_id" class="form-label">Tipe Tiket</label>
                            <select name="ticket_type_id" id="ticket_type_id" class="form-select @error('ticket_type_id') is-invalid @enderror" required>
                                <option value="">Pilih Tipe Tiket</option>
                                @foreach($types as $type)
                                    <option value="{{ $type->id }}" {{ old('ticket_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('ticket_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="sla_id" class="form-label">SLA</label>
                            <select name="sla_id" id="sla_id" class="form-select @error('sla_id') is-invalid @enderror" required>
                                <option value="">Pilih SLA</option>
                                @foreach($slas as $sla)
                                    <option value="{{ $sla->id }}" {{ old('sla_id') == $sla->id ? 'selected' : '' }}>
                                        {{ $sla->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sla_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Judul Tiket</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                id="title" name="title" value="{{ old('title') }}"
                                placeholder="Masukkan judul/subjek tiket" required>
                            @error('tittle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="detail" class="form-label">Detail Masalah</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="5" 
                                placeholder="Jelaskan detail masalah Anda..." required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('user.tickets.index') }}" class="btn btn-outline-secondary">
                                Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Buat Tiket
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection