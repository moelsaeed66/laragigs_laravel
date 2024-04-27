<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ListingController extends Controller
{
    public function index()
    {
        $listings=Listing::latest()->filter(\request(['tags','search']))->paginate(6);
        return view('listings.index',compact('listings'));
    }
    public function create()
    {
        return view('listings.create');
    }
    public function store(Request $request)
    {
        $data=$request->validate([
            'title' => 'required',
            'company' => ['required', Rule::unique('listings', 'company')],
            'location' => 'required',
            'website' => 'required',
            'email' => ['required', 'email'],
            'tags' => 'required',
            'description' => 'required'
        ]);
        if($request->hasFile('logo'))
        {
            $data['logo']=$request->file('logo')->store('logos','public');
        }
        $data['user_id']=auth()->id();
        Listing::create($data);

        return redirect('/')->with('message','listing created successfully');
    }
    public function show(Listing $listing)
    {
       return view('listings.show',compact('listing'));
    }
    public function edit(Listing $listing)
    {
        return view('listings.edit',compact('listing'));
    }
    public function update(Request $request,Listing $listing)
    {
        $data = $request->validate([
            'title' => 'required',
            'company' => ['required', Rule::unique('listings', 'company')],
            'location' => 'required',
            'website' => 'required',
            'email' => ['required', 'email'],
            'tags' => 'required',
            'description' => 'required'
        ]);
        if($request->hasFile('logo'))
        {
            $data['logo']=$request->file('logo')->store('logos','public');
        }
        $data['user_id']=auth()->id();
        $listing->update($data);
        return redirect('/')->with('message','listing updated successfully');

    }
    public function delete(Listing $listing)
    {
        $listing->delete();
        return redirect('/')->with('message','listing deleted successfully');

    }
    public function manage()
    {
        $listings=auth()->user()->listings()->get();
        return view('listings.manage',compact('listings'));
    }
}
