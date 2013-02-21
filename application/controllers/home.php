<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home extends CI_Controller
{
    /*
     * default view
     */
    public function index()
    {
        //var_dump($this->session->all_userdata());
        if ($this->session->userdata('logged_in') == TRUE) {
            redirect('/home/inside', 'refresh');
        }
        $this->load->view('header');
        $this->load->view('home_view');
        $this->load->view('footer');
    }
    
    /*
     * render this when the user is authenticated 
     */
    public function inside()
    {
        //var_dump($this->session->all_userdata());
        if ($this->session->userdata('logged_in') == TRUE) {
            $this->load->view('header');
            $this->load->view('inside_view');
            $this->load->view('footer');
        } else {
            redirect('/home/index', 'refresh');
        }
    }
}