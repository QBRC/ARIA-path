@extends('layouts.app')

@section('page-title')
    {{ __('Users') }}
@endsection

@section('content')
    <!-- DataTales Example -->
    @if(session('succeed'))
        <div class="alert alert-success flash mb-3" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>{{ session('succeed') }}</strong>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <a href="" data-toggle="modal" data-target="#addModal" class="btn btn-primary btn-icon-split">
                    <span class="text"><i class="fas fa-plus mr-1"></i> Add new user</span>
                </a>
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Team</th>
                        <th>Role</th>
                        <th>Last Login Time</th>
                        <th>Last Login IP</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th>Updated Date</th>
                        <th>Actions</th>
                    </tr>
                    </thead>

                    <tbody>

                    @foreach($users as $k=>$user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
{{--                            <td><a href="{{ url('groups/'.$user->group_id) }}">{{ $user->group }}</a></td>--}}
                            <td>{{ $user->group->name }}</td>
                            <td>{{ config('app.iv.user.role')[$user->role] }}</td>
                            <td>{{ $user->last_login_at }}</td>
                            <td>{{ $user->last_login_ip }}</td>
                            <td class="{{ $user->is_delete == 1 ? 'text-danger font-weight-bold' : '' }}">{{ config('app.iv.delete')[$user->is_delete] }}</td>
                            <td>{{ $user->created_at->toDateString() }}</td>
                            <td>{{ is_null($user->updated_at) ? $user->updated_at : $user->updated_at->toDateString() }}</td>
                            <td>
                                @if($user->role != 1 && Auth::user()->role == 1)
                                    @if($user->is_delete == 1)
                                        <a href="{{ url('users/'.$user->id.'/restore') }}" class="btn text-primary btn-icon-split" title="Activate" alt="Activate" data-toggle="tooltip" data-placement="auto" title="Activate">
                                            <i class="fas fa-undo-alt"></i>
                                        </a>
                                    @else
                                        <a href="" data-toggle="modal" data-target="#editModal{{ $k }}" class="btn text-success btn-icon-split">
                                            <i class="fas fa-edit" title="Edit" alt="Edit" data-toggle="tooltip" data-placement="auto" title="Edit"></i>
                                        </a>

                                        <a href="{{ url('users/'.$user->id.'/delete') }}" class="btn text-danger btn-icon-split ml-3" title="Deactivate" alt="Deactivate" data-toggle="tooltip" data-placement="auto" title="Deactivate">
                                            <i class="fas fa-ban"></i>
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
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add new user</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <form action="{{ url('users') }}" method="POST">
                    @csrf

                    <div class="modal-body px-5">
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="role" class="col-md-4 col-form-label text-md-right">Team</label>

                            <div class="col-md-6">
                                <select id="group" class="form-control" name="group" required>
                                    @foreach($groups as $group)
                                        <option value="{{$group->id}}">{{$group->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="role" class="col-md-4 col-form-label text-md-right">{{ __('Role') }}</label>

                            <div class="col-md-6">
                                <select id="role" class="form-control" name="role" required>
                                    @foreach(config('app.iv.user.role') as $k=>$v)
                                        @if($k>1)
                                            <option value="{{$k}}">{{$v}}</option>
                                        @endif
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

        @foreach($users as $k=>$user)
            <div class="modal fade" id="editModal{{ $k }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Update user profile</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>

                        <form action="{{ url('/users/'.$user->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                        <div class="modal-body px-5">
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0 font-weight-bold">Name</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    {{ $user->name }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0 font-weight-bold">Email</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    {{ $user->email }}
                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0 font-weight-bold">Role</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <select id="role" class="form-control form-control-sm w-50" name="role" required>
                                        @foreach(config('app.iv.user.role') as $k=>$v)
                                            @if($k>1)
                                                @if($user->role == $k)
                                                    <option value="{{$k}}" selected>{{$v}}</option>
                                                @else
                                                    <option value="{{$k}}">{{$v}}</option>
                                                @endif
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0 font-weight-bold">Team</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <select id="group" class="form-control form-control-sm w-50" name="group" required>
                                        @foreach($groups as $group)
                                            @if($user->group_id == $group->id)
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

{{--            <div class="modal fade" id="eraseModal{{ $k }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"--}}
{{--                 aria-hidden="true">--}}
{{--                <div class="modal-dialog" role="document">--}}
{{--                    <div class="modal-content">--}}
{{--                        <form action="{{ url('datarecords/'.$name['model'].'/'.$batch->id.'/erase') }}" method="POST">--}}
{{--                            @csrf--}}
{{--                            @method('PUT')--}}

{{--                            <input type="hidden" id="old_id" name="old_id" value="{{$batch->id}}">--}}
{{--                            <div class="modal-header">--}}
{{--                                <h5 class="modal-title" id="exampleModalLabel">Confirmation</h5>--}}
{{--                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">--}}
{{--                                    <span aria-hidden="true">×</span>--}}
{{--                                </button>--}}
{{--                            </div>--}}
{{--                            <div class="modal-body">--}}
{{--                                Are you sure to delete <a href="{{ url('datarecords/'.$name['model'].'/'.$batch->id.'/show') }}" class="alert-link">{{ $batch->num }} {{ $batch->num>1?'records':'record' }} {{ $batch->delnum>0? "(".$batch->delnum." of them ".($batch->delnum>1?"are ":"is ")." deleted)":"" }}</a> which {{ $batch->num>1?'are':'is' }} associated with <strong>{{ $batch->name }}</strong>?--}}

{{--                            </div>--}}
{{--                            <div class="modal-footer">--}}
{{--                                <button class="btn btn-outline-secondary mr-3" type="button" data-dismiss="modal">Cancel</button>--}}
{{--                                <button type="submit" class="btn btn-danger">Delete</button>--}}
{{--                            </div>--}}
{{--                        </form>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

        @endforeach

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "order": [[ 8, "desc" ],[ 7, "desc" ]],
                columnDefs: [{
                    "targets": [ 9 ],
                    "orderable": false
                }],
            });

        });
    </script>
@stop
