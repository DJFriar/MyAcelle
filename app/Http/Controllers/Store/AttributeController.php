<?php

namespace Acelle\Http\Controllers\Store;

use Acelle\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Acelle\Model\Attribute;
use Acelle\Model\Category;

class AttributeController extends Controller
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

        // Get sending servers
        $attributes = Attribute::search($keyword)
            ->paginate($perPage);

        return view('store.attributes.index', [
            'attributes'    => $attributes,
            'keyword'       => $keyword,
            'view'          => $view,
            'perPage'       => $perPage,
        ]);
    }

    public function list(Request $request)
    {
        // init
        $perPage    =   $request->perPage ?? 10;
        $keyword    =   $request->keyword ?? '' ;
        $filters    =   $request->filters ?? '' ;
        $status     =   $request->status ?? 'all';
        $view       =   $request->view ?? 'list' ;

        // Get sending servers
        $attributes = Attribute::search($keyword);

        // filter by status
        if ($status != 'all') {
            $attributes->where('status', 'like', $status);
        }
        if ($request->filters) {
            $attributes->whereIn('type', $filters);
        }

        if ($request->sort) {
            $attributes->orderBy($request->sort['by'], $request->sort['direction']);
        }

        return view('store.attributes.'.$view, [
            'attributes'   => $attributes->paginate($perPage),
            'keyword'     => $keyword,
            'perPage'     => $perPage,
            'sort_by'     => $request->sort['by'] ?? '',
            'sort_direction' => $request->sort['direction'] ?? '',
        ]);
    }

    public function deleteSelected(Request $request)
    {
        // find and delete record
        $attributes = Attribute::whereIn('id', $request->ids)->get(); // collection map filter
        foreach ($attributes as $smsAtrtibute) {
            $smsAtrtibute->delete();
        }
        // retur alert
        return response()->json([
            'status' => 'success',
            'message' => trans('store.sms_attributes.delete.success'),
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
        $attribute = new Attribute();
        return view('store.attributes.create', [
            'attribute' => $attribute,
            'categories' => Category::all(),
        ]);
    }

    public function store(Request $request)
    {
        // init
        $attribute = Attribute::newDefault();

        // set active
        $attribute->status = 'Active';

        // Try to save
        $validator = $attribute->saveFromParams($request->all());

        // if error
        if ($validator->fails()) {
            return response()->view('store.attributes.create', [
                'attribute' => $attribute,
                'errors' => $validator->errors(),
            ], 400);
        }

        // Send messenge
        $request->session()->flash('alert-success', trans('store.sms_categories.create.success'));

        // redirect
        return redirect()->action('Store\AttributeController@index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit(Request $request, $id)
    {
        $SmsAttribute = Attribute::find($id);
        return view('store.attributes.edit', [
           'attribute' => $SmsAttribute,
           'categories' => Category::all(),
        ]);
    }

    /**
     * Get message content for create sms campaign
     */

    public function getmessage(Request $request)
    {
        $smsAtrtibute = Attribute::find($request->id);
        return response()->json([
            'status' => 'success',
            'message' =>  $smsAtrtibute->name,
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
        $smsAtrtibute = Attribute::find($id);

        // Try to save
        $validator = $smsAtrtibute->saveFromParams($request->all());

        // errors
        if ($validator->fails()) {
            return response()->view('store.attributes.edit', [
                'smsAtrtibute' => $smsAtrtibute,
                'errors' => $validator->errors(),
            ], 400);
        }

        // Send messenge if change
        $request->session()->flash('alert-success', trans('store.sms_attributes.update.success'));

        // redirect
        return redirect()->action('Store\AttributeController@index');
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
        $solu       =   isset($request->solu) ? $request->solu : '' ;
        if ($solu == 'activemany') {
            if ($request->ids) {
                try {
                    Attribute::whereIn("id", $request->ids)
                        ->update([
                            'status' => 'Active',
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
                    Attribute::destroy($request->ids);
                    $mesenger   =   " Delete sucessful";
                } catch(\Exception $e) {
                    report($e);
                }
            }
        }
        return redirect()->action('Store\AttributeController@index', [
                            'keyword'   => $request->keyword,
                            'page'      => $request->page,
                        ])->with([  'mesenger'  => $mesenger, ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(request $request, $id)
    {
        $smsAtrtibute = Attribute::find($id);
        return view('store.attributes.show', [
            'smsAtrtibute'    =>  $smsAtrtibute,
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
        $smsAtrtibute = Attribute::find($request->id);

        // delete record
        $smsAtrtibute->delete();

        // retur alert
        return response()->json([
            'status' => 'success',
            'message' => trans('store.sms_attributes.delete.success'),
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
        $smsFunnel = Funnel::findOrFail($id)->first();
        $smsFunnel->delete();
        return redirect()->action('Store\AttributeController@index', [
            'page'      =>  $request->page,
            'perPage'   => $request->perPage,
        ]);
    }
    /**
     * Tongo On/off Status
     */
    public function updateStatus(Request $request)
    {
        $smsFunnel = Funnel::find($request->id);
        $smsFunnel->status = $request->status;
        $smsFunnel->save();
        return response()->json(['success' => trans('store.sms_attributes.status.updatesuccess')]);
    }
}
