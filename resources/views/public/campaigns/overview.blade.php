@extends('layouts.core.frontend_public', [
    'menu' => 'campaign',
])

@section('title', $campaign->name)

@section('head')
    <script type="text/javascript" src="{{ AppUrl::asset('core/echarts/echarts.min.js') }}"></script>
    <script type="text/javascript" src="{{ AppUrl::asset('core/echarts/dark.js') }}"></script> 
@endsection

@section('page_header')

    @include("public.campaigns._header")

@endsection

@section('content')

    @include("public.campaigns._menu", [
        'menu' => 'overview',
    ])

    @include("public.campaigns._info")

    <br />

    @include("public.campaigns._chart")

    @include("public.campaigns._open_click_rate")

    @include("public.campaigns._count_boxes")

    <br />

    @include("public.campaigns._24h_chart")


    <br />

    @include("public.campaigns._top_link")

    <br />

    @include("public.campaigns._most_click_country")

    <br />

    @include("public.campaigns._most_open_country")

    <br />

    @include("public.campaigns._most_open_location")

    <br />
@endsection
