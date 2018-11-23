<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Transformers\ExpenseTransformer;
use App\Expense;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExpenseRequest;
use Dingo\Api\Contract\Http\Request;
use Dingo\Api\Routing\Helpers;
use JWTAuth;

class ExpensesController extends Controller
{
    use Helpers;

    protected $transformer;

    public function __construct(ExpenseTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * return list of user's expenses
     *
     * @return \Dingo\Api\Http\Response
     */
    public function index()
    {
        return $this->response->collection($this->user()->expenses, $this->transformer);
    }

    /**
     * return list of all expenses
     *
     * @return \Dingo\Api\Http\Response
     */
    public function all()
    {
        return $this->response->collection(Expense::all(), $this->transformer);
    }

    /**
     * create new user's expense and return it
     *
     * @param ExpenseRequest|Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(ExpenseRequest $request)
    {
        $expense = $this->user()->expenses()->create($request->only('datetime', 'description', 'amount', 'comment'));

        return $this->response->item($expense, $this->transformer)->setStatusCode(201);
    }

    /**
     * update existing expense and return it
     *
     * @param ExpenseRequest $request
     * @param Expense $expense
     * @return \Dingo\Api\Http\Response
     */
    public function update(ExpenseRequest $request, Expense $expense)
    {
        $expense->update($request->only('datetime', 'description', 'amount', 'comment'));

        return $this->response->item($expense, $this->transformer);
    }

    /**
     * delete expense
     *
     * @param Expense $expense
     * @return \Dingo\Api\Http\Response
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();

        return $this->response->noContent()->setStatusCode(200);
    }
}