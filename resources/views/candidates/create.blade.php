@extends('layouts.app')

@section('content')
       @php
                $parsedData = session('parsedData');
                $firstName = $parsedData['first_name'] ?? '';
                $lastName = $parsedData['last_name'] ?? '';
                $email = $parsedData['email'] ?? '';            
            @endphp



<div class="container-fluid py-4 px-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">ADD</h5>
                </div>
                <div class="card-body">
                    <form action="{{route('candidates.store')}}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" name="first_name" id="first_name" class="form-control border border-dark p-2" value="<?php echo $firstName; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" name="last_name" id="last_name" class="form-control border border-dark p-2" value="<?php echo $lastName; ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control border border-dark p-2" value="<?php echo $email; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="job_id" class="form-label">Opening</label>
                            <select name="job_id" id="job_id" class="form-select border border-dark p-2">
                                <option value="">Select Opening</option>
                                @foreach($openings as $opening)
                                    <option value="{{ $opening->id }}">
                                        {{ $opening->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select border border-dark p-2" required>
                                @foreach(['New', 'Shortlisted', 'Interviewed', 'Hired', 'Rejected'] as $status)
                                    <option value="{{ $status }}" >
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="resume_file" class="form-label">Resume (Optional)</label>
                            <input type="file" name="resume_file" id="resume_file" class="form-control border border-dark p-2">
                           
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn bg-primary text-white">{{  'Save' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

           
@endsection