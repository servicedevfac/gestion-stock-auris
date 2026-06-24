<?php

namespace App\Http\Controllers;

use App\Models\UserLogin;
use Illuminate\Http\Request;

class UserLoginController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $logins = UserLogin::with('user')->paginate(10);
        return view('user-logins.index', compact('logins'));
    }

    /**
     * Show the form for creating a new resource.
     */


    /**
     * Store a newly created resource in storage.
     */


    /**
     * Display the specified resource.
     */

    /**
     * Show the form for editing the specified resource.
     */



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserLogin $userLogin)
    {
        $userLogin->delete();
        return redirect()->route('user-logins.index')->with('success', 'Connexion supprimée avec succès');
    }
}
