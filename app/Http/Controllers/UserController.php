<?php

namespace App\Http\Controllers;

use App\Film;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function show()
    {
        $user = Auth::user();
        $critics = Film::where('user_id', '=', Auth::user()->id)->get();
        $data = collect([$user,$critics]);
        return view('moncompte',['data' => $data]);
    }

    public function addCritic(Request $request, $idfilm){
        $critic = new Film();
        $iduser = Auth::user()->id;
        $critic->user_id = $iduser;
        $critic->film_id = $idfilm;
        $critic->titre   = $request->input('titre');
        $critic->contenu = $request->input('contenu');
        $critic->film_titre = $request->input('film_titre');
        $critic->save();   
    }

    public function delete($id){
        Film::destroy([$id]);
        return back();
    }
}
