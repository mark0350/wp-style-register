<?php

namespace WpStyleRegister;


use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

trait WpSendsPasswordResetEmails
{
    use SendsPasswordResetEmails;

    public function sendResetLinkEmail( array $data)
    {
        $response = $this->broker()->sendResetLink(
            $data
        );

        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($response)
            : $this->sendResetLinkFailedResponse($data, $response);
    }

}