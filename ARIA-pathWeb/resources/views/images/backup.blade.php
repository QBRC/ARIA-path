@extends('layouts.app')

@section('page-title')
    Database Backup
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
{{--                        <h6 class="m-0 font-weight-bold text-primary">--}}
{{--                            <a href="" data-toggle="modal" data-target="#addModal"--}}
{{--                               class="btn btn-primary btn-icon-split">--}}
{{--                                <span class="text"><i class="fas fa-plus mr-1"></i> Create a new backup</span>--}}
{{--                            </a>--}}
{{--                        </h6>--}}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>File Name</th>
                        <th>Creator</th>
                        <th>Create Date</th>
                    </tr>
                    </thead>

                    <tbody>

                    @foreach($files as $k=>$file)

                        <tr>
                            <td>{{ $file['file'] }}</td>
                            <td>{{ $file['creator'] }}</td>
                            <td>{{ $file['time'] }}</td>
                        </tr>

                    @endforeach


                    </tbody>
                </table>
            </div>

        </div>
    </div>

@endsection


@section('scripts')

    <script>
        $(document).ready(function () {
            $('#dataTable').DataTable({
                "scrollX": true,
                "order": [[ 2, "desc" ]],
                // columnDefs: [
                //     {bSortable: false, targets: [3]} // Disable sorting on columns
                // ],
            });


        });
    </script>

@stop
