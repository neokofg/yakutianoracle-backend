<?php

namespace App\Http\Controllers\WebHook\CloudPayments;

use App\Http\Controllers\Controller;
use App\Http\Requests\PayRequest;
use App\Jobs\SendData;
use App\Models\User;
use App\Presenters\JsonPresenter;
use Exception;
use Faker\Provider\ru_RU\Person;
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
        $password = $this->random_str(10);
        $email = $request->AccountId;
        User::create([
            'name' => Person::firstNameMale(),
            'email' => $email,
            'password' => $password,
        ]);
        SendData::dispatch($email,$password)->onQueue('default');
        return $this->presenter->present(['code' => 0]);
    }

    function random_str(
        $length,
        $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
    ) {
        $str = '';
        $max = mb_strlen($keyspace, '8bit') - 1;
        if ($max < 1) {
            throw new Exception('$keyspace must be at least two characters long');
        }
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[random_int(0, $max)];
        }
        return $str;
    }
}
