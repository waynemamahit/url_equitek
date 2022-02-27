<?php

namespace App\Http\Controllers;

use App\Models\ClickLink;
use App\Models\User;
use App\Models\UserLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Stevebauman\Location\Facades\Location;

class ClickLinkController extends Controller
{
    public function visit(User $user, $url)
    {
        try {
            $userLink = UserLink::where('url', $url)
                ->where('user_id', $user->id)
                ->first();
            
            if (! $userLink) return abort(404);

            // $location = Location::get($request->getClientIp());

            $new_click = new ClickLink();

            // $new_click->location = $location->countryName;
            $new_click->location = "Indonesia";
            $new_click->user_link_id = $userLink->id;
            
            $new_click->save();

            return Redirect::to($userLink->source);

        } catch (\Error $err) {
            $err;
            return abort(500);
        }
    }
    
    public function test(Request $request)
    {
        return $request->getClientIp();
    }
}
