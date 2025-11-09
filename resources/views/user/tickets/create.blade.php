@extends('template.layout')

@section('title', 'Create New Ticket')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Create New Ticket</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('user.tickets.store') }}" method="POST">
                            @csrf

                            {{-- Ticket Type --}}
                            <div class="mb-3">
                                <label for="ticket_type_id" class="form-label">Ticket Type</label>
                                <select name="ticket_type_id" id="ticket_type_id"
                                    class="form-select @error('ticket_type_id') is-invalid @enderror" required>
                                    <option value="">Select Ticket Type</option>
                                    @foreach ($types as $type)
                                        <option value="{{ $type->id }}"
                                            {{ old('ticket_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('ticket_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Priority --}}
                            <div class="mb-3">
                                <label for="sla_id" class="form-label">Priority</label>
                                <select name="sla_id" id="sla_id"
                                    class="form-select @error('sla_id') is-invalid @enderror" required>
                                    <option value="">Select Priority</option>
                                    @foreach ($slas as $sla)
                                        <option value="{{ $sla->id }}"
                                            {{ old('sla_id') == $sla->id ? 'selected' : '' }}>
                                            {{ $sla->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('sla_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Outlet --}}
                            <div class="mb-3">
                                <label for="outlet_id" class="form-label">Outlet</label>
                                <select name="outlet_id" id="outlet_id"
                                    class="form-select @error('outlet_id') is-invalid @enderror" required>
                                    <option value="">Select Outlet</option>
                                    @foreach ($outlets as $outlet)
                                        <option value="{{ $outlet->id }}"
                                            {{ old('outlet_id', auth()->user()->outlet_id) == $outlet->id ? 'selected' : '' }}>
                                            {{ $outlet->name }}{{ $outlet->address ? ' - ' . $outlet->address : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('outlet_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Title --}}
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                    id="title" name="title" value="{{ old('title') }}"
                                    placeholder="Enter ticket title/subject" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div class="mb-3">
                                <label for="description" class="form-label">Issue Details</label>
                                <textarea id="description" name="description" rows="5">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Buttons --}}
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('user.tickets.index') }}" class="btn btn-outline-secondary">Back</a>
                                <button type="submit" class="btn btn-primary">Create Ticket</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#description'), {
                ckfinder: {
                    uploadUrl: '{{ route('ckeditor.upload') . '?_token=' . csrf_token() }}',
                },
            })
            .then(editor => {
                let previousImages = [];

                // Track deleted images
                editor.model.document.on('change:data', debounce(() => {
                    const currentImages = getImageUrls(editor);
                    const deletedImages = previousImages.filter(url => !currentImages.includes(url));

                    deletedImages.forEach(url => {
                        const fileName = url.split('/').pop();
                        fetch('{{ route('ckeditor.delete') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                fileName
                            })
                        }).catch(err => console.error('Delete error:', err));
                    });

                    previousImages = currentImages;
                }, 500));

                function getImageUrls(editor) {
                    const div = document.createElement('div');
                    div.innerHTML = editor.getData();
                    return Array.from(div.querySelectorAll('img')).map(img => img.src);
                }

                function debounce(fn, delay) {
                    let timer;
                    return (...args) => {
                        clearTimeout(timer);
                        timer = setTimeout(() => fn(...args), delay);
                    };
                }

            })
            .catch(error => console.error(error));
    </script>
@endpush
