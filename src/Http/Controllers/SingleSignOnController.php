<?php

namespace JimmyHoweDotCom\SingleSignOn\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Single Sign On Controller
 *
 * @package JimmyHoweDotCom\SingleSignOn
 */
class SingleSignOnController extends Controller
{
    /**
     * Login using SSO server.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request): RedirectResponse
    {
        $request->session()->put('state', $state = Str::random(40));

        $query = http_build_query([
            'client_id'     => config('sso.client_id'),
            'redirect_uri'  => config('sso.client_callback'),
            'response_type' => 'code',
            'scope'         => config('sso.scopes'),
            'state'         => $state,
        ]);

        $redirect = config('sso.host').'/oauth/authorize?'.$query;

        return redirect($redirect);
    }

    /**
     * Callback response from server.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function callback(Request $request): RedirectResponse
    {
        $state = $request->session()->pull('state');

        throw_unless(
            strlen($state) > 0 && $state === $request->get('state'),
            InvalidArgumentException::class
        );

        $sso_token_endpoint = config('sso.host').'/oauth/token';

        $response = Http::asForm()->post($sso_token_endpoint,
            [
                'grant_type'    => 'authorization_code',
                'client_id'     => config('sso.client_id'),
                'client_secret' => config('sso.client_secret'),
                'redirect_uri'  => config('sso.client_callback'),
                'code'          => $request->get('code'),
            ]);

        $request->session()->put($response->json());

        return redirect(route('sso.connect'));
    }

    /**
     * Connect to SSO server account.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function connect(Request $request): RedirectResponse
    {
        $access_token = $request->session()->get('access_token');

        $uri = config('sso.host').'/api/user';

        $headers = [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$access_token,
        ];

        $response = Http::withHeaders($headers)->get($uri);

        $userArray = $response->json();

        try
        {
            $email = $userArray['email'];
        } catch (Exception $e)
        {
            return redirect('login')->withErrors([
                'connect' => "Failed to connect to identity server. ({$e->getMessage()})",
            ]);
        }

        $user = User::where('email', $email)->first();

        if (!$user)
        {
            $user = new User();
            $user->name = $userArray['name'];
            $user->email = $userArray['email'];
            $user->email_verified_at = $userArray['email_verified_at'];
            $user->save();
        }
        Auth::login($user);

        return redirect(route('home'));
    }

    /**
     * Disconnect from SSO server.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function disconnect(Request $request): RedirectResponse
    {
        $user = Auth::user()->token()->revoke();

        return redirect()->to(route('home'));
    }
}
