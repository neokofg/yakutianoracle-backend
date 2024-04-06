<?php

namespace App\Http\Controllers\WebHook\CloudPayments;

use App\Http\Controllers\Controller;
use App\Jobs\SendData;
use App\Models\User;
use Faker\Provider\ru_RU\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PayController extends Controller
{
    public function __invoke(Request $request)
    {
        $password = rand(100000,999999);
        $email = $request->AccountId;
        User::create([
            'name' => Person::firstNameMale(),
            'email' => $email,
            'password' => Hash::make($password),
        ]);
        SendData::dispatch($email,$password)->onQueue('default');
    }
}
