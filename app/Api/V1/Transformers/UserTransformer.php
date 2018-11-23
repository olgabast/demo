<?php
namespace App\Api\V1\Transformers;

use App\Expense;
use App\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            'id'    => (int)$user->id,
            'email' => $user->email,
            'role'  => $user->role,
        ];
    }
}