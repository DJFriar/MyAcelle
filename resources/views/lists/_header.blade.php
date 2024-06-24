<div class="page-title">
    <ul class="breadcrumb breadcrumb-caret position-right">
        <li class="breadcrumb-item"><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ action("MailListController@index") }}">{{ trans('messages.lists') }}</a></li>
    </ul>

    <div class="d-flex align-items-center my-4 pt-2">
        <h1 class="mb-0 me-auto">
            <span class="text-semibold">{{ $list->name }}</span>
        </h1>
        @if (config('app.demo'))
            <style>
                .avatars-stack {
            
                }
                .avatars-stack img {
                    width: 32px;
                    height: 32px;
                    border-radius: 100%;
                    display: inline-block;
                    margin-left: -13px;
                    box-shadow: 0 0 2px rgba(0,0,0,0.1);
                    border: solid 0px #ccc;
                }
                .avatars-stack .btn-trans {
                    background-color: transparent!important;
                    border-radius: 100px;
                    border: none;
                }
                .avatars-stack .btn-trans:hover {
                    background-color: rgba(0, 0, 0, 0.04)!important;
                    border-radius: 100px;
                    box-shadow: none!important;
                }
            </style>
            <a class="avatars-stack d-flex align-items-center" href="{{ action('SubscriberController@index', $list->uid) }}">
                <div class=" me-2">
                    <img src="https://i.pravatar.cc/300?v={{ rand(0,10000000) }}" alt="">
                    <img src="https://i.pravatar.cc/300?v={{ rand(0,10000000) }}" alt="">
                    <img src="https://i.pravatar.cc/300?v={{ rand(0,10000000) }}" alt="">
                </div>
                <div class="me-4">
                    <button class="btn btn-default btn-trans"> Last subscription 3 days before</button>
                </div>
            </a>
        @endif
        <div>
            <div class="btn-group">
                <button role="button" class="btn btn-light px-3 py-2 fw-600" data-bs-toggle="dropdown">
                    {{ trans('messages.change_list') }} <span class="material-symbols-rounded ms-2">double_arrow</span>
                </button>
                <ul class="dropdown-menu">
                    @forelse ($list->otherLists() as $l)
                        <li>
                            <a class="dropdown-item" href="{{ action('MailListController@overview', ['uid' => $l->uid]) }}">
                                {{ $l->readCache('LongName', $l->name) }}
                            </a>
                        </li>
                    @empty
                        <li style="pointer-events:none;"><a href="#" class="dropdown-item">({{ trans('messages.empty') }})</a></li>
                    @endforelse
                </ul>
            </div>
        </div>

    </div>

    <span class="badge badge-info bg-info-800 badge-big">{{ number_with_delimiter($list->readCache('SubscriberCount')) }}</span> {{ trans('messages.subscribers') }}
</div>
