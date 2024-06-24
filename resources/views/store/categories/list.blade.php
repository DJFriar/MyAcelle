@if($categorys->total() > 0 )
<table class="table text-nowrap border-top">
    <thead class="table-light">
        <tr>
            <th width="1%" style="text-align: center">
                <label>
                    <input list-control="all-checker" class="dt-checkboxes checkSingle styled" type="checkbox" value="">
                    <span class="check-symbol"></span>
                </label>
            </th> 
            <th>
                id
            </th>
            <th>
                <span class="d-flex align-items-center">
                    <span>{{ trans('store.categories.name') }}</span>
                    <span class="column-controls ms-auto">
                        <span
                            list-action="sort"
                            sort-by="name"
                            sort-direction="{{ $sort_by == 'name' ? $sort_direction : 'asc' }}"
                            class="list_column_action ms-2 {{ $sort_by == 'name' ? 'active' : '' }}"
                        >
                            <span class="material-symbols-rounded f-icon">sort</span>
                        </span>
                    </span>
                </span>
            </th> 
           
            <th>
                <span class="d-flex align-items-center">
                    <span>{{ trans('store.categories.attribute') }}</span>
                    <span class="column-controls ms-auto">
                        <span
                            list-action="sort"
                            sort-by="status"
                            sort-direction="{{ $sort_by == 'status' ? $sort_direction : 'asc' }}"
                            class="list_column_action ms-2 {{ $sort_by == 'status' ? 'active' : '' }}"
                        >
                            <span class="material-symbols-rounded f-icon">sort</span>
                        </span>
                    </span>
                </span>
            </th>
            <th class="text-center" style="width: 20px;">{{ trans('messages.actions') }} <i class="ph-arrow-circle-dowsn"></i></th>
        </tr>
    </thead>
    <tbody>
        @foreach($categorys as $key => $category) 
        <tr>
            <td width="1%" style="text-align: center">
                <div class="formcheck"> 
                    <label>
                        <input list-control="row-checker" class="form-check-input dt-checkboxes checkSingle styled"  name="ids[]" type="checkbox" value="{{ $category->id }}" id="{{ $category->id }}">
                        <span class="check-symbol"></span>
                    </label>
                    <label class="form-check-label" for="{{ $category->id }}"></label>
                </div>
            </td>
            <td>{{ $category->id ?? '' }}</td>
            <td>
                {{ $category->name }} 
            </td> 
            <td>
                @if($category->attributes->isNotEmpty()) 
                <uL>
                    @forEach($category->attributes as $attribute)
                    <li>{{ $attribute->name ?? '' }}</li>
                    @endforeach
                </uL> 
                @endif
            </td>
            <td class="">
                <div class="d-flex justify-content-end list-actions">
                    
                    <div class="py-0 px-1 me-1 form-switch" list-action="status-update">
                        <input class="form-check-input cbstatus" type="checkbox" role="switch"  
                        data-id="{{ $category->id }}" class="toggle-class"  
                        data-onstyle="success" data-offstyle="danger" data-toggle="toggle"
                        data-on="active" data-off="inactive" {{ $category->status =='active'? 'checked' : '' }}>
                    </div>
                    <a  class="btn btn-icon py-0 px-1 me-1" 
                            href="{{ action('Store\CategoryController@edit', [
                            'category' => $category,
                            'page' => request()->page,
                        ]) }}">
                        <span class="material-symbols-rounded">edit</span>
                    </a> 
                    <a  class="btn btn-icon py-0 px-1 me-1"
                        list-action="delete-template" 
                        href="{{ action('Store\CategoryController@delete', [
                            'id' =>   $category->id,
                            'page'    => request()->page,
                            'perPage' => $perPage
                            ]) }}">
                        <span class="material-symbols-rounded">delete_outline</span>  
                    </a>

                </div>
            </td>                     
        </tr>
        @endforeach
    </tbody>
</table>
@endif
<div class="d-flex justify-content-between mx-0 mb-3 small">
    <div class="d-flex align-items-center">
        <div class="me-1">
            <div class="">
                <label class="mr-2">
                    @include('store.helpers.pagination.per_page', [
                        'perPage' => $perPage,
                    ])
                </label>
                records per page. 
                @if($categorys->total() > 0 )
                    Showing {{ $categorys->firstItem()  }} 
                    to {{ $categorys->lastItem() }} 
                    of {{ $categorys->total() }} 
                    entries
                @endif
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="dataTables_paginate paging_simple_numbers" id="DataTables_Table_0_paginate">
            {{ $categorys->appends(array( 
                'perPage' => $perPage
                ))->links('vendor.pagination.bootstrap-4') }} 
        </div>
    </div>
</div>


<script>
    $(function() {  
        smsFunnelList.getDeleteCampaignsButtons().forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                let url = button.getAttribute('href');
                smsFunnelList.deleteCampaign(url);
            });
        });        
    });

    var smsFunnelList = {
        init: function() {
            // events
            this.events();
        },
        getDeleteCampaignsButtons() {
            return FunnelIndex.funnelList.getContent().querySelectorAll('[list-action="delete-template"]');
        },
        deleteCampaign(url) {
            new Dialog('confirm', {
                message: "{{ trans('store.categorys.delete._confirm') }}",
                ok: function() {
                    FunnelIndex.funnelList.addLoadingEffect();
                    // load list via ajax
                    $.ajax({
                        url: url,
                        method: 'POST',
                        data:{
                            _method: 'delete' ,
                            _token: CSRF_TOKEN,  
                        },
                    }).done(function(response) {
                        notify({
                            type: response.status,
                            message: response.message,
                        });
                        // load list
                        FunnelIndex.funnelList.load();
                    }).fail(function(jqXHR, textStatus, errorThrown){
                    }).always(function() {
                    });
                }
            })
        },
    }
</script>

