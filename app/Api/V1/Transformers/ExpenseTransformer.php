<?php
namespace App\Api\V1\Transformers;

use App\Expense;
use League\Fractal\TransformerAbstract;

class ExpenseTransformer extends TransformerAbstract
{
    public function transform(Expense $expense)
    {
        /* to get serializerd dates */
        $expense_array = $expense->toArray();

        return [
            'id'          => (int)$expense->id,
            'user_id'     => (int)$expense->user_id,
            'user_email'  => $expense->user->email,
            'datetime'    => $expense_array['datetime'],
            'description' => $expense->description,
            'amount'      => (float)$expense->amount,
            'comment'     => $expense->comment,
        ];
    }
}