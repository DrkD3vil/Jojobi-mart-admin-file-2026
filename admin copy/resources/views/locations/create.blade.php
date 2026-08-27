@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-3">Add Location</h3>

    @if($errors->any())
        <div class="alert alert-danger">Please fix the errors below.</div>
    @endif

    <form action="{{ route('locations.store') }}" method="POST">
        @include('locations._form', ['buttonText' => 'Create'])
    </form>
</div>
@endsection
