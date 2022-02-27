<?php

namespace App\Http\Controllers;

use App\Models\UserLink;
use Carbon\Carbon;
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserLinkController extends Controller
{
    private function file_get_contents_curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * Display a the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get()
    {
        try {
            $links = Auth::user()->links;

            foreach ($links as $linkItem) {
                $linkItem->clicks;
            }

            return response()->json([
                "data" => $links
            ], 200);

        } catch (\Error $err) {
            
            return response()->json([
                "errors" => $err->getMessage(),
                "message" => "Something went wrong on server!"
            ], 200);
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
        try {
            $validator = Validator::make($request->all(),  [
                'url' => "required",
                'source' => "required|url",
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    "errors" => $validator->errors()->all(),
                    "message" => "Invalid input data, try again!"
                ], 200);
            }
            
            $html = $this->file_get_contents_curl($request->source);

            //parsing begins here:
            $doc = new DOMDocument();
            @$doc->loadHTML($html);
            $nodes = $doc->getElementsByTagName('title');
            
            // return dd("{$nodes->item(0)->nodeValue}");

            //get and display what you need:
            $title = $nodes->item(0)->nodeValue;
            
            $new_link = new UserLink();

            $new_link->title = $title;
            $new_link->url = $request->url;
            $new_link->source = $request->source;
            $new_link->user_id = Auth::user()->id;
            
            $new_link->save();

            $new_link->clicks;
            
            return response()->json([
                "data" => $new_link,
                "message" => "New link has been created" 
            ], 200);

        } catch (\Error $err) {
            
            return response()->json([
                "errors" => $err->getMessage(),
                "message" => "Something went wrong on server!"
            ], 200);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserLink  $userLink
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserLink $userLink)
    {
        try {
            $validator = Validator::make($request->all(),  [
                'url' => "required",
                'source' => "required|url",
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    "errors" => $validator->errors()->all(),
                    "message" => "Invalid input data, try again!"
                ], 200);
            }

            $html = $this->file_get_contents_curl($request->source);

            // Check link specific user

            //parsing begins here:
            $doc = new DOMDocument();
            @$doc->loadHTML($html);
            $nodes = $doc->getElementsByTagName('title');
            

            //get and display what you need:
            $title = $nodes->item(0)->nodeValue;

            $userLink->title = $title;
            $userLink->url = $request->url;
            $userLink->source = $request->source;

            $userLink->save();

            $userLink->clicks;
            
            return response()->json([
                "data" => $userLink,
                "message" => "Link has been updated!" 
            ], 200);

        } catch (\Error $err) {
            
            return response()->json([
                "errors" => $err->getMessage(),
                "message" => "Something went wrong on server!"
            ], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserLink  $userLink
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserLink $userLink)
    {
        try {
            
            $userLink->delete();
            
            return response()->json([
                "data" => "OK",
                "message" => "Link has been deleted!"
            ]);
            
        } catch (\Error $err) {
            
            return response()->json([
                "errors" => $err->getMessage(),
                "message" => "Something went wrong on server!"
            ], 200);
        }
    }

    public function search(Request $request)
    {
        try {
            
            $links = UserLink::where('title', 'like', "%{$request->keyword}%")
                ->orWhere('source', 'like', "%{$request->keyword}%")
                ->orWhere('url', 'like', "%{$request->keyword}%")
                ->get();
            
            foreach ($links as $linkItem) {
                $linkItem->clicks;
            }
            
            return response()->json([
                "data" => $links
            ], 200);
            
        } catch (\Error $err) {
            
            return response()->json([
                "errors" => $err->getMessage(),
                "message" => "Something went wrong on server!"
            ], 200);
        }
    }

    public function filter(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),  [
                'start' => "required|date",
                'end' => "required|date",
            ]);

            $start_time = strtotime($request->start);
            $end_time = strtotime($request->end);
            
            if ($end_time < $start_time) {
                return response()->json([
                    "message" => "Invalid datetime range on input. Start date more than end date!"
                ], 200);
            }
            
            if ($validator->fails()) {
                return response()->json([
                    "errors" => $validator->errors()->all(),
                    "message" => "Invalid input data, try again!"
                ], 200);
            }
            
            $start_date = Carbon::parse($request->start)
                ->toDateTimeString();

            $end_date = Carbon::parse($request->end)
                ->toDateTimeString();

            $links = UserLink::whereBetween('created_at', [
                $start_date, $end_date
            ])->get();
            
            foreach ($links as $linkItem) {
                $linkItem->clicks;
            }
            
            return response()->json([
                "data" => $links
            ], 200);
            
        } catch (\Error $err) {
            
            return response()->json([
                "errors" => $err->getMessage(),
                "message" => "Something went wrong on server!"
            ], 200);
        }
    }
}
