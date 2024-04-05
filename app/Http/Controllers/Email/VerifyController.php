<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use App\Models\EmailToken;
use App\Models\TrustedIp;
use App\Presenters\JsonPresenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerifyController extends Controller
{
    public function __construct(
        private JsonPresenter $presenter,
    )
    {
    }

    public function __invoke(string $token)
    {
        return DB::transaction(function () use ($token) {
            $emailToken = EmailToken::where('token', '=', $token)->first();
            if(!$emailToken) {
                return $this->presenter->present(['status' => 'false']);
            }

            $trustedIP = new TrustedIp();
            $trustedIP->ipv4 = $emailToken->ipv4;
            $trustedIP->user_id = $emailToken->user_id;
            $trustedIP->save();

            return $this->presenter->present(['status' => 'true']);
        });
    }
}
