<?php namespace App\Controllers;

use CodeIgniter\Controller;

/**
 * Home Controller
 *
 * Handles the homepage and public landing page
 */
class Home extends BaseController
{
    /**
     * Display homepage
     */
    public function index()
    {
        // If user is already logged in, redirect to dashboard
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }

        return view('home/index');
    }
}
