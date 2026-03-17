@extends('layouts.app')

@section('page-title')
    {{ __('Batches') }}
@endsection

@section('content')

    @if(session('succeed'))
        <div class="alert alert-success flash mb-3" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>{{ session('succeed') }}</strong>
        </div>
    @endif

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
{{--            <h6 class="m-0 font-weight-bold text-primary">--}}
{{--                <a href="" data-toggle="modal" data-target="#addModal" class="btn btn-primary btn-icon-split">--}}
{{--                    <span class="text"><i class="fas fa-plus mr-1"></i> Add new batch</span>--}}
{{--                </a>--}}
{{--            </h6>--}}
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Project Name</th>
                        <th>Batch/Dataset Name</th>
                        <th>Provider</th>
                        <th># of Images</th>
                        <th>Teams</th>
                        <th>Status</th>
                        <th>Create Date</th>
                        <th>Update Date</th>
                        <th>Actions</th>
                    </tr>
                    </thead>

                    <tbody>

                    @foreach($batches as $k=>$batch)

                            <tr>
                                <td>{{ $batch->id }}</td>
                                <td>{{ $batch->project }}</td>
                                <td>{{ $batch->name }}</td>
                                <td>{{ $batch->providerName->name }}</td>
                                <td>{{ count($batch->images) }}</td>
                                <td>
                                    <ul class="pl-1">
                                        @forelse($batch->groups as $group)
                                            <li>{{ $group->name }}</li>
                                        @empty
                                            None
                                        @endforelse
                                    </ul>
                                </td>
                                <td class="{{ $batch->is_delete == 1 ? 'text-danger font-weight-bold' : '' }}">{{ config('app.iv.delete')[$batch->is_delete] }}</td>
                                <td>{{ $batch->created_at }}</td>
                                <td>{{ $batch->updated_at }}</td>
                                <td>
                                    @if(Auth::user()->role == 1 && $batch->id != 1)
                                        @if($batch->is_delete == 0)
{{--                                            <a href="" data-toggle="modal" data-target="#editModal{{ $batch->id }}" class="btn text-success btn-icon-split">--}}
{{--                                                <i class="fas fa-edit" title="Edit" alt="Edit" data-toggle="tooltip" data-placement="auto" title="Edit"></i>--}}
{{--                                            </a>--}}

                                            <a href="" data-toggle="modal" data-target="#deleteModal{{ $batch->id }}" class="btn text-danger btn-icon-split ml-3" alt="Delete">
                                                <i class="fas fa-ban" title="Deactivate" alt="Deactivate" data-toggle="tooltip" data-placement="auto"></i>
                                            </a>

                                        @else
                                            <a href="{{ url('batches/'.$batch->id.'/restore') }}" class="btn text-primary btn-icon-split" title="Activate" alt="Activate" data-toggle="tooltip" data-placement="auto">
                                                <i class="fas fa-undo-alt"></i>
                                            </a>
                                        @endif
                                    @endif

                                </td>
                            </tr>

                    @endforeach


                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- dialog Modal-->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add new batch</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <form action="{{ url('batches') }}" method="POST">
                    @csrf

                    <div class="modal-body px-5">
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">Batch name</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Batch name" required autofocus>

                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="role" class="col-md-4 col-form-label text-md-right">Provider</label>

                            <div class="col-md-6">
                                <select id="group" class="form-control" name="group" required>
                                    @foreach($groups as $group)
                                        <option value="{{$group->id}}">{{$group->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-outline-secondary mr-3" type="button" data-dismiss="modal">Cancel</button>

                        <button type="submit" class="btn btn-primary">
                            Create
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>


    @foreach($batches as $batch)
        <div class="modal fade" id="editModal{{ $batch->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Update batch</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>

                    <form action="{{ url('batches/'.$batch->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-body px-5">
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0 font-weight-bold">ID</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    {{ $batch->id }}
                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0 font-weight-bold">Name</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <input id="name" name="name" placeholder="Batch name" value="{{ old('name', $batch->name) }}" type="text" class="form-control @error('name') is-invalid @enderror" required autofocus>

                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0 font-weight-bold">Provider</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <select id="group" class="form-control form-control-sm w-50" name="group" required>
                                        @foreach($groups as $group)
                                            @if($batch->group_id == $group->id)
                                                <option value="{{$group->id}}" selected>{{$group->name}}</option>
                                            @else
                                                <option value="{{$group->id}}">{{$group->name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-outline-secondary mr-3" type="button" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                {{ __('Submit') }}
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>


        <div class="modal fade" id="deleteModal{{ $batch->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ url('batches/'.$batch->id.'/delete') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Are you sure to delete {{ $batch->name }}?</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            @if(count($batch->images) > 0)
                                {{ $batch->name }} contains {{ count($batch->images)==1 ? '1 record' : count($batch->images).' records' }} which would be moved to:
                                <div class="d-inline">
                                    <select id="new" name="new" class="form-control w-50 mt-2" required>
                                        <option value="0">Trash bin</option>
                                        @foreach($batches as $v)
                                            @if($batch->id != $v->id)
                                                <option value="{{$v->id}}">{{$v->name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            @endif


                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-outline-secondary mr-3" type="button" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Confirm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    @endforeach

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "scrollX": true,
                "order": [[ 8, "desc" ],[ 7, "desc" ]],
                columnDefs: [
                    {bSortable: false, targets: [9]} // Disable sorting on columns
                ],
            });

        });
    </script>
@stop
