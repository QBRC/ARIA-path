@extends('layouts.app')

@section('page-title')
    {{ __('Profile') }}
@endsection

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">

                @if(session('succeed'))
                    <div class="alert alert-success flash mb-5" role="alert">
                        <a href="#" class="close" data-dismiss="alert" arial-lable="close">&times;</a>
                        <strong>{{ session('succeed') }}</strong>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <div class="d-flex">
                            <div>
                                {{ __('Detail') }}
                            </div>
{{--                            <div class="ml-auto font-weight-bold">--}}
{{--                                @if($user->role > 1 && Auth::user()->role == 1)--}}
{{--                                    @if($user->is_delete == 0)--}}
{{--                                        <a href="{{ url('/users/'.$user->id.'/edit') }}" class="btn btn-primary btn-sm" title="Edit" alt="Edit" data-toggle="tooltip" data-placement="auto">--}}
{{--                                            <i class="fas fa-edit"></i> Edit--}}
{{--                                        </a>--}}

{{--                                        <a href="{{ url('/users/'.$user->id.'/delete') }}" class="btn btn-danger btn-sm ml-3" title="Delete" alt="Delete" data-toggle="tooltip" data-placement="auto">--}}
{{--                                            <i class="fas fa-trash-alt"></i> Delete--}}
{{--                                        </a>--}}
{{--                                    @else--}}
{{--                                        <a href="{{ url('/users/'.$user->id.'/restore') }}" class="btn btn-primary btn-sm" title="Restore" alt="Restore" data-toggle="tooltip" data-placement="auto">--}}
{{--                                            <i class="fas fa-undo-alt"></i> Restore--}}
{{--                                        </a>--}}
{{--                                    @endif--}}
{{--                                @endif--}}
{{--                            </div>--}}
                        </div>
                    </div>

                    <div class="card-body">

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
                                {{ config('app.iv.user.role')[$user->role] }}
                            </div>
                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <h6 class="mb-0 font-weight-bold">Group</h6>
                            </div>
                            <div class="col-sm-9 text-secondary">
                                {{ $user->group->name }}
                            </div>
                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <h6 class="mb-0 font-weight-bold">Status</h6>
                            </div>
                            <div class="col-sm-9 text-secondary">
                                {{ config('app.iv.delete')[$user->is_delete] }}
                            </div>
                        </div>

                    @if($user->role > 2)
                            <hr>

                            <h6 class="mb-4 font-weight-bold">Current Permission</h6>

                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                    <tr>
                                        <th class="border-top-0 text-gray-500">Project Name</th>
                                        <th class="border-top-0 text-gray-500">Authorization</th>
                                    </tr>
                                    </thead>

                                    <tbody>

{{--                                    @foreach($projects as $project)--}}
{{--                                        <tr>--}}
{{--                                            <td>{{ $project->name }}</td>--}}
{{--                                            <td>--}}
{{--                                                @for($i=1; $i<=3; $i++)--}}
{{--                                                    @if(isset($authority[$project->id]) && $i<=$authority[$project->id])--}}
{{--                                                        <i class="fas fa-check text-success {{ $i==1?"":"ml-4" }}"></i> <span class="text-success">{{ config('app.iv.project.permission')[$i] }}</span>--}}
{{--                                                    @else--}}
{{--                                                        <i class="far fa-times-circle {{ $i==1?"":"ml-4" }}"></i> {{ config('app.iv.project.permission')[$i] }}--}}
{{--                                                    @endif--}}
{{--                                                @endfor--}}
{{--                                            </td>--}}
{{--                                        </tr>--}}
{{--                                    @endforeach--}}

                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
