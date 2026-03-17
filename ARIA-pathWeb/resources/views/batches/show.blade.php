@extends('layouts.app')

@section('page-title')
    {{ ucfirst($name['model']) }}
@endsection

@section('content')
    <div class="container my-5">
        <div class="row flex justify-content-center pt-20">
            <div class="col-md-10">


                @if(session('succeed'))
                    <div class="alert alert-success flash mb-3" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>{{ session('succeed') }}</strong>
                    </div>
                @endif

                @if($record->num > 0)
                    <div class="alert alert-warning mb-5">
                        <i class="fas fa-exclamation-triangle"></i> We find <a href="{{ url('datarecords/'.$name['model'] .'/'. $record->id) }}" class="alert-link">{{ $record->num }} {{ $record->num>1?'records':'record' }} {{ $record->delnum>0? "(".$record->delnum." of them ".($record->delnum>1?"are ":"is ")." deleted)":"" }}</a> which {{ $record->num>1?'are':'is' }} associated with <strong>{{ $record->name }}</strong>.
                    </div>
                @endif

                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex">
                                <div>
                                    {{ __('Detail') }}
                                </div>
                                <div class="ml-auto">
                                    @if($record->is_delete == 0)
                                        <a href="{{ url('/'.$name['route'].'/'.$record->id.'/edit') }}" class="btn btn-primary btn-sm" title="Edit" alt="Edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>

                                        <a href="" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm ml-3" alt="Delete">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </a>
                                    @else
                                        <a href="{{ url('/'.$name['route'].'/'.$record->id.'/restore') }}" class="btn btn-primary btn-sm" title="Restore" alt="Restore">
                                            <i class="fas fa-undo-alt"></i> Restore
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="card-body">

                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0 font-weight-bold">{{ ucfirst($name['model']) }} Name</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    {{ $record->name }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0 font-weight-bold">Created at</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    {{ $record->created_at }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0 font-weight-bold">Updated at</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    {{ $record->updated_at }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0 font-weight-bold">Status</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    {{ $record->is_delete == 0 ? 'Active' : 'Deleted' }}
                                </div>
                            </div>
                        </div>
                    </div>

            </div>
        </div>
    </div>


    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ url($name['route'].'/'.$record->id.'/delete') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" id="old_id" name="old_id" value="{{$record->id}}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Are you sure to delete <strong class="text-primary">{{ $record->name }}</strong>?</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        We find <a href="{{ url('datarecords/'.$name['model'].'/'.$record->id.'/show') }}" class="alert-link">{{ $record->num }} {{ $record->num>1?'records':'record' }} {{ $record->delnum>0? "(".$record->delnum." of them ".($record->delnum>1?"are ":"is ")." deleted)":"" }}</a> which {{ $record->num>1?'are':'is' }} associated with <strong>{{ $record->name }}</strong>.
                        You need to assign these records to:
                        <div class="d-inline">
                            <select id="new_id" name="new_id" class="form-control w-50 mt-2" required>
                                @foreach($list as $v)
                                    @if($v->id != $record->id)
                                        <option value="{{$v->id}}">{{$v->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline-secondary mr-3" type="button" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
