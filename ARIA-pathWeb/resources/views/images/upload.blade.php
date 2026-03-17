@extends('layouts.app')

@section('page-title')
    Import
@endsection

@section('style')
    <style>
        .circleNumber {
            background: #4B6073;
            border-radius: 3em;
            -moz-border-radius: 3em;
            -webkit-border-radius: 3em;
            color: #ffffff;
            height: 1.8em;
            width: 1.8em;
            display: inline-block;
            font-weight: bold;
            font-family: 'Roboto', Helvetica, Sans-serif;
            line-height: 2em;
            text-align: center;
        }
    </style>
@endsection

@section('content')

    @if(session('succeed'))
        <div class="alert alert-success flash mb-3" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <strong>{{ session('succeed') }}</strong>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger flash mb-3" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">


        </div>


        <div class="card-body">
            <h4>
                <span class="circleNumber bg-primary">1</span> <span
                    class="text-primary font-weight-bold">Upload Image</span>
            </h4>
            Please upload the images to server. Contact the administrator if you have any questions.

            <h4 class="mt-5">
                <span class="circleNumber bg-primary">2</span> <span class="text-primary font-weight-bold">Import Information</span>
            </h4>
            Please download and fill out the <a href="{{ asset('input_template.csv') }}">input template</a>, then upload it.
            <form id="uploadForm" action="{{ route('upload.handle') }}" method="post" enctype="multipart/form-data"
                  class="w-50">
                @csrf
                <div class="input-group mb-3">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="fileInput" name="file" accept=".csv">
                        <label class="custom-file-label" for="fileInput">Choose file</label>
                    </div>
                    <div class="input-group-append">
{{--                        <button type="button" class="btn btn-primary" onclick="uploadFile()">Upload</button>--}}
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </div>
            </form>

            <h4 class="mt-5">
                <span class="circleNumber bg-primary">3</span> <span class="text-primary font-weight-bold">Review & Submit</span>
            </h4>

            @if(session('result'))
                @php
                    $data = session('result');
                @endphp


                @switch($data['status'])
                    @case(1)
                        <div class="alert alert-warning mb-3" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                            <strong>Please ensure that step 1 is completed and verify the accuracy of the path and image name.
                            <ul>
                                <li><i class="fas fa-question-circle text-danger"></i> The image could not be located on the server.</li>
                                <li><i class="fas fa-exclamation-circle text-warning"></i> The image already exist in the database.</li>
                                <li><i class="fas fa-exclamation-triangle text-secondary"></i> The image name is invalid. (must only contain letters, numbers, underscores, dots and dashes)</li>
                            </ul>
                            </strong>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                <tr>
                                    <th></th>
                                    @foreach($data['header'] as $v)
                                        <th>{{$v}}</th>
                                    @endforeach
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($data['csv'] as $row)

                                    <tr>
                                        @if($row['exist']==1)
                                            <th><i class="fas fa-check-circle text-success"></i></th>
                                        @elseif($row['exist']==2)
                                            <th><i class="fas fa-exclamation-circle text-warning"></i></th>
                                        @elseif($row['exist']==3)
                                            <th><i class="fas fa-exclamation-triangle text-secondary"></i></th>
                                        @elseif($row['exist']==0)
                                            <th><i class="fas fa-question-circle text-danger"></i></th>
                                        @endif

                                        @foreach($row as $k=>$v)
                                            @if(in_array($k, $data['header']))
                                                <td>{{ $v }}</td>
                                            @endif
                                        @endforeach
                                    </tr>

                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <button type="button" class="btn btn-primary" disabled>Submit</button>

                        @break

                    @case(2)
                        <div class="alert alert-warning mb-3" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                            <strong>The header of uploaded file doesn't match the input template!</strong>
                        </div>
                        @break

                    @case(9)
                        <div class="alert alert-success mb-3" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                            <strong>Your uploaded image information successfully passed all validation check!</strong>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                <tr>
                                    <th></th>
                                    @foreach($data['header'] as $v)
                                        <th>{{$v}}</th>
                                    @endforeach
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($data['csv'] as $row)

                                    <tr>
                                        @if($row['exist'])
                                            <th><i class="fas fa-check-circle text-success"></i></th>
                                        @else
                                            <th><i class="fas fa-question-circle text-danger"></i></th>
                                        @endif

                                        @foreach($row as $k=>$v)
                                            @if(in_array($k, $data['header']))
                                                <td>{{ $v }}</td>
                                            @endif
                                        @endforeach
                                    </tr>

                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <hr>
                        <form action="{{ route('upload.import') }}" method="POST">
                            @csrf
                            <input type="hidden" name="imageInfo" value="{{ json_encode($data['csv']) }}">
                            <div class="form-group row">
                                <div class="col-md-3 text-right d-flex justify-content-center align-items-center">
                                    <label for="provider" class="font-weight-bold text-primary mb-0">Who owns the uploaded image(s):</label>
                                </div>
                                <div class="col-md-9">
                                    <select class="form-control @error('provider') is-invalid @enderror" name="provider" id="provider" required>
                                        <option value="">Select Team</option>
                                        @foreach($data['provider'] as $k=>$v)
                                            <option value="{{$k}}">{{$v}}</option>
                                        @endforeach
                                    </select>
                                    @error('provider')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>


                        @break

                    @default
                        <div class="alert alert-danger flash mb-3" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                            <strong>An unexpected error occurred, please try it again!</strong>
                        </div>
                @endswitch
            @endif


        </div>
    </div>

@endsection


@section('scripts')

    <script>
        $(document).ready(function () {
            $('#dataTable').DataTable({
                "scrollX": true,
                // "order": [[ 8, "desc" ],[ 7, "desc" ]],
                // columnDefs: [
                //     {bSortable: false, targets: [9]} // Disable sorting on columns
                // ],
            });

            // Update the display of the selected file name when a file is chosen
            $('#fileInput').change(function () {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').html(fileName);
            });
        });

        function importImage() {
            console.log("import");
        }

        function uploadFile() {
            let formData = new FormData(document.getElementById('uploadForm'));

            $.ajax({
                type: 'POST',
                url: '{{ route('upload.handle') }}',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    // Handle success response
                    console.log(response);
                    alert('File uploaded successfully!');
                },
                error: function (error) {
                    // Handle error response
                    console.log(error.responseJSON);
                    alert('File upload failed. Please check the file and try again.');
                }
            });
        }
    </script>

@stop
