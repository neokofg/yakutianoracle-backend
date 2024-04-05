<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Presenters\JsonPresenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GetController extends Controller
{
    public function __construct(
        private User $user,
        private JsonPresenter $presenter
    )
    {
    }

    public function __invoke()
    {
        $this->user = Auth::user();
        return $this->presenter->present($this->user);
    }
}
