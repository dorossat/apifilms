<?php

namespace App\Http\Controllers;

use App\Film;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Le constructeur
    public function __construct(){
        $this->middleware('auth'); // Middleware d'authentification
    }

    // Afficher les données de l'utilisateur
    public function show(){
        $user = Auth::user();
        $critics = Film::where('user_id', '=', Auth::user()->id)->get();
        $data = collect([$user,$critics]);
        return view('moncompte',['data' => $data]);
    }

    // Ajouter une critique
    public function addCritic(Request $request, $idfilm){
        $critic = new Film();
        $iduser = Auth::user()->id;
        $critic->user_id = $iduser;
        $critic->film_id = $idfilm;
        $critic->titre   = $request->input('titre');
        $critic->contenu = $request->input('contenu');
        $critic->film_titre = $request->input('film_titre');
        $critic->note = $request->input('rating');
        $critic->save();   
    }

    // Supprimer une critique
    public function delete($id){
        Film::destroy([$id]);
        return back();
    }

    // Supprimer un user
    public function destroyUser($id){
        $this->authorize('isAdmin');
        $film = new Film;
        $user = $film->with('user')->get('id');
        for($i = 0; $i< count($user); $i++){
            Film::destroy([$user[$i]->id]);
        }
        User::destroy([$id]);
        return back();
    }

    // Dashboard admin
    public function panel(){
        $this->authorize('isAdmin');
        $films = new Film();
        $users = new User();
        $data = array();
        //return view('admin',['users' => $users]);
        $users = $films->with('user')->get();
        foreach($users as $user){
            array_push($data,collect([$user->id,$user['user']->name,$user->film_titre,$user->contenu]));
        }

        return view('admin',['data' => $data, 'users' => User::all()]);

    }

    
    // récuprere l'utilisateur
    public function getUser($id){
        $this->authorize('isAdmin');
        return ['user' => User::find($id)];
    }

    public function update(Request $request, $id){
        $user = User::find($id);
        $user->name = $request->input('nom');
        $user->email = $request->input('email');
        $user->type = $request->input('type');
        $user->save();
        if(count(User::all()) >0){
            return back();
        }
        else{
            return view('index');
        }
    }

}
