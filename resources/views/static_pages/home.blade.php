@extends('layouts.default')

@section('content')
    <div class="bg-light p-3 p-sm-5 rounded">
        <h1>Hello Weibo</h1>
        <p class="lead">
            这里是「Weibo」首页
        </p>

        <a class="btn btn-lg btn-success" href="{{ route('signup') }}">现在注册</a>
    </div>
@endsection
