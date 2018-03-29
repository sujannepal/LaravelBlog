@extends('layouts.app')
<style type="text/css" media="screen">
    .avatar{
        border-radius: 100%;
        max-width: 100px;
    }
</style>

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            @if(count($errors) > 0 )
                @foreach($errors->all() as $error)
                    <div class="alert alert-danger">{{$error}}</div>
                @endforeach
            @endif

            @if(session('response'))
                <div class="alert alert-success">{{session('response')}}</div>
            @endif

            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-md-4">Dashboard</div>
                        <div class="col-md-8">
                            <form method="POST" action='{{ url("/search") }}'>
                            {{ csrf_field() }}
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="search here...">
                                        <span class="input-group-btn">
                                            <button type="submit" class="btn btn-default">
                                                Go!
                                            </button>
                                        </span>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="panel-body">
                   
                    <div class="col-md-4">

                        @if(!empty($profile))
                            <img src="{{ $profile->profile_pic }}" alt="Avatar" class="avatar">
                        @else
                            <img src="{{ url('images/avatar.png') }}" alt="Avatar" class="avatar">
                        @endif

                        @if(!empty($profile))
                            <p class="lead"> {{ $profile->name }} </p>
                        @else
                            <p> </p>
                        @endif

                        @if(!empty($profile))
                            <p class="lead"> {{ $profile->designation }} </p>
                        @else
                            <p> </p>
                        @endif



                    </div>
                <div class="col-md-8">
                    @if(count($posts) > 0)
                        @foreach($posts->all() as $post)
                            <h4>{{$post->post_title}}</h4>
                            <img src="{{$post->post_image}}" alt="post Image" style="max-height: 200px; width: auto;" />
                            <p>{{ substr($post->post_body, 0, 150) }}</p>

                            <ul class="nav nav-pills">
                                <li role="presentation">
                                    <a href='{{ url("/view/{$post->id}") }}'>
                                        <span class="fas fa-eye"> VIEW</span>
                                    </a>
                                </li>

                                @if(Auth::id() == 1)
                                    <li role="presentation">
                                        <a href='{{ url("/edit/{$post->id}") }}'>
                                            <span class="fas fa-edit"> EDIT</span>
                                        </a>
                                    </li>

                                    <li role="presentation">
                                        <a href='{{ url("/delete/{$post->id}") }}'>
                                            <span class="fas fa-minus-circle"> DELETE</span>
                                        </a>
                                    </li>

                                @endif


                            </ul>


                            <cite style="float: left;">Posted on: {{ date('M j, Y H:i', strtotime($post->updated_at) )}}</cite>
                            <hr/>
                        @endforeach
                    @else
                        <p>No Post Available</p>
                        
                    @endif

                    {{ $posts->links() }} <!-- use for displaying links of pagination-->
                </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
