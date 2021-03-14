@extends('backend.layouts.master')
@section('title')
    User create Page
@endsection
@section('css')

@endsection
<!-- page content -->
@section('content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>User Management </h3>
                </div>
                <div class="title_right">
                    <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                        <div class="col-md-5 col-sm-5 col-xs-12 form-group top_search" style="padding-left: 130px;">
                           {{-- <div class="input-group">
                                <a href="{{route('product.list')}}" class="btn btn-success">View Product</a>
                            </div>--}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            @if(Session::has('success_message'))
                <div class="alert alert-success">
                    {{ Session::get('success_message') }}
                </div>
            @endif
            @if(Session::has('error_message'))
                <div class="alert alert-danger">
                    {{ Session::get('error_message') }}
                </div>
            @endif
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Edit User</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                       aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="#">Settings 1</a>
                                        </li>
                                        <li><a href="#">Settings 2</a>
                                        </li>
                                    </ul>
                                </li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <form action="{{route('user.update',$users->id)}}" method="post">
                                {{ csrf_field()}}
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" placeholder="Full Name" class="form-control" value="{{$users->name}}"/ requird>
                                    <input type="hidden" name="user_id" placeholder="Full Name" class="form-control" value="{{$users->id}}"/>
                                    <span class="error">
                                    @if($errors->has('name'))
                                        {{$errors->first('name')}}
                                    @endif
                                </span>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email*</label>
                                    <input type="email" name="email" placeholder="Email" class="form-control" value="{{$users->email}}"/>
                                    <span class="error">
                                    @if($errors->has('email'))
                                        {{$errors->first('email')}}
                                    @endif
                                </span>
                                </div>
                                <div class="form-group">
                                    <label for="username">User Name*</label>
                                    <input type="text" name="username" placeholder="Username" class="form-control" value="{{$users->username}}" readonly/>
                                    <span class="error">
                                        @if($errors->has('username'))
                                            {{$errors->first('username')}}
                                        @endif
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label for="role_id">Role*</label>
                                    <input type="text" name="role" placeholder="Role" class="form-control" value="{{$users->role_name}}" readonly/>
                                    <input type="hidden" name="role_id" placeholder="Role" class="form-control" value="{{$users->role_id}}"/>
                                   
                                    <span class="error">
                                    @if($errors->has('role'))
                                        {{$errors->first('role')}}
                                    @endif
                                </span>
                            </div>
                            <div class="form-group">
                                    <label for="role_id">Role*</label>
                                   <select class="form-control" id="role_id" name="newrole" style="height: 38px; width: 300px" class="form-control">
                                    <option value="">Select Role</option>

                                    @foreach($roles as $m)
                                    @if($m->name != 'Admin')
                                        <option value="{{$m->id}}">{{$m->name}}</option>
                                    @endif
                                    @endforeach
                                </select>
                                    <span class="error">
                                    @if($errors->has('role'))
                                        {{$errors->first('role')}}
                                    @endif
                                </span>
                            </div>
                                <div class="form-group">
                                    <label for="price">Password*</label>
                                    <input type="text" name="password" placeholder="Password" class="form-control" value="{{$users->hint_password}}"/>
                                    <span class="error">
                                    @if($errors->has('password'))
                                        {{$errors->first('password')}}
                                    @endif
                                </span>
                                </div>
                                <div class="form-group">
                                    <label for="username">Salary*</label>
                                    <input type="text" name="salary" placeholder="salary" value="{{$users->salary}}" class="form-control"/>
                                    <span class="error">
                                        @if($errors->has('salary'))
                                            {{$errors->first('salary')}}
                                        @endif
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label>Status</label>
                                    <input type="radio" name="status" value="1" id="Active" checked=""><label for="Active"> Active</label>
                                    <input type="radio" name="status" id="deactive" value="0"><label for="deactive">DeActive</label>
                                </div>
                                <!-- /.box-body -->
                                <div class="form-actions">
                                    <span class="pull-right"><a href="{{route('user.dashboard')}}" class="flip-link btn btn-info">Back to Dashboard </a></span>
                                    <span class="pull-left"><button type="submit" name="btnSave" class="btn btn-success"> Update</button></span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /page content -->
@endsection

@section('script')
    <script src="{{asset('backend/plugins/ckeditor/ckeditor.js')}}"></script>
    <script type="text/javascript">
        $(function(){
            var $foo = $('#name');
            var $bar = $('#slug');
            function onChange() {
                $bar.val($foo.val().replace(/\s+/g, '-').toLowerCase());
            };
            $('#name')
                .change(onChange)
                .keyup(onChange);
        });
    </script>
@endsection