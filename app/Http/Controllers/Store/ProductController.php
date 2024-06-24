<?php

namespace Acelle\Http\Controllers\Store;

use Acelle\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Acelle\Model\Product;
use Acelle\Model\Category;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // init
        $perPage    =   $request->perPage ?? 10;
        $keyword    =   $request->keyword ?? '' ;
        $view       =   $request->view ?? '' ;

        return view('store.products.index', [
            'statuslist'    => Product::select('status')->distinct()->get(),
            'keyword'       => $keyword,
            'view'          => $view,
            'perPage'       => $perPage,
        ]);
    }
    public function list(Request $request)
    {
        //echo "view:".$request->view.'@';
        // init
        $perPage    =   $request->perPage ?? 10;
        $keyword    =   $request->keyword ?? '' ;
        $filters    =   $request->filters ?? '' ;
        $status     =   $request->status ?? 'all';
        $view       =   $request->view ?? 'list' ;
        // Get sending servers
        $products = Product::search($keyword);

        // filter by status
        if ($status != 'all') {
            $products->where('status', 'like', $status);
        }
        /*
        if($filters!=''){
             $products->whereIn( 'type', $filters );
        }
        */
        if ($request->sort) {
            $products->orderBy($request->sort['by'], $request->sort['direction']);
        }

        //echo( $request->view.'-'.$view);

        return view('store.products.'.$view, [
            'products'   => $products->paginate($perPage),
            'keyword'     => $keyword,
            'perPage'     => $perPage,
            'sort_by'     => $request->sort['by'] ?? '',
            'sort_direction' => $request->sort['direction'] ?? '',
        ]);
    }

    public function deleteSelected(Request $request)
    {
        // find and delete record
        $products = Product::whereIn('id', $request->ids)->get(); // collection map filter
        foreach ($products as $product) {
            // $campaign->delete();
        }
        // retur alert
        return response()->json([
            'status' => 'success',
            'message' => trans('store.product.delete.success'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // init
        $product = $request->user()->customer->newProduct();

        return view('store.products.create', [
            'product' => $product,
        ]);
    }

    public function store(Request $request)
    {
        // init
        $product = $request->user()->customer->newProduct();

        // Try to save
        $validator = $product->saveFromParams($request->all());

        // if error
        if ($validator->fails()) {
            return response()->view('store.products.create', [
                'product' => $product,
                'errors' => $validator->errors(),
            ], 400);
        }

        // Send messenge
        $request->session()->flash('alert-success', trans('store.product.create.success'));

        // redirect
        return redirect()->action('Store\ProductController@edit', [
            'product' => $product,
        ]);
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit(Request $request, $id)
    {
        $product = Product::find($id);

        return view('store.products.edit', [
            'product'    =>  $product,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        // init
        $product = Product::find($id);

        // Try to save
        $validator = $product->saveFromParams($request->all());

        // errors
        if ($validator->fails()) {
            return response()->view('store.products.edit', [
                'product' => $product,
                'errors' => $validator->errors(),
            ], 400);
        }

        // Send messenge if change
        $request->session()->flash('alert-success', trans('store.product.update.success'));

        // redirect
        return redirect()->action('Store\ProductController@edit', [
            'product' => $product,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function multiltask(Request $request)
    {
        $mesenger   =   "";
        $keyword    =   isset($request->keyword) ? $request->keyword : '' ;
        $status    =   isset($request->status) ? $request->status : '' ;
        $solu       =   isset($request->solu) ? $request->solu : '' ;
        if ($solu == 'activemany') {
            if ($request->ids) {
                try {
                    Product::whereIn("id", $request->ids)
                        ->update([
                            'status' => $status,
                        ]);
                    $mesenger   =   " Active sucessful";
                } catch(\Exception $e) {
                    report($e);
                }
            }
        }
        if ($solu == 'delmany') {
            if ($request->ids) {
                try {
                    Product::destroy($request->ids);
                    $mesenger   =   " Delete sucessful";
                } catch(\Exception $e) {
                    report($e);
                }
            }
        }
        // retur alert
        return response()->json([
            'status' => 'success',
            'message' => trans('store.product.status.updatesuccess'),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(request $request, $id)
    {
        $product = Product::find($id);
        return view('store.product.show', [
            'product'    =>  $product,
            'page'      =>  $request->page
        ]);
    }


    /**
     * Delete the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function delete(Request $request)
    {
        // find and delete record
        $product = Product::find($request->id);

        // delete record
        $product->delete();

        // Send messenge
        $request->session()->flash('alert-success', trans('store.product.delete.success'));

        // retur alert
        return response()->json([
            'status' => 'success',
            'message' => trans('store.product.delete.success'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $product = Product::findOrFail($id)->first();
        $product->delete();
        return redirect()->action('Store\ProductController@index', [
            'page'      =>  $request->page,
            'perPage'   => $request->perPage,
        ]);
    }
    /**
     * Tongo On/off Status
     */
    public function updateStatus(Request $request)
    {
        $product = Product::find($request->id);
        $product->status = $request->status;
        $product->save();
        return response()->json(['success' => trans('store.product.status.updatesuccess')]);
    }

    public function attributes(Request $request)
    {
        $category = Category::findByUid($request->category_uid);
        $attributes = $category->attributes;

        if ($request->uid) {
            $product = Product::findByUid($request->uid);
        } else {
            $product = $request->user()->customer->newProduct();
        }

        return view('store.products.attributes', [
            'product' => $product,
            'attributes' => $attributes,
        ]);
    }
}
