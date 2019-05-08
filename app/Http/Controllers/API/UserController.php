<?php

namespace App\Http\Controllers\API;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('isAdmin');
        if($q = request()->q) {
            $user = User::where(function($query) use ($q) {
                $query->where('name', 'LIKE', "%$q%")
                ->orWhere('email', 'LIKE', "%$q%");
            })->paginate(10);
        } else {
            $user = User::paginate(10);
        }

        return $user;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required']
        ]);
        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    public function profile()
    {
        return auth()->user();
    }

    public function updateProfile(Request $request) 
    {
        $user = auth('api')->user();
        
        $this->validate($request,[
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users,email,'.$user->id,
            'password' => 'sometimes|required|min:8'
        ]);
        
        $currentPhoto = $user->photo;

        if($request->photo != $currentPhoto) {
            $imageName = time() . '.' . explode('/', explode(';', $request->photo)[0])[1];
            \Image::make($request->photo)->resize(512, 512)->save(public_path('img/profile/') . $imageName);
            $request->merge(['photo' => $imageName]);

            $oldUserPhoto = public_path('img/profile/') . $currentPhoto;
            if(file_exists($oldUserPhoto)) {
                unlink($oldUserPhoto);
            }
        }

        if(!empty($request->password)) {
            $request->merge(['password' => bcrypt($request->password)]);
        }

        $user->update($request->all());

        return response(null, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['sometimes', 'required', 'string', 'min:8'],
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        if($request->has('password')) {
            $user->password = bcrypt($request->password);
        }
        if($request->has('role')) {
            $user->role = $request->role;
        }
        $user->save();

        return response(null, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return response(null, 200);
    }
}
