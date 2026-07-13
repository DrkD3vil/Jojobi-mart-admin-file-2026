@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-3">Edit Location</h3>

    @if($errors->any())
        <div class="alert alert-danger">Please fix the errors below.</div>
    @endif

    <form action="{{ route('locations.update', $location) }}" method="POST">
        @method('PUT')
        @include('locations._form', ['buttonText' => 'Update'])
    </form>
</div>
@endsection
