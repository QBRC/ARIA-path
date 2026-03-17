@extends('layouts.app')

@section('page-title')
    Activity Log
@endsection

@section('content')
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
{{--                <a href="{{ url('/'.$name['plural'].'/create') }}" class="btn btn-primary btn-icon-split">--}}
{{--                    <span class="text">Add new {{ $name['singular'] }}</span>--}}
{{--                </a>--}}
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Item</th>
                        <th>Causer</th>
                        <th>Action</th>
                        <th>Changes</th>
                        <th>Date/Time</th>
                        <th></th>
                    </tr>
                    </thead>

                    <tbody>
{{--                    {{ dd($logs) }}--}}
                    @foreach($logs as $log)
                        <tr class="text-secondary">
{{--                            @if(empty($log->subject_type::find($log->subject_id)))--}}
{{--                                {{ dd($log) }}--}}
{{--                            @endif--}}
                        @if(in_array($log->log_name, ['Data record', 'Server', 'Project', 'Category', 'Status', 'User']))
                            <td>{{ $log->log_name }}</td>
                            <td>{{ is_null($log->subject_type)?'':$log->subject_type::find($log->subject_id)->name }}</td>
                            <td>{{ $log->causer_type::find($log->causer_id)->name }}</td>
                            <td>{{ $log->description == 'deleted' ? '<i class="fas fa-exclamation text-danger"></i> ':''. ucfirst($log->description) }}</td>
                            <td>{!! $log->show_changes !!}</td>
                            <td>{{ $log->created_at }}</td>
                        @elseif(in_array($log->log_name, ['Permission']))
                            <td>{{ $log->log_name }}</td>
                            <td>{{ $log->item_name }}</td>
                            <td>{{ $log->causer_type::find($log->causer_id)->name }}</td>
                            <td>{{ $log->description == 'deleted' ? '<i class="fas fa-exclamation text-danger"></i> ':''. ucfirst($log->description) }}</td>
                            <td>{!! $log->show_changes !!}</td>
                            <td>{{ $log->created_at }}</td>
                        @elseif(in_array($log->log_name, ['default']))
                            <td>{{ $log->getExtraProperty('name') }}</td>
                            <td>Record(s) of {{ $log->subject_type::find($log->getExtraProperty('gid'))->name }} ({{ ucfirst($log->getExtraProperty('group')) }})</td>
                                <td>{{ $log->causer_type::find($log->causer_id)->name }}</td>
                            <td>{{ ucfirst($log->description) }}</td>
                            <td></td>
                            <td>{{ $log->created_at }}</td>
                        @endif


                        @if(in_array($log->description, ['batch-delete']))
                            <td><a href="{{ url('datarecords/'.$log->getExtraProperty('group').'/'.$log->getExtraProperty('gid').'/show') }}" title="View" alt="View" data-toggle="tooltip" data-placement="auto"><i class="far fa-eye"></i></a></td>
                        @else
                            <td><a href="{{ url(config('app.iv.log.name')[$log->log_name].'/'.$log->subject_id) }}" title="View" alt="View" data-toggle="tooltip" data-placement="auto"><i class="far fa-eye"></i></a></td>
                        @endif
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
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "scrollX": true,
                "order": [[ 5, "desc" ]],
                columnDefs: [
                    {bSortable: false, targets: [6]} // Disable sorting on columns
                ],
            });

        });
    </script>
@stop
