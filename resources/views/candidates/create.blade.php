@extends('layouts.app')

@section('content')

<div class="container-fluid px-5 py-4 bg-light">
    <div class="card border-0 shadow-sm rounded-0">
        <div class="card-header text-white rounded-0 bg-primary" >
            <h4 class="mb-0">Add Candidate</h4>
        </div>

        <div class="card-body bg-white">
            <form method="POST" action="{{ route('candidates.store') }}" enctype="multipart/form-data">
            @csrf

            @php
                $parsedData = session('parsedData');
            @endphp

            @include('candidates.form', [
                'candidate' => (object) ($parsedData ?? []),
                'openings' => $openings
            ])            
            </form>
        </div>
    </div>
</div>
@endsection
