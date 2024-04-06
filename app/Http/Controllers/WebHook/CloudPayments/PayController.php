<?php

namespace App\Http\Controllers\WebHook\CloudPayments;

use App\Http\Controllers\Controller;
use App\Http\Requests\PayRequest;
use App\Jobs\SendData;
use App\Models\User;
use App\Presenters\JsonPresenter;
use Faker\Provider\ru_RU\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PayController extends Controller
{
    public function __construct(
        private JsonPresenter $presenter
    )
    {
    }

    public function __invoke(PayRequest $request)
    {
        $password = rand(100000,999999);
        $email = $request->AccountId;
        User::create([
            'name' => Person::firstNameMale(),
            'email' => $email,
            'password' => Hash::make($password),
        ]);
        SendData::dispatch($email,$password)->onQueue('default');
        return $this->presenter->present(['code' => 0]);
    }
}
