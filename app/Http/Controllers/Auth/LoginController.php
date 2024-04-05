<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendVerifyEmail;
use App\Models\EmailToken;
use App\Models\TrustedIp;
use App\Presenters\JsonPresenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    public function __construct(
        private JsonPresenter $presenter
    )
    {
    }

    public function __invoke(Request $request)
    {
        $response = DB::transaction(function () use ($request) {
            return $this->login($request);
        });

        return $this->presenter->present($response);
    }

    private function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email,'password' => $request->password])) {
            return $this->checkIpAddress($request);
        } else {
            return ['status' => 'false'];
        }
    }

    private function checkIpAddress(Request $request)
    {
        $trustedIp = TrustedIp::where('user_id','=',Auth::id())
            ->where('ipv4', '=', $request->ip())
            ->first();
        if(!$trustedIp) {
            return $this->sendTrustedMail($request);
        } else {
            return $this->generateToken();
        }
    }

    private function generateToken()
    {
        return ['token' => Auth::user()->createToken('auth-token')->plainTextToken];
    }

    private function sendTrustedMail(Request $request)
    {
        $token = hash_hmac('sha256', Auth::id() . now()->format('Y-m-d H:i:s'), env('APP_KEY'));
        $emailToken = EmailToken::firstOrNew([
            'user_id' => Auth::id(),
            'ipv4' => $request->ip()
        ]);
        $emailToken->token = $token;
        $emailToken->save();

        SendVerifyEmail::dispatch($token, Auth::user()->email, $request->ip())->onQueue('default');

        return ['status' => 'true'];
    }
}
