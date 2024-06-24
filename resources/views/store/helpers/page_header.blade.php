<div class="page-title">
    <ul class="breadcrumb breadcrumb-caret position-right">
        <li class="breadcrumb-item"><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
    </ul>
    <h1>
        <span class="text-semibold"><span class="material-symbols-rounded">format_list_bulleted</span>
            {{ $title }}
        </span>
    </h1>
</div>

<div class="d-flex align-items-top border p-4 mb-4 rounded shadow-sm bg-white">
    <div class="me-4">
        <span class="material-symbols-rounded" style="font-size:42px;color:rgb(241, 189, 108);line-height:34px">
            tips_and_updates
        </span>
    </div>
    <p class="pe-5 mb-0">
        {{ $guide }}
    </p>
    @if(isset($link_title))
    <div class="ms-auto">
        <a class="btn btn-primary rounded text-nowrap" type="button" href="{{ $link }}">
            <span class="material-symbols-rounded">add</span>
            {{ $link_title }}
        </a> 
    </div>
    @endif
</div>