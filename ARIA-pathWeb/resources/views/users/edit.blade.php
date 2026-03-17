@extends('layouts.app')

@section('page-title')
    {{ __('Update User') }}
@endsection

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">

            @if(session('succeed'))
                <div class="alert alert-success flash mb-5" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>{{ session('succeed') }}</strong>
                </div>
            @endif

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
                <form action="{{ url('/users/'.$user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
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
                            <h6 class="mb-0 font-weight-bold">Affiliation</h6>
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

{{--                     Grant specific permission on projects --}}

{{--                    <div class="authorization">--}}
{{--                        <hr>--}}

{{--                        <h6 class="mb-4 font-weight-bold">Permission</h6>--}}

{{--                        <div class="table-responsive">--}}
{{--                            <table class="table table-striped table-hover" id="dataTable1" width="100%" cellspacing="0">--}}
{{--                                <thead>--}}
{{--                                <tr>--}}
{{--                                    <th class="border-top-0 text-gray-500">Project Name</th>--}}
{{--                                    <th class="border-top-0 text-gray-500">Authorization</th>--}}
{{--                                    <th></th>--}}
{{--                                </tr>--}}
{{--                                </thead>--}}

{{--                                <tbody>--}}

{{--                                @foreach($projects as $project)--}}
{{--                                    <tr>--}}
{{--                                        <td>{{ $project->name }}</td>--}}
{{--                                        <td>--}}
{{--                                            @for($i=1; $i<=3; $i++)--}}
{{--                                                @if(isset($authority[$project->id]) && $i<=$authority[$project->id])--}}
{{--                                                    <i class="fas fa-check text-success {{ $i==1?"":"ml-4" }}"></i> <span class="text-success">{{ config('app.iv.project.permission')[$i] }}</span>--}}
{{--                                                @else--}}
{{--                                                    <i class="far fa-times-circle {{ $i==1?"":"ml-4" }}"></i> {{ config('app.iv.project.permission')[$i] }}--}}
{{--                                                @endif--}}
{{--                                            @endfor--}}
{{--                                        </td>--}}
{{--                                        <td>--}}
{{--                                            <a href="" data-toggle="modal" data-target="#editModal{{ $project->id }}"  class="btn text-primary btn-icon-split">--}}
{{--                                                <i class="fas fa-edit"></i>--}}
{{--                                            </a>--}}
{{--                                        </td>--}}
{{--                                    </tr>--}}
{{--                                @endforeach--}}

{{--                                </tbody>--}}
{{--                            </table>--}}
{{--                        </div>--}}

{{--                    </div>--}}

                    <div class="mt-4">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary mr-3">Back</a>
                        <button type="submit" class="btn btn-primary">
                            {{ __('Submit') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <!-- dialog Modal-->
{{--    @foreach($projects as $project)--}}
{{--        <div class="modal fade" id="editModal{{ $project->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"--}}
{{--             aria-hidden="true">--}}
{{--            <div class="modal-dialog" role="document">--}}
{{--                <div class="modal-content">--}}
{{--                    <form action="{{ route('authorize') }}" method="POST">--}}
{{--                        @csrf--}}

{{--                        <input type="hidden" id="user_id" name="user_id" value="{{$user->id}}">--}}
{{--                        <input type="hidden" id="user_role" name="user_role" value="{{$user->role}}">--}}
{{--                        <input type="hidden" id="project_id" name="project_id" value="{{$project->id}}">--}}
{{--                        <div class="modal-header">--}}
{{--                            <h5 class="modal-title" id="exampleModalLabel">Grant <strong>{{ $user->name }}</strong> permission on <strong>{{$project->name}}</strong></h5>--}}
{{--                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">--}}
{{--                                <span aria-hidden="true">×</span>--}}
{{--                            </button>--}}
{{--                        </div>--}}
{{--                        <div class="modal-body">--}}
{{--                            <ul class="list-group list-group-flush">--}}
{{--                            @for($i=3; $i>=0; $i--)--}}
{{--                                <li class="list-group-item radio-check">--}}
{{--                                    <input type="radio" value="{{$i}}" name="permission"--}}
{{--                                        {{ (isset($authority[$project->id]) && $i==$authority[$project->id])?"checked":"" }}>--}}

{{--                                    @if($i==0)--}}
{{--                                        <span class="ml-3">{{ config('app.iv.project.permission')[0] }}</span>--}}
{{--                                    @else--}}
{{--                                        @for($j=1; $j<=$i; $j++)--}}
{{--                                            <span class="ml-3">{{ config('app.iv.project.permission')[$j] }}</span>--}}
{{--                                        @endfor--}}
{{--                                    @endif--}}

{{--                                </li>--}}
{{--                            @endfor--}}
{{--                            </ul>--}}
{{--                        </div>--}}
{{--                        <div class="modal-footer">--}}
{{--                            <button class="btn btn-outline-secondary mr-3" type="button" data-dismiss="modal">Cancel</button>--}}
{{--                            <button type="submit" class="btn btn-primary">Confirm</button>--}}
{{--                        </div>--}}
{{--                    </form>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    @endforeach--}}


@endsection


@section('scripts')
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "scrollX": true,
                "order": [0, "asc"],
                columnDefs: [
                    {bSortable: false, targets: [1,2]} // Disable sorting on columns
                ],
            });

            $('li.radio-check').click(function(e){
                $(this).find('input').prop('checked', 'checked');
            });

        //    Edit<Create<Delete
            @if($user->role>1 && $user->role<4)
                $(".authorization").show();
            @else
                $(".authorization").hide();
            @endif

            $('select#role').on('change', function () {
                if ($(this).val() < 4 && $(this).val() > 1){
                    $(".authorization").show();
                }else {
                    $(".authorization").hide();
                }
            });
        });
    </script>
@stop
