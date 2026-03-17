@extends('layouts.app')

@section('page-title')
    Teams
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
            <h6 class="m-0 font-weight-bold text-primary">
                <a href="" data-toggle="modal" data-target="#addModal" class="btn btn-primary btn-icon-split">
                    <span class="text"><i class="fas fa-plus mr-1"></i> Add new team</span>
                </a>
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Team</th>
                        <th>Institution</th>
                        <th>PI</th>
                        <th>Members</th>
                        <th>Batches</th>
                        <th>Status</th>
                        <th>Create Date</th>
                        <th>Update Date</th>
                        <th>Actions</th>
                    </tr>
                    </thead>

                    <tbody>

                    @foreach($groups as $group)

                            <tr>
                                <td>{{ $group->id }}</td>
                                <td>{{ $group->name }}</td>
                                <td>{{ $group->org }}</td>
                                <td>{{ $group->pi }}</td>
                                <td>
                                    <ul class="pl-1">
                                        @forelse($group->users as $user)
                                            <li>{{ $user->name }}</li>
                                        @empty
                                            None
                                        @endforelse
                                    </ul>
                                </td>
                                <td>
                                    <ul class="pl-1">
                                        @forelse($group->showBatches as $batch)
                                            <li>{{ $batch->name }}</li>
                                        @empty
                                            None
                                        @endforelse

                                    </ul>
                                </td>
                                <td class="{{ $group->is_delete == 1 ? 'text-danger font-weight-bold' : '' }}">{{ config('app.iv.delete')[$group->is_delete] }}</td>
                                <td>{{ $group->created_at }}</td>
                                <td>{{ $group->updated_at }}</td>
                                <td>
                                    @if(Auth::user()->role == 1)
                                        @if($group->is_delete == 0)
                                            <a href="" data-toggle="modal" data-target="#editModal{{ $group->id }}" class="btn text-success btn-icon-split">
                                                <i class="fas fa-edit" title="Edit" alt="Edit" data-toggle="tooltip" data-placement="auto" title="Edit"></i>
                                            </a>

                                            <a href="" data-toggle="modal" data-target="#deleteModal{{ $group->id }}" class="btn text-danger btn-icon-split ml-3" alt="Disable group">
                                                <i class="fas fa-ban" title="Disable {{ $group->name }}" alt="Disable {{ $group->name }}" data-toggle="tooltip" data-placement="auto"></i>
                                            </a>
                                        @else
                                            <a href="{{ url('groups/'.$group->id.'/restore') }}" class="btn text-primary btn-icon-split" title="Activate" alt="Activate" data-toggle="tooltip" data-placement="auto">
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

    <small class="ml-3">* Data/Batches are directly associated to teams but not individual user. All members in a team have same permissions.</small>


    <!-- dialog Modal-->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add new team</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <form action="{{ url('groups') }}" method="POST">
                    @csrf

                    <div class="modal-body px-5">
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">Team <span class="text-danger">*</span></label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Team name" required autofocus>

                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="org" class="col-md-4 col-form-label text-md-right">Institution</label>

                            <div class="col-md-6">
                                <input id="org" type="text" class="form-control @error('org') is-invalid @enderror" name="org" placeholder="Institution name" autofocus>

                                @error('org')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="pi" class="col-md-4 col-form-label text-md-right">PI</label>

                            <div class="col-md-6">
                                <input id="pi" type="text" class="form-control @error('pi') is-invalid @enderror" name="pi" placeholder="Principal investigator" autofocus>

                                @error('pi')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
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


    @foreach($groups as $group)
        <div class="modal fade" id="editModal{{ $group->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Update team</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>

                    <form action="{{ url('groups/'.$group->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-body px-5">
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0 font-weight-bold">Team</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <input id="name" name="name" placeholder="Team name" value="{{ old('name', $group->name) }}" type="text" class="form-control @error('name') is-invalid @enderror" required autofocus>

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
                                    <h6 class="mb-0 font-weight-bold">PI</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <input id="pi" name="pi" placeholder="Principal investigator" value="{{ old('name', $group->pi) }}" type="text" class="form-control @error('pi') is-invalid @enderror" autofocus>

                                    @error('pi')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0 font-weight-bold">Institution</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <input id="org" name="org" placeholder="Institution name" value="{{ old('name', $group->org) }}" type="text" class="form-control @error('org') is-invalid @enderror" autofocus>

                                    @error('org')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0 font-weight-bold">Permission</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <ul class="list-group list-group-flush">
                                        @foreach($batches as $batch)
                                        <li class="list-group-item check-permission">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="{{ $batch->id }}" id="batch" name="batch[]"
                                                       style="width: 20px; height: 20px;margin-left: -1.6rem;"
                                                    {{ ( isset($permission[$group->id]) && in_array($batch->id, $permission[$group->id])) ? 'checked':'' }}
                                                    {{ ( isset($provider[$group->id][$batch->id]) && $provider[$group->id][$batch->id] == 1) ? 'disabled':'' }}
                                                >
                                                <label class="form-check-label" for="flexCheckDefault">
                                                    <img src="{{ asset('img/viewer/folderblue.png') }}" width="17px" alt="Project folder" class="img-fluid">
                                                    {{ $batch->project }} / <img src="{{ asset('img/viewer/folderyellow.png') }}" width="17px" alt="Dataset folder" class="img-fluid">
                                                    {{ $batch->name }}
                                                </label>
                                            </div>
                                        </li>
                                        @endforeach
                                    </ul>
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

        <div class="modal fade" id="deleteModal{{ $group->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ url('groups/'.$group->id.'/delete') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Are you sure to delete {{ $group->name }}?</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            @if(count($group->users) > 0)
                                {{ $group->name }} contains {{ count($group->users)==1 ? '1 user' : count($group->users).' users' }} which would be moved to:
                                <div class="d-inline">
                                    <select id="new" name="new" class="form-control w-50 mt-2" required>
                                        <option value="0">Trash bin</option>
                                        @foreach($groups as $v)
                                            @if($group->id != $v->id)
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

            // $('li.check-permission').click(function(e){
            //     var checkbox=$(this).find('input');
            //     if(!checkbox.prop("disabled")){
            //         checkbox.prop("checked", !checkbox.prop("checked"));
            //     }
            //
            // });
        });
    </script>
@stop
