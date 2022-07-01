<?php
namespace App\Lib;

use GetStream\StreamChat\StreamException;

class StreamChatServices{
    private $serverClient;
    /**
     * StreamChatServices constructor.
     */
    public function __construct()
    {

        // instantiate your stream client using the API key and secret
        // the secret is only used server side and gives you full access to the API
        $this->serverClient = new \GetStream\StreamChat\Client(env('STREAM_CHAT_API_KEY'), env('STREAM_CHAT_API_SECRET'));
    }

    public  function createStreamUserToken($user_handle){
        return $this->serverClient->createToken($user_handle);
    }

    public  function revokeStreamUserToken($user_handle){
        try {
            return $this->serverClient->revokeUserToken($user_handle);
        } catch (StreamException $e) {
        }
    }



}
