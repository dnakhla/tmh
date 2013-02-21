<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('/home/index', 'location');
    }
    
    public function get_current_user()
    {
     if ($this->session->userdata('logged_in')){
        $this->load->library('rdio');
        $rdio = new Rdio(array(
            $this->config->item('RDIO_CONSUMER_KEY'),
            $this->config->item('RDIO_CONSUMER_SECRET')
        ), array(
            $this->session->userdata('oauth_token'),
            $this->session->userdata('oauth_token_secret')
        ));
        $cu = $rdio->call('currentUser');
        echo json_encode($cu); 
     }else{
        show_error('You\'re not logged in - come on man', 401);
     }
    }

    public function get_user_id()
    {
     if ($this->session->userdata('logged_in')){
        $this->load->library('rdio');
        $rdio = new Rdio(array(
            $this->config->item('RDIO_CONSUMER_KEY'),
            $this->config->item('RDIO_CONSUMER_SECRET')
        ), array(
            $this->session->userdata('oauth_token'),
            $this->session->userdata('oauth_token_secret')
        ));
        $cu = $rdio->call('findUser', array('vanityName'=>'jvitelli'));
        echo json_encode($cu); 
     }else{
        show_error('You\'re not logged in - come on man', 401);
     }
    }

    public function compare_with_dan()
    {
     if ($this->session->userdata('logged_in')){
        $this->load->library('rdio');
        $rdio = new Rdio(array(
            $this->config->item('RDIO_CONSUMER_KEY'),
            $this->config->item('RDIO_CONSUMER_SECRET')
        ), array(
            $this->session->userdata('oauth_token'),
            $this->session->userdata('oauth_token_secret')
        ));
        $dan_music = $rdio->call('getHeavyRotation',
            array('user'=> $this->config->item('daniel_nakhla_id'), 'type'=>'artists'));
        /*'s12043' is J's memeber key for testing*/ 
        $user_music = $rdio->call('getHeavyRotation',
            array('user'=> $rdio->call('currentUser')->result->key, 'type'=>'artists'));
        
        //get common artists here
        $final_result = array('Dan'=>$dan_music->result, 'You'=>$user_music->result, 'Common'=>"");
        foreach ($dan_music->result as $dan_artist){
            foreach ($user_music->result as $user_artist){
               if ($dan_artist->name === $user_artist->name){
                $final_result['Common'][]=$dan_artist;
               }
            }
        }
        //return all 3 sets
        echo json_encode($final_result);
     }else{
        show_error('You\'re not logged in - come on man', 401);
     }
    }

     public function add_dan()
    {
     if ($this->session->userdata('logged_in')){
        $this->load->library('rdio');
        $rdio = new Rdio(array(
            $this->config->item('RDIO_CONSUMER_KEY'),
            $this->config->item('RDIO_CONSUMER_SECRET')
        ), array(
            $this->session->userdata('oauth_token'),
            $this->session->userdata('oauth_token_secret')
        ));
        $cu = $rdio->call('addFriend',array('user'=> $this->config->item('daniel_nakhla_id')));
        echo json_encode($cu);
     }else{
        show_error('You\'re not logged in - come on man', 401);
     }
    }

    public function auth()
    {
        $this->session->unset_userdata('oauth_token_secret');
        $this->session->unset_userdata('oauth_token');
        // set up our call back
        $callback_url = "http" . ((!empty($_SERVER['HTTPS'])) ? "s" : "") . "://" . $_SERVER['SERVER_NAME'] . '/index.php/api/auth_callback';
        $this->load->library('rdio');
        $rdio    = new Rdio(array(
            $this->config->item('RDIO_CONSUMER_KEY'),
            $this->config->item('RDIO_CONSUMER_SECRET')
        ));
        $authorize_url = $rdio->begin_authentication($callback_url);
        $this->session->set_userdata('oauth_token', $rdio->token[0]);
        $this->session->set_userdata('oauth_token_secret', $rdio->token[1]);
        redirect($authorize_url, 'refresh');
    }
    
    public function auth_callback()
    {
        if ($this->session->userdata('oauth_token') && $this->session->userdata('oauth_token_secret')) {
            $this->load->library('rdio');
            $rdio = new Rdio(array(
                $this->config->item('RDIO_CONSUMER_KEY'),
                $this->config->item('RDIO_CONSUMER_SECRET')
            ), array(
                $this->session->userdata('oauth_token'),
                $this->session->userdata('oauth_token_secret')
            ));
            if (isset($_GET['oauth_verifier'])) {
                // we've been passed a verifier, that means that we're in the middle of authentication.
                $rdio->complete_authentication($_GET['oauth_verifier']);
                // save the new token in our session
                $this->session->set_userdata('oauth_token', $rdio->token[0]);
                $this->session->set_userdata('oauth_token_secret', $rdio->token[1]);
                // i know we don't NEED this, but its nice to have a dedicated value for this
                $this->session->set_userdata('logged_in', TRUE); 
            }
            redirect('/home/inside', 'refresh');
        } else {
            //try to authenticate again, we dont have the session keys, 
            //we got here by accident
            $this->auth();
        }
    }
    
}
