<?php namespace abstractions\repositories\user;

use Auth;

class EloquentUserRepository implements UserRepositoryInterface{

	public function create($input, $id){
	  $user = new User;
	  try
	  {
		if(!isset($input['email']))
			return ['status'=>'FAIL','info'=>'missing email'];
		if(!isset($input['alias']))
			return ['status'=>'FAIL','info'=>'missing email'];
		if(!isset($input['first']))
			return ['status'=>'FAIL','info'=>'missing first name'];
		if(!isset($input['last']))
			return ['status'=>'FAIL','info'=>'missing last name'];
		if(!isset($input['zip']))
			return ['status'=>'FAIL','info'=>'missing zip'];
		if(!isset($input['password']))
			return ['status'=>'FAIL','info'=>'missing password'];

		$user->email=$input['email'];
		$user->alias=$input['alias'];
		$user->first=$input['first'];
		$user->last=$input['last'];
		$user->zip=$input['zip'];
		$user->password=Hash::make($input['password']);
		$user->save();
		return ['status'=>'OK','user id created: '=>$user->id];
	   }catch(Exception $e){
		Log::error("UserCreateException $e->getMessage()");
		return ['status'=>'FAIL','info'=>$e->getMessage()];
	   }
		Log::error("UserCreateException $e->getMessage()");
		return ['status'=>'FAIL','info'=>'Exception check server logs'];
	}

	public function getUser($id){
		try{
			$user = User::where('id','=',$id)->take(1)->get();
			return $user->toArray();
		}catch(Exception $e){
			Log::error("User get exception: $e->getMessage()");
			return ['status'=>'FAIL','info'=>$e->getMessage()];
		}
		return ['status'=>'FAIL','info'=>'Exception check server logs'];
	}
}
