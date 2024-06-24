@extends('layouts.core.frontend', [
	'menu' => 'attributes',
])

@section('title', trans('store.attributes'))

@section('page_header')

    @include('store.helpers.page_header', [
                'title' => trans('store.attributes'),
                'guide' => 'An SMS Template is a marketing strategy that uses text messages to communicate with customers. SMS campaigns can be used to promote products or services, send updates, or provide customer support.',
                'link' => action('Store\AttributeController@create',[ 'page' => request()->page, ]),
                'link_title' => trans('store.attributes.add_new'),
            ]) 
@endsection 

@section('content')

<div id="TemplateList" class=""> 
    <div class="d-flex align-items-center mb-3">

        <form id="smsTemplate" name="smsTemplate" action="{{ action('Store\AttributeController@multiltask',[ 'page' => request()->page,]) }}" method="POST">
            {{ csrf_field() }}
            @method('PUT')

            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex align-items-center mb-2">
                        <div class="dropdown d-inline-block mr-2">
                            <button list-contro="list-action-button" class="btn btn-light dropdown-toggle dropdownaction rounded disabled" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ trans('store.action') }}
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a list-control="delete-selected-button"
                                        href="{{ action('Store\AttributeController@deleteSelected') }}"
                                        class="dropdown-item delmany" href="javascript:void(0)">  
                                            <span class="material-symbols-rounded">delete_outline</span>   
                                            {{ trans('store.sms_sending_servers.delete') }}
                                    </a>
                                </li> 
                                <li>
                                    <a list-control="active-selected-button"
                                    class="dropdown-item" href="{{ action('Store\AttributeController@multiltask') }}">
                                    <span class="material-symbols-rounded">done</span>   
                                    {{ trans('store.sms_sending_servers.active') }}
                                    </a>
                                </li>
                                <li>
                                    <a list-control="deactive-selected-button"
                                    class="dropdown-item" href="{{ action('Store\AttributeController@multiltask') }}">
                                    <span class="material-symbols-rounded">share</span>
                                        {{ trans('store.sms_sending_servers.inactive') }}
                                    </a>
                                </li>
                            </ul>
                        </div> 
                        <div class="dropdown d-inline-block  me-1">
                            <button list-control="status-button" class="ftext-capitalize btn btn-light dropdown-toggle dropdownaction rounded status" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Status
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item status-link ftext-capitalize <?php echo (isset(request()->status)? '':'active') ?>" 
                                        list-control="all-selected-button"
                                        filter-by ="all"
                                        href="{{ action('Store\AttributeController@index',[ 
                                            'status'=> 'all',
                                            'type'=> request()->type,
                                            'page' => request()->page,
                                            'perPage' =>  request()->perPage
                                        ]) }}">
                                        {{ trans('store.all') }}
                                    </a>
                                </li> 
                                @if( isset($statuslist) )
                                @foreach($statuslist as $key => $eachstatus)
                                <li>
                                    <a class="dropdown-item status-link ftext-capitalize {{  $eachstatus->status === request()->status ? 'active':'' }}"                                          
                                        list-control="active-selected-button"
                                        filter-by="{{ $eachstatus->status }}"
                                        href="{{ action('Store\AttributeController@index',[ 
                                            'status'=> $eachstatus->status ,
                                            'type'=> request()->type,
                                            'page' => request()->page,
                                            'perPage' =>  request()->perPage
                                        ]) }}"> 
                                        {{ $eachstatus->status }}
                                    </a>
                                </li> 
                                @endforeach
                                @endif
                            </ul> 
                        </div>
                        <div list-control="selected-count-label" class="ms-2">

                        </div>
                    </div>
                </div>

            </div>
        </form>
        <div class="mt-2 mt-sm-0 ms-sm-auto">
            <div class="input-group rounded">
                <input type="text"
                    list-control="search-input"
                    name="keyword"
                    class="form-control rounded"
                    placeholder="{{ trans('store.enter_to_search') }}"
                    value=""
                />
                <button
                    list-control="search-button"
                    type="submit" name="search" value="search" class="btn btn-default ml-2 rounded"
                >
                    <i class="fas fa-search"></i> {{ trans('store.search') }}
                </button>
            </div>
        </div>
    </div>
    <div list-control="content">     
    </div>
</div> 

<script>
    $(function() {
        TemplateIndex.init();
        TemplateIndex.templateList.load();
    });

    var TemplateIndex = {
        init: function() {
            // khởi tạo biến list cho trang SmsCampaignIndex
            this.templateList = new DataList({

                // param 1: container chứa list
                container: document.querySelector('#TemplateList'),

                // param 2: link tới trang list
                url: '{{ action('Store\AttributeController@list') }}', 

                // set is default loading 
                perPage:  '{{ request()->perPage ?? 5 }}', 

            });
        },
        removeNotification: function() {
            alert('hehe');
        }
    }

    // class
    var DataList = class {
        constructor(options){
            this.container = options.container;
            this.url = options.url;
            this.name = options.name;
            this.perPage = options.perPage 
            this.keyword = '';
            this.page =  options.page ?? 1 ;
            this.sort = '';
            // gan events sau khi load trang
            this.events();
        }
        getContainer() {
            return this.container;
        }
        getContent() {            
            return this.getContainer().querySelector('[list-control="content"]');
        }
        addLoadingEffect() { 
            this.getContent().classList.add('list-loading');
        }
        removeLoadingEffect() { 
            this.getContent().classList.remove('list-loading');
        }
        getSearchInput() {
            return this.getContainer().querySelector('[list-control="search-input"]');
        }
        getSearchInputValue() {
            return this.getSearchInput().value;
        }
        getListActionButton() {
            return this.getContainer().querySelector('[list-contro="list-action-button"]');
        }
        disableListActionButton() {
            this.getListActionButton().classList.add('disabled');
        }
        enableListActionButton() {  
            this.getListActionButton().classList.remove('disabled');
        }
        getStatusInput() {
            return this.getContent().querySelectorAll('[list-action="status-update"]');
        }
        getSearchButton() {
            return this.getContainer().querySelector('[list-control="search-button"]');
        }
        getAllChecker() {
            return this.getContent().querySelector('[list-control="all-checker"]');
        }
        getRowCheckers() {
            return this.getContent().querySelectorAll('[list-control="row-checker"]');
        }
        getCheckedRowCheckers() {
            return this.getContent().querySelectorAll('[list-control="row-checker"]:checked');
        }
        // for filter list
        getfilterButton() {
            return this.getContent().querySelector('[list-action="dofilter"]');
        }
        getCheckedRowfilters() {
            return this.getContent().querySelectorAll('[list-action="filter"]:checked');
        }
        getCheckRowfilters() {
            return this.getContent().querySelectorAll('[list-action="filter"]');
            //return this.getContent().querySelectorAll('.lsfilter');
        }
        getDeleteSelectedButton() {
            return this.getContainer().querySelector('[list-control="delete-selected-button"]');
        }
        getActiveSelectedButton(){
            return this.getContainer().querySelector('[list-control="active-selected-button"]');
        } 
        getDeactiveSelectedButton(){
            return this.getContainer().querySelector('[list-control="deactive-selected-button"]');
        }
        getPaginationLinks() {
            return this.getContent().querySelectorAll('.page-link');
        }
        getPerPageSelectBox() {
            return this.getContent().querySelector('[list-control="per-page"]');
        }
        getSelectedRowsLabel() {
            return this.getContainer().querySelector('[list-control="selected-count-label"]');
        }
        updateSelectedRowLabel() {
            var count = this.getCheckedRowCheckers().length;
            if (count == 0) {
                this.getSelectedRowsLabel().innerHTML = '';
            } else {
                this.getSelectedRowsLabel().innerHTML = `
                    <label><strong>`+count+`</strong> items selected</label>
                `;
            }
        }
        getKeyword() {
            return this.keyword;
        }
        setKeyword(value) {
            this.keyword = value.trim();
        }

        getSort() {
            return this.sort;
        }
        setSort(sort_by, sort_direction) {
            this.sort = {
                by: sort_by,
                direction: sort_direction,
            };
        }
        getSortButtons() {
            return this.getContent().querySelectorAll('[list-action="sort"]');
        }
        getUrl() {
            return this.url;
        }
        setUrl(url) {
            this.url = url;
        }
        getPage() {
            return this.page;
        }
        setPage(page) {
            this.page = page; 
            this.url = '{{ action('Store\AttributeController@list') }}' + '?page='+this.page;
        }        
        getPerPage() {
            return this.perPage;
        }
        setPerPage(value) {
            this.perPage = value;
            this.setPage(1);
        }
        /**
         *  status filter
        */ 
        getStatusSelectbutton() {
            return this.getContainer().querySelector('[list-control="status-button"]');
        }
        getStatusSelectBox() {
            return this.getContainer().querySelectorAll('.status-link');
        }
        getStatus() {
            return this.status;
        }
        setStatus( status ) {
            this.status = status;
            this.url = '{{ action('Store\AttributeController@list') }}';
        }               
        updateSelectedStatus(){ 
            var status = this.getStatus(); 
            if ( typeof(status) == "undefined" || status == null ) { 
                status ='all';
            } 
            this.getStatusSelectBox().forEach(  function(node, index) {  
                node.classList.remove("active");
                if(status == node.getAttribute("filter-by")){
                    node.classList.add("active"); 
                }
            }); 
        }
        /**
         * action for many record
        */
        getAllSelectedIds() {
            let ids = [];
            this.getCheckedRowCheckers().forEach(checker => {
                ids.push(checker.value);
            });
            return ids;
        }
        getAllFilterIds() {
            let filterlist = [];
            this.getCheckedRowfilters().forEach(checker => {
                filterlist.push(checker.value);
            });
            return filterlist;
        }
        reload_FilterLabel(){
            var filters = this.getFilter();
            if (filters != undefined){  
                this.getCheckRowfilters().forEach(checker => {
                    checker.checked = false;
                    if(filters.includes(checker.value) ){  
                        checker.checked = true;
                    }
                });  
            } 
        }
        applyFilter(){
            var _this = this;
            var filters = this.getAllFilterIds();
            // set filter
            _this.setFilter(filters);
            // reload list
            _this.load(); 
        }
        changeStatusSelectedIds(url, status){
            var _this = this;
            var ids = this.getAllSelectedIds(); 
            new Dialog('confirm', {
                message: 'Are you sure to delete all selected campaigns',
                ok: function() {
                    $.ajax({
                        url: url,
                        type: 'PATCH',
                        data:{
                            _token: CSRF_TOKEN,  
                            ids: ids,
                            status:status,
                        },
                    }).done(function(response) { 
                        notify({
                            type: response.status,
                            message: response.message,
                        });
                        _this.load();
                    }).fail(function(jqXHR, textStatus, errorThrown){
                    }).always(function() {
                    });
                }
            })
        }        
        deleleAllSelectedIds(url) {
            var _this = this;
            var ids = this.getAllSelectedIds();            
            new Dialog('confirm', {
                message: 'Are you sure to delete all selected campaigns',
                ok: function() { 
                    $.ajax({
                        url: url,
                        method: 'POST',
                        data:{
                            _method: 'delete' ,
                            _token: CSRF_TOKEN,  
                            ids: ids
                        },
                    }).done(function(response) {
                        notify({
                            type: response.status,
                            message: response.message,
                        });
                        _this.load();
                    }).fail(function(jqXHR, textStatus, errorThrown){
                    }).always(function() {
                    });
                }
            })
        }
        
        capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        afterLoadEvents() {
            var _this = this;

            // khi thay đổi perpage
            this.getPerPageSelectBox().addEventListener('change', function() {
                let value = _this.getPerPageSelectBox().value;
                
                // thay đổi per page và load lại list
                _this.setPerPage(value);

                // load lại list
                _this.load();
            }); 

            // bắt sự kiện khi nhấn vào link pagination
            this.getPaginationLinks().forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    let url = link.getAttribute('href');
                    if (url !== null) {
                        _this.setUrl(url);

                        // load list
                        _this.load();
                    }
                });
            }); 

            // khi nhấn vào nút check all row
            this.getAllChecker().addEventListener('change', function(e) {                
                let checked = _this.getAllChecker().checked;                
                if (checked) {
                    _this.getRowCheckers().forEach(checker => {
                        checker.checked = true;
                    });
                    _this.enableListActionButton();
                } else { 
                    _this.getRowCheckers().forEach(checker => {
                        checker.checked = false;
                    })
                    _this.disableListActionButton();
                }
                 _this.updateSelectedRowLabel();
            });

            this.getRowCheckers().forEach(checker => {
                checker.addEventListener('change', function() {
                    let checked = checker.checked; 
                    if (_this.getCheckedRowCheckers().length == _this.getRowCheckers().length) {
                        _this.getAllChecker().checked = true;
                    } else {
                        _this.getAllChecker().checked = false;
                    }
                    if (_this.getCheckedRowCheckers().length > 0) { 
                        _this.enableListActionButton();
                    } else { 
                        _this.disableListActionButton();
                    }
                    _this.updateSelectedRowLabel();
                });
            }); 

            // gán sự kiện cho swith status change 
            this.getStatusInput().forEach(button => {  
                button.addEventListener('change', function(e) {
                    var status =  e.target.checked == true ? 'active' : 'inactive';
                    let id = $(this).children("input").data('id'); 
                    $.ajax({
                        type: "PATCH",
                        dataType: "json",
                        url: '{{ action('Store\AttributeController@updateStatus') }}',
                        data: {'status': status, 'id': id, "_token": "{{ csrf_token() }}" }, 
                        success: function(data){                             
                            notify({
                                type: data.status,
                                message: data.success,
                            });                            
                            // load lai du lieu
                            _this.load();
                        }
                    });
                });
            });

            // khi click vào filter 
            this.getSortButtons().forEach(button => {
                button.addEventListener('click', function() {
                    let by = button.getAttribute('sort-by');
                    let direction = button.getAttribute('sort-direction');
                    let newDirection = direction == 'asc' ? 'desc' : 'asc';
                    
                    // set sort
                    _this.setSort(by, newDirection);

                    // load list
                    _this.load();
                });
            });

            // gán sự kiện cho swith status change 
            this.getStatusInput().forEach(button => {  
                button.addEventListener('change', function(e) {
                    var status =  e.target.checked == true ? 'active' : 'inactive';
                    let id = $(this).children("input").data('id'); 
                    console.log(status);
                    $.ajax({
                        type: "PATCH",
                        dataType: "json",
                        url: '{{ action('Store\AttributeController@updateStatus') }}',
                        data: {'status': status, 'id': id, "_token": "{{ csrf_token() }}" }, 
                        success: function(data){                             
                            notify({
                                type: data.status,
                                message: data.success,
                            });                            
                            // load lai du lieu
                            _this.load();
                        }
                    });
                });
            });
            

        }

        /**
         *  gán sự kiện bên ngoài Datalist ( gán lần đầu)
        */
        events(){
            var _this = this;             
            this.getSearchInput().addEventListener('keyup', function(e) {
                if (e.key === 'Enter' || e.keyCode === 13) { 
                    _this.setKeyword(_this.getSearchInputValue()); 
                    _this.load();
                }
            });
            this.getSearchButton().addEventListener('click', function(e){
                _this.setKeyword(_this.getSearchInputValue());
                _this.load();
            });
            this.getSearchInput().addEventListener('click', function(e){
                e.preventDefault();
                _this.setKeyword(_this.getSearchInputvalue());
                _this.load();
            });            
            this.getDeleteSelectedButton().addEventListener('click', function(e) {
                e.preventDefault();
                var url = _this.getDeleteSelectedButton().getAttribute('href');  
                _this.deleleAllSelectedIds(url);
            });
            this.getActiveSelectedButton().addEventListener('click', function(e) {
                e.preventDefault();
                var url = _this.getActiveSelectedButton().getAttribute('href');   
                _this.changeStatusSelectedIds(url,'Active');
            });
            this.getDeactiveSelectedButton().addEventListener('click', function(e) {
                e.preventDefault();
                var url = _this.getActiveSelectedButton().getAttribute('href');   
                _this.changeStatusSelectedIds(url,'Deactive');
            });
            /**
             *  status filter
            */            
            this.getStatusSelectBox().forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();                    
                    let status = link.getAttribute('filter-by');
                    if (status !== null) {             
                        _this.setStatus(status);
                        _this.getStatusSelectbutton().innerHTML = _this.capitalizeFirstLetter(status);
                        _this.load(); 
                    }
                });
            });
        }

        /** 
         *  load lại trang
         */
        load(){
            var _this = this; 
            // thêm hiệu ứng khi load list
            this.addLoadingEffect(); 
            $.ajax({
                url: this.getUrl(),
                type: 'GET',
                data: {
                    perPage: this.getPerPage(), 
                    keyword: this.getKeyword(), 
                    sort: this.getSort(), 
                    status: this.getStatus(), 
                    
                }
            }).done(function(response){
                $(_this.getContent()).html(response);

                // gán sự kiện cho các nút
                 _this.afterLoadEvents();

                  // reset checked label
                _this.updateSelectedRowLabel();

                 // có hiện nút action hay không. Hiện khi checked rows > 0
                if (_this.getCheckedRowCheckers().length > 0) {
                    // enable action button
                    _this.enableListActionButton();
                } else {
                    // enable action button
                    _this.disableListActionButton();
                }
                
                 /**  set active select box status */
                 _this.updateSelectedStatus(); 


            }).fail(function(jqXHR, textStatus, errorThrown){

            }).always(function() {
                   
               _this.removeLoadingEffect();
            });
        }

    } 
 
</script>

@endsection
