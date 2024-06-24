<?php

namespace Acelle\Http\Controllers\Store;

use Acelle\Model\Order;
use Illuminate\Http\Request;
use Acelle\Http\Controllers\Controller;

use Acelle\Model\Product;
use Acelle\Model\Category;
use Illuminate\Support\Facades\Storage;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //echo '<img src="'.asset('storage/shop.png').'">';
        // init
        $perPage    =   $request->perPage ?? 10;
        $keyword    =   $request->keyword ?? '' ;
        $view       =   $request->view ?? '' ;
        // Get sending servers
        $orders = Order::search($keyword)
            ->paginate($perPage);

        return view('store.orders.index', [
            'orders'  => $orders,
            'statuslist' => Order::select('status')->distinct()->get(),
            'keyword' => $keyword,
            'view' => $view,
            'perPage' => $perPage,
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
        $orders = Order::search($keyword);

        // filter by status
        if ($status != 'all') {
            $orders->where('status', 'like', $status);
        }
        /*
        if($filters!=''){
             $orders->whereIn( 'type', $filters );
        }
        */
        if ($request->sort) {
            $orders->orderBy($request->sort['by'], $request->sort['direction']);
        }

        //echo( $request->view.'-'.$view);

        return view('store.orders.'.$view, [
            'orders'   => $orders->paginate($perPage),
            'keyword'     => $keyword,
            'perPage'     => $perPage,
            'sort_by'     => $request->sort['by'] ?? '',
            'sort_direction' => $request->sort['direction'] ?? '',
        ]);
    }


    public function deleteSelected(Request $request)
    {
        // find and delete record
        $orders = Order::whereIn('id', $request->ids)->get(); // collection map filter
        foreach ($orders as $order) {
            // $campaign->delete();
        }
        // retur alert
        return response()->json([
            'status' => 'success',
            'message' => trans('store.orders.delete.success'),
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
        $order = Order::newDefault();

        $categories = Category::whereNull('parent_id')
                            ->orWhere(function ($query) {
                                $query->whereIn('id', Category::select('id')->whereNotIn('parent_id', Category::select('id')->distinct()));
                            })->get();

        return view('store.orders.create', [
            'order' => $order,
            'categories' => $categories,
            'tags' => Order::getTags(),
        ]);
    }

    public function selfToDrap(Request $request)
    {
        $order = Order::where('uid', $request->uid)->first();

        if (is_null($order)) {
            // init
            $order = Order::newDefault();
            // Try to save
            $order->status = Order::STATUS_DRAPP;
            $order->uid = $request->uid;
        } else {
            $order->status = Order::STATUS_DRAPP;
        }
        // save all
        $validator = $order->saveFromParams($request->all());

        // if error
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        return response()->json([
                                    'status' => 'success',
                                    'data' => 'Chuc mung ban da cap nhat thanh cong',
                                    'message' => 'Information saved successfully!'
                                ], 200);
    }

    public function store(Request $request)
    {
        $order = Order::where('uid', $request->uid)->first();

        if (is_null($order)) {
            // init
            $order = Order::newDefault();

            // set active
            $order->status = Order::STATUS_ACTIVE;
            // set Uidv
        } else {
            // set active
            $order->status = Order::STATUS_ACTIVE;
        }

        // Try to save
        $validator = $order->saveFromParams($requests->all());

        // if error
        if ($validator->fails()) {
            return response()->view('store.orders.create', [
                'product' => $order,
                'tags' => Order::getTags(),
                'errors' => $validator->errors(),
            ], 400);
        }

        // Send messenge
        $request->session()->flash('alert-success', trans('store.orders.create.success'));

        // redirect
        return redirect()->action('Store\OrdersController@index');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit(Request $request, $id)
    {
        $order = Order::find($id);
        $categories = Category::whereNull('parent_id')
                       ->orWhere(function ($query) {
                           $query->whereIn('id', Category::select('id')->whereNotIn('parent_id', Category::select('id')->distinct()));
                       })->get();
        return view('store.orders.edit', [
           'product'    =>  $order,
           'categories' => $categories,
           'tags' => Order::getTags(),
        ]);
    }

    /**
     * Get message content for create sms campaign
     */

    public function getmessage(Request $request)
    {
        $order = Order::find($request->id);
        return response()->json([
            'status' => 'success',
            'message' =>  $order->message,
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
        $order = Order::find($id);

        // set active
        $order->status = Order::STATUS_ACTIVE;

        // Try to save
        $validator = $order->saveFromParams($request->all());

        // errors
        if ($validator->fails()) {
            return response()->view('store.orders.edit', [
                'product' => $order,
                'tags' => Order::getTags(),
                'errors' => $validator->errors(),
            ], 400);
        }

        // Send messenge if change
        $request->session()->flash('alert-success', trans('store.orders.update.success'));

        // redirect
        return redirect()->action('Store\OrdersController@index');
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
                    Order::whereIn("id", $request->ids)
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
                    Order::destroy($request->ids);
                    $mesenger   =   " Delete sucessful";
                } catch(\Exception $e) {
                    report($e);
                }
            }
        }
        // retur alert
        return response()->json([
            'status' => 'success',
            'message' => trans('store.orders.status.updatesuccess'),
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
        $order = Order::find($id);
        return view('store.orders.show', [
            'product'    =>  $order,
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
        $order = Order::find($request->id);

        // delete record
        $order->delete();

        // Send messenge
        $request->session()->flash('alert-success', trans('store.orders.delete.success'));

        // retur alert
        return response()->json([
            'status' => 'success',
            'message' => trans('store.orders.delete.success'),
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
        $order = Order::findOrFail($id)->first();
        $order->delete();
        return redirect()->action('Store\OrdersController@index', [
            'page'      =>  $request->page,
            'perPage'   => $request->perPage,
        ]);
    }
    /**
     * Tongo On/off Status
     */
    public function updateStatus(Request $request)
    {
        $order = Order::find($request->id);
        $order->status = $request->status;
        $order->save();
        return response()->json(['success' => trans('store.orders.status.updatesuccess')]);
    }
}
