@extends ('layouts.app')

@section('title')
    {{ $title }}
@endsection

@section('content')

    <{{ $vue }}
    @foreach ((array) @$params as $field => $value)
        {{ $field }}="{{ $value }}"
    @endforeach
    >{{ @$content }}</{{ $vue }}>

@endsection
