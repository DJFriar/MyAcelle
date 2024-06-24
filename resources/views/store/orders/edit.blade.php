@extends('layouts.core.frontend', [
	'menu' => 'product',
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
                            <li class="breadcrumb-item"><a class="text-muted2" href="{{ action("Store\ProductController@index") }}">{{  trans('store.product') }}</a></li>
                            <li class="breadcrumb-item">{{  trans('store.product.create.createnew') }}</li>
                        </ul>
                    </nav> 
                    <div class="title-area d-flex justify-content-between align-items-center"> 
                        <h3 class="title-head py-0" style="font-size: 24px;color: rgba(0,0,0,.65);font-weight: 600;">
                            Thêm sản phẩm
                        </h3>  
                    </div> 
                </div>
            </div> 
        </div>
    </div>
@endsection

@section('content')
  
<div class="align-items-sm-center"> 
    <form id="sendingserverCreate" enctype="multipart/form-data" action="{{ action('Store\ProductController@update',[
                        'product' => $product->id, 
                        'page' => request()->page,
                    ]) }}" method="POST">
                    {{ csrf_field() }}
                    @method('PUT') 

    <div class="row">
        <div class="col-md-9 col-12 pr-0">
            @include('store.products._form',[ 'title' => 'Update Products'])                
            <div class="col-12 mt-2 my-3 ">
                <button type="submit" class="btn btn-primary mr-1 mb-1 waves-effect waves-float waves-light">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-save"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                    {{ trans('store.product.save') }}
                </button> 
                <a href="{{ action('Store\ProductController@index') }}" class="btn btn-link ms-2 ">
                    {{ trans('messages.cancel') }}
                </a>
            </div>
        </div> 
        <div class="col-lg-3 col-12 pl-2">
            <div class="sticky-lg-top"> 
            
                <!-- tìh trạng nhập liệu -->
                <div class="card card-body mb-2 mb-lg-5 laza-card productprogress">
                    <div class="card-header-title d-flex justify-content-between">
                        <div class="pr-3">
                            <span class="fs-6" style="line-height: 12px ">
                                Điểm nội dung
                            </span>
                        </div>
                        <div class="text-muted2 pr-2">
                            <span style="line-height: 10px; font-size:12px ">
                                Đang cập nhật
                            </span>
                        </div>
                        <span class="material-symbols-rounded spaninfo info-icons" style="font-size: 15px;float: left;line-height: 20px;;font-size:20pxx;color: #b3b3b3;font-style: normal;text-transform: none;">
                            info
                        </span>
                     </div> 
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="progress flex-grow-1" style="margin: 10px 0 0 0;--bs-progress-height: 0.5rem">
                            <div class="progress-bar" role="progressbar" style="width: 15%;" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <span class="ms-4">15%</span>
                    </div> 
                    <div class="fs-7">
                        <span class="text-warning">
                            Week
                        </span>
                    </div>
                </div>
                <div class="card laza-card mb-2 mb-lg-5" style="position: relative;"> 
            

                    <!-- Header -->
                    <div class="card-header bg-white card-header-content-between">
                        <div class="card-header-title fs-5">Basic Infomation</div>
                    </div>
                    <!-- End Header -->

                    <!-- Body -->
                    <div class="steps pl-3 pt-2">
                    
                        <div class="full-size"> 
    
                                <div class="bulets"> 
                                    <ul>
                                        <li class="active d-flex justify-content-start mb-4">
                                            <div class="roundUnCheckeds"></div>
                                            <div class="inner-panels">
                                                <div class="col-xs-3">
                                                    <a href="#coban" class="bullet-item-link">
                                                        <span class="fw-bold">Thông tin cơ bản</span> 
                                                    </a>
                                                </div> 
                                            </div>
                                        </li>
                                        <li class=" d-flex justify-content-start  mb-4">
                                            <div class="roundUnCheckeds"></div> 
                                            <div class="inner-panels"> 
                                                <div class="col-xs-3">
                                                    <a href="#dactinh" class="bullet-item-link">
                                                        <span class="fw-bold">Đặc tính sản phẩm</span> 
                                                    </a>
                                                </div> 
                                            </div>
                                        </li>
                                        <li class="d-flex justify-content-start mb-4">
                                            <div class="roundUnCheckeds"></div>
                                            <div class="inner-panels">
                                                <div class="col-xs-3">
                                                    <a href="#giaban" class="bullet-item-link">
                                                        <span class="fw-bold">Giá bán, kho hàng và biến thể</span> 
                                                    </a>
                                                </div> 
                                            </div>
                                        </li>
                                        <li class="d-flex justify-content-start mb-4">
                                            <div class="roundUnCheckeds"></div>
                                            <div class="inner-panels">
                                                <div class="col-xs-3">
                                                    <a href="#motasanpham" class="bullet-item-link">
                                                        <span class="fw-bold">Mô tả sản phẩm</span> 
                                                    </a>
                                                </div> 
                                            </div>
                                        </li>
                                        <li class="d-flex justify-content-start mb-4">
                                            <div class="roundUnCheckeds"></div>
                                            <div class="inner-panels">
                                                <div class="col-xs-3">
                                                    <a href="#vanchuyenvabaohanh" class="bullet-item-link">
                                                        <span class="fw-bold">Vận chuyển và bảo hành</span> 
                                                    </a>
                                                </div> 
                                            </div>
                                        </li>
                                    </ul>

                                </div> 
                        
                        </div> 
                    </div>   
                    <!-- End Body -->
                </div>

                <div class="card laza-card  card-body mb-3 mb-lg-5">
                    <div class="card-header-title fs-5 pb-2">Tips </div>  
                    <div class="d-flex justify-content-between align-items-center">
                        <p>Vui lòng tải lên hình ảnh, điền tên sản phẩm và chọn đúng ngành hàng trước khi đăng tải sản phẩm.</p>
                    </div> 
                </div>

            </div> 
        </div>
    </div>
    </form>
</div>  

 
<script>
    $(document).ready(function () {

        const indicators = document.querySelectorAll(".bullet-item-link");
        const sections = document.querySelectorAll(".laza-section");

        const resetCurrentActiveIndicator = () => { 
            var list_bulets =  document.querySelectorAll(".bullet-item-link");
            list_bulets.forEach(list_bulet=>{
                list_bulet.closest('li').classList.remove("active"); 
            });
        }; 
        const onSectionLeavesViewport = (section) => {
            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            resetCurrentActiveIndicator();
                            const element = entry.target; 
                            //console.log(element);
                            const indicator = document.querySelector(`a[href='#${element.id}']`); 
                            indicator.closest('li').classList.add("active");
                            return;
                        }
                    });
                },
                {
                    root: null,
                    rootMargin: "0px",
                    threshold: 0.75
                }
            );
            observer.observe(section);
        };
        /*
        indicators.forEach((indicator) => {
            indicator.addEventListener("click", function (event) {
                event.preventDefault();
                document
                    .querySelector(this.getAttribute("href"))
                    .scrollIntoView({ behavior: "smooth" });
                resetCurrentActiveIndicator();
                this.closest('li').classList.add("active");
            });
        });
        */ 
        sections.forEach(onSectionLeavesViewport);

        /*
            code already runing
        */


        $(".bullet-item-link").each(function(e) { 
            var my = this;
            var parrent =  $(this).parent().parent().parent();  
            // parrent.removeClass('active') ;  // remove active when load 
            my.addEventListener("click", function() {  
                $(".bullet-item-link").each(function(e) {
                    $(this).parent().parent().parent().removeClass('active') ;   // remove active when click
                }); 
                parrent.addClass('active'); 
            });
        });
        // set active 

        $('select[name="tags"]').change(function() {
            var $option = $(this).find('option:selected');
            var value = $option.val() ;
            var text = $option.text() ;
            var curPos =   document.getElementById("messange").selectionStart;
            let x = $("#messange").val(); 
            $("#messange").val(
                x.slice(0, curPos) + '{' + value + '}' + x.slice(curPos)
            );
        });
    });
</script>
@endsection