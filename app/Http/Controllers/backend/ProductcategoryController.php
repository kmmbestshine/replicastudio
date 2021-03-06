<?php

namespace App\Http\Controllers\backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use  App\Models\Productcategory;
use Illuminate\Support\Facades\Auth;
use Session;


class ProductcategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->checkpermission('productcategory-list');
        $productcategory = Productcategory::where('company_id',Auth::user()->company_id)->get();
        return view('backend.productcategory.list',compact('productcategory'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->checkpermission('productcategory-create');
        return view('backend.productcategory.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
           // 'slug' => 'required',
        ]);
         // Product Code No Exist Return
        $check_slug = Productcategory::where('slugss', $request->slug)
                ->where('company_id', Auth::user()->company_id)
                ->first();
        if ($check_slug) {
            return redirect()->route('productcategory.create')->with('error_message', 'Category slug Already Exist !...');
        }
        $message = Productcategory::create([
            'name' => $request->name,
            'slugss' => $request->slug,
            'company_id' => Auth::user()->company_id,
            'status' => $request->status,
            'created_by' => Auth::user()->username,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        if ($message) {
            return redirect()->route('productcategory.list')->with('success_message', 'successfully created ');
        } else {
            return redirect()->route('productcategory.create')->with('error_message', 'Failed To create');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->checkpermission('productcategory-edit');
        $productcategory = Productcategory::where('company_id',Auth::user()->company_id)->find($id);
        return view('backend.productcategory.edit',compact('productcategory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'slug' => 'required',
        ]);
        $pc = Productcategory::where('company_id',Auth::user()->company_id)->find($id);
        $pc->name = $request->name;
        $pc->slugss = $request->slug;
        $pc->company_id = Auth::user()->company_id;
        $pc->status = $request->status;
        $pc->modified_by = Auth::user()->username;
        $pc->updated_at = date('Y-m-d H:i:s');
        $message = $pc->update();
        if ($message) {
            return redirect()->route('productcategory.list')->with('success_message', 'successfully updated');
        } else {
            return redirect()->route('productcategory.update')->with('error_message', 'failed to  update');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $check = $this->checkpermission('productcategory-delete');
        if ($check) {
            $this->checkpermission('productcategory-delete');
        } else {
            $productcategory = Productcategory::where('company_id',Auth::user()->company_id)->find($id);
            $message = $productcategory->delete();
            if ($message) {
                return redirect()->route('productcategory.list')->with('success_message', 'successfully Deleted');
            } else {
                return redirect()->route('productcategory.update')->with('error_message', 'failed to  Delete');
            }
        }
    }
}
