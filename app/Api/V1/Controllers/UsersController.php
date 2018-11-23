<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Transformers\UserTransformer;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UserStoreRequest;
use App\User;
use Dingo\Api\Contract\Http\Request;
use Dingo\Api\Routing\Helpers;

class UsersController extends Controller
{
    use Helpers;

    protected $transformer;

    public function __construct(UserTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * return list of users
     *
     * @return \Dingo\Api\Http\Response
     */
    public function index()
    {
        return $this->response->collection(User::all(), $this->transformer);
    }

    /**
     * create new user and return it
     *
     * @param UserStoreRequest $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(UserStoreRequest $request)
    {
        $user = User::create($request->only('email', 'password', 'role'));

        return $this->response->item($user, $this->transformer)->setStatusCode(201);
    }

    /**
     * update existing user and return it
     *
     * @param UserRequest $request
     * @param User $user
     * @return \Dingo\Api\Http\Response
     */
    public function update(UserRequest $request, User $user)
    {
        $user->update($request->only('email', 'password', 'role'));

        return $this->response->item($user, $this->transformer);
    }

    /**
     * delete user
     *
     * @param Expense $expense
     * @return \Dingo\Api\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return $this->response->noContent()->setStatusCode(200);
    }
}
