<?php

namespace App\Http\Controllers;

use App\Http\Resources\Value as ValueResource;
use App\Values;
use App\Http\Resources\ValueCollection;

use Illuminate\Http\Request;

class ValuesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        if ($request->has('keys')) {

            $key = $request->input('keys');
            $key = explode(',', $key);
            // dd($key);
            $collection = new ValueCollection(Values::all()->whereIn('key', $key));
            $plucked = $collection->pluck('value', 'key');

            $plucked->all();
            
            return response($plucked, 200);

        }else{

            $collection = new ValueCollection(Values::all());
            $plucked = $collection->pluck('value', 'key');
            $plucked->all();
            return response($plucked, 200);
        }
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $arr = array();
        foreach ($request->except('_token') as $key => $part) {

            $duplicate = Values::where('key', '=', $key)->first();
            if($duplicate){
                $response['warning'] = [
                    $key => "This Key exist! If you want to update, Please send a Patch Request.",
                ];
            }else{
                $arr[] = [
                    'key' => $key,
                    'value' => $part,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            
          }

          if(Values::insert($arr)){
            $response['message'] = 'Data Saved Successfully.';
            $response['link-all-data'] = '/api/values';
            $response['status'] = 200;
            return response($response, 200);
          }else{
            $response['message'] = 'Something Went Wrong!';
            $response['status'] = 500;
            return response($response, 500);
          }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
       //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $arr = array();
        foreach ($request->except('_token') as $key => $part) {
            $arr[] = [
                'value' => $part,
                'updated_at' => now()
            ];

            Values::where('key', $key)
                    ->update([
                        'value' => $part,
                        'updated_at' => now()
                    ]);
          }

        $response['message'] = 'Data Updated Successfully.';
        $response['link-all-data'] = '/api/values';
        $response['status'] = 200;
        return response($response, 200);
          
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
