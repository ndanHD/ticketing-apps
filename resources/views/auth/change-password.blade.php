@extends('template.layout')

@section('title', 'Change Password')

@section('content')
    <div class="container">
        <h3>Change Password</h3>
        <form method="POST" action="{{ route('password.change.submit') }}">
            @csrf
            @if (!auth()->user()->must_change_password)
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input id="current_password" type="password" name="current_password" class="form-control" required>
                </div>
            @endif
            <div class="form-group">
                <label for="password">New Password</label>
                <input id="password" type="password" name="password" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control"
                    required>
            </div>

            <button type="submit" class="btn btn-primary">Change Password</button>
        </form>
    </div>
@endsection
