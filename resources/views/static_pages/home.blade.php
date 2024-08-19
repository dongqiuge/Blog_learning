@php use Illuminate\Support\Facades\Auth; @endphp
@extends('layouts.default')

@section('content')
    @if(Auth::check())
        <div class="row">
            <div class="col-md-8">
                <section>
                    @include('shared._status_form')
                </section>
                <h4>微博列表</h4>
                <hr>
                @include('shared._feed')
            </div>
            <aside class="col-md-4">
                <section class="user_info">
                    @include('shared._user_info', ['user' => Auth::user()])
                </section>
            </aside>
        </div>
    @else
        <div class="bg-light p-3 p-sm-5 rounded">
            <h1>Hello Weibo</h1>
            <p class="lead">
                这里是「Weibo」首页
            </p>

            <a class="btn btn-lg btn-success" href="{{ route('signup') }}">现在注册</a>
        </div>
    @endif
@endsection
