<?php

namespace App\Http\Controllers;

use App\Http\Resources\Value as ValueResource;
use App\Values;
use App\Http\Resources\ValueCollection;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ValuesController extends Controller
{
    
    public function index(Request $request)
    {
        $this->destroy();
        // First Check IF Query String given
        if ($request->has('keys')) {

            //getting Query String
            $key = $request->input('keys');

            //Making Query String an array
            $key = explode(',', $key);
            // Getting All data related to the keys(s)

            //Get all data
            $values = Values::all()->whereIn('key', $key);
            // dd($values);
            //Make Collection
            $collection = new ValueCollection($values);
            
            // checking if the collection is not empty
            if(!$collection->isEmpty()){

                // now Update time Stamp for the keys
                $values->each->update(array('updated_at' => now()));

                //as the collection is not empty, return formatted data
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

            if(!$collection->isEmpty()){
                $plucked = $collection->pluck('value', 'key');
                $plucked->all();
                $response['data'] = $plucked;
                $response['status'] = 200;
                
                return response($response, 200);
            }else{
                //if the collection is empty return 404
                $response['message'] = 'No Data Found! Try Adding Some Data.';
                $response['status'] = 205;
                return response($response, 205);
            }
            
        }
        
    }

    
    public function store(Request $request)
    {
        //adding all data in a array from request

        $new_values = array();
        $check = 0;
        foreach ($request->except('_token') as $key => $part) {

            // checking if the key exist. Then we will return key exist
            $duplicate = Values::where('key', '=', $key)->first();
            if($duplicate){
                $response['warning'] = [
                    $key => "This Key exist! If you want to update, Please send a Patch Request.",
                ];
                $check++;
            }else{

                // if the key is uniq, we will add that to database with the value
                $new_values[] = [
                    'key' => $key,
                    'value' => $part,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            
          }

          //insert into database
          $saved = Values::insert($new_values);

          //Now Check IF it saved Successfully
          if($saved){
            if($check){
                $response['message'] = 'Some Data Can Not Be Saved Successfully. Please Check Warning Message. Rest of the Data Saved.';
                $response['status'] = 202;
            }else{
                $response['message'] = 'Data Saved Successfully.';
                $response['status'] = 201;
            }
            
            $response['link-trail-all-data'] = '/api/values';
            
            return response($response, $response['status']);
          }else{
              //if something went wrong, return 500 server error
            $response['message'] = 'Something Went Wrong! in Server. Please try again!';
            $response['status'] = 500;
            return response($response, 500);
          }
        
    }

   // Patch Function
    public function update(Request $request)
    {
        //Go through all key and Update the value
        foreach ($request->except('_token') as $key => $part) {

            $patch = Values::where('key', $key)
                    ->update([
                        'value' => $part,
                        'updated_at' => now()
                    ]);

            // in Case of error,
            if(!$patch){
                $response['error'] = [
                    $key => "Can't be Updated! Please Try Again.",
                    'Status' => 202
                ];
            }
        }

        $response['message'] = 'Data Updated Successfully.';
        $response['link-trail-all-data'] = '/api/values';
        $response['status'] = 201;
        return response($response, 201);
          
    }

    // TTL Delete Function
    public function destroy()
    {
        //Get All Values
        $values = Values::all();

        //Set Current Timestamp
        $now_timestamp = Carbon::now()->timestamp;

        //Go through All values
        foreach ($values as $value){ 
            //Geeting Updated time
            $updated_at = $value->updated_at->timestamp;

            // Taking Difference Between now and Updated Time
            $diff = ($now_timestamp - $updated_at) / 60;

            //If Updated Time is More Than 5 Minuite, Delete
            if($diff > 5){
                Values::find($value->id)->delete();
            }
        }

        // Now Update the rest of the data updated Time to now. Uncomment Below Line to Update the Timestamp on every get request.
        // DB::table('values')->update(array('updated_at' => now()));
    }
}
