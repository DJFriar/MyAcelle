@extends('layouts.core.frontend', [
	'menu' => 'products',
])

@section('title', trans('store.product'))

@section('head')
    <link rel="stylesheet" type="text/css" href="{{ AppUrl::asset('core/css/laza.css') }}">
@endsection
 
@section('page_header')
    <div class="page-home-content">        
        <div class="row">
            <div class="col-lg-12">  
                <div class="page-title py-0"> 
                    <nav aria-label="Breadcrumb" class="Breadcrumbnew">
                        <ul class="breadcrumb breadcrumb-caret position-right">
                            <li class="breadcrumb-item"><a class="text-muted2" href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
                            <li class="breadcrumb-item">{{  trans('store.product') }}</li>
                        </ul>
                    </nav>
                    <div class="title-area d-flex justify-content-between align-items-center">
                        <h3 class="title-head my-auto" style="font-size: 24px;color: rgba(0,0,0,.65);font-weight: 600;">
                            Quản lý sản phẩm
                        </h3>
                        <div class="action-area d-flex justify-item-start">
                            <a href="#" target="_blank" class="next-btn next-medium next-btn-secondary" >
                                <span class="next-btn-helper">Tối ưu sản phẩm</span>
                            </a>
                            <div class="dropdown">
                                <button class="btn next-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"  style="border-radius: 8px;border-width: 1px;font-size: 14px;height: 32px;padding: 0 12px;position: relative;text-align: center;border-color: #9fb7f9;color: #1971ff;margin-right: 4px;background: #fff;border-color: #9fb7f9;border-style: solid;">
                                    Quản Lý Số Lượng Lớn
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Thêm hàng loạt</a></li>
                                    <li><a class="dropdown-item" href="#">Chỉnh sủa hàng loạt</a></li>
                                    <li><a class="dropdown-item" href="#">Quản lý hình ảnh</a></li>
                                </ul>
                            </div> 
                            <a href="{{ action("Store\ProductController@create") }}" target="_blank" class="action-add next-btn next-medium next-btn-primary" style="color:#fff;">
                                <span class="material-symbols-rounded">add</span>
                                <span class="next-btn-helper">Thêm sản phẩm</span>
                            </a>
                        </div>
                    </div>                    
                </div>

                <div class="alert alert-primary laza-alert d-flex align-items-top my-3">
                    <span class="material-symbols-rounded spaninfo info-icons" style="font-size: 21px;float: left;line-height: 20px;;font-size:20pxx;color: #1a71ff;font-style: normal;text-transform: none;">
                        info
                    </span>
                    <a role="button" aria-label="Đóng" class="pro-alert-close" style="right: 16px;top: 16px;color: #858b9c;cursor: pointer;font-size: 0;position: absolute;">
                        <span class="material-symbols-rounded" style="height: 12px;line-height: 1em;width: 12px;font-size:16px;">
                            close
                        </span>
                    </a>
                   
                    <div class="laza-message">
                        <div class="laza-message-content xtooltip" title="gfggggfgfgfgf">
                            Chào mừng bạn đến với trang quản lý sản phẩm.  <a href="#" target="_blank"> Tìm hiểu thêm  </a> 
                            <br>Vui lòng lưu ý rằng mục điền Thumbnail Image sẽ bị tắt kể từ ngày 1 tháng 7 năm 2023, bạn vẫn có thể sửa đổi mục Thumbnail Image từ hiện tại cho đến hết ngày 17 tháng 7 năm 2023. Sau đó, tất cả các Thumbnail Image được sử dụng trong các sản phẩm của bạn sẽ được tự động chuyển sang hình ảnh đầu tiên của mã biến thể tương ứng (nếu hình ảnh chính và Thumbnail Image bị trùng lặp thì sẽ được hợp nhất với hình ảnh đầu tiên của mã biến thể sản phẩm tương ứng). 
                            <a href="#"" target="_blank">  Tìm hiểu thêm </a>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
    </div>
@endsection 


@section('content')
 
<div id="ProductList" class=""> 
    <div class="litems">
        <div class="row"> 
            <div class="col-lg-12"> 

                <div style="margin:7px 0; display:flex; justify-content: space-between; ">
                    <div class="filtertabs mb-2" >
                        <!-- Tab function  -->
                        <ul class="nav nav-under">
                            <li class="nav-item">
                                <a  class="nav-link "
                                    list-control="status-filter"
                                    href="{{ action('Store\ProductController@index',[ 
                                                'status'=> 'all',
                                                'type'=> request()->type,
                                                'page' => request()->page,
                                                'perPage' =>  request()->perPage
                                            ]) }}"> 
                                    Tất cả
                                </a>
                            </li>
                            <li class="nav-item has-count active">
                                <a  class="nav-link"
                                    list-control="status-filter"
                                    href="{{ action('Store\ProductController@index',[ 
                                                'status'=> Acelle\Model\Product::STATUS_ACTIVE,
                                                'type'=> request()->type,
                                                'page' => request()->page,
                                                'perPage' =>  request()->perPage
                                            ]) }}"> 
                                    Đang hoạt động
                                    <sup class="sup">1</sup>
                                </a>
                                
                            </li>
                            <li class="nav-item">
                                <a  class="nav-link"
                                    list-control="status-filter"
                                    href="{{ action('Store\ProductController@index',[ 
                                                'status'=> Acelle\Model\Product::STATUS_INACTIVE,
                                                'type'=> request()->type,
                                                'page' => request()->page,
                                                'perPage' =>  request()->perPage
                                            ]) }}"
                                            >
                                    Không hoạt động
                                </a>
                            </li> 
                            <li class="nav-item">
                                <a  class="nav-link"
                                    list-control="status-filter"
                                    href="{{ action('Store\ProductController@index',[ 
                                                'status'=> Acelle\Model\Product::STATUS_DRAPP,
                                                'type'=> request()->type,
                                                'page' => request()->page,
                                                'perPage' =>  request()->perPage
                                            ]) }}">
                                    Bản nháp
                                </a>
                            <li>
                            <li class="nav-item">
                                <a  class="nav-link"
                                    list-control="status-filter"
                                    href="{{ action('Store\ProductController@index',[ 
                                            'status'=> Acelle\Model\Product::STATUS_INPROGRESS,
                                            'type'=> request()->type,
                                            'page' => request()->page,
                                            'perPage' =>  request()->perPage
                                        ]) }}">
                                    Chờ duyệt
                                </a>
                            <li>
                            <li class="nav-item">
                                <a  class="nav-link"
                                    list-control="status-filter"
                                    href="{{ action('Store\ProductController@index',[ 
                                                'status'=> Acelle\Model\Product::STATUS_WARNING,
                                                'type'=> request()->type,
                                                'page' => request()->page,
                                                'perPage' =>  request()->perPage
                                            ]) }}">
                                    Vi phạm
                                </a>
                            <li>
                            <li class="nav-item">
                                <a  class="nav-link"
                                    list-control="status-filter"
                                    href="{{ action('Store\ProductController@index',[ 
                                            'status'=> Acelle\Model\Product::STATUS_REMOVE,
                                            'type'=> request()->type,
                                            'page' => request()->page,
                                            'perPage' =>  request()->perPage
                                        ]) }}">
                                    Đã xóa
                                    <sup class="sup">1</sup>
                                </a> 
                            </li>                     
                        </ul>
                    </div>
                    <div style="width:250px; pađing-top:7px; text-align:right"> 
                        <span style="display:flex; font-size:10px">
                            <span>1/1.000 sản phẩm</span>
                            <span class="material-symbols-rounded spaninfo info-icons" style="font-size: 10px;float: left;line-height: 12px;color: rgba(0,0,0,.65);font-style: normal;text-transform: none; padding-left:5px">
                                info
                            </span>
                        </span>
                        <!-- New markup -->
                        <div  class="progress" role="progressbar" aria-label="Basic example" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" 
                            style="margin: 10px 0 0 0;--bs-progress-height: 0.5rem;">
                            <div class="progress-bar" style="width: 25%"></div>
                        </div>  
  
                    </div>
                </div>
            </div> 
        </div>
    </div>

    <div class="pro-filter-area" style="margin-bottom: 12px;background: #fff;border: 0 solid transparent;border-radius: 16px;box-shadow: 0 1px 12px 1px rgba(132,152,208,.07);min-width: 100px;overflow: hidden; padding-top:3px">
        <div class="card border-0">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="d-flex align-items-center"> 
                            <div  class="ms-2 my-3">
                                Lọc sản phẩm
                            </div>
                            <div class="ml-2">
                                <span class="border border-light px-2 py-2 " style="background-color: #f2f3f7;border-radius: 8px;border-color: #f2f3f7;color: #4f5669;">
                                    Hết hàng
                                </span> 
                            </div> 
                        </div>
                    </div>
                </div>
            
                <div class="row">
                    <div class="col-lg-4 pr-2">
                        <div class="input-group input-group-sm ml-1">  
                            <select class="form-select pr-0" id="inputGroupSelect02">
                            <option selected>Tên sản phẩm</option>
                            <option value="1">One</option>
                            <option value="2">Two</option>
                            <option value="3">Three</option>
                            </select>
                            <input type="text" class="form-control" 
                                style="border-right:none" 
                                list-control="search-input" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                <span class="input-group-text bg-white rd-rt-10 rd-rb-10" style="border-left:none;cursor:hand" >
                                    <a href="#" list-control="search-button" >
                                        <span class="material-symbols-rounded" style="line-height: 1 !important;">search</span> 
                                    </a>
                                </span> 
                        </div>
                    </div>
                    <div class="col-lg-4 px-1">
                        <div class="input-group input-group-sm mb-3">
                            <label class="input-group-text bg-white rd-lt-10 rd-lb-10" for="inputGroupSelect02">Ngành hàng</label>
                            <select class="form-select" id="inputGroupSelect02">
                            <option selected>vui lòng chọn</option>
                            <option value="1">One</option>
                            <option value="2">Two</option>
                            <option value="3">Three</option>
                            </select>
                        </div>  
                    </div>
                    <div class="col-lg-4 pl-1">
                        <div class="input-group input-group-sm mb-3 mr-1">
                            <label class="input-group-text bg-white rd-lt-10 rd-lb-10" for="inputGroupSelect02">Sắp xếp</label>
                            <select class="form-select" id="inputGroupSelect02">
                            <option selected>vui lòng chọn</option>
                            <option value="1">One</option>
                            <option value="2">Two</option>
                            <option value="3">Three</option>
                            </select>
                        </div>  
                    </div>

                </div>  
            </div>
        </div>
    </div>

    <div id="NumberList" class=""> 
        <div class="card" style="border-radius: 16px;box-shadow: 0 1px 12px 1px rgba(132,152,208,.07);">
            <div class="card-body"> 
                <div class="container-fuid">
                    <div class="row">
                        <div class="col-lg-12"> 

                            <div class="d-flex align-items-center mb-3">
                                <form id="product" name="product" action="{{ action('Store\ProductController@multiltask',[ 'page' => request()->page,]) }}" method="POST" >
                                    {{ csrf_field() }}
                                    @method('PUT') 
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="d-flex align-items-center">
                                                <div list-control="selected-count-label" class="ms-2 pl-2">
                                                </div> 
                                                <div class="dropdown ml-2"> 
                                                    <button class="btn btn-light btn-sm dropdown-toggle rounded disabled dropdownaction" type="button" data-bs-toggle="dropdown" aria-expanded="false" list-contro="list-action-button">
                                                        {{ trans('store.action') }}
                                                    </button>
                                                    <ul class="dropdown-menu me-1">
                                                        <li>
                                                            <a list-control="delete-selected-button"
                                                                href="{{ action('Store\ProductController@deleteSelected') }}"
                                                                class="dropdown-item delmany" href="javascript:void(0)">  
                                                                    <span class="material-symbols-rounded">delete_outline</span>   
                                                                    {{ trans('store.product.delete') }}
                                                            </a>
                                                        </li> 
                                                        <li>
                                                            <a  list-control="active-selected-button"
                                                                class="dropdown-item" href="{{ action('Store\ProductController@multiltask') }}">
                                                                <span class="material-symbols-rounded">done</span>   
                                                                {{ trans('store.product.active') }}
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a list-control="deactive-selected-button"
                                                            class="dropdown-item" href="{{ action('Store\ProductController@multiltask') }}">
                                                            <span class="material-symbols-rounded">share</span>
                                                                {{ trans('store.product.inactive') }}
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="dropdown me-1 ml-2">
                                                    <button list-control="status-button" class="btn btn-light btn-sm dropdown-toggle ftext-capitalize dropdownaction rounded status" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        Status
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item status-link ftext-capitalize <?php echo (isset(request()->status)? '':'active') ?>" 
                                                                list-control="all-selected-button"
                                                                filter-by ="all"
                                                                href="{{ action('Store\ProductController@index',[ 
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
                                                                href="{{ action('Store\ProductController@index',[ 
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
                
                                            </div>
                                        </div>

                                    </div>
                                </form>  
                            </div>

                            <div list-control="content">     
                            </div> 
                        </div>
                    </div> 

                </div>
            </div>
        </div>
    
    </div> 

</div>
 
<script>
    $(function() {
        ProductIndex.init();
        ProductIndex.productList.load();
    });

    var ProductIndex = {
        init: function() {
            // khởi tạo biến list cho trang SmsCampaignIndex
            this.productList = new DataList({

                // param 1: container chứa list
                container: document.querySelector('#ProductList'),

                // param 2: link tới trang list
                url: '{{ action('Store\ProductController@list') }}', 

                // set is default loading 
                perPage:  '{{ request()->perPage ?? 8 }}',  

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
            this.view = '{{ request()->view }}';
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
            this.url = '{{ action('Store\ProductController@list') }}' + '?page='+this.page;
        }        
        getPerPage() {
            return this.perPage;
        }
        setPerPage(value) {
            this.perPage = value;
            this.setPage(1);
        }
        getView() {
            return this.view;
        }
        setView(view) {
            this.view = view;
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
            this.url = '{{ action('Store\ProductController@list') }}';
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
                message: "{{ trans('store.product.status.comfirm') }}",
                ok: function() {
                    $.ajax({
                        url: url,
                        type: 'PUT',
                        data:{
                            _token: CSRF_TOKEN,  
                            ids: ids,
                            status:status,
                            solu:'activemany',
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

            if(this.view !='grid'){ 
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
            }

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
                        url: '{{ action('Store\ProductController@updateStatus') }}',
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
                    $.ajax({
                        type: "PATCH",
                        dataType: "json",
                        url: '{{ action('Store\ProductController@updateStatus') }}',
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
                _this.changeStatusSelectedIds(url,'active');
            });
            this.getDeactiveSelectedButton().addEventListener('click', function(e) {
                e.preventDefault();
                var url = _this.getActiveSelectedButton().getAttribute('href');   
                _this.changeStatusSelectedIds(url,'ineactive');
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
                    view: this.getView(), 
                }
            }).done(function(response){

                
                $(_this.getContent()).html(response);

                // 
                initJs($(_this.getContent())); 

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
