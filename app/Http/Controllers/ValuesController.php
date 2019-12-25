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
        // First Check IF Query String given
        if ($request->has('keys')) {

            //getting Query String
            $key = $request->input('keys');

            //Making Query String an array
            $key = explode(',', $key);
            // Getting All data related to the keys(s)

            $collection = new ValueCollection(Values::all()->whereIn('key', $key));
            // checking if the collection is not empty
            if(!$collection->isEmpty()){

                //as the collection is not empty, return data
                $plucked = $collection->pluck('value', 'key');

                $plucked->all();

                $response['data'] = $plucked;
                $response['status'] = 200;
                
                return response($response, 200);
            }else{

                //if the collection is empty return 404
                $response['message'] = 'No Related Data Found! Please Check Spelling. Remember that, Keys are case sensitive.';
                $response['status'] = 404;
                return response($response, 404);
            }
            

        }else{

            //if there is no Query String, Return All Data

            $collection = new ValueCollection(Values::all());
            $plucked = $collection->pluck('value', 'key');
            $plucked->all();
            $response['data'] = $plucked;
            $response['status'] = 200;
            
            return response($response, 200);
        }
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //adding all data in a array from request

        $arr = array();
        foreach ($request->except('_token') as $key => $part) {

            // checking if the key exist. Then we will return key exist
            $duplicate = Values::where('key', '=', $key)->first();
            if($duplicate){
                $response['warning'] = [
                    $key => "This Key exist! If you want to update, Please send a Patch Request.",
                ];
            }else{

                // if the key is uniq, we will add that to database with the value
                $arr[] = [
                    'key' => $key,
                    'value' => $part,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            
          }

          //insert into database
          $saved = Values::insert($arr);

          //Now Check IF it saved Successfully
          if($saved){
            $response['message'] = 'Data Saved Successfully.';
            $response['link-all-data'] = '/api/values';
            $response['status'] = 200;
            return response($response, 200);
          }else{
              //if something went wrong, return 500 server error
            $response['message'] = 'Something Went Wrong! in Server. Please try again!';
            $response['status'] = 500;
            return response($response, 500);
          }
        
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
