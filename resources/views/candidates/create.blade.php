@extends('layouts.app')

@section('content')
       @php
                $parsedData = session('parsedData');
            @endphp

                 @include('candidates.form', [
                'candidate' => (object) ($parsedData ?? [])
            ])     
@endsection