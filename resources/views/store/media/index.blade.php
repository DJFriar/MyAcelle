@extends('layouts.core.frontend', [
	'menu' => 'orders',
])

@section('title', trans('sms.orders'))

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
                            <li class="breadcrumb-item">{{  trans('sms.orders') }}</li>
                        </ul>
                    </nav>
                    <div class="title-area d-flex justify-content-between align-items-center">
                        <h3 class="title-head my-auto" style="font-size: 24px;color: rgba(0,0,0,.65);font-weight: 600;">
                            Quản lý đơn hàng
                        </h3>
                        <div class="card-body bg-white">
                            <div class="row">
                                <div class="col-lg-4">
asdasdasd
                                </div>
                                <div class="col-lg-4">
asdasda
                                </div>
                                <div class="col-lg-4">
asdada
                                </div>
 
                               
                            </div>
                        </div>
                       
                    </div>
                </div>                
                <div class="laza-alert-area d-flex align-items-top my-3">
                    <a role="button" aria-label="Đóng" class="pro-alert-close" style="right: 16px;top: 16px;color: #858b9c;cursor: pointer;font-size: 0;position: absolute;">
                        <span class="material-symbols-rounded" style="height: 12px;line-height: 1em;width: 12px;font-size:16px;">
                            close
                        </span>
                    </a>
                    <span class="material-symbols-rounded spaninfo info-icons" style="font-size: 21px;float: left;line-height: 20px;;font-size:20pxx;color: #1a71ff;font-style: normal;text-transform: none;">
                        info
                    </span>
                    <div class="laza-message">
                        <div class="laza-message-content xtooltip" title="gfggggfgfgfgf">
                            Chào mừng bạn đến với trang quản lý sản phẩm.  <a href="#" target="_blank"> Tìm hiểu thêm  </a> 
                            <br>Vui lòng lưu ý rằng mục điền Thumbnail Image sẽ bị tắt kể từ ngày 1 tháng 7 năm 2023, bạn vẫn có thể sửa đổi mục Thumbnail Image từ hiện tại cho đến hết ngày 17 tháng 7 năm 2023. Sau đó, tất cả các Thumbnail Image được sử dụng trong các sản phẩm của bạn sẽ được tự động chuyển sang hình ảnh đầu tiên của mã biến thể tương ứng (nếu hình ảnh chính và Thumbnail Image bị trùng lặp thì sẽ được hợp nhất với hình ảnh đầu tiên của mã biến thể sản phẩm tương ứng). 
                            <a href="#" target="_blank">  Tìm hiểu thêm </a>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
    </div>
@endsection 


@section('content')
 
 
 
 
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
           // this.events();
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
                message: "{{ trans('sms.orders.status.comfirm') }}",
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
