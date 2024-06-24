<script src="https://cdn.ckeditor.com/ckeditor5/23.0.0/classic/ckeditor.js"></script>

<script src="{{ asset('core/js/plupload.full.min.js')  }}"></script>

 
<div class="laza-card laza-section" id="coban">
    <div class=" px-4 py-3 rounded shadow-sm bg-white mb-4">
        <h4 class="mb-2">Thông tin cơ bản</h4> 
        <div class="mb-4"> 
            <label for="picture" class="form-label">Ảnh đại diện</label>
            <p>{{ $product->status }}</p>
            <p>{{ $product->uid }}</p>
            <p>
                Đây là hình ảnh chính trên trang sản phẩm. Bạn có thể up nhiều hình ảnh cùng lúc và tối đa có thể có 8 hình. Hình ảnh cần có kích thước từ 330x300 px đến 5000x5000px và không dược phép chứa nội dung nhạy cảm. Kích thước tối đa: 3 MB
            </p>
            <div class="mb-3">
                <div class="upload-area">
                    <div id="filelist" class="mb-3 upload-items"> 
                        <div class="upload-item">
                            <a id="pickfiles" href="javascript:;"> 
                                <div class="upload-icons holder-icons"></div>
                            </a>
                        </div> 
                    </div>
                    <div class="upload-action">
                        <div id="container">
                            
                            <a id="uploadfiles" href="javascript:;">Đăng tải</a> | <a id="managerfiles" href="javascript:;">Thư viện đa phương tiện</a>
                        
                        </div>
                    </div>
                </div>
            </div> 
        </div>

        <div class="mb-4"> 
            <label class="form-label required" for="name">{{ trans('store.product.name') }}</label>
            <input type="text" id="name" name="name" class="form-control form-control-sm rd-10 {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ $product->title }}" name="name">
            <input type="hidden" id="uid" name="uid" value="{{ $product->uid }}">
            @if ($errors->has('name')) 
                <div class="invalid-feedback"> {{ $errors->first('name') }} </div>
            @endif
            <div class="input-group input-group-sm mt-3">
                <span class="input-group-text rd-lt-10 rd-lb-10" id="basic-addon1">Endlish</span>
                <input type="text" class="form-control rd-rt-10 rd-rb-10" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
            </div>
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
            <select name="categories_id" id="categories_id" class=" form-control form-select form-select-sm rd-10" onchange="make_dactinh(this)"> 
                @foreach($smsCategory as $key => $smsCategory) 
                    <option value="{{ $smsCategory->id }}" 
                            {{ $product->categories_id == $smsCategory->id ? 'selected':''}} >
                            {{ $smsCategory->name ?? '' }}
                    </option>
                    @if($smsCategory->children)                    
                        @forEach($smsCategory->children as $category) 
                        <option value="{{ $category->id }}"
                            {{ $product->categories_id == $category->id ? 'selected':''}} >
                            -> {{ $category->name ?? '' }}
                        </option>    
                        @endforeach
                    @endif
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
    <div class=" px-4 py-3 rounded shadow-sm bg-white mb-4">
        <h4 class="mb-1">Đặc tính sản phẩm</h4>
        <div class="mb-4 text-muted2"> 
            <span>Cung cấp đầy đủ đặc tính sản phẩm để tối ưu kết quả tìm kiếm sản phẩm.</span>
        </div>
        <div class="mb-4">
            <div class="row" id="dactinh_content">
                @if($product->attribute) 
                    @foreach( collect(json_decode( $product->attribute ))  as $key => $item )  
                    <div class="col col-sm-6">
                        <div class="mb-3">
                            <label for="{{ $key }}" class="form-label">{{ Acelle\Model\SmsAttribute::where('uid', $key)->first()->name }}</label>
                            <input class="form-control form-control-sm" type="text" id="{{ $key }}" name="attribute[{{ $key }}]" value="{{ $item }}">
                        </div>
                    </div> 
                    @endforeach 
                @endif 
            </div>
            
        </div>   
    </div>
</div>

<div class="laza-card mt-3 laza-section" id="giaban"> 
    <div class=" px-4 py-3 rounded shadow-sm bg-white mb-4">
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
    <div class=" px-4 py-3 rounded shadow-sm bg-white mb-4" id="motasanpham">    
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
    <div class=" px-4 py-3 rounded shadow-sm bg-white mb-4">
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
                <div class="form-outline">
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
<!-- bảo hành và van chuyển -->
<script>



function build_element( label, etype, ename, eid, eclass , eplaceholder )
{   
    var eLement = document.createElement('input');
    eLement.setAttribute("type", etype);
    eLement.setAttribute("id",  eid);
    eLement.setAttribute("name",  'attribute['+ename+']');
    eLement.setAttribute("class", eclass);
    eLement.setAttribute("placeholder", eplaceholder);
    var iDiv = document.createElement('div'); 
    iDiv.className = 'col col-sm-6'; 
    
    var subDiv = document.createElement('div'); 
    subDiv.className = 'mb-3';
    
    var sublabel = document.createElement('label'); 
    sublabel.className = 'form-label';
    sublabel.setAttribute("for",  'label');
    sublabel.innerHTML =label;
    
    subDiv.appendChild(sublabel);
    subDiv.appendChild(eLement); 

    iDiv.appendChild(subDiv); 
    var formdactinh = document.getElementById("dactinh_content");
    formdactinh.appendChild(iDiv);
}
 
function make_dactinh(item){
    const myNode = document.getElementById("dactinh_content");
    myNode.innerHTML = '';
    $.ajax({
        url: "",
        type: 'GET',
        data: {
            catid:  item.value,
        }
    }).done(function(response){
        if(response){ 
            response.forEach(element => {
                build_element (element.name, 'text', element.uid, element.id, 'form-control', '');
            });
        } 
    }).fail(function(jqXHR, textStatus, errorThrown){

    }).always(function() {
        
    });
}

</script>
<script>

/*
$(document).on('input',function () { 
    $.ajax({
        url: "{{ action('Store\ProductController@selfToDrap') }}",
        type: 'POST',
        data: { 
            uid: '{{ $product->uid }}',
            name: document.getElementById('name').value,
        }
    }).done(function(response){
        console.log('salf to drap');
    }).fail(function(jqXHR, textStatus, errorThrown){

    }).always(function() {
        
    });
});
*/

</script>

<script>
var uploader = new plupload.Uploader({
	runtimes : 'html5,flash,silverlight,html4',
	browse_button : 'pickfiles', // you can pass an id...
	container: document.getElementById('container'), // ... or DOM Element itself
	url : "{{ action('FileUploadController@store') }}",
	flash_swf_url : '../js/Moxie.swf',
	silverlight_xap_url : '../js/Moxie.xap',
    /*
	views: {
                list: true,
                thumbs: true, // Show thumbs
                active: 'thumbs'
            },
    */
	filters : {
		max_file_size : '10mb',
		mime_types: [
			{title : "Image files", extensions : "jpg,gif,png"},
			{title : "Zip files", extensions : "zip"}
		]
	},

	init: {
		PostInit: function() {
			//document.getElementById('filelist').innerHTML = '';
			document.getElementById('uploadfiles').onclick = function() {
				uploader.start();
				return false;
			};
		},
        FilesAdded: function(up, files) {
        },
        /*
		FilesAdded: function(up, files) {
			plupload.each(files, function(file) {
				document.getElementById('filelist').innerHTML += 
                    '<div class="upload-item"><div class="file-item" id="' + file.id + '">' + 
                    '<div id="preview"></div>' + 
                    '</div><div class="upload-item-remove"><a href="javascript:;" class="file-item-remove"><span class="material-symbols-rounded">delete</span></a></div></div>';
			});
            remove_file_item();
		},
        */
		UploadProgress: function(up, file) {
            /*
			    document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
            */
		},

		Error: function(up, err) {
            /*
			document.getElementById('console').appendChild(document.createTextNode("\nError #" + err.code + ": " + err.message));
            */
		},
	}
});
uploader.init();

// PROGRESS ADDED HANDLER
uploader.bind('FilesAdded', function(up, files) {
    $.each(files, function(file){ 
		var img = new moxie.image.Image(); 
		img.onload = function( ) { 
            //li.innerHTML = '<div class="upload-item-remove"><a href="javascript:;" class="file-item-remove"><span class="material-symbols-rounded">delete</span></a></div></div>';
            var alink = document.createElement('a');
            alink.setAttribute('class','file-item-remove"' );
            alink.setAttribute("href", "javascript:;");  
            alink.innerHTML = '<span class="material-symbols-rounded">delete</span>';
            alink.addEventListener( "click", remove_this );
            alink.myParam = alink;
            var overlink = document.createElement('div');
            overlink.setAttribute('class','upload-item-remove' );
            overlink.appendChild(alink); 
            var li = document.createElement('li');
            li.setAttribute('class','upload-item' );
            li.appendChild(overlink);
            li.id = this.uid;   
            document.getElementById('filelist').appendChild(li);
            // embed the actual thumbnail
            this.embed(li.id, {
                width: 100,
                height: 100,
                crop: true
            }); 
		};
		img.onembedded = function() {
			this.destroy();
		};

		img.onerror = function() {
			this.destroy();
		};
		img.load(this.getSource());     
    }); 
});
/*
//PROGRESS BEFORE EACH UPLOAD HANDLER
uploader.bind('BeforeUpload',function(up,file){
    var processId       = file.processId;
    var photoDiv        = jQuery('#photo'+processId+' .photoThumbDiv');
    var image           = jQuery(new Image()).appendTo(photoDiv);
    var preloader       = new mOxie.Image();

    preloader.downsize(100,100);
    preloader.onload    = function() {
        var img         = preloader.getAsDataURL();
        jQuery('#photo'+processId+' .photoThumbDiv').html('<img src="'+img+'">');
    };
    preloader.load(file.getSource());

    uploader.settings.multipart_params.processId = file.processId;
});
*/

/*
//PROGRESS HANDLER 4
uploader.bind('UploadProgress',function(up,file){
    var processId = file.processId;
    var pb = collection.getProgressBar(processId);
    if(file.percent<100){
        var progress = file.percent;
        pb.html('<div class="progressBar"><div class="progressBarColor"> </div></div>');
        jQuery('#photo'+processId+' .progressBarColor').css({width:progress+'%'});
    }else{
        pb.html('<div class="loaderSmall"></div> Processing');
    }
});
*/
//FILE FINISHED HANDLER 5
uploader.bind('FileUploaded',function(up,file,info){
    var processId       = file.processId;
    var fileName        = file.name;
    file.fileName       = file.name;
    var params          = file;
    delete params.getNative;
    delete params.getSource;
    delete params.destroy;
    console.log(fileName);
    /*
        ui.request('registerFile','Photo',params);
   
        //ALL COMPLETE
        if(uploadedCnt == up.files.length){
            //uploader.splice();
            uploader.refresh();
        }
    */
});

uploader.bind('Error', function(up, error)  {
    console.log(error.code)
    console.log(plupload.HTTP_ERROR)
    if(error.code !== plupload.HTTP_ERROR) {
        return true;
    }
    uploader.stop();
    console.warn('[plupload] stopped (HTTP Error)');
    // window.setTimeout(function(){retry(up)}, 5000);
    return false;
});

function remove_this(me){ 
    var toi = me.currentTarget.myParam;  
    toi.parentElement.parentElement.remove(); 
}

</script>

<script>
ClassicEditor
.create( document.querySelector( '#content' ) )
.catch( error => {
    console.error( error );
} );
</script>
