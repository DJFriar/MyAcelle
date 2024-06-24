@if($smsFunnels->total() > 0 )
<section class="section-products">
    <div class="container-fuid"> 
            <div class="row">
                    <!-- Single Product -->
                    @foreach($smsFunnels as $key => $smsFunnel)                    
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div id="product-1" class="single-product border p-3 bg-white shadow-sm rounded ">
                            <div class="part-1">
                                <div class="d-flex flex-row mb-3">
                                    @if($smsFunnel->file !='') 
                                        <img src="{{ asset('storage/funnels/'.$smsFunnel->file)  }}" alt="" title="" width="100%">
                                    @else
                                        <img src="{{ asset('storage/nophoto.png')  }}" alt="" title="" width="100%">
                                    @endif
                                </div> 
                            </div>
                            <div class="part-2 p-2">
                                <h3 class="product-title">
                                    {{ $smsFunnel->name }} 
                                </h3>
                            </div> 
                            <div class="part-3 py-2">
                                <div class="d-flex justify-content-between"> 
                                    <div class="formcheck"> 
                                        <label>
                                            <input list-control="row-checker" class="form-check-input dt-checkboxes checkSingle styled"  name="ids[]" type="checkbox" value="{{ $smsFunnel->id }}" id="{{ $smsFunnel->id }}">
                                            <span class="check-symbol"></span>
                                        </label>
                                        <label class="form-check-label" for="{{ $smsFunnel->id }}"></label>
                                    </div>
                                    <ul class="m-0 p-0">
                                        <li>
                                            <a  class="btn btn-sm btn-icon py-0 px-1 me-1" 
                                                href="{{ action('Sms\FunnelController@edit', [
                                                    'funnel' => $smsFunnel,
                                                    'page' => request()->page,
                                                    ]) }}">
                                                <span class="material-symbols-rounded">edit</span>
                                            </a> 
                                        </li>
                                        <li>
                                            <a  class="btn btn-sm btn-icon py-0 px-1 me-1"
                                                list-action="delete-product" 
                                                href="{{ action('Sms\FunnelController@delete', [
                                                    'id' =>   $smsFunnel->id,
                                                    'page'    => request()->page,
                                                    'perPage' => $perPage
                                                    ]) }}">
                                                <span class="material-symbols-rounded">delete_outline</span>  
                                                </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <!-- Single Product -->
            </div>
    </div>
</section>
  

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
                @if($smsFunnels->total() > 0 )
                    Showing {{ $smsFunnels->firstItem()  }} 
                    to {{ $smsFunnels->lastItem() }} 
                    of {{ $smsFunnels->total() }} 
                    entries
                @endif
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="dataTables_paginate paging_simple_numbers" id="DataTables_Table_0_paginate">
            {{ $smsFunnels->appends(array( 
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
            return TemplateIndex.productList.getContent().querySelectorAll('[list-action="delete-product"]');
        },
        deleteCampaign(url) {
            new Dialog('confirm', {
                message: "{{ trans('store.product.delete._confirm') }}",
                ok: function() {
                    TemplateIndex.productList.addLoadingEffect();
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
                        TemplateIndex.productList.load();
                    }).fail(function(jqXHR, textStatus, errorThrown){
                    }).always(function() {
                    });
                }
            })
        },
    }
</script>
@endif