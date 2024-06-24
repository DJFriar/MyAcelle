<?php

namespace Acelle\Http\Controllers\Store;

use Acelle\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Acelle\Model\Category;

class CategoryController extends Controller
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
        $categories = Category::search($keyword)
            ->paginate($perPage);

        return view('store.categories.index', [
            'categories'    => $categories,
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
        $categorys = Category::search($keyword);

        // filter by status
        if ($status != 'all') {
            $categorys->where('status', 'like', $status);
        }
        if ($request->filters) {
            $categorys->whereIn('type', $filters);
        }

        if ($request->sort) {
            $categorys->orderBy($request->sort['by'], $request->sort['direction']);
        }

        return view('store.categories.'.$view, [
            'categorys'   => $categorys->paginate($perPage),
            'keyword'     => $keyword,
            'perPage'     => $perPage,
            'sort_by'     => $request->sort['by'] ?? '',
            'sort_direction' => $request->sort['direction'] ?? '',
        ]);
    }
    public function get_attrible_catid(Request $request)
    {
        $atributes = Category::find($request->catid)->smsAttribute;
        return$atributes;
    }
    public function deleteSelected(Request $request)
    {
        // find and delete record
        $categories = Category::whereIn('id', $request->ids)->get(); // collection map filter
        foreach ($categories as $category) {
            $category->delete();
        }
        // retur alert
        return response()->json([
            'status' => 'success',
            'message' => trans('store.categories.delete.success'),
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
        $category = new Category();
        return view('store.categories.create', [
            'category' => $category,
            'parents' => Category::where('parent_id', null)->get()
        ]);
    }

    public function store(Request $request)
    {
        // init
        $category = new Category();

        // set active
        $category->status = 'Active';

        // Try to save
        $validator = $category->saveFromParams($request->all());

        // if error
        if ($validator->fails()) {
            return response()->view('store.categories.create', [
                'category' => $category,
                'errors' => $validator->errors(),
            ], 400);
        }

        // Send messenge
        $request->session()->flash('alert-success', trans('store.categories.create.success'));

        // redirect
        return redirect()->action('Store\CategoryController@index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit(Request $request, $id)
    {
        $category = Category::find($id);
        return view('store.categories.edit', [
           'category' =>  $category,
           'parents' => Category::where('parent_id', null)
                                       ->whereNotIn('id', [ $id ])
                                       ->get()
        ]);
    }

    /**
     * Get message content for create sms campaign
     */

    public function getmessage(Request $request)
    {
        $category = Category::find($request->id);
        return response()->json([
            'status' => 'success',
            'message' =>  $category->name,
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
        $category = Category::find($id);

        // Try to save
        $validator = $category->saveFromParams($request->all());

        // errors
        if ($validator->fails()) {
            return response()->view('store.categories.edit', [
                'category' => $category,
                'tags' => Category::getTags(),
                'errors' => $validator->errors(),
            ], 400);
        }

        // Send messenge if change
        $request->session()->flash('alert-success', trans('store.categories.update.success'));

        // redirect
        return redirect()->action('Store\CategoryController@index');
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
                    Category::whereIn("id", $request->ids)
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
                    Category::destroy($request->ids);
                    $mesenger   =   " Delete sucessful";
                } catch(\Exception $e) {
                    report($e);
                }
            }
        }
        return redirect()->action('Store\CategoryController@index', [
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
        $categories = Category::find($id);
        return view('store.categories.show', [
            'categories'    =>  $categories,
            'page'      =>  $request->page
        ]);
    }

    public function collection(request $request)
    {
        $categories = Category::find($request->id);
        $collection = [];
        if ($categories) {
            foreach ($categories as $category) {
                array_push($collection, '<option value="'.$category->id.'">'. $category->name.'</option>');
            }
        }
        return join('', $collection);
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
        $smsFunnel = Funnel::find($request->id);

        // delete record
        $smsFunnel->delete();

        // retur alert
        return response()->json([
            'status' => 'success',
            'message' => trans('store.categories.delete.success'),
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
        return redirect()->action('Store\CategoryController@index', [
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
        return response()->json(['success' => trans('store.categories.status.updatesuccess')]);
    }
}
