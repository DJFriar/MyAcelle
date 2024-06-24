<div class="row">
    <div class="col-md-9 col-12 pr-0">
        <div class="laza-card laza-section" id="coban">
            <div class=" px-4 pt-3 rounded shadow-sm bg-white">
                <h4 class="mb-2 fw-semibold">Thông tin cơ bản</h4> 
                <div class="mb-4"> 
                    <label for="picture" class="form-label fw-semibold">Add new Product</label>
                    <p>
                        This is the main image on the product page. You can upload multiple images at once, with a maximum of 8 images.
                        The images should have a size ranging from 330x300 px to 5000x5000 px, and they must not contain sensitive content.
                        The maximum file size allowed is 3 MB.
                    </p>
                    <div form-control="images-container" class="mb-3">
                        <div class="upload-area">
                            <div form-control="thumbs" class="mb-3 upload-items"> 
                                {{-- <div class="upload-item">
                                    <a id="pickfiles" href="javascript:;"> 
                                        <div class="upload-icons holder-icons"></div>
                                    </a>
                                </div>  --}}
                            </div>
                            <div class="upload-action">
                                <div id="container">
                                    <a form-control="upload-image" href="javascript:;">Upload Image</a>
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>
    
                <div class="mb-4"> 
                    <label class="form-label required">{{ trans('store.product.name') }}</label>
                    <input type="text" name="title" value="{{ $product->title }}""
                        class="form-control form-control-sm rd-10 {{ $errors->has('title') ? 'is-invalid' : '' }}"
                    ">
                    @if ($errors->has('title')) 
                        <div class="invalid-feedback"> {{ $errors->first('title') }} </div>
                    @endif
                </div>
                <div class="mb-3 laza-card" style="background-color: var(--color-notice-1, #f5f8fe);"> 
                    <div class="list-suggest p-3">
                        <div class="pb-2">Gọi ý ngành hàng</div>
    
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="option1" checked>
                            <label class="form-check-label" for="exampleRadios1">
                                Máy vi tính & Laptop > Máy tính để bàn > Máy tính tự lắp ráp
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="option2">
                            <label class="form-check-label" for="exampleRadios2">
                                Màn hình & Máy in > Màn hình vi tính
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios3" value="option3" disabled>
                            <label class="form-check-label" for="exampleRadios3">
                                Máy vi tính & Laptop > Laptop > Laptop cơ bản
                            </label>
                        </div> 
                    </div>
                </div>
                <div class="mb-4"> 
                    <span class="require fs-17">*</span>
                    <label class="form-label required" for="category">{{ trans('store.product.category') }}</label>   
                    <select from-control="category" name="category_uid" class="form-control form-select form-select-sm"> 
                        <option value="">Select category</option>
                        @foreach(\Acelle\Model\Category::all() as $category) 
                            <option {{ $product->category_id == $category->id ? 'selected' : '' }} value="{{ $category->uid }}">
                                {{ $category->name }}
                            </option>
                        @endforeach  
                    </select>
                    @if ($errors->has('category')) 
                        <div class="invalid-feedback"> {{ $errors->first('category') }} </div>
                    @endif 
                    <div id="categorylHelp" class="form-text pl-2 pt-1"> 
                        Gần đây sử dụng:
                        <a href="#" class="pl-2 rounded">Máy tính tự lắp ráp</a>
                        <a href="#" class="pl-2 rounded">Laptop chơi game</a>
                        <a href="#" class="pl-2 rounded">Laptop cơ bản</a>
                    </div> 
                </div>  
    
                <div class="mb-3">
                    
                    <p class="mb-2 text-muted"> 
                        <span>
                            Hình ảnh quảng cáo cho người mua
                        </span>
                    </p>
                    <p class="mb-4 text-muted2"> 
                        <span>
                            Ảnh thu nhỏ sẽ được dùng để hiển thị sản phẩm của bạn ở một số nơi như trang kết quả tìm kiếm, trang gợi ý sản phẩm, vv... Ảnh thu nhỏ giúp thu hút người dùng nhấp vào sản phẩm của bạn.
                        </span>
                    </p>
                    <div class="mb-3">
                        @if($product->file !='')
                            <img class="proimage rounded-3" src="{{ asset('storage/products/'.$product->file) }}" alt="" title="" style="width:150px"> 
                            <div class="mb-3">
                                <label for="formFileSm" class="form-label">Thay ảnh khác( Tỉ lệ ảnh 1:1)</label>
                                <input class="form-control form-control-sm" id="picture" name="picture" type="file">
                            </div>
                        @else
                            <label for="formFileSm" class="form-label">Ảnh vuông ( Tỉ lệ ảnh 1:1)</label>
                            <input class="form-control form-control-sm" id="picture" name="picture" type="file">
                        @endif
                    </div>
                </div>
            </div>
        </div>
    
        <div class="laza-card mt-3 laza-section" id="dactinh">
            <div class=" px-4 pt-3 rounded shadow-sm bg-white">
                <h4 class="mb-1">Đặc tính sản phẩm</h4>
                <div class="mb-4 text-muted2"> 
                    <span>Cung cấp đầy đủ đặc tính sản phẩm để tối ưu kết quả tìm kiếm sản phẩm.</span>
                </div>
                <div class="mb-4">
                    <div from-control="attributes-box">
                            
                    </div>
                </div>   
            </div>
        </div>
    
        <div class="laza-card mt-3 laza-section" id="giaban"> 
            <div class=" px-4 pt-3 rounded shadow-sm bg-white">
                <h4 class="mb-1">Giá bán, Kho hàng và Biến thể</h4>
                <div class="mb-4 text-muted2"> 
                    <span>Tạo biến thể nếu sản phẩm có hơn một tùy chọn, ví dụ như về kích thước hay màu sắc.</span>
                </div> 
                
                <div class="mb-4"> 
                    
                    <div class="product-container product-bdr ml-0 mr-0">
                        <table class="table border product-list-table table-inside"> 
                            <thead class="product-header-bg">
                                <tr>
                                    <th>Giá</th>
                                    <th>Giá đặc biệt </th>
                                    <th>Kho hàng</th> 
                                    <th>SallerSku</th>
                                    <th>Bộ sản phẩm bao gồm</th>
                                    <th>GTN</th>
                                    <th>Mở bán</th>
                                </tr>
                            </thead>
                            <tbody> 
                                
                                <tr> 
                                    <td style="text-align: center">
                                        <input type="email" class="form-control me-2" id="exampleFormControlInput1" placeholder="Giá">
                                    </td>
                                    <td>
                                        <a href="#">
                                            Thêm
                                        </a>
                                    </td>
                                    <td>
                                        <input type="email" class="form-control me-2" id="exampleFormControlInput1" placeholder="Kho hàng">
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-start">
                                            <input type="email" class="form-control me-2" id="exampleFormControlInput1" placeholder="SallerSku">
                                        </div>
                                    </td> 
                                    <td>
                                        <div class="d-flex justify-content-start">
                                            <input type="email" class="form-control me-2" id="exampleFormControlInput1" placeholder="">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="pt-1 me-1 form-switch" list-action="status-update">
                                            <input type="email" class="form-control me-2" id="exampleFormControlInput1" placeholder="">
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <div class="pt-1 me-1 form-switch" list-action="status-update">
                                            <input class="form-check-input cbstatus" type="checkbox" role="switch"  
                                            data-id="1" class="toggle-class"  
                                            data-onstyle="success" data-offstyle="danger" data-toggle="toggle"
                                            data-on="active" data-off="inactive" checked>
                                        </div>
                                    
                                    </td>                  
                                </tr>
                            
                            </tbody>
                        </table>
                    </div>
    
                </div>
            </div>
        </div>
    
        <!-- Đặc tính sản phẩm -->
        <div class="laza-card mt-3 laza-section" id="motasanpham"> 
            <div class=" px-4 pt-3 rounded shadow-sm bg-white" id="motasanpham">    
                <h4 class="mb-4">Mô tả sản phẩm</h4>
                <div class="mb-4">  
                    <textarea  id="content" rows="10" class="form-control {{ $errors->has('content') ? 'is-invalid' : '' }} p-2 bg-light border rounded"
                        name="content"
                    >
                    {{ $product->content }}</textarea>
                    @if ($errors->has('content')) 
                        <div class="invalid-feedback"> {{ $errors->first('content') }} </div>
                    @endif
                    <div id="contentlHelp" class="form-text">   {{  trans('store.product.remaining') }}  </div> 
                </div>
            </div>
        </div>
        <!-- Đặc tính sản phẩm -->
    
        <!-- bảo hành và van chuyển -->
        <div class="laza-card mt-3 laza-section" id="vanchuyenvabaohanh">
            <div class=" px-4 pt-3 rounded shadow-sm bg-white">
                <h4 class="mb-4">Vận chuyển và bảo hành</h4>
                <div class="mb-4">
                    <p>Kích thước phân loại sản phẩm khác nhau</p>
                    <div class="form-switch" list-action="status-update">
                        <input class="form-check-input cbstatus" type="checkbox" role="switch"  
                        data-id="{{ $product->id }}" class="toggle-class"  
                        data-onstyle="success" data-offstyle="danger" data-toggle="toggle"
                        data-on="active" data-off="inactive" {{ $product->status =='active'? 'checked' : '' }}>
                        <span>Bật lên nếu các biến thể có kích thước và trọng lượng khác nhau</span>
                    </div>
                </div>
                <div class="mb-4"> 
                    <label class="form-label required" for="price">* Khối lượng kiện hàng</label> 
                    <div class="d-flex justify-content-start">
                        <div class="form-outline me-1">
                            <input type="number" id="typeNumber" class="form-control" />
                        </div> 
                        <select class="form-select select w-auto">
                            <option value="1" selected>kg</option>
                            <option value="2">g</option>
                        </select>
                    </div>
                </div>
                <div class="mb-4"> 
                    <label class="form-label required" for="price">* Kích thước kiện hàng (cm): Dài * Rộng * Cao</label>
                    <div class="d-flex justify-content-between">
                        <div class="form-outline">
                            <input type="number" id="typeNumber" class="form-control" />
                        </div> 
                        <div class="form-outline">
                            <input type="number" id="typeNumber" class="form-control" />
                        </div> 
                        <div class="form-outline">
                            <input type="number" id="typeNumber" class="form-control" />
                        </div> 
                    </div>
                </div>
                <div class="mb-4"> 
                    <label class="form-label required" for="price">Hàng hóa nguy hiểm</label>
                    <div class="d-flex justify-content-start">
                        <div class="form-check mr-4">
                            <input class="form-check-input" type="radio" name="flexRadioDisabled" id="flexRadioDisabled" >
                            <label class="form-check-label" for="flexRadioDisabled">
                                Không có
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="flexRadioDisabled" id="flexRadioCheckedDisabled" checked >
                            <label class="form-check-label" for="flexRadioCheckedDisabled">
                                Chứa pin / chất cháy / chất lỏng
                            </label>
                        </div>
                    </div>
                </div>
                <div class="mb-4"> 
                    <label class="form-label">Tùy chọn vận chuyển</label>
                    <p class="mt-2">
                        Giao hàng tiêu chuẩn là tùy chọn vận chuyển mặc định. Lưu ý rằng cài đặt về vận chuyển chỉ áp dụng cho sản phẩm này và phương thức vận chuyển khả dụng sẽ tùy vào vị trí kho hàng.
                        Tùy chọn bạn vô hiệu sẽ không được sử dụng để vận chuyển sản phẩm này.
                    </p>
                    <div class="d-flex justify-content-between pb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="flexRadioDisabled" id="flexRadioDisabled">
                            <label class="form-check-label" for="flexRadioDisabled">
                                Vận chuyển bởi Brand
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="flexRadioDisabled" id="flexRadioCheckedDisabled" checked>
                            <label class="form-check-label" for="flexRadioCheckedDisabled">
                                Vận chuyển bởi nhà bán hàng
                            </label>
                        </div>
                        <div class="form-check">
                            Áp dụng cho NBH đáp ứng được các tiêu chí của Brand
                        </div>
                    </div>
                    <p>
                        Giao hàng tiêu chuẩn mặc định sẽ áp dụng cho sản phẩm này.
                    </p>
                </div>
                
                <div class="mb-4"> 
                    <label class="form-label required" for="price">{{ trans('store.product.guarantee.category') }}</label>
                    <select class="form-select select" aria-label="Default select example"> 
                        <option value="1" selected >Bảo hành bởi Nhà sản xuất quốc tế</option>
                        <option value="2">Không bảo hành</option>
                        <option value="3">Bằng hộp sản phẩm hoặc Số seri</option>
                        <option value="3">Nhà cung cấp trong nước bảo hành</option>
                        <option value="4">Bảo hành bởi Nhà sản xuất trong nước</option>
                        <option value="5">Bảo hành bởi Nhà bán hàng quốc tế</option>
                        <option value="6">Bảo hành bởi Nhà phân phối trong nước</option> 
                    </select>
                </div>
    
                <div class="mb-4"> 
                    <label class="form-label required" for="price">{{ trans('store.product.guarantee') }}</label>
                    <select class="form-select select" aria-label="Default select example"> 
                        <option value="1" selected>1 tháng</option>
                        <option value="1">3 tháng</option>
                        <option value="1">6 tháng</option>
                        <option value="1">9 tháng</option>
                        <option value="2">1 Năm<option>
                        <option value="2">2 năm</option> 
                        <option value="2">3 năm</option> 
                        <option value="2">4 năm</option> 
                        <option value="2">5 năm</option> 
                        <option value="2">10 năm</option> 
                        <option value="2">20 năm</option> 
                        <option value="2">trọn đời</option> 
                    </select>
                </div>
    
                <div class="mb-4">
                    <label class="form-label" for="policy">{{ trans('store.product.policy') }}</label>
                    <input type="text" name="policy" class="form-control {{ $errors->has('policy') ? 'is-invalid' : '' }}"  name="policy">
                </div> 
                
            </div>
        </div>
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

                            <div form-control="navigation" class="bulets"> 
                                <ul>
                                    <li form-control="navigation-item" class="active d-flex justify-content-start mb-4">
                                        <div class="roundUnCheckeds"></div>
                                        <div class="inner-panels">
                                            <div class="col-xs-3">
                                                <a href="#coban" class="bullet-item-link">
                                                    <span class="fw-bold">Thông tin cơ bản</span> 
                                                </a>
                                            </div> 
                                        </div>
                                    </li>
                                    <li form-control="navigation-item" class=" d-flex justify-content-start  mb-4">
                                        <div class="roundUnCheckeds"></div> 
                                        <div class="inner-panels"> 
                                            <div class="col-xs-3">
                                                <a href="#dactinh" class="bullet-item-link">
                                                    <span class="fw-bold">Đặc tính sản phẩm</span> 
                                                </a>
                                            </div> 
                                        </div>
                                    </li>
                                    <li form-control="navigation-item" class="d-flex justify-content-start mb-4">
                                        <div class="roundUnCheckeds"></div>
                                        <div class="inner-panels">
                                            <div class="col-xs-3">
                                                <a href="#giaban" class="bullet-item-link">
                                                    <span class="fw-bold">Giá bán, kho hàng và biến thể</span> 
                                                </a>
                                            </div> 
                                        </div>
                                    </li>
                                    <li form-control="navigation-item" class="d-flex justify-content-start mb-4">
                                        <div class="roundUnCheckeds"></div>
                                        <div class="inner-panels">
                                            <div class="col-xs-3">
                                                <a href="#motasanpham" class="bullet-item-link">
                                                    <span class="fw-bold">Mô tả sản phẩm</span> 
                                                </a>
                                            </div> 
                                        </div>
                                    </li>
                                    <li form-control="navigation-item" class="d-flex justify-content-start mb-4">
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

<script>
    $(function() {
        // Images Uploader
        var imagesUploader = new ImagesUploader({
            container: document.querySelector( '[form-control="images-container"]' ),
            thumbList: document.querySelector( '[form-control="thumbs"]' ),
            uploadButton: document.querySelector( '[form-control="upload-image"]' ),
            urls: {!! json_encode($product->getImageUrls()) !!},
            inputName: 'images[]',
        });

        // Category Attribute selector
        var categoryAttributeForm = new CategoryAttributeForm({
            categorySelector: document.querySelector('[from-control="category"]'),
            attributesBox: document.querySelector('[from-control="attributes-box"]'),
            url: '{{ action('Store\ProductController@attributes', [
                'uid' => $product->id ? $product->uid : null,
            ]) }}',
        });

        // Navigation
        var formNavigation = new FormNavigation({
            container: document.querySelector('[form-control="navigation"]'),
        });
    });

    var FormNavigation = class {
        constructor(options) {
            this.container = options.container;

            // add events
            this.events()

            // detect current menu
            this.detectCurrentItemWhenScroll();
        }

        getContainer() {
            return this.container;
        }

        getItems() {
            return this.getContainer().querySelectorAll('[form-control="navigation-item"]');
        }

        goToItem(item) {
            // remove all active
            this.getItems().forEach(item => {
                item.closest('li').classList.remove('active');
            });

            // active current item
            item.closest('li').classList.add('active');
        }

        detectCurrentItemWhenScroll() {
            [...this.getItems()].reverse().forEach(item => {
                var target = document.querySelector(item.querySelector('a').getAttribute('href'));
                if (!this.preventScrollEvent && this.isScrolledIntoView(target)) {
                    this.goToItem(item);
                    return;
                }
            });
        }

        isScrolledIntoView(el) {
            var rect = el.getBoundingClientRect();
            var elemTop = rect.top;
            var elemBottom = rect.bottom;

            // Only completely visible elements return true:
            var isVisible = (elemTop >= 0) && (elemBottom <= window.innerHeight);
            // Partially visible elements return true:
            //isVisible = elemTop < window.innerHeight && elemBottom >= 0;
            return isVisible;
        }

        events() {
            // click item
            this.getItems().forEach(item => {
                item.addEventListener('click', (e) => {
                    this.preventScrollEvent = true;
                    this.goToItem(item);
                    setTimeout(() => {
                        this.goToItem(item);
                        this.preventScrollEvent = false;
                    }, 500);
                });
            });

            // when scroll
            document.addEventListener("scroll", (event) => {
                this.detectCurrentItemWhenScroll();
            });
        }
    }

    var CategoryAttributeForm = class {
        constructor(options) {
            this.categorySelector = options.categorySelector;
            this.attributesBox = options.attributesBox;
            this.url = options.url;

            // load first time
            this.loadAttributeBox();

            // events
            this.events();
        }

        getCategorySelector() {
            return this.categorySelector;
        }

        getAttributeBox() {
            return this.attributesBox;
        }

        getCategoryUid() {
            return this.getCategorySelector().value;
        }

        getUrl() {
            return this.url;
        }

        events() {
            var _this = this;

            // change category
            this.getCategorySelector().addEventListener('change', (e) => {
                _this.loadAttributeBox();
            });
        }

        loadAttributeBox() {
            var _this = this;
            var uid = this.getCategoryUid();

            if (!uid) {
                this.getAttributeBox().innerHTML = `
                    <p class="alert alert-warning">No category selected!</p>
                `;

                return;
            }

            // load attributes from ajax
            $.ajax({
                url: this.getUrl(),
                type: 'GET',
                data: {
                    category_uid: this.getCategoryUid(),
                }
            }).done(function(response) {
                $(_this.getAttributeBox()).html(response);
            }).fail(function(jqXHR, textStatus, errorThrown){
            }).always(function() {
            });
        }
    }

    var ImagesUploader = class {
        constructor(options) {
            this.container = options.container;
            this.thumbList = options.thumbList;
            this.thumbs = [];
            this.inputName = options.inputName;
            this.uploadButton = options.uploadButton;

            // add thumb from url
            if (options.urls) {
                options.urls.forEach(url => {
                    this.addThumb(new ImagesUploaderThumb(this, {
                        url: url
                    }));
                });
            }

            // add 1 placeholder thumb
            this.addPlaceholderThumb();

            //
            this.events();
        }

        events() {
            var _this = this;

            // click upload button
            this.uploadButton.addEventListener('click', (e) => {
                this.getFirstEmptyThumb().browseFile();
            })

            // drop
            this.container.addEventListener(
                "drop",
                (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    // let draggedData = e.dataTransfer;
                    // let files = draggedData.files;
                    // Array.from(files).forEach((file) => {
                    //     _this.handleDropFile(file, file.name, file.type);
                    // });
                },
                false
            );
        }

        addThumb(thumb) {
            this.thumbs.push(thumb);
            this.thumbList.appendChild(thumb.getElement());
        }

        addPlaceholderThumb() {
            this.addThumb(new ImagesUploaderThumb(this, {}));
        }

        getFirstEmptyThumb() {
            var theThumb = null;
            
            this.thumbs.forEach(thumb => {
                if (thumb.isEmpty()) {
                    theThumb = thumb;
                }
            });

            if (theThumb == null) {
                alert('Exception: no empty thumb!');
            }

            return theThumb;
        }

        checkIfAllThumbsOccupied() {
            let isFull = true;

            this.thumbs.forEach(thumb => {
                if (thumb.isEmpty()) {
                    isFull = false;
                }
            });

            return isFull;
        }

        onChange() {
            // add placeholder if full
            if (this.checkIfAllThumbsOccupied()) {
                this.addPlaceholderThumb();
            }
        }

        onRemove() {
            let emptyThumbs = [];

            this.thumbs.forEach(thumb => {
                // count
                if (thumb.isEmpty()) {
                    emptyThumbs.push(thumb);
                }
            });

            emptyThumbs = emptyThumbs.reverse().slice(-1);
            emptyThumbs.forEach(thumb => {
                this.removeThumb(thumb);
            });
        }

        removeThumb(thumb) {
            thumb.element.remove();
            this.thumbs = this.thumbs.filter(function(tb) { return tb.id != thumb.id; });
        }

        handleDropFile(file, name, type) {
            if (type.split("/")[0] !== "image") {
                //File Type Error
                alert("Please upload an image file");
                return false;
            }
            // let reader = new FileReader();
            // reader.readAsDataURL(file);
            // reader.onloadend = () => {
            //     // img.src = reader.result;
            // };

            let src = URL.createObjectURL(file);
            console.log(src);
        };
    }

    var ImagesUploaderThumb = class {
        constructor(uploader, options) {
            // 
            this.uploader = uploader;
            this.url = options.url ?? null;
            this.element = null;
            this.id = Math.random().toString(16).slice(2);

            // create element
            this.createElement();
        }

        events() {
            // click on placeholder
            this.getBrowseButton().addEventListener('click', e => {
                this.browseFile();
            });

            // preview image after browse
            this.getFileInput().addEventListener('change', e => {
                // if image exists, delete Image
                if (this.hasImage()) {
                    this.setDeleted();
                }
                

                // preview thumb
                this.preview();

                // on change callback
                this.uploader.onChange();
            });

            // click remove button
            this.getRemoveButton().addEventListener('click', e => {
                this.remove();
            });
        }

        remove() {
            // remove browsed file if existed
            this.getFileInput().value = '';

            // if image exists, delete Image
            if (this.hasImage()) {
                this.setDeleted();
            }

            // re-render thumb
            this.render();

            // 
            this.uploader.onRemove();
        }

        setDeleted() {
            // set as delete image
            this.deleteInput = document.createElement('input');
            this.deleteInput.setAttribute('name', 'delete_images[]');
            this.deleteInput.setAttribute('value', this.getUrl());

            // add to element
            this.uploader.container.appendChild(this.deleteInput);
            
            // reset url to null
            this.setUrl(null);
        }

        isEmpty() {
            return this.isPlaceholder() && this.getPreviewImage() == null;
        }

        getPreviewImage() {
            return this.element.querySelector('[thumb-control="preview-image"]');
        }

        preview() {
            const [file] = this.getFileInput().files;

            if (file) {
                let src = URL.createObjectURL(file);
                // prepair image tag
                this.getImageContainer().innerHTML = `
                    <div thumb-control="placeholder">
                        <img thumb-control="preview-image" width="100%" src="`+src+`" />
                    </div>
                `;

                // show remove button
                this.showRemoveButton();
            }
        }

        browseFile() {
            this.getFileInput().click();
        }

        getBrowseButton() {
            return this.element.querySelector('[thumb-action="browse"]');
        }

        isPlaceholder() {
            return this.url == null;
        }

        hasImage() {
            return this.url != null;
        }

        getUrl() {
            return this.url;
        }

        setUrl(url) {
            this.url = url;
        }

        getFileInput() {
            return this.element.querySelector('[thumb-control="input"]');
        }

        getImageContainer() {
            return this.element.querySelector('[thumb-control="image-container"]');
        }

        getRemoveButton() {
            return this.element.querySelector('[thumb-control="remove"]');
        }

        createElement() {
            this.element = document.createElement('div');

            this.render();
        }

        render() {
            // input field
            this.element.innerHTML = `
                <input thumb-control="input" accept="image/*" type="file" name="`+this.uploader.inputName+`" style="display:none" />
            `;

            // placeholder
            if (this.isPlaceholder()) {
                this.renderPlaceholder();
            // has url
            } else {
                this.renderExistedImage();

                // show remove button
                this.showRemoveButton();
            }

            // event
            this.events();
        }

        renderPlaceholder() {
            this.element.innerHTML += `
                <div class="position-relative">
                    <div class="upload-item">
                        <a thumb-action="browse" href="javascript:;" thumb-control="image-container"> 
                            <div thumb-control="placeholder">
                                <div class="upload-icons holder-icons"></div>
                            </div>
                        </a>
                    </div>
                    <span thumb-control="remove" class="remove-image btn btn-danger py-0 px-1" style="position:absolute;top:2px;right:2px;display:none;z-index:1;">
                        <span class="material-symbols-rounded">delete</span>
                    </span>
                </div>
            `;
        }

        showRemoveButton() {
            this.getRemoveButton().style.display = 'block';
        }

        hideRemoveButton() {
            this.getRemoveButton().style.display = 'none';
        }

        renderExistedImage() {
            this.element.innerHTML += `
                <div class="position-relative">
                    <div class="upload-item">
                        <a thumb-action="browse" href="javascript:;" thumb-control="image-container"> 
                            <img width="100%" src="`+this.getUrl()+`" />
                        </a>
                    </div>
                    <span thumb-control="remove" class="remove-image btn btn-danger py-0 px-1" style="position:absolute;top:2px;right:2px;display:none;z-index:1;">
                        <span class="material-symbols-rounded">delete</span>
                    </span>
                </div>
            `;
        }

        getElement() {
            return this.element;
        }
    }

</script>