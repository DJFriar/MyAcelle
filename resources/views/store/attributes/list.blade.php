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
                Category
            </th>
            <th>
                <span class="d-flex align-items-center">
                    <span>{{ trans('store.attributes.name') }}</span>
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
                    <span>{{ trans('store.attributes.description') }}</span>
                    <span class="column-controls ms-auto">
                        <span
                            list-action="sort"
                            sort-by="description"
                            sort-direction="{{ $sort_by == 'description' ? $sort_direction : 'asc' }}"
                            class="list_column_action ms-2 {{ $sort_by == 'description' ? 'active' : '' }}"
                        >
                            <span class="material-symbols-rounded f-icon">sort</span>
                        </span>
                    </span>
                </span>
            </th>
            <th>
                <span class="d-flex align-items-center">
                    <span>{{ trans('store.attributes.status') }}</span>
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
        @foreach($attributes as $key => $attribute)
        <tr>
            <td width="1%" style="text-align: center">
                <div class="formcheck"> 
                    <label>
                        <input list-control="row-checker" class="form-check-input dt-checkboxes checkSingle styled"  name="ids[]" type="checkbox" value="{{ $attribute->id }}" id="{{ $attribute->id }}">
                        <span class="check-symbol"></span>
                    </label>
                    <label class="form-check-label" for="{{ $attribute->id }}"></label>
                </div>
            </td> 
            <td>
                @if( $attribute->category)
                    {{ $attribute->category->name }}
                @endif
            </td>
            <td>
                {{ $attribute->name }} 
            </td>  
            <td>{{ $attribute->description }}</td> 
            <td>
                <span class="badge bg-{{ $attribute->status }}" style="white-space:break-spaces">{{ $attribute->status }}</span>
            </td>
            <td class="">
                <div class="d-flex justify-content-end list-actions">
                    
                    <div class="py-0 px-1 me-1 form-switch" list-action="status-update">
                        <input class="form-check-input cbstatus" type="checkbox" role="switch"  
                        data-id="{{ $attribute->id }}" class="toggle-class"  
                        data-onstyle="success" data-offstyle="danger" data-toggle="toggle"
                        data-on="active" data-off="inactive" {{ $attribute->status =='active'? 'checked' : '' }}>
                    </div>
                    <a  class="btn btn-icon py-0 px-1 me-1" 
                            href="{{ action('Store\AttributeController@edit', [
                            'attribute' => $attribute,
                            'page' => request()->page,
                        ]) }}">
                        <span class="material-symbols-rounded">edit</span>
                    </a> 
                    <a  class="btn btn-icon py-0 px-1 me-1"
                        list-action="delete-template" 
                        href="{{ action('Store\AttributeController@delete', [
                            'id' =>   $attribute->id,
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
                @if($attributes->total() > 0 )
                    Showing {{ $attributes->firstItem()  }} 
                    to {{ $attributes->lastItem() }} 
                    of {{ $attributes->total() }} 
                    entries
                @endif
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="dataTables_paginate paging_simple_numbers" id="DataTables_Table_0_paginate">
            {{ $attributes->appends(array( 
                'perPage' => $perPage
                ))->links('vendor.pagination.bootstrap-4') }} 
        </div>
    </div>
</div>


<script>
    $(function() {  
        smsTemplateList.getDeleteCampaignsButtons().forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                let url = button.getAttribute('href');
                smsTemplateList.deleteCampaign(url);
            });
        });        
    });

    var smsTemplateList = {
        init: function() {
            // events
            this.events();
        },
        getDeleteCampaignsButtons() {
            return TemplateIndex.templateList.getContent().querySelectorAll('[list-action="delete-template"]');
        },
        deleteCampaign(url) {
            new Dialog('confirm', {
                message: "{{ trans('store.attributes.delete._confirm') }}",
                ok: function() {
                    TemplateIndex.templateList.addLoadingEffect();
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
                        TemplateIndex.templateList.load();
                    }).fail(function(jqXHR, textStatus, errorThrown){
                    }).always(function() {
                    });
                }
            })
        },
    }
</script>

