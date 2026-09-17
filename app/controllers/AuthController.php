<?php

/**
 * Auth Controller
 * Handles login, logout, and registration.
 */
class AuthController extends Controller
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // ── Login ────────────────────────────────────────────────

    /**
     * GET /auth/login
     */
    public function login(): void
    {
        Middleware::requireGuest();

        $stats    = $this->userModel->getLoginStats();
        $programs = $this->userModel->getPrograms();

        $this->view('auth/login', compact('stats', 'programs'), 'auth');
    }

    /**
     * POST /auth/login
     */
    public function loginPost(): void
    {
        Middleware::requireGuest();
        $this->validateCsrf();

        $email    = $this->input('email', '');
        $password = $this->input('password', '');

        // Basic input validation
        if (empty($email) || empty($password)) {
            Session::flash('error', 'Email and password are required.');
            $this->redirect('auth/login');
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            Session::flash('error', 'Invalid email or password.');
            auditLog('login_failed', 'auth', null, "Failed login attempt for: {$email}");
            $this->redirect('auth/login');
        }

        // Successful login
        Auth::login($user);
        $this->userModel->updateLastLogin((int) $user['id']);
        auditLog('login', 'auth', (int) $user['id'], "User logged in: {$email}");

        Session::flash('success', 'Welcome back, ' . $user['first_name'] . '!');
        $this->redirect('dashboard');
    }

    // ── Logout ───────────────────────────────────────────────

    /**
     * GET /auth/logout
     */
    public function logout(): void
    {
        $userId = Auth::id();
        auditLog('logout', 'auth', $userId, 'User logged out');
        Auth::logout();
        Session::flash('success', 'You have been logged out successfully.');
        $this->redirect('auth/login');
    }

    // ── Registration ─────────────────────────────────────────

    /**
     * GET /auth/register
     */
    public function register(): void
    {
        Middleware::requireGuest();

        $programs = $this->userModel->getPrograms();
        $stats    = $this->userModel->getLoginStats();

        $this->view('auth/register', compact('programs', 'stats'), 'auth');
    }

    /**
     * POST /auth/register
     */
    public function registerPost(): void
    {
        Middleware::requireGuest();
        $this->validateCsrf();

        $data = [
            'first_name'   => $this->input('first_name'),
            'last_name'    => $this->input('last_name'),
            'email'        => $this->input('email'),
            'password'     => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
            'role'         => 'student', // self-registration is student only
            'program_id'   => $this->input('program_id'),
            'employee_id'  => $this->input('student_id'),
            'contact_no'   => $this->input('contact_no'),
        ];

        // Validate
        $v = Validator::make($data)
            ->required('first_name', 'First Name')
            ->required('last_name', 'Last Name')
            ->required('email', 'Email Address')
            ->email('email', 'Email Address')
            ->required('program_id', 'Program')
            ->minLength('password', 8, 'Password')
            ->confirmed('password', 'password_confirm', 'Password');

        if ($v->fails()) {
            foreach ($v->allErrors() as $error) {
                Session::flash('error', $error);
            }
            Session::set('reg_old', $data);
            $this->redirect('auth/register');
        }

        // Check duplicate email
        if ($this->userModel->emailExists($data['email'])) {
            Session::flash('error', 'That email address is already registered.');
            Session::set('reg_old', $data);
            $this->redirect('auth/register');
        }

        // Create user
        $userId = $this->userModel->register($data);
        auditLog('register', 'auth', (int) $userId, "New student registration: {$data['email']}");

        Session::flash('success', 'Account created successfully! You can now sign in.');
        $this->redirect('auth/login');
    }
}
