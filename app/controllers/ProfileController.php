<?php
class ProfileController extends Controller
{
    public function index(): void
    {
        Middleware::requireAuth();
        $this->view('profile/index', ['pageTitle' => 'My Profile']);
    }
}
