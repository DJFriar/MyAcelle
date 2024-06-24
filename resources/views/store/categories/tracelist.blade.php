@if($smsCategory->total() > 0 )

<ul>
    <li>
        <div class="border">
            <input type="text" name="topsearch" id="topsearch">
            <ul>
                <li></li>
                <li></li>
            </ul>
        </div>
    </li>  
</ul>





<script>
    $(function() {
        Catelist.init();
        Catelist.datalist.load();
    });

    var Catelist = {
        init: function() {
            // khởi tạo biến list cho trang SmsCampaignIndex
            this.datalist = new DataList({

                // param 1: container chứa list
                container: document.querySelector('#CatelList'),

                // param 2: link tới trang list
                url: '{{ action('Store\CategoryController@list_level_one') }}', 

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
            this.perPage = options.perPage; 
            this.page =  options.page ?? 1 ;
            this.keyword = '';
            
            this.catlist_one = '';
            this.catlist_two = '';
            this.catlist_three = '';
            this.catlist_four = '';
            this.catlist_five = '';
            this.catid = '';

            this.sort = '';
            this.view = '{{ request()->view }}';
            // gan events sau khi load trang
            this.build();
        }
        getContainer() {
            return this.container;
        }         
        getKeyword() {
            return this.keyword;
        }
        setKeyword(value) {
            this.keyword = value.trim();
        } 
        getCatID() {
            return this.catid;
        }
        setCatID(catid) {
            this.catid = catid;
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
            this.url = '{{ action('Store\CategoryController@list') }}' + '?page='+this.page;
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
        }

        /**
         *  gán sự kiện bên ngoài Datalist ( gán lần đầu)
        */
       
        build(){
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
                    catid: this.getCatID(),
                }
            }).done(function(response){
                $(_this.getContent()).html(response);
 
                if(_this.getView() !='grid'){ 
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
                }else{
                    // bắt sự kiện khi nhấn vào link pagination
                    _this.getPaginationLinks().forEach(link => {
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
                }

            }).fail(function(jqXHR, textStatus, errorThrown){

            }).always(function() {
                   
               _this.removeLoadingEffect();
            });
        }

    } 
 
</script>



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
                message: "{{ trans('store.sms_template.delete._confirm') }}",
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

