<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Api extends CI_Controller
{
    // we'll be using this guy in our other controllers
    public $rdio = null;
    
    public function logout()
    {
        $this->session->sess_destroy();
        echo "Session:";
        print_r($this->session->all_userdata());
        exit;
    }
    
    public function auth_callback()
    {
        if ($this->session->userdata('oauth_token') && $this->session->userdata('oauth_token_secret')) {
            $this->load->library('rdio');
            $this->rdio = new Rdio(array(
                $this->config->item('RDIO_CONSUMER_KEY'),
                $this->config->item('RDIO_CONSUMER_SECRET')
            ), array(
                $this->session->userdata('oauth_token'),
                $this->session->userdata('oauth_token_secret')
            ));
            
            $this->rdio->token = array(
                $this->session->userdata('oauth_token'),
                $this->session->userdata('oauth_token_secret')
            );
            if ($_GET['oauth_verifier']) {
                // we've been passed a verifier, that means that we're in the middle of authentication.
                $this->rdio->complete_authentication($_GET['oauth_verifier']);
                // save the new token in our session
                $this->session->set_userdata('oauth_token', $this->rdio->token[0]);
                $this->session->set_userdata('oauth_token_secret', $this->rdio->token[1]);
                $currentUser = $this->rdio->call('currentUser');
                var_dump($currentUser);
            }
        } else {
            //try to authenticate again, we dont have the session keys, 
            //we got here by accident
            $this->auth();
        }
    }
    
    public function auth()
    {
        //check session for token data
        if ($this->session->userdata('oauth_token') && $this->session->userdata('oauth_token_secret')) {
            //we already have the tokens in our session no need to request them
            $this->auth_callback();
        } else {
            // set up our call back
            $callback_url = "http" . ((!empty($_SERVER['HTTPS'])) ? "s" : "") . "://" 
            . $_SERVER['SERVER_NAME'] . '/index.php/api/auth_callback';
            $this->load->library('rdio');
            $this->rdio    = new Rdio(array(
                $this->config->item('RDIO_CONSUMER_KEY'),
                $this->config->item('RDIO_CONSUMER_SECRET')
            ));
            $authorize_url = $this->rdio->begin_authentication($callback_url);
            $this->session->set_userdata('oauth_token', $this->rdio->token[0]);
            $this->session->set_userdata('oauth_token_secret', $this->rdio->token[1]);
            header('Location: ' . $authorize_url);
        }
    }
}
