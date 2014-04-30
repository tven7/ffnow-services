<?php
namespace abstractions\repositories\user;

interface UserRepositoryInterface {
	public function getUser($id);
	public function create($input, $id);
}
